<?php

declare(strict_types=1);

namespace Cdn77CodingStandard\Tests\Sniffs\Arrays;

use Cdn77CodingStandard\Sniffs\Arrays\ArrayCommaSpacesSniff;
use Cdn77CodingStandard\Tests\TestCase;

class ArrayCommaSpacesSniffTest extends TestCase
{
    public function testNoErrors() : void
    {
        self::assertNoSniffErrorInFile(self::checkFile(__DIR__ . '/data/ArrayCommaSpacesSniffNoErrors.php'));
    }

    public function testErrors() : void
    {
        $file = self::checkFile(__DIR__ . '/data/ArrayCommaSpacesSniffErrors.php');

        self::assertSame(5, $file->getErrorCount());

        self::assertSniffError($file, 3, ArrayCommaSpacesSniff::CODE_SPACE_BEFORE_COMMA, '1 found');
        self::assertSniffError($file, 3, ArrayCommaSpacesSniff::CODE_SPACE_AFTER_COMMA, '2 found');
        self::assertSniffError($file, 3, ArrayCommaSpacesSniff::CODE_SPACE_AFTER_COMMA, '0 found');
        self::assertSniffError($file, 3, ArrayCommaSpacesSniff::CODE_SPACE_BEFORE_COMMA, '5 found');
        self::assertSniffError($file, 3, ArrayCommaSpacesSniff::CODE_SPACE_AFTER_COMMA, '5 found');

        self::assertAllFixedInFile($file);
    }
}
