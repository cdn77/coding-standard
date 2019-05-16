<?php

declare(strict_types=1);

namespace Cdn77CodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function assert;
use function in_array;
use function is_int;
use function is_string;
use function preg_replace;
use function rtrim;
use function sprintf;
use function str_replace;
use function strlen;
use const T_FUNCTION;
use const T_OPEN_CURLY_BRACKET;
use const T_SEMICOLON;
use const T_WHITESPACE;

class SingleLineMethodDeclarationWhenPossibleSniff implements Sniff
{
    public const CODE_UNNECESSARY_MULTI_LINE_METHOD = 'UnnecessaryMultiLineMethod';

    /** @var int */
    public $maxLineLength = 120;

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_FUNCTION];
    }

    /**
     * @param int $pointer
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     */
    public function process(File $file, $pointer) : void
    {
        if (! FunctionHelper::isMethod($file, $pointer)) {
            return;
        }

        $tokens = $file->getTokens();

        $lineStartPtr = $file->findFirstOnLine(T_WHITESPACE, $pointer);
        assert(is_int($lineStartPtr));

        $methodDeclarationEndPtr = $file->findNext([T_OPEN_CURLY_BRACKET, T_SEMICOLON], $pointer);
        assert(is_int($methodDeclarationEndPtr));

        $declarationEndLine = $tokens[$methodDeclarationEndPtr]['line'];
        if (in_array($tokens[$lineStartPtr]['line'], [$declarationEndLine, $declarationEndLine - 1], true)) {
            return;
        }

        $methodDeclaration = TokenHelper::getContent($file, $lineStartPtr, $methodDeclarationEndPtr - 1);
        $methodDeclaration = preg_replace('~\n +~', ' ', $methodDeclaration);
        assert(is_string($methodDeclaration));

        $methodDeclaration = str_replace(['( ', ' )'], ['(', ')'], $methodDeclaration);
        $methodDeclaration = rtrim($methodDeclaration);

        if (strlen($methodDeclaration) > $this->maxLineLength) {
            return;
        }

        $error = sprintf('Method "%s" can be placed on a single line.', FunctionHelper::getName($file, $pointer));
        $fix = $file->addFixableError($error, $pointer, self::CODE_UNNECESSARY_MULTI_LINE_METHOD);
        if (! $fix) {
            return;
        }

        $whitespaceBeforeMethod = $tokens[$lineStartPtr]['content'];

        $file->fixer->beginChangeset();

        for ($i = $lineStartPtr; $i <= $methodDeclarationEndPtr; $i++) {
            $file->fixer->replaceToken($i, '');
        }

        if ($tokens[$methodDeclarationEndPtr]['code'] === T_OPEN_CURLY_BRACKET) {
            $replacement = sprintf("%s\n%s{", $methodDeclaration, $whitespaceBeforeMethod);
        } else {
            $replacement = $methodDeclaration . ';';
        }

        $file->fixer->replaceToken($lineStartPtr, $replacement);

        $file->fixer->endChangeset();
    }
}
