<?php

declare(strict_types=1);

namespace Cdn77CodingStandard\Sniffs\Classes;

use Cdn77CodingStandard\Sniffs\TestCase;

class ClassStructureSniffTest extends TestCase
{
    public function testNoErrors() : void
    {
        self::assertNoSniffErrorInFile(self::checkFile(__DIR__ . '/data/ClassStructureSniffNoErrors.php'));
    }

    public function testErrors() : void
    {
        $file = self::checkFile(__DIR__ . '/data/ClassStructureSniffErrors.php');

        self::assertSame(6, $file->getErrorCount());

        self::assertSniffError($file, 7, ClassStructureSniff::CODE_INVALID_MEMBER_PLACEMENT);
        self::assertSniffError($file, 18, ClassStructureSniff::CODE_INVALID_MEMBER_PLACEMENT);
        self::assertSniffError($file, 33, ClassStructureSniff::CODE_INVALID_MEMBER_PLACEMENT);
        self::assertSniffError($file, 48, ClassStructureSniff::CODE_INVALID_MEMBER_PLACEMENT);
        self::assertSniffError($file, 52, ClassStructureSniff::CODE_INVALID_MEMBER_PLACEMENT);
        self::assertSniffError($file, 56, ClassStructureSniff::CODE_INVALID_MEMBER_PLACEMENT);
    }
}
