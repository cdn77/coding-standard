<?php

declare(strict_types=1);

namespace Cdn77CodingStandard\Sniffs\Operators;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\OperatorSpacingSniff as SquizOperatorSpacingSniff;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function strlen;
use const T_INLINE_ELSE;
use const T_INLINE_THEN;
use const T_INSTANCEOF;
use const T_WHITESPACE;

/**
 * We need this sniff until Squiz accepts option allowMultipleStatementsAlignment and adds T_INSTANCEOF handling
 *
 * @see https://github.com/squizlabs/PHP_CodeSniffer/pull/2515
 * @see https://github.com/squizlabs/PHP_CodeSniffer/pull/2516
 */
final class OperatorSpacingSniff extends SquizOperatorSpacingSniff
{
    public const CODE_SPACE_BEFORE = 'SpaceBefore';
    public const CODE_SPACE_AFTER = 'SpaceAfter';

    /** @var bool */
    public $allowMultipleStatementsAlignment = true;

    /**
     * {@inheritdoc}
     */
    public function register() : array
    {
        $tokens = parent::register();
        $tokens[] = T_INSTANCEOF;

        return $tokens;
    }

    /**
     * @param int $pointer
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     */
    public function process(File $file, $pointer) : void
    {
        if ($this->allowMultipleStatementsAlignment) {
            parent::process($file, $pointer);

            return;
        }

        if (! $this->isOperator($file, $pointer)) {
            return;
        }

        $this->ensureOneSpaceBeforeOperator($file, $pointer);
        $this->ensureOneSpaceAfterOperator($file, $pointer);
    }

    private function ensureOneSpaceBeforeOperator(File $file, int $pointer) : void
    {
        if (! $this->shouldValidateBefore($file, $pointer)) {
            return;
        }

        $tokens = $file->getTokens();

        $numberOfSpaces = $this->numberOfSpaces($tokens[$pointer - 1]);
        if ($numberOfSpaces === 1
            || ! $this->recordErrorBefore($file, $pointer, $tokens[$pointer], 1, $numberOfSpaces)) {
            return;
        }

        if ($numberOfSpaces === 0) {
            $file->fixer->addContentBefore($pointer, ' ');

            return;
        }

        $file->fixer->replaceToken($pointer - 1, ' ');
    }

    private function ensureOneSpaceAfterOperator(File $file, int $pointer) : void
    {
        if (! $this->shouldValidateAfter($file, $pointer)) {
            return;
        }

        $tokens = $file->getTokens();

        $numberOfSpaces = $this->numberOfSpaces($tokens[$pointer + 1]);
        if ($numberOfSpaces === 1
            || ! $this->recordErrorAfter($file, $pointer, $tokens[$pointer], 1, $numberOfSpaces)) {
            return;
        }

        if ($numberOfSpaces === 0) {
            $file->fixer->addContent($pointer, ' ');

            return;
        }

        $file->fixer->replaceToken($pointer + 1, ' ');
    }

    private function shouldValidateBefore(File $file, int $pointer) : bool
    {
        $tokens = $file->getTokens();
        $currentToken = $tokens[$pointer];
        $previousToken = $tokens[$pointer - 1];

        if ($currentToken['code'] === T_INLINE_ELSE && $previousToken['code'] === T_INLINE_THEN) {
            return false;
        }

        $previousEffective = TokenHelper::findPreviousEffective($file, $pointer - 1);

        return $previousEffective === null || $currentToken['line'] === $tokens[$previousEffective]['line'];
    }

    /**
     * @param mixed[] $token
     */
    private function recordErrorBefore(File $file, int $pointer, array $token, int $expected, int $found) : bool
    {
        return $file->addFixableError(
            'Expected exactly %d space before "%s"; %d found',
            $pointer,
            self::CODE_SPACE_BEFORE,
            [$expected, $token['content'], $found]
        );
    }

    private function shouldValidateAfter(File $file, int $pointer) : bool
    {
        $tokens = $file->getTokens();

        if (! isset($tokens[$pointer + 1])) {
            return false;
        }

        if ($tokens[$pointer]['code'] === T_INLINE_THEN && $tokens[$pointer + 1]['code'] === T_INLINE_ELSE) {
            return false;
        }

        $nextEffective = TokenHelper::findNextEffective($file, $pointer + 1);

        return $nextEffective === null || $tokens[$pointer]['line'] === $tokens[$nextEffective]['line'];
    }

    /**
     * @param mixed[] $token
     */
    private function recordErrorAfter(File $file, int $pointer, array $token, int $expected, int $found) : bool
    {
        return $file->addFixableError(
            'Expected exactly %d space after "%s"; %d found',
            $pointer,
            self::CODE_SPACE_AFTER,
            [$expected, $token['content'], $found]
        );
    }

    /**
     * @param mixed[] $token
     */
    private function numberOfSpaces(array $token) : int
    {
        if ($token['code'] !== T_WHITESPACE) {
            return 0;
        }

        return strlen($token['content']);
    }
}
