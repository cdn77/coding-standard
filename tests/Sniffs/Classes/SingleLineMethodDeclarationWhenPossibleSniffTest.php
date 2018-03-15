<?php

declare(strict_types=1);

namespace Cdn77CodingStandard\Sniffs\Classes;

use Cdn77CodingStandard\Sniffs\TestCase;

final class SingleLineMethodDeclarationWhenPossibleSniffTest extends TestCase
{
    public function testNoErrors() : void
    {
        $file = self::checkFile(__DIR__ . '/data/SingleLineMethodDeclarationWhenPossibleSniffNoErrors.php');
        self::assertNoSniffErrorInFile($file);
    }

    public function testErrors() : void
    {
        $file = self::checkFile(__DIR__ . '/data/SingleLineMethodDeclarationWhenPossibleSniffErrors.php');
        self::assertSame(4, $file->getErrorCount());

        $errors = $file->getErrors();
        self::assertSame('Method "someMethod" can be placed on a single line.', $errors[5][12][0]['message']);
        self::assertSame(
            'Method "someMethodWithNoReturnType" can be placed on a single line.',
            $errors[11][12][0]['message']
        );
        self::assertSame('Method "someMethod" can be placed on a single line.', $errors[20][12][0]['message']);
        self::assertSame(
            'Method "someMethodWithNoReturnType" can be placed on a single line.',
            $errors[25][12][0]['message']
        );
    }
}
