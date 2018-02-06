<?php

declare(strict_types=1);

namespace Cdn77CodingStandard\Sniffs\Classes;

use Cdn77CodingStandard\Sniffs\TestCase;

class InlinePropertyVarTypeHintSniffTest extends TestCase
{
    public function testNoErrors() : void
    {
        self::assertNoSniffErrorInFile(self::checkFile(__DIR__ . '/data/InlinePropertyVarTypeHintSniffNoErrors.php'));
    }

    public function testErrors() : void
    {
        $file = self::checkFile(__DIR__ . '/data/InlinePropertyVarTypeHintSniffErrors.php');

        self::assertSame(4, $file->getErrorCount());

        self::assertSniffError($file, 5, InlinePropertyVarTypeHintSniff::CODE_MULTILINE_PROPERTY_COMMENT);
        self::assertSniffError($file, 10, InlinePropertyVarTypeHintSniff::CODE_MULTILINE_PROPERTY_COMMENT);
        self::assertSniffError($file, 19, InlinePropertyVarTypeHintSniff::CODE_MULTILINE_PROPERTY_COMMENT);
        self::assertSniffError($file, 23, InlinePropertyVarTypeHintSniff::CODE_MULTILINE_PROPERTY_COMMENT);
    }
}
