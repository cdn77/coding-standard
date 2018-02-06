<?php

declare(strict_types=1);

namespace Cdn77CodingStandard\Sniffs\WhiteSpace;

use Cdn77CodingStandard\Sniffs\TestCase;

class OperatorSpacingSniffTest extends TestCase
{
    public function testNoErrors() : void
    {
        self::assertNoSniffErrorInFile(self::checkFile(__DIR__ . '/data/OperatorSpacingSniffNoErrors.php'));
    }

    public function testErrors() : void
    {
        $file = self::checkFile(__DIR__ . '/data/OperatorSpacingSniffErrors.php');

        self::assertSame(2, $file->getErrorCount());

        self::assertSniffError($file, 5, 'NoSpaceBefore');
        self::assertSniffError($file, 5, 'NoSpaceAfter');
    }
}
