<?php

declare(strict_types=1);

namespace Cdn77\Sniffs\Ordering;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\FixerHelper;

use function array_key_first;
use function array_map;
use function implode;
use function in_array;
use function sort;
use function sprintf;
use function strtolower;
use function ucfirst;
use function usort;

use const T_ARRAY;
use const T_CONST;
use const T_EQUAL;
use const T_OPEN_SHORT_ARRAY;
use const T_OPEN_TAG;
use const T_PRIVATE;
use const T_PROTECTED;
use const T_PUBLIC;
use const T_SEMICOLON;
use const T_STRING;

/**
 * @phpstan-type NameWithValueShape array{
 *       name: NameShape,
 *       value: ValueShape
 *  }
 * @phpstan-type NameShape array{
 *       content: string,
 *       lowercaseContent: string,
 *       ptr: int
 *  }
 * @phpstan-type ValueShape array{
 *       content: string,
 *       startPtr: int,
 *       endPtr: int
 *  }
 */
final class AlphabeticallyOrderedConstantsSniff implements Sniff
{
    public const CodeIncorrectConstantOrder = 'IncorrectConstantOrder';

    public function register(): array
    {
        return [T_OPEN_TAG];
    }

    public function process(File $phpcsFile, mixed $stackPtr): void
    {
        $namesWithValuesByVisibility = $this->findConstantNamesWithValuesByVisibility($phpcsFile);

        if ($namesWithValuesByVisibility === []) {
            return;
        }

        foreach ($namesWithValuesByVisibility as $visibility => $namesWithValues) {
            $constantNames = array_map(
                static fn (array $nameWithValue): string => $nameWithValue['name']['lowercaseContent'],
                $namesWithValues,
            );
            $sortedConstantNames = $constantNames;
            sort($sortedConstantNames);

            if ($sortedConstantNames === $constantNames) {
                continue;
            }

            $firstNameWithValue = $namesWithValues[array_key_first($namesWithValues)];
            $fix = $phpcsFile->addFixableError(
                sprintf('%s constant names are not alphabetically ordered.', ucfirst($visibility)),
                $firstNameWithValue['name']['ptr'],
                self::CodeIncorrectConstantOrder,
            );

            if (! $fix) {
                continue;
            }

            $this->fix($phpcsFile, $namesWithValues);
        }
    }

    /** @param list<NameWithValueShape> $namesWithValues */
    private function fix(File $file, array $namesWithValues): void
    {
        $fixer = $file->fixer;
        $sortedNameAndValueTokens = $namesWithValues;
        usort(
            $sortedNameAndValueTokens,
            static fn (array $a, array $b): int => $a['name']['lowercaseContent'] <=> $b['name']['lowercaseContent'],
        );

        $fixer->beginChangeset();

        foreach ($namesWithValues as $key => $nameWithValue) {
            $sortedNameAndValueToken = $sortedNameAndValueTokens[$key];

            $namePointer = $nameWithValue['name']['ptr'];
            FixerHelper::removeBetweenIncluding($file, $namePointer, $namePointer);
            $fixer->addContent($namePointer, $sortedNameAndValueToken['name']['content']);

            $value = $nameWithValue['value'];
            FixerHelper::removeBetweenIncluding($file, $value['startPtr'], $value['endPtr']);
            $fixer->addContent($value['startPtr'], $sortedNameAndValueToken['value']['content']);
        }

        $fixer->endChangeset();
    }

