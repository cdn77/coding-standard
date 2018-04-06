<?php

declare(strict_types=1);

namespace Cdn77CodingStandard\Sniffs\WhiteSpace;

use Cdn77CodingStandard\Sniffs\TestCase;

class EmptyLineAfterTraitUsageSniffTest extends TestCase
{
    public function testNoErrors() : void
    {
        $file = self::checkFile(__DIR__ . '/data/EmptyLineAfterTraitUsageSniffNoErrors.php');
        self::assertNoSniffErrorInFile($file);
    }

    public function testErrors() : void
    {
        $file = self::checkFile(__DIR__ . '/data/EmptyLineAfterTraitUsageSniffErrors.php');
        self::assertSame(3, $file->getErrorCount());

        self::assertSniffError($file, 8, EmptyLineAfterTraitUsageSniff::CODE_NO_EMPTY_LINE);
        self::assertSniffError($file, 17, EmptyLineAfterTraitUsageSniff::CODE_NO_EMPTY_LINE);
        self::assertSniffError($file, 24, EmptyLineAfterTraitUsageSniff::CODE_NO_EMPTY_LINE);

        $this->assertAllFixedInFile($file);
    }
}
