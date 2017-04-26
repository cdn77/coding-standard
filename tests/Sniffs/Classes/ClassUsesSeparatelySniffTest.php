<?php

declare(strict_types=1);

namespace Cdn77CodingStandard\Sniffs\Classes;

use Cdn77CodingStandard\Sniffs\TestCase;

class ClassUsesSeparatelySniffTest extends TestCase
{
    public function testNoErrors() : void
    {
        $this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/ClassUsesSeparatelySniffNoErrors.php'));
    }

    public function testErrors() : void
    {
        $file = $this->checkFile(__DIR__ . '/data/ClassUsesSeparatelySniffErrors.php');

        $this->assertSame(7, $file->getErrorCount());

        $this->assertSniffError($file, 5, ClassUsesSeparatelySniff::CODE_MULTIPLE_USES_PER_LINE);
        $this->assertSniffError($file, 6, ClassUsesSeparatelySniff::CODE_MULTIPLE_USES_PER_LINE);
        $this->assertSniffError($file, 9, ClassUsesSeparatelySniff::CODE_MULTIPLE_USES_PER_LINE);
        $this->assertSniffError($file, 14, ClassUsesSeparatelySniff::CODE_MULTIPLE_USES_PER_LINE);
        $this->assertSniffError($file, 15, ClassUsesSeparatelySniff::CODE_MULTIPLE_USES_PER_LINE);
        $this->assertSniffError($file, 18, ClassUsesSeparatelySniff::CODE_MULTIPLE_USES_PER_LINE);
        $this->assertSniffError($file, 27, ClassUsesSeparatelySniff::CODE_MULTIPLE_USES_PER_LINE);
    }
}
