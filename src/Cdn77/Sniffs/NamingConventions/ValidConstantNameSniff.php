<?php

declare(strict_types=1);

namespace Cdn77\Sniffs\NamingConventions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

use function preg_match;
use function sprintf;
use function strrpos;
use function strtolower;
use function substr;

use const T_CONST;
use const T_CONSTANT_ENCAPSED_STRING;
use const T_DOUBLE_COLON;
use const T_NULLSAFE_OBJECT_OPERATOR;
use const T_OBJECT_OPERATOR;
use const T_STRING;
use const T_WHITESPACE;

class ValidConstantNameSniff implements Sniff
{
    public const CodeConstantNotMatchPattern = 'ConstantNotUpperCase';
    public const CodeClassConstantNotMatchPattern = 'ClassConstantNotUpperCase';
    private const PatternPascalCase = '\b([A-Z][a-zA-Z0-9]*?([A-Z][a-zA-Z0-9]*?)*?)\b';

    public string $pattern = self::PatternPascalCase;

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return list<int>
     */
    public function register(): array
    {
        return [
            T_STRING,
            T_CONST,
        ];
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int  $stackPtr  The position of the current token in the stack passed in $tokens.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] === T_CONST) {
            // This is a class constant.
            $constant = $phpcsFile->findNext(Tokens::$emptyTokens, $stackPtr + 1, null, true);
            if ($constant === false) {
                return;
            }

            $constName = $tokens[$constant]['content'];

            if ($this->matchesRegex($constName, $this->pattern)) {
                return;
            }

            $error = sprintf('Constant "%%s" does not match pattern "%s"', $this->pattern);
            $data = [$constName];
            $phpcsFile->addError(
                $error,
                $constant,
                self::CodeClassConstantNotMatchPattern,
                $data,
            );

            return;
        }

        // Only interested in define statements now.
        if (strtolower($tokens[$stackPtr]['content']) !== 'define') {
            return;
        }

        // Make sure this is not a method call.
        $prev = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 1, null, true);
        if (
            $tokens[$prev]['code'] === T_OBJECT_OPERATOR
            || $tokens[$prev]['code'] === T_DOUBLE_COLON
            || $tokens[$prev]['code'] === T_NULLSAFE_OBJECT_OPERATOR
        ) {
            return;
        }

        // If the next non-whitespace token after this token
        // is not an opening parenthesis then it is not a function call.
        $openBracket = $phpcsFile->findNext(Tokens::$emptyTokens, $stackPtr + 1, null, true);
        if ($openBracket === false) {
            return;
        }

        // The next non-whitespace token must be the constant name.
        $constPtr = $phpcsFile->findNext(T_WHITESPACE, $openBracket + 1, null, true);
        if ($tokens[$constPtr]['code'] !== T_CONSTANT_ENCAPSED_STRING) {
            return;
        }

        $constName = $tokens[$constPtr]['content'];

        // Strip namespace from constant like /foo/bar/CONSTANT.
        $splitPos = strrpos($constName, '\\');
        if ($splitPos !== false) {
            $prefix = substr($constName, 0, $splitPos + 1);
            $constName = substr($constName, $splitPos + 1);
        }

        if ($this->matchesRegex($constName, $this->pattern)) {
            return;
        }

        $error = sprintf('Constant "%%s" does not match pattern "%s"', $this->pattern);
        $data = [$constName];
        $phpcsFile->addError($error, $stackPtr, self::CodeConstantNotMatchPattern, $data);
    }

    private function matchesRegex(string $varName, string $pattern): bool
    {
        return preg_match(sprintf('~%s~', $pattern), $varName) === 1;
    }
}
