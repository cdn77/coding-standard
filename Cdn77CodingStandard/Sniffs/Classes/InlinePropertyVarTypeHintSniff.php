<?php

declare(strict_types=1);

namespace Cdn77CodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\PropertyHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

class InlinePropertyVarTypeHintSniff implements Sniff
{
    public const CODE_MULTILINE_PROPERTY_COMMENT = 'MultiLinePropertyComment';

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_PUBLIC, T_PROTECTED, T_PRIVATE, T_VAR];
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     * @param int $pointer
     */
    public function process(File $file, $pointer) : void
    {
        $tokens = $file->getTokens();
        $propertyPointer = TokenHelper::findNextEffective($file, $pointer + 1);

        // not a property
        if ($tokens[$propertyPointer]['code'] !== T_VARIABLE) {
            return;
        }

        // only validate properties with comment
        if (!DocCommentHelper::hasDocComment($file, $propertyPointer)) {
            return;
        }

        // only validate properties without description
        if (DocCommentHelper::hasDocCommentDescription($file, $propertyPointer)) {
            return;
        }

        $docCommentStartPointer = DocCommentHelper::findDocCommentOpenToken($file, $propertyPointer);
        $docCommentEndPointer = $file->findNext(
            [T_DOC_COMMENT_CLOSE_TAG],
            $docCommentStartPointer + 1,
            $propertyPointer - 1
        );

        $tagPointer = TokenHelper::findNextExcluding(
            $file,
            [T_DOC_COMMENT_WHITESPACE, T_DOC_COMMENT_STAR],
            $docCommentStartPointer + 1,
            $docCommentEndPointer - 1
        );

        // ignore if the annotation is not a var
        if ($tokens[$tagPointer]['code'] !== T_DOC_COMMENT_TAG || $tokens[$tagPointer]['content'] !== '@var') {
            return;
        }

        for (
            $currentPointer = $file->findNext([T_DOC_COMMENT_STAR], $tagPointer, $docCommentEndPointer - 1);
            $currentPointer !== false;
            $currentPointer = $file->findNext([T_DOC_COMMENT_STAR], $currentPointer + 1, $docCommentEndPointer - 1)
        ) {
            $nextEffectivePointer = TokenHelper::findNextExcluding(
                $file,
                [T_DOC_COMMENT_WHITESPACE, T_DOC_COMMENT_STAR],
                $currentPointer + 1,
                $docCommentEndPointer - 1
            );

            // we found a string or something that is probably meaningful
            if ($nextEffectivePointer !== null) {
                return;
            }
        }

        if ($tokens[$docCommentEndPointer]['line'] - $tokens[$docCommentStartPointer]['line'] !== 0) {
            $file->addError(
                sprintf(
                    'Found multi-line comment for property %s, use one-line instead.',
                    PropertyHelper::getFullyQualifiedName($file, $propertyPointer)
                ),
                $docCommentStartPointer,
                self::CODE_MULTILINE_PROPERTY_COMMENT
            );
        }
    }
}
