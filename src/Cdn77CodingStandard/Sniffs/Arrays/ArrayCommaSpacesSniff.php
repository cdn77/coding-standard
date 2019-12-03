<?php

declare(strict_types=1);

namespace Cdn77CodingStandard\Sniffs\Arrays;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use const T_COMMA;
use const T_OPEN_PARENTHESIS;
use const T_OPEN_SHORT_ARRAY;
use const T_WHITESPACE;

class ArrayCommaSpacesSniff implements Sniff
{
    public const CODE_SPACE_BEFORE_COMMA = 'SpaceBeforeComma';
    public const CODE_SPACE_AFTER_COMMA = 'SpaceAfterComma';

    /**
     * @return mixed[]
     */
    public function register() : array
    {
        return [T_OPEN_SHORT_ARRAY];
    }

    /**
     * @param int $stackPointer
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    public function process(File $phpcsFile, $stackPointer) : int
    {
        $tokens = $phpcsFile->getTokens();

        $arrayStart = $stackPointer;
        $arrayEnd = $tokens[$stackPointer]['bracket_closer'];

        // Check only single-line arrays.
        if ($tokens[$arrayStart]['line'] !== $tokens[$arrayEnd]['line']) {
            return $arrayEnd;
        }

        for ($i = $arrayStart + 1; $i < $arrayEnd; $i++) {
            // Skip bracketed statements, like function calls.
            if ($tokens[$i]['code'] === T_OPEN_PARENTHESIS) {
                $i = $tokens[$i]['parenthesis_closer'];

                continue;
            }

            // Skip bracketed statements, like function calls.
            if ($tokens[$i]['code'] === T_OPEN_PARENTHESIS) {
                $i = $tokens[$i]['parenthesis_closer'];

                continue;
            }

            if ($tokens[$i]['code'] !== T_COMMA) {
                continue;
            }

            // Before checking this comma, make sure we are not at the end of the array.
            $next = $phpcsFile->findNext(T_WHITESPACE, $i + 1, $arrayEnd, true);
            if ($next === false) {
                return $arrayEnd;
            }

            $this->checkWhitespaceBeforeComma($phpcsFile, $i);
            $this->checkWhitespaceAfterComma($phpcsFile, $i);
        }

        return $arrayEnd;
    }

    private function checkWhitespaceBeforeComma(File $phpcsFile, int $comma) : void
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$comma - 1]['code'] !== T_WHITESPACE) {
            return;
        }

        $error = 'Expected 0 spaces between "%s" and comma; %s found';
        $content = $tokens[$comma - 2]['content'];
        $spaceLength = $tokens[$comma - 1]['length'];
        $fix = $phpcsFile->addFixableError($error, $comma, self::CODE_SPACE_BEFORE_COMMA, [$content, $spaceLength]);
        if (! $fix) {
            return;
        }

        $phpcsFile->fixer->replaceToken($comma - 1, '');
    }

    private function checkWhitespaceAfterComma(File $phpcsFile, int $comma) : void
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$comma + 1]['code'] !== T_WHITESPACE) {
            $error = 'Expected 1 space between comma and "%s"; 0 found';
            $content = $tokens[$comma + 1]['content'];
            $fix = $phpcsFile->addFixableError($error, $comma, self::CODE_SPACE_AFTER_COMMA, [$content]);
            if ($fix) {
                $phpcsFile->fixer->addContent($comma, ' ');
            }

            return;
        }

        $spaceLength = $tokens[$comma + 1]['length'];
        if ($spaceLength === 1) {
            return;
        }

        $error = 'Expected 1 space between comma and "%s"; %s found';
        $content = $tokens[$comma + 2]['content'];
        $fix = $phpcsFile->addFixableError($error, $comma, self::CODE_SPACE_AFTER_COMMA, [$content, $spaceLength]);
        if (! $fix) {
            return;
        }

        $phpcsFile->fixer->replaceToken($comma + 1, ' ');
    }
}
