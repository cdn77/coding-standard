<?php

declare(strict_types=1);

namespace Cdn77CodingStandard\Sniffs\Classes;

use Cdn77CodingStandard\Sniffs\TestCase;

class InlinePropertyVarTypeHintSniffTest extends TestCase
{
    public function testNoErrors() : void
    {
        $this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/InlinePropertyVarTypeHintSniffNoErrors.php'));
    }

    public function testErrors() : void
    {
        $file = $this->checkFile(__DIR__ . '/data/InlinePropertyVarTypeHintSniffErrors.php');

        $this->assertSame(4, $file->getErrorCount());

        $this->assertSniffError($file, 5, InlinePropertyVarTypeHintSniff::CODE_MULTILINE_PROPERTY_COMMENT);
        $this->assertSniffError($file, 10, InlinePropertyVarTypeHintSniff::CODE_MULTILINE_PROPERTY_COMMENT);
        $this->assertSniffError($file, 19, InlinePropertyVarTypeHintSniff::CODE_MULTILINE_PROPERTY_COMMENT);
        $this->assertSniffError($file, 23, InlinePropertyVarTypeHintSniff::CODE_MULTILINE_PROPERTY_COMMENT);
    }
}
