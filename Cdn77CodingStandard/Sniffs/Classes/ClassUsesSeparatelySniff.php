<?php

declare(strict_types=1);

namespace Cdn77CodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Helpers\UseStatementHelper;

/**
 * This sniff ensures that trait uses are written as separate statements.
 */
class ClassUsesSeparatelySniff implements \PHP_CodeSniffer_Sniff
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
    public function process(\PHP_CodeSniffer_File $file, $pointer) : void
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
                || UseStatementHelper::isAnonymousFunctionUse($file, $useTokenPointer)
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
