<?php

declare(strict_types=1);

namespace Cdn77CodingStandard\Tests\Sniffs\Classes;

use Cdn77CodingStandard\Sniffs\Classes\PropertyAndConstantSpacingSniff;
use Cdn77CodingStandard\Tests\TestCase;

class PropertyAndConstantSpacingSniffTest extends TestCase
{
    public function testNoErrors() : void
    {
        self::assertNoSniffErrorInFile(self::checkFile(__DIR__ . '/data/PropertyAndConstantSpacingSniffNoErrors.php'));
    }

    public function testErrors() : void
    {
        $file = self::checkFile(__DIR__ . '/data/PropertyAndConstantSpacingSniffErrors.php');

        self::assertSame(14, $file->getErrorCount());

        $code = PropertyAndConstantSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY;
        self::assertSniffError($file, 5, $code);
        self::assertSniffError($file, 7, $code);
        self::assertSniffError($file, 9, $code);
        self::assertSniffError($file, 11, $code);
        self::assertSniffError($file, 13, $code);
        self::assertSniffError($file, 15, $code);
        self::assertSniffError($file, 17, $code);
        self::assertSniffError($file, 34, $code);
        self::assertSniffError($file, 38, $code);
        self::assertSniffError($file, 42, $code);
        self::assertSniffError($file, 46, $code);
        self::assertSniffError($file, 51, $code);
        self::assertSniffError($file, 55, $code);
        self::assertSniffError($file, 61, $code);

        self::assertAllFixedInFile($file);
    }
}
