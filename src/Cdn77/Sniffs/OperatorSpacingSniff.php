<?php

declare(strict_types=1);

namespace Cdn77CodingStandard\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\OperatorSpacingSniff as SquizOperatorSpacingSniff;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function array_merge;
use function array_unique;
use function in_array;
use function strlen;
use const T_CLOSE_PARENTHESIS;
use const T_CLOSE_SHORT_ARRAY;
use const T_CLOSE_SQUARE_BRACKET;
use const T_COMMA;
use const T_COMMENT;
use const T_CONSTANT_ENCAPSED_STRING;
use const T_DEC;
use const T_DNUMBER;
use const T_ENCAPSED_AND_WHITESPACE;
use const T_INC;
use const T_INLINE_ELSE;
use const T_INLINE_THEN;
use const T_INSTANCEOF;
use const T_LNUMBER;
use const T_MINUS;
use const T_NUM_STRING;
use const T_OPEN_PARENTHESIS;
use const T_OPEN_SQUARE_BRACKET;
use const T_SEMICOLON;
use const T_STRING;
use const T_STRING_CONCAT;
use const T_VARIABLE;
use const T_WHITESPACE;

final class OperatorSpacingSniff extends SquizOperatorSpacingSniff
{
    public const CODE_SPACE_BEFORE = 'SpaceBefore';
    public const CODE_SPACE_AFTER = 'SpaceAfter';

    private const UNARY_NO = 0;
    private const UNARY_LEFT = 1;
    private const UNARY_RIGHT = 2;

    /**
     * {@inheritdoc}
     */
    public function register() : array
    {
        return array_unique(
            array_merge(
                Tokens::$comparisonTokens,
                Tokens::$operators,
                Tokens::$assignmentTokens,
                Tokens::$booleanOperators,
                [
                    T_INLINE_THEN,
                    T_INLINE_ELSE,
                    T_STRING_CONCAT,
                    T_INSTANCEOF,
                    T_INC,
                    T_DEC,
                ]
            )
        );
    }

    /**
     * @param int $pointer
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     */
    public function process(File $file, $pointer) : void
    {
        $tokens = $file->getTokens();
        if ($tokens[$pointer]['code'] !== T_MINUS && ! $this->isOperator($file, $pointer)) {
            return;
        }

        $unaryType = $this->getUnaryOperatorType($file, $pointer);
        if ($unaryType === self::UNARY_NO) {
            $this->ensureOneSpaceBeforeOperator($file, $pointer);
            $this->ensureOneSpaceAfterOperator($file, $pointer);

            return;
        }

        if ($unaryType === self::UNARY_LEFT) {
            $this->ensureOneSpaceBeforeOperator($file, $pointer);
            $this->ensureZeroSpaceAfterUnaryOperator($file, $pointer);

            return;
        }

        $this->ensureZeroSpaceBeforeUnaryOperator($file, $pointer);
        $this->ensureOneSpaceAfterOperator($file, $pointer);
    }

