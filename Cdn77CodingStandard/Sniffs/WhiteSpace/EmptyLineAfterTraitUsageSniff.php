<?php

declare(strict_types=1);

namespace Cdn77CodingStandard\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\UseStatementHelper;
use const T_CLOSE_CURLY_BRACKET;
use const T_USE;

final class EmptyLineAfterTraitUsageSniff implements Sniff
{
    public const CODE_NO_EMPTY_LINE = 'NoEmptyLine';

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_USE];
    }

    /**
     * @param int $pointer
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     */
    public function process(File $file, $pointer) : void
    {
        if (!UseStatementHelper::isTraitUse($file, $pointer)) {
            return;
        }

        $tokens = $file->getTokens();

        $firstOnNextLinePtr = TokenHelper::findFirstTokenOnNextLine($file, $pointer);
        $nextPtr = TokenHelper::findNextEffective($file, $firstOnNextLinePtr);
        if ($tokens[$nextPtr]['code'] === T_USE) {
            return; // multiple trait uses, skip to the next one
        }

        $useScopeCondition = $tokens[$pointer]['scope_condition'] ?? null;
        if ($useScopeCondition === null && $tokens[$firstOnNextLinePtr]['content'] === "\n") {
            return; // there is a new line as expected
        }

        if ($useScopeCondition !== null && isset($tokens[$nextPtr]['conditions'][$useScopeCondition])) {
            // complicated trait use with aliases and stuff, skip to the end of it
            $nextPtr = TokenHelper::findFirstTokenOnNextLine($file, $tokens[$pointer]['scope_closer']);
            if ($tokens[$nextPtr]['content'] === "\n") {
                return; // there is a new line as expected
            }
        }

        if ($tokens[$nextPtr]['code'] === T_CLOSE_CURLY_BRACKET
            && isset($tokens[$pointer]['conditions'][$tokens[$nextPtr]['scope_condition']])) {
            return; // end of class, no new line required
        }

        $fix = $file->addFixableError('Missing empty line after trait use.', $pointer, self::CODE_NO_EMPTY_LINE);
        if (!$fix) {
            return;
        }

        $file->fixer->beginChangeset();

        $useEndPtr = TokenHelper::findPreviousEffective($file, $nextPtr - 1);
        $file->fixer->addNewline($useEndPtr);

        $file->fixer->endChangeset();
    }
}
