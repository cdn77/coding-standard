<?php

declare(strict_types=1);

namespace Cdn77CodingStandard\Sniffs\WhiteSpace;

use Cdn77CodingStandard\Sniffs\TestCase;

class MethodSpacingSniffTest extends TestCase
{
    public function testNoErrors() : void
    {
        self::assertNoSniffErrorInFile(self::checkFile(__DIR__ . '/data/MethodSpacingSniffNoErrors.php'));
    }

    public function testErrors() : void
    {
        $file = self::checkFile(__DIR__ . '/data/MethodSpacingSniffErrors.php');

        self::assertSame(5, $file->getErrorCount());

         self::assertSniffError($file, 7, MethodSpacingSniff::CODE_INCORRECT_SPACING);
         self::assertSniffError($file, 17, MethodSpacingSniff::CODE_INCORRECT_SPACING);
         self::assertSniffError($file, 20, MethodSpacingSniff::CODE_INCORRECT_SPACING);
         self::assertSniffError($file, 21, MethodSpacingSniff::CODE_INCORRECT_SPACING);
         self::assertSniffError($file, 35, MethodSpacingSniff::CODE_INCORRECT_SPACING);
    }
}
