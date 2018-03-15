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

        $errors = $file->getErrors();
        $message = 'Missing empty line after trait use.';
        self::assertSame($message, $errors[8][5][0]['message']);
        self::assertSame($message, $errors[17][5][0]['message']);
        self::assertSame($message, $errors[24][5][0]['message']);
    }
}
