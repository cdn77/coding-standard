<?php

declare(strict_types=1);

namespace Cdn77CodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\UseStatementHelper;
use const T_ANON_CLASS;
use const T_CLASS;
use const T_COMMA;
use const T_SEMICOLON;
use const T_TRAIT;
use const T_USE;

/**
 * This sniff ensures that trait uses are written as separate statements.
 */
class ClassUsesSeparatelySniff implements Sniff
{
    public const CODE_MULTIPLE_USES_PER_LINE = 'MultipleUsesPerLine';

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_CLASS, T_TRAIT, T_ANON_CLASS];
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     * @param int $pointer
     */
    public function process(File $file, $pointer) : void
    {
        $tokens = $file->getTokens();
        $rootScopeToken = $tokens[$pointer];
        $rootScopeOpenerPointer = $rootScopeToken['scope_opener'];
        $rootScopeCloserPointer = $rootScopeToken['scope_closer'];

        $currentPointer = $rootScopeOpenerPointer + 1;
        do {
            $useTokenPointer = $file->findNext(T_USE, $currentPointer, $rootScopeCloserPointer - 1);

            if ($useTokenPointer === false) {
                return;
            }

            $useToken = $tokens[$useTokenPointer];

            // might be closure use or nested class
            if (
                $useToken['level'] - $tokens[$rootScopeOpenerPointer]['level'] !== 1
                || !UseStatementHelper::isTraitUse($file, $useTokenPointer)
            ) {
                $currentPointer++;
                continue;
            }

            $useEndPointer = $useToken['scope_closer'] ?? $file->findNext(T_SEMICOLON, $useTokenPointer);
            $commaPointer = $file->findNext(T_COMMA, $useTokenPointer, $useEndPointer);

            if ($commaPointer !== false) {
                $file->addError(
                    'Multiple trait uses per use statement are forbidden.',
                    $commaPointer,
                    self::CODE_MULTIPLE_USES_PER_LINE
                );
            }

            $currentPointer = $useEndPointer + 1;
        } while ($currentPointer < $rootScopeCloserPointer);
    }
}
