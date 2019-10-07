<?php

declare(strict_types=1);

namespace Cdn77CodingStandard\Tests\Sniffs\Operators;

use Cdn77CodingStandard\Tests\TestCase;

class NegationOperatorSpacingSniffTest extends TestCase
{
    public function testNoErrors() : void
    {
        self::assertNoSniffErrorInFile(self::checkFile(__DIR__ . '/data/NegationOperatorSpacingSniffNoErrors.php'));
    }

    public function testErrors() : void
    {
        $file = self::checkFile(
            __DIR__ . '/data/NegationOperatorSpacingSniffRequireSpaceErrors.php',
            ['requireSpace' => true]
        );

        self::assertSame(97, $file->getErrorCount());

        self::assertAllFixedInFile($file);
    }
}
