<?php

declare(strict_types=1);

namespace Cdn77CodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function array_key_exists;
use function in_array;
use function sprintf;
use const T_COMMENT;
use const T_CONST;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_FUNCTION;
use const T_PRIVATE;
use const T_PROTECTED;
use const T_PUBLIC;
use const T_SEMICOLON;
use const T_STRING;
use const T_VAR;
use const T_VARIABLE;

class PropertyAndConstantSpacingSniff implements Sniff
{
    public const CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY = 'IncorrectCountOfBlankLinesAfterProperty';

    /** @var int */
    public $minLinesCountBeforePropertyWithComment = 1;

    /** @var int */
    public $maxLinesCountBeforePropertyWithComment = 1;

    /** @var int */
    public $minLinesCountBeforePropertyWithoutComment = 0;

    /** @var int */
    public $maxLinesCountBeforePropertyWithoutComment = 1;

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_CONST, T_VAR, T_PUBLIC, T_PROTECTED, T_PRIVATE];
    }

    /**
     * @param int $pointer
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     */
    public function process(File $file, $pointer) : int
    {
        $tokens = $file->getTokens();

        $nextFunctionPointer = TokenHelper::findNext($file, [T_FUNCTION, T_STRING, T_VARIABLE], $pointer + 1);
        if ($nextFunctionPointer === null || $tokens[$nextFunctionPointer]['code'] === T_FUNCTION) {
            return $nextFunctionPointer ?? $pointer;
        }

        $nextSemicolon = TokenHelper::findNext($file, [T_SEMICOLON], $pointer);
        if ($nextSemicolon === null) {
            return $pointer;
        }

        $firstOnLinePointer = $this->findFirstTokenOnNextLine($file, $nextSemicolon);
        if ($firstOnLinePointer === null) {
            return $pointer;
        }

        $nextFunctionPointer = TokenHelper::findNext($file, [T_FUNCTION, T_STRING, T_VARIABLE], $firstOnLinePointer);
        if ($nextFunctionPointer === null || $tokens[$nextFunctionPointer]['code'] === T_FUNCTION) {
            return $nextFunctionPointer ?? $firstOnLinePointer;
        }

        $types = [T_COMMENT, T_DOC_COMMENT_OPEN_TAG, T_CONST, T_VAR, T_PUBLIC, T_PROTECTED, T_PRIVATE];
        $nextPointer = TokenHelper::findNext($file, $types, $firstOnLinePointer);

        $linesBetween = $tokens[$nextPointer]['line'] - $tokens[$nextSemicolon]['line'] - 1;
        if (in_array($tokens[$nextPointer]['code'], [T_DOC_COMMENT_OPEN_TAG, T_COMMENT], true)) {
            $minExpectedLines = $this->minLinesCountBeforePropertyWithComment;
            $maxExpectedLines = $this->maxLinesCountBeforePropertyWithComment;
        } else {
            $minExpectedLines = $this->minLinesCountBeforePropertyWithoutComment;
            $maxExpectedLines = $this->maxLinesCountBeforePropertyWithoutComment;
        }

        if ($linesBetween >= $minExpectedLines && $linesBetween <= $maxExpectedLines) {
            return $firstOnLinePointer;
        }

        $message = 'Expected %d to %d blank lines after property/constant, found %d';
        $error = sprintf($message, $minExpectedLines, $maxExpectedLines, $linesBetween);
        $fix = $file->addFixableError($error, $pointer, self::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY);
        if (! $fix) {
            return $firstOnLinePointer;
        }

        $file->fixer->beginChangeset();

        if ($linesBetween > $maxExpectedLines) {
            for ($i = $firstOnLinePointer; $i < $firstOnLinePointer + $maxExpectedLines; $i++) {
                $file->fixer->replaceToken($i, '');
            }
        } else {
            for ($i = 0; $i < $minExpectedLines; $i++) {
                $file->fixer->addNewlineBefore($firstOnLinePointer);
            }
        }

        $file->fixer->endChangeset();

        return $firstOnLinePointer;
    }

    private function findFirstTokenOnNextLine(File $file, int $pointer) : ?int
    {
        $tokens = $file->getTokens();
        $line = $tokens[$pointer]['line'];

        do {
            $pointer++;
            if (! array_key_exists($pointer, $tokens)) {
                return null;
            }
        } while ($tokens[$pointer]['line'] === $line);

        return $pointer;
    }
}
