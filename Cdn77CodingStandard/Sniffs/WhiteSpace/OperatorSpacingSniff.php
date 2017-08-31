<?php

declare(strict_types=1);

namespace Cdn77CodingStandard\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\OperatorSpacingSniff as BaseOperatorSpacingSniff;
use SlevomatCodingStandard\Helpers\TokenHelper;

/**
 * Decorates Squiz.Sniffs.WhiteSpace_OperatorSpacing, but ignores declare(...).
 */
class OperatorSpacingSniff implements Sniff
{
    /** @var BaseOperatorSpacingSniff */
    private $decoratedSniff;

    /** @var bool */
    public $ignoreNewlines;

    /** @var string[] */
    public $supportedTokenizers;

    public function __construct()
    {
        $this->decoratedSniff = new BaseOperatorSpacingSniff();
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
    public function process(File $phpcsFile, $pointer) : void
    {
        if ($this->isDeclareStatement($phpcsFile, $pointer)) {
            return;
        }

        $this->decoratedSniff->process($phpcsFile, $pointer);
    }

    private function isDeclareStatement(File $file, int $pointer) : bool
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
