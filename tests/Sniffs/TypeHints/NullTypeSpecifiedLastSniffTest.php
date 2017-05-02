<?php

declare(strict_types=1);

namespace Cdn77CodingStandard\Sniffs\TypeHints;

use Cdn77CodingStandard\Sniffs\TestCase;

class NullTypeSpecifiedLastSniffTest extends TestCase
{
    public function testNoErrors() : void
    {
        $this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/NullTypeSpecifiedLastNoErrors.php'));
    }

    public function testErrors() : void
    {
        $file = $this->checkFile(__DIR__ . '/data/NullTypeSpecifiedLastErrors.php');

        $this->assertSame(17, count($file->getErrors()));

        $this->assertSniffError($file, 3, NullTypeSpecifiedLastSniff::CODE_NULL_NOT_SPECIFIED_LAST);
        $this->assertSniffError($file, 4, NullTypeSpecifiedLastSniff::CODE_NULL_NOT_SPECIFIED_LAST);
        $this->assertSniffError($file, 5, NullTypeSpecifiedLastSniff::CODE_NULL_NOT_SPECIFIED_LAST);
        $this->assertSniffError($file, 7, NullTypeSpecifiedLastSniff::CODE_NULL_NOT_SPECIFIED_LAST);

        $this->assertSniffError($file, 10, NullTypeSpecifiedLastSniff::CODE_NULL_NOT_SPECIFIED_LAST);
        $this->assertSniffError($file, 11, NullTypeSpecifiedLastSniff::CODE_NULL_NOT_SPECIFIED_LAST);
        $this->assertSniffError($file, 12, NullTypeSpecifiedLastSniff::CODE_NULL_NOT_SPECIFIED_LAST);

        $this->assertSniffError($file, 18, NullTypeSpecifiedLastSniff::CODE_NULL_NOT_SPECIFIED_LAST);
        $this->assertSniffError($file, 21, NullTypeSpecifiedLastSniff::CODE_NULL_NOT_SPECIFIED_LAST);
        $this->assertSniffError($file, 24, NullTypeSpecifiedLastSniff::CODE_NULL_NOT_SPECIFIED_LAST);
        $this->assertSniffError($file, 27, NullTypeSpecifiedLastSniff::CODE_NULL_NOT_SPECIFIED_LAST);
        $this->assertSniffError($file, 31, NullTypeSpecifiedLastSniff::CODE_NULL_NOT_SPECIFIED_LAST);
        $this->assertSniffError($file, 37, NullTypeSpecifiedLastSniff::CODE_NULL_NOT_SPECIFIED_LAST);

        $this->assertSniffError($file, 42, NullTypeSpecifiedLastSniff::CODE_NULL_NOT_SPECIFIED_LAST);
        $this->assertSniffError($file, 43, NullTypeSpecifiedLastSniff::CODE_NULL_NOT_SPECIFIED_LAST);
        $this->assertSniffError($file, 50, NullTypeSpecifiedLastSniff::CODE_NULL_NOT_SPECIFIED_LAST);
        $this->assertSniffError($file, 57, NullTypeSpecifiedLastSniff::CODE_NULL_NOT_SPECIFIED_LAST);
    }
}
