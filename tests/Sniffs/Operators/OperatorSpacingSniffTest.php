<?php

declare(strict_types=1);

namespace Cdn77CodingStandard\Tests\Sniffs\Operators;

use Cdn77CodingStandard\Tests\TestCase;

class OperatorSpacingSniffTest extends TestCase
{
    public function testNoErrors() : void
    {
        self::assertNoSniffErrorInFile(
            self::checkFile(
                __DIR__ . '/data/OperatorSpacingSniffNoErrors.php',
                ['allowMultipleStatementsAlignment' => false]
            )
        );
    }

    public function testErrors() : void
    {
        $file = self::checkFile(
            __DIR__ . '/data/OperatorSpacingSniffErrors.php',
            ['allowMultipleStatementsAlignment' => false]
        );

        self::assertSame(67, $file->getErrorCount());

        self::assertAllFixedInFile($file);
    }
}
