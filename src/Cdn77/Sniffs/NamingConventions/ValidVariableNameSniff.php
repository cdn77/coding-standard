<?php

declare(strict_types=1);

namespace Cdn77\Sniffs\NamingConventions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;
use PHP_CodeSniffer\Util\Tokens;

use function assert;
use function ltrim;
use function preg_match;
use function preg_match_all;
use function sprintf;
use function strpos;
use function substr;
use function ucfirst;

use const T_DOUBLE_COLON;
use const T_NULLSAFE_OBJECT_OPERATOR;
use const T_OBJECT_OPERATOR;
use const T_OPEN_PARENTHESIS;
use const T_STRING;
use const T_WHITESPACE;

class ValidVariableNameSniff extends AbstractVariableSniff
{
    public string $pattern = '\b(([a-zA-Z][a-zA-Z0-9]*?([A-Z][a-zA-Z0-9]*?)*?)|_+)\b';

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
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

                    // There is no way for us to know if the var is public or
                    // private, so we have to ignore a leading underscore if there is
                    // one and just check the main part of the variable name.
                    $originalVarName = $objVarName;
                    if (strpos($objVarName, '_') === 0) {
                        $objVarName = substr($objVarName, 1);
                    }

                    if (! $this->matchesRegex($objVarName)) {
                        $error = 'Member variable "%s" is not in valid camel caps format';
                        $data = [$originalVarName];
                        $phpcsFile->addError($error, $var, 'MemberNotCamelCaps', $data);
                    }
                }
            }
        }

        $objOperator = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 1, null, true);
        if ($tokens[$objOperator]['code'] === T_DOUBLE_COLON) {
            // The variable lives within a class, and is referenced like
            // this: MyClass::$_variable, so we don't know its scope.
            $objVarName = $varName;
            if (strpos($objVarName, '_') === 0) {
                $objVarName = substr($objVarName, 1);
            }

            if (! $this->matchesRegex($objVarName)) {
                $error = 'Member variable "%s" is not in valid camel caps format';
                $data = [$tokens[$stackPtr]['content']];
                $phpcsFile->addError($error, $stackPtr, 'MemberNotCamelCaps', $data);
            }

            return;
        }

        // There is no way for us to know if the var is public or private,
        // so we have to ignore a leading underscore if there is one and just
        // check the main part of the variable name.
        $originalVarName = $varName;
        if (strpos($varName, '_') === 0) {
            $inClass = $phpcsFile->hasCondition($stackPtr, Tokens::$ooScopeTokens);
            if ($inClass === true) {
                $varName = substr($varName, 1);
            }
        }

        if ($this->matchesRegex($varName)) {
            return;
        }

        $error = 'Variable "%s" is not in valid camel caps format';
        $data = [$originalVarName];
        $phpcsFile->addError($error, $stackPtr, 'NotCamelCaps', $data);
    }

    /**
     * Processes class member variables.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
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

        $public = ($memberProps['scope'] !== 'private');
        $errorData = [$varName];

        if ($public === true) {
            if (strpos($varName, '_') === 0) {
                $error = '%s member variable "%s" must not contain a leading underscore';
                $data = [
                    ucfirst($memberProps['scope']),
                    $errorData[0],
                ];
                $phpcsFile->addError($error, $stackPtr, 'PublicHasUnderscore', $data);
            }
        } elseif (strpos($varName, '_') !== 0) {
            $error = 'Private member variable "%s" must contain a leading underscore';
            $phpcsFile->addError($error, $stackPtr, 'PrivateNoUnderscore', $errorData);
        }

        // Remove a potential underscore prefix for testing CamelCaps.
        $varName = ltrim($varName, '_');

        if ($this->matchesRegex($varName)) {
            return;
        }

        $error = 'Member variable "%s" is not in valid camel caps format';
        $phpcsFile->addError($error, $stackPtr, 'MemberNotCamelCaps', $errorData);
    }

    /**
     * Processes the variable found within a double quoted string.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the double quoted
     *                                               string.
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

            if ($this->matchesRegex($varName)) {
                continue;
            }

            $error = 'Variable "%s" is not in valid camel caps format';
            $data = [$varName];
            $phpcsFile->addError($error, $stackPtr, 'StringNotCamelCaps', $data);
        }
    }

    private function matchesRegex(string $varName): bool
    {
        return preg_match(sprintf('~%s~', $this->pattern), $varName) === 1;
    }
}
