<?php

declare(strict_types=1);

namespace Cdn77CodingStandard\Sniffs\TypeHints;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\TokenHelper;
use const T_DOC_COMMENT_STRING;
use const T_DOC_COMMENT_TAG;
use const T_DOC_COMMENT_WHITESPACE;
use function array_map;
use function count;
use function explode;
use function in_array;
use function preg_split;

class NullTypeSpecifiedLastSniff implements Sniff
{
    public const CODE_NULL_NOT_SPECIFIED_LAST = 'NullNotSpecifiedLast';

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_DOC_COMMENT_TAG];
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     * @param int $pointer
     */
    public function process(File $file, $pointer) : void
    {
        $tokens = $file->getTokens();
        $annotationTagToken = $tokens[$pointer];
        $annotationTagName = $annotationTagToken['content'];

        if (!in_array($annotationTagName, ['@var', '@param', '@return'], true)) {
            return;
        }

        $annotationContentPointer = TokenHelper::findNextExcluding($file, [T_DOC_COMMENT_WHITESPACE], $pointer + 1);
        if ($annotationContentPointer === null || $tokens[$annotationContentPointer]['code'] !== T_DOC_COMMENT_STRING) {
            return;
        }

        $contentParts = preg_split('~\s+~', $tokens[$annotationContentPointer]['content'], 3);
        if ($annotationTagName === '@var') {
            if ($contentParts[0][0] !== '$') {
                $typeDeclaration = $contentParts[0];
            } else {
                if ($contentParts[0][0] !== '$' || !isset($contentParts[1])) {
                    return;
                }

                $typeDeclaration = $contentParts[1];
            }
        } elseif ($annotationTagName === '@param') {
            if ($contentParts[0][0] === '$') {
                return;
            }

            $typeDeclaration = $contentParts[0];
        } else {
            $typeDeclaration = $contentParts[0];
        }

        $this->validateTypeDeclaration($file, $pointer, $typeDeclaration);
    }

    private function validateTypeDeclaration(File $file, int $pointer, string $typeDeclaration) : void
    {
        $types = array_map('strtolower', explode('|', $typeDeclaration));

        if (!in_array('null', $types, true) || $types[count($types) - 1] === 'null') {
            return;
        }

        $file->addError(
            'Null type must be specified as the last type.',
            $pointer,
            self::CODE_NULL_NOT_SPECIFIED_LAST
        );
    }
}
