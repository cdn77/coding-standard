<?php

declare(strict_types=1);

namespace Cdn77CodingStandard\Sniffs\WhiteSpace;

use Cdn77CodingStandard\Sniffs\TestCase;

class MethodSpacingSniffTest extends TestCase
{
    public function testNoErrors() : void
    {
        $this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/MethodSpacingSniffNoErrors.php'));
    }

    public function testErrors() : void
    {
        $file = $this->checkFile(__DIR__ . '/data/MethodSpacingSniffErrors.php');

        $this->assertSame(5, $file->getErrorCount());

         $this->assertSniffError($file, 7, MethodSpacingSniff::CODE_INCORRECT_SPACING);
         $this->assertSniffError($file, 17, MethodSpacingSniff::CODE_INCORRECT_SPACING);
         $this->assertSniffError($file, 20, MethodSpacingSniff::CODE_INCORRECT_SPACING);
         $this->assertSniffError($file, 21, MethodSpacingSniff::CODE_INCORRECT_SPACING);
         $this->assertSniffError($file, 35, MethodSpacingSniff::CODE_INCORRECT_SPACING);
    }
}
