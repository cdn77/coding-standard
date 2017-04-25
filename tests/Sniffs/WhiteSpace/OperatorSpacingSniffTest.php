<?php

declare(strict_types=1);

namespace Cdn77CodingStandard\Sniffs\WhiteSpace;

use Cdn77CodingStandard\Sniffs\TestCase;

class OperatorSpacingSniffTest extends TestCase
{
    public function testNoErrors() : void
    {
        $this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/OperatorSpacingSniffNoErrors.php'));
    }

    public function testErrors() : void
    {
        $file = $this->checkFile(__DIR__ . '/data/OperatorSpacingSniffErrors.php');

        $this->assertSame(2, $file->getErrorCount());

        $this->assertSniffError($file, 5, 'NoSpaceBefore');
        $this->assertSniffError($file, 5, 'NoSpaceAfter');
    }
}
