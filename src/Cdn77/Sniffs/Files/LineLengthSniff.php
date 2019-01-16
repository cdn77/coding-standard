<?php

declare(strict_types=1);

namespace Cdn77CodingStandard\Sniffs\Files;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\UseStatementHelper;
use function in_array;
use function is_int;
use function ltrim;
use function strlen;
use function strrpos;
use const T_COMMENT;
use const T_DOC_COMMENT_STRING;
use const T_OPEN_TAG;
use const T_USE;

class LineLengthSniff implements Sniff
{
    public const CODE_LINE_TOO_LONG = 'LineTooLong';

    /**
     * The limit that the length of a line must not exceed.
     *
     * @var int
     */
    public $lineLengthLimit = 120;

    /**
     * Whether or not to ignore comment lines.
     *
     * @var bool
     */
    public $ignoreComments = false;

    /**
     * Whether or not to ignore import lines (use).
     *
     * @var bool
     */
    public $ignoreImports = true;

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_OPEN_TAG];
    }

    /**
     * @param int $pointer
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     */
    public function process(File $file, $pointer) : int
    {
        $tokens = $file->getTokens();
        for ($i = 0; $i < $file->numTokens; $i++) {
            if ($tokens[$i]['column'] !== 1) {
                continue;
            }

            $this->checkLineLength($file, $tokens, $i);
        }

        return $file->numTokens + 1;
    }

    /**
     * @param mixed[] $tokens
     */
    private function checkLineLength(File $file, array $tokens, int $pointer) : void
    {
        if ($tokens[$pointer]['column'] === 1 && $tokens[$pointer]['length'] === 0) {
            // Blank line.
            return;
        }

        $line = $tokens[$pointer]['line'];
        $nextLineStartPtr = $pointer;
        while (isset($tokens[$nextLineStartPtr]) && $line === $tokens[$nextLineStartPtr]['line']) {
            $pointer = $nextLineStartPtr;
            $nextLineStartPtr++;
        }

        if ($tokens[$pointer]['content'] === $file->eolChar) {
            $pointer--;
        }

        $lineLength = $tokens[$pointer]['column'] + $tokens[$pointer]['length'] - 1;
        if ($lineLength <= $this->lineLengthLimit) {
            return;
        }

        if (in_array($tokens[$pointer]['code'], [T_COMMENT, T_DOC_COMMENT_STRING], true)) {
            if ($this->ignoreComments === true) {
                return;
            }

            // If this is a long comment, check if it can be broken up onto multiple lines.
            // Some comments contain unbreakable strings like URLs and so it makes sense
            // to ignore the line length in these cases if the URL would be longer than the max
            // line length once you indent it to the correct level.
            if ($lineLength > $this->lineLengthLimit) {
                $oldLength = strlen($tokens[$pointer]['content']);
                $newLength = strlen(ltrim($tokens[$pointer]['content'], "/#\t "));
                $indent = $tokens[$pointer]['column'] - 1 + $oldLength - $newLength;

                $nonBreakingLength = $tokens[$pointer]['length'];

                $space = strrpos($tokens[$pointer]['content'], ' ');
                if ($space !== false) {
                    $nonBreakingLength -= ($space + 1);
                }

                if ($nonBreakingLength + $indent > $this->lineLengthLimit) {
                    return;
                }
            }
        }

        if ($this->ignoreImports) {
            $usePointer = $file->findPrevious(T_USE, $pointer - 1);
            if (is_int($usePointer)
                && $tokens[$usePointer]['line'] === $tokens[$pointer]['line']
                && !UseStatementHelper::isTraitUse($file, $usePointer)) {
                return;
            }
        }

        $error = 'Line exceeds maximum limit of %s characters; contains %s characters';
        $file->addError(
            $error,
            $pointer,
            self::CODE_LINE_TOO_LONG,
            [$this->lineLengthLimit, $lineLength]
        );
    }
}
