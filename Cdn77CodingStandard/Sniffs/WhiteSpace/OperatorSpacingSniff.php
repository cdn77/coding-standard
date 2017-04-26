<?php

declare(strict_types=1);

namespace Cdn77CodingStandard\Sniffs\WhiteSpace;

use SlevomatCodingStandard\Helpers\TokenHelper;

/**
 * Decorates Squiz.Sniffs.WhiteSpace_OperatorSpacing, but ignores declare(...).
 */
class OperatorSpacingSniff implements \PHP_CodeSniffer_Sniff
{
    /** @var \Squiz_Sniffs_WhiteSpace_OperatorSpacingSniff */
    private $decoratedSniff;

    /** @var bool */
    public $ignoreNewlines;

    /** @var string[] */
    public $supportedTokenizers;

    public function __construct()
    {
        $this->decoratedSniff = new \Squiz_Sniffs_WhiteSpace_OperatorSpacingSniff();
        $this->supportedTokenizers = &$this->decoratedSniff->supportedTokenizers;
        $this->ignoreNewlines = &$this->decoratedSniff->ignoreNewlines;
    }

    /**
     * @return int[]
     */
    public function register() : array
    {
        return $this->decoratedSniff->register();
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     * @param int $pointer
     */
    public function process(\PHP_CodeSniffer_File $phpcsFile, $pointer) : void
    {
        if ($this->isDeclareStatement($phpcsFile, $pointer)) {
            return;
        }

        $this->decoratedSniff->process($phpcsFile, $pointer);
    }

    private function isDeclareStatement(\PHP_CodeSniffer_File $file, int $pointer) : bool
    {
        $tokens = $file->getTokens();

        // look for "=" sign
        if ($tokens[$pointer]['code'] !== T_EQUAL) {
            return false;
        }

        // look for a string before "=" sign
        $beforeEqualPointer = TokenHelper::findPreviousEffective($file, $pointer - 1);
        if ($beforeEqualPointer === null || $tokens[$beforeEqualPointer]['code'] !== T_STRING) {
            return false;
        }

        // look for opening "("
        $openingParenthesisPointer = TokenHelper::findPreviousEffective($file, $beforeEqualPointer - 1);
        if ($openingParenthesisPointer === null || $tokens[$openingParenthesisPointer]['code'] !== T_OPEN_PARENTHESIS) {
            return false;
        }

        // check whether it actually is a "declare" statement
        $declarePointer = TokenHelper::findPreviousEffective($file, $openingParenthesisPointer - 1);
        return $declarePointer !== null && $tokens[$declarePointer]['code'] === T_DECLARE;
    }
}