    private function ensureOneSpaceBeforeOperator(File $file, int $pointer) : void
    {
        $tokens = $file->getTokens();

        if (! $this->shouldValidateBefore($file, $pointer)) {
            return;
        }

        $expectedSpaces = 1;
        if (in_array($tokens[$pointer]['code'], [T_INC, T_DEC], true)) {
            $previousEffective = TokenHelper::findPreviousEffective($file, $pointer - 1);
            $types = [T_OPEN_PARENTHESIS, T_OPEN_SQUARE_BRACKET];
            if ($previousEffective !== null && in_array($tokens[$previousEffective]['code'], $types, true)) {
                $expectedSpaces = 0;
            }
        }

        $numberOfSpaces = $this->numberOfSpaces($tokens[$pointer - 1]);
        if ($numberOfSpaces === $expectedSpaces
            || ! $this->recordErrorBefore($file, $pointer, $tokens[$pointer], $expectedSpaces, $numberOfSpaces)) {
            return;
        }

        if ($numberOfSpaces < $expectedSpaces) {
            $file->fixer->addContentBefore($pointer, ' ');

            return;
        }

        $file->fixer->replaceToken($pointer - 1, $expectedSpaces === 0 ? '' : ' ');
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

    private function shouldValidateBefore(File $file, int $pointer) : bool
    {
        $tokens = $file->getTokens();
        $currentToken = $tokens[$pointer];
        $previousToken = $tokens[$pointer - 1];

        if ($currentToken['code'] === T_INLINE_ELSE && $previousToken['code'] === T_INLINE_THEN) {
            return false;
        }

        if ($previousToken['code'] === T_COMMENT && $previousToken['line'] < $currentToken['line']) {
            return false;
        }

        $previousEffective = TokenHelper::findPreviousEffective($file, $pointer - 1);

        return $currentToken['line'] === $tokens[$previousEffective]['line'];
    }

    private function getUnaryOperatorType(File $file, int $pointer) : int
    {
        $tokens = $file->getTokens();
        $previousEffective = TokenHelper::findPreviousEffective($file, $pointer - 1);

        if (in_array($tokens[$pointer]['code'], [T_INC, T_DEC], true)) {
            return in_array($tokens[$previousEffective]['code'], [T_VARIABLE, T_STRING], true)
                ? self::UNARY_RIGHT
                : self::UNARY_LEFT;
        }

        if ($tokens[$pointer]['code'] !== T_MINUS) {
            return self::UNARY_NO;
        }

        if ($previousEffective === null) {
            return self::UNARY_LEFT;
        }

        $possibleOperandTypes = [
            T_CONSTANT_ENCAPSED_STRING,
            T_CLOSE_PARENTHESIS,
            T_CLOSE_SHORT_ARRAY,
            T_CLOSE_SQUARE_BRACKET,
            T_DNUMBER,
            T_ENCAPSED_AND_WHITESPACE,
            T_LNUMBER,
            T_NUM_STRING,
            T_STRING,
            T_VARIABLE,
        ];

        return in_array($tokens[$previousEffective]['code'], $possibleOperandTypes, true)
            ? self::UNARY_NO
            : self::UNARY_LEFT;
    }

    private function ensureZeroSpaceAfterUnaryOperator(File $file, int $pointer) : void
    {
        $tokens = $file->getTokens();

        $whitespacePointer = $pointer + 1;
        if (! isset($tokens[$whitespacePointer])) {
            return;
        }

        $numberOfSpaces = $this->numberOfSpaces($tokens[$whitespacePointer]);
        if ($numberOfSpaces === 0
            || ! $this->recordErrorAfter($file, $pointer, $tokens[$pointer], 0, $numberOfSpaces)) {
            return;
        }

        $file->fixer->replaceToken($whitespacePointer, '');
    }

    private function ensureZeroSpaceBeforeUnaryOperator(File $file, int $pointer) : void
    {
        $tokens = $file->getTokens();

        $whitespacePointer = $pointer - 1;
        if (! isset($tokens[$whitespacePointer])) {
            return;
        }

        $numberOfSpaces = $this->numberOfSpaces($tokens[$whitespacePointer]);
        if ($numberOfSpaces === 0
            || ! $this->recordErrorBefore($file, $pointer, $tokens[$pointer], 0, $numberOfSpaces)) {
            return;
        }

        $file->fixer->replaceToken($whitespacePointer, '');
    }

    private function ensureOneSpaceAfterOperator(File $file, int $pointer) : void
    {
        $tokens = $file->getTokens();

        if (! $this->shouldValidateAfter($pointer, $tokens)) {
            return;
        }

        $expectedNumberOfSpaces = 1;
        if (in_array($tokens[$pointer]['code'], [T_INC, T_DEC], true)) {
            $nextEffective = TokenHelper::findNextEffective($file, $pointer + 1);
            $types = [T_CLOSE_PARENTHESIS, T_SEMICOLON, T_COMMA, T_CLOSE_SQUARE_BRACKET];
            if ($nextEffective !== null && in_array($tokens[$nextEffective]['code'], $types, true)) {
                $expectedNumberOfSpaces = 0;
            }
        }

        $numberOfSpaces = $this->numberOfSpaces($tokens[$pointer + 1]);
        if ($numberOfSpaces === $expectedNumberOfSpaces
            || ! $this->recordErrorAfter($file, $pointer, $tokens[$pointer], 1, $numberOfSpaces)) {
            return;
        }

        if ($numberOfSpaces < $expectedNumberOfSpaces) {
            $file->fixer->addContent($pointer, ' ');

            return;
        }

        $file->fixer->replaceToken($pointer + 1, $expectedNumberOfSpaces === 0 ? '' : ' ');
    }

    /**
     * @param mixed[] $tokens
     */
    private function shouldValidateAfter(int $pointer, array $tokens) : bool
    {
        if (! isset($tokens[$pointer + 1])) {
            return false;
        }

        return $tokens[$pointer]['code'] !== T_INLINE_THEN || $tokens[$pointer + 1]['code'] !== T_INLINE_ELSE;
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