    /** @return array<string, list<NameWithValueShape>> */
    private function findConstantNamesWithValuesByVisibility(File $phpcsFile): array
    {
        $constantNamesWithValues = [];
        $tokens = $phpcsFile->getTokens();

        foreach ($tokens as $stackPtr => $token) {
            if ($token['code'] !== T_CONST) {
                continue;
            }

            $visibility = $this->getVisibility($phpcsFile, $stackPtr);
            $constantName = $this->findConstantName($phpcsFile, $stackPtr);

            if ($constantName === null) {
                continue;
            }

            $equalsTokenPointer = $this->findEqualsPointer($phpcsFile, $constantName['ptr']);

            if ($equalsTokenPointer === null) {
                continue;
            }

            $value = $this->findValue($phpcsFile, $equalsTokenPointer);

            if ($value === null) {
                continue;
            }

            $constantNamesWithValues[$visibility][] = [
                'name' => $constantName,
                'value' => $value,
            ];
        }

        return $constantNamesWithValues;
    }

    private function getVisibility(File $phpcsFile, int $constStackPtr): string
    {
        $tokens = $phpcsFile->getTokens();
        $visibilityTokenPointer = $phpcsFile->findPrevious(
            types: Tokens::$emptyTokens,
            start: $constStackPtr - 1,
            exclude: true,
            local: true,
        );

        return in_array($tokens[$visibilityTokenPointer]['code'], [T_PUBLIC, T_PROTECTED, T_PRIVATE], true)
            ? (string) $tokens[$visibilityTokenPointer]['content']
            : 'public';
    }

    /** @phpstan-return NameShape|null */
    private function findConstantName(File $phpcsFile, int $constStackPtr): array|null
    {
        $tokens = $phpcsFile->getTokens();
        $constantNameTokenPointer = $phpcsFile->findNext(
            types: Tokens::$emptyTokens,
            start: $constStackPtr + 1,
            exclude: true,
            local: true,
        );

        if ($constantNameTokenPointer === false || $tokens[$constantNameTokenPointer]['code'] !== T_STRING) {
            return null;
        }

        return [
            'content' => $tokens[$constantNameTokenPointer]['content'],
            'lowercaseContent' => strtolower($tokens[$constantNameTokenPointer]['content']),
            'ptr' => $constantNameTokenPointer,
        ];
    }

    private function findEqualsPointer(File $phpcsFile, int $constNameStackPtr): int|null
    {
        $tokens = $phpcsFile->getTokens();
        $equalsTokenPointer = $phpcsFile->findNext(
            types: Tokens::$emptyTokens,
            start: $constNameStackPtr + 1,
            exclude: true,
            local: true,
        );

        if ($equalsTokenPointer === false || $tokens[$equalsTokenPointer]['code'] !== T_EQUAL) {
            return null;
        }

        return $equalsTokenPointer;
    }

    /** @phpstan-return ValueShape|null */
    private function findValue(File $phpcsFile, int $equalsTokenPointer): array|null
    {
        $tokens = $phpcsFile->getTokens();
        $startValueTokenPointer = $phpcsFile->findNext(
            types: Tokens::$emptyTokens,
            start: $equalsTokenPointer + 1,
            exclude: true,
            local: true,
        );

        if ($startValueTokenPointer === false) {
            return null;
        }

        $firstValueToken = $tokens[$startValueTokenPointer];
        $endValueTokenPointer = $startValueTokenPointer;
        $content = $firstValueToken['content'];

        if (in_array($firstValueToken['code'], [T_ARRAY, T_OPEN_SHORT_ARRAY], true)) {
            $values = [];
            $endValueTokenPointer = $firstValueToken['bracket_closer'] ?? $firstValueToken['parenthesis_closer'];

            for ($i = $startValueTokenPointer; $i <= $endValueTokenPointer; $i++) {
                $values[] = $tokens[$i]['content'];
            }

            $content = implode('', $values);
        }

        $afterValueTokenPointer = $phpcsFile->findNext(
            types: Tokens::$emptyTokens,
            start: $endValueTokenPointer + 1,
            exclude: true,
            local: true,
        );

        if ($tokens[$afterValueTokenPointer]['code'] !== T_SEMICOLON) {
            return null;
        }

        return [
            'content' => $content,
            'startPtr' => $startValueTokenPointer,
            'endPtr' => $endValueTokenPointer,
        ];
    }
}
