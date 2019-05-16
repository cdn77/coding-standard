<?php

declare(strict_types=1);

namespace Cdn77CodingStandard\Tests\Sniffs;

use Cdn77CodingStandard\Tests\TestCase;

class OperatorSpacingSniffTest extends TestCase
{
    public function testNoErrors() : void
    {
        self::assertNoSniffErrorInFile(self::checkFile(__DIR__ . '/data/OperatorSpacingSniffNoErrors.php'));
    }

    public function testErrors() : void
    {
        $file = self::checkFile(__DIR__ . '/data/OperatorSpacingSniffErrors.php');

        self::assertSame(89, $file->getErrorCount());

        self::assertAllFixedInFile($file);
    }
}
