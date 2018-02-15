<?php

declare(strict_types=1);

namespace Cdn77CodingStandard\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use const T_CLOSE_CURLY_BRACKET;
use const T_FUNCTION;
use const T_SEMICOLON;
use const T_WHITESPACE;
use function array_merge;
use function in_array;
use function sprintf;

/**
 * Decorates Squiz..WhiteSpace.FunctionSpacing to ignore spacing before first / after last class member.
 */
class MethodSpacingSniff implements Sniff
{
    public const CODE_INCORRECT_SPACING = 'IncorrectSpacing';

    /** @var int */
    public $spacing = 1;

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_FUNCTION];
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     * @param int $pointer
     */
    public function process(File $phpcsFile, $pointer) : void
    {
        if (!FunctionHelper::isMethod($phpcsFile, $pointer)) {
            return;
        }

        $this->processSpacing($phpcsFile, $pointer);
    }

    private function processSpacing(File $phpcsFile, int $pointer) : void
    {
        $tokens = $phpcsFile->getTokens();
        $spacing = SniffSettingsHelper::normalizeInteger($this->spacing);

        // find the end of the function, either of its scope or semicolon for abstract methods
        $scopeClosingPointer = $tokens[$pointer]['scope_closer'] ?? $phpcsFile->findNext(T_SEMICOLON, $pointer);

        // nothing found, should not happen
        if ($scopeClosingPointer === false) {
            return;
        }

        // find next non-blank token
        $nextTokenPointer = $phpcsFile->findNext(
            T_WHITESPACE,
            $scopeClosingPointer + 1,
            null,
            true
        );
        // find next non-empty token
        $nextNonEmptyTokenPointer = $phpcsFile->findNext(
            Tokens::$emptyTokens,
            $scopeClosingPointer + 1,
            null,
            true
        );

        // we found the end of the class, ignore
        if ($tokens[$nextTokenPointer]['code'] === T_CLOSE_CURLY_BRACKET) {
            return;
        }

        $nextMethodPointer = $phpcsFile->findNext(
            array_merge(Tokens::$emptyTokens, Tokens::$methodPrefixes),
            $nextNonEmptyTokenPointer,
            null,
            true
        );

        // next method not found, ignore
        if ($nextMethodPointer === false || $tokens[$nextMethodPointer]['code'] !== T_FUNCTION) {
            return;
        }

        $linesDifference = $tokens[$nextNonEmptyTokenPointer]['line'] - $tokens[$scopeClosingPointer]['line'];

        // ignore all lines with comments
        for ($i = $scopeClosingPointer + 1; $i <= $nextNonEmptyTokenPointer; $i++) {
            if (!in_array($tokens[$i]['code'], Tokens::$commentTokens, true)) {
                continue;
            }

            if ($tokens[$i - 1]['code'] === T_WHITESPACE) {
                $linesDifference--;
                continue;
            }

            $linesDifference -= $tokens[$i]['line'] - $tokens[$i - 1]['line'];
        }

        if ($linesDifference - 1 === $spacing) {
            return;
        }

        $phpcsFile->addError(
            sprintf('There must be exactly %d spaces between functions.', $spacing),
            $scopeClosingPointer,
            self::CODE_INCORRECT_SPACING
        );
    }
}
