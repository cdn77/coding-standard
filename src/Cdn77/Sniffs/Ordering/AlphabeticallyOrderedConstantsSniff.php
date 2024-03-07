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

use const T_CONST;
use const T_EQUAL;
use const T_OPEN_TAG;
use const T_PRIVATE;
use const T_PROTECTED;
use const T_PUBLIC;
use const T_SEMICOLON;
use const T_STRING;
use const T_WHITESPACE;

/**
 * @phpstan-type TypeNameValueShape array{
 *       type: TypeShape|null,
 *       name: NameShape,
 *       value: ValueShape
 *  }
 * @phpstan-type TypeNameShape array{
 *       type: TypeShape|null,
 *       name: NameShape
 *  }
 * @phpstan-type TypeShape array{
 *       content: string,
 *       ptr: int
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

    /** @param list<TypeNameValueShape> $namesWithValues */
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

            $typePointer = $nameWithValue['type']['ptr'] ?? null;
            $namePointer = $nameWithValue['name']['ptr'];
            FixerHelper::removeBetweenIncluding($file, $typePointer ?? $namePointer, $namePointer);
            $fixer->addContent(
                $typePointer ?? $namePointer,
                $sortedNameAndValueToken['type'] === null ?
                    $sortedNameAndValueToken['name']['content']
                    : sprintf(
                        '%s %s',
                        $sortedNameAndValueToken['type']['content'],
                        $sortedNameAndValueToken['name']['content'],
                    ),
            );

            $value = $nameWithValue['value'];
            FixerHelper::removeBetweenIncluding($file, $value['startPtr'], $value['endPtr']);
            $fixer->addContent($value['startPtr'], $sortedNameAndValueToken['value']['content']);
        }

        $fixer->endChangeset();
    }

    /** @return array<string, list<TypeNameValueShape>> */
    private function findConstantNamesWithValuesByVisibility(File $phpcsFile): array
    {
        $constantNamesWithValues = [];
        $tokens = $phpcsFile->getTokens();

        foreach ($tokens as $stackPtr => $token) {
            if ($token['code'] !== T_CONST) {
                continue;
            }

            $visibility = $this->getVisibility($phpcsFile, $stackPtr);
            $typeAndConstantName = $this->findTypeAndConstantName($phpcsFile, $stackPtr);

            if ($typeAndConstantName === null) {
                continue;
            }

            $equalsTokenPointer = $this->findEqualsPointer($phpcsFile, $typeAndConstantName['name']['ptr']);

            if ($equalsTokenPointer === null) {
                continue;
            }

            $value = $this->findValue($phpcsFile, $equalsTokenPointer);

            if ($value === null) {
                continue;
            }

            $constantNamesWithValues[$visibility][] = [
                'type' => $typeAndConstantName['type'],
                'name' => $typeAndConstantName['name'],
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

    /** @phpstan-return TypeNameShape|null */
    private function findTypeAndConstantName(File $phpcsFile, int $constStackPtr): array|null
    {
        $tokens = $phpcsFile->getTokens();
        $assignmentOperatorTokenPtr = $phpcsFile->findNext(
            types: [T_EQUAL, T_SEMICOLON],
            start: $constStackPtr + 1,
        );

        if ($assignmentOperatorTokenPtr === false || $tokens[$assignmentOperatorTokenPtr]['code'] !== T_EQUAL) {
            return null;
        }

        $constNameTokenPtr = $phpcsFile->findPrevious(
            types: Tokens::$emptyTokens,
            start: $assignmentOperatorTokenPtr - 1,
            end: $constStackPtr + 1,
            exclude: true,
        );

        if ($constNameTokenPtr === false || $tokens[$constNameTokenPtr]['code'] !== T_STRING) {
            return null;
        }

        $type = null;
        $typeTokenPtr = $phpcsFile->findPrevious(
            types: Tokens::$emptyTokens,
            start: $constNameTokenPtr - 1,
            end: $constStackPtr,
            exclude: true,
        );

        if ($typeTokenPtr !== false && $tokens[$typeTokenPtr]['code'] === T_STRING) {
            $type = [
                'content' => $tokens[$typeTokenPtr]['content'],
                'ptr' => $typeTokenPtr,
            ];
        }

        return [
            'type' => $type,
            'name' => [
                'content' => $tokens[$constNameTokenPtr]['content'],
                'lowercaseContent' => strtolower($tokens[$constNameTokenPtr]['content']),
                'ptr' => $constNameTokenPtr,
            ],
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

        $endValueTokenPointer = $startValueTokenPointer;
        $valueToken = $tokens[$endValueTokenPointer];
        $values = [];

        while ($valueToken['code'] !== T_SEMICOLON) {
            if (
                $valueToken['code'] === T_WHITESPACE
                && in_array($valueToken['content'], ["\n", "\r\n", "\r"], true)
            ) {
                return null;
            }

            $values[] = $valueToken['content'];
            $valueToken = $tokens[++$endValueTokenPointer];
        }

        return [
            'content' => implode('', $values),
            'startPtr' => $startValueTokenPointer,
            'endPtr' => $endValueTokenPointer - 1,
        ];
    }
}
