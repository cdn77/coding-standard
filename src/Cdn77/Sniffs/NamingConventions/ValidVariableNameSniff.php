<?php

declare(strict_types=1);

namespace Cdn77\Sniffs\NamingConventions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;

use function assert;
use function ltrim;
use function preg_match;
use function preg_match_all;
use function sprintf;

use const T_DOUBLE_COLON;
use const T_NULLSAFE_OBJECT_OPERATOR;
use const T_OBJECT_OPERATOR;
use const T_OPEN_PARENTHESIS;
use const T_STRING;
use const T_WHITESPACE;

class ValidVariableNameSniff extends AbstractVariableSniff
{
    public const CODE_DOES_NOT_MATCH_PATTERN = 'DoesNotMatchPattern';
    public const CODE_MEMBER_DOES_NOT_MATCH_PATTERN = 'MemberDoesNotMatchPattern';
    public const CODE_STRING_DOES_NOT_MATCH_PATTERN = 'StringDoesNotMatchPattern';
    private const PATTERN_CAMEL_CASE = '\b([a-zA-Z][a-zA-Z0-9]*?([A-Z][a-zA-Z0-9]*?)*?)\b';
    private const PATTERN_CAMEL_CASE_OR_UNUSED = '\b(([a-zA-Z][a-zA-Z0-9]*?([A-Z][a-zA-Z0-9]*?)*?)|_+)\b';

    public string $pattern = self::PATTERN_CAMEL_CASE_OR_UNUSED;
    public string $memberPattern = self::PATTERN_CAMEL_CASE;
    public string $stringPattern = self::PATTERN_CAMEL_CASE;

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int  $stackPtr  The position of the current token in the stack passed in $tokens.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    protected function processVariable(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();
        $varName = ltrim($tokens[$stackPtr]['content'], '$');

        // If it's a php reserved var, then its ok.
        if (isset($this->phpReservedVars[$varName]) === true) {
            return;
        }

        $objOperator = $phpcsFile->findNext([T_WHITESPACE], $stackPtr + 1, null, true);
        assert($objOperator !== false);

        if (
            $tokens[$objOperator]['code'] === T_OBJECT_OPERATOR
            || $tokens[$objOperator]['code'] === T_NULLSAFE_OBJECT_OPERATOR
        ) {
            // Check to see if we are using a variable from an object.
            $var = $phpcsFile->findNext([T_WHITESPACE], $objOperator + 1, null, true);
            assert($var !== false);

            if ($tokens[$var]['code'] === T_STRING) {
                $bracket = $phpcsFile->findNext([T_WHITESPACE], $var + 1, null, true);
                if ($tokens[$bracket]['code'] !== T_OPEN_PARENTHESIS) {
                    $objVarName = $tokens[$var]['content'];

                    if (! $this->matchesRegex($objVarName, $this->memberPattern)) {
                        $error = sprintf('Member variable "%%s" does not match pattern "%s"', $this->memberPattern);
                        $data = [$objVarName];
                        $phpcsFile->addError($error, $var, self::CODE_MEMBER_DOES_NOT_MATCH_PATTERN, $data);
                    }
                }
            }
        }

        $objOperator = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 1, null, true);
        if ($tokens[$objOperator]['code'] === T_DOUBLE_COLON) {
            if (! $this->matchesRegex($varName, $this->memberPattern)) {
                $error = sprintf('Member variable "%%s" does not match pattern "%s"', $this->memberPattern);
                $data = [$tokens[$stackPtr]['content']];
                $phpcsFile->addError($error, $stackPtr, self::CODE_MEMBER_DOES_NOT_MATCH_PATTERN, $data);
            }

            return;
        }

        if ($this->matchesRegex($varName, $this->pattern)) {
            return;
        }

        $error = sprintf('Variable "%%s" does not match pattern "%s"', $this->pattern);
        $data = [$varName];
        $phpcsFile->addError($error, $stackPtr, self::CODE_DOES_NOT_MATCH_PATTERN, $data);
    }

    /**
     * Processes class member variables.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int  $stackPtr  The position of the current token in the stack passed in $tokens.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    protected function processMemberVar(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        $varName = ltrim($tokens[$stackPtr]['content'], '$');
        $memberProps = $phpcsFile->getMemberProperties($stackPtr);
        if ($memberProps === []) {
            // Couldn't get any info about this variable, which
            // generally means it is invalid or possibly has a parse
            // error. Any errors will be reported by the core, so
            // we can ignore it.
            return;
        }

        $errorData = [$varName];

        if ($this->matchesRegex($varName, $this->memberPattern)) {
            return;
        }

        $error = sprintf('Member variable "%%s" does not match pattern "%s"', $this->memberPattern);
        $phpcsFile->addError($error, $stackPtr, self::CODE_MEMBER_DOES_NOT_MATCH_PATTERN, $errorData);
    }

    /**
     * Processes the variable found within a double quoted string.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int  $stackPtr  The position of the double quoted string.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    protected function processVariableInString(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        if (
            preg_match_all(
                '|[^\\\]\${?([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)|',
                $tokens[$stackPtr]['content'],
                $matches
            ) === 0
        ) {
            return;
        }

        foreach ($matches[1] as $varName) {
            // If it's a php reserved var, then its ok.
            if (isset($this->phpReservedVars[$varName]) === true) {
                continue;
            }

            if ($this->matchesRegex($varName, $this->stringPattern)) {
                continue;
            }

            $error = sprintf('Variable "%%s" does not match pattern "%s"', $this->stringPattern);
            $data = [$varName];
            $phpcsFile->addError($error, $stackPtr, self::CODE_STRING_DOES_NOT_MATCH_PATTERN, $data);
        }
    }

    private function matchesRegex(string $varName, string $pattern): bool
    {
        return preg_match(sprintf('~%s~', $pattern), $varName) === 1;
    }
}
