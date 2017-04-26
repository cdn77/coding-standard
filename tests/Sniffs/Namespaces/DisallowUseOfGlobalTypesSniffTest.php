<?php

declare(strict_types=1);

namespace Cdn77CodingStandard\Sniffs\Namespaces;

use Cdn77CodingStandard\Sniffs\TestCase;

class DisallowUseOfGlobalTypesSniffTest extends TestCase
{
    public function testNoErrors() : void
    {
        $this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/DisallowUseOfGlobalTypesNoErrors.php'));
    }

    public function testErrors() : void
    {
        $file = $this->checkFile(__DIR__ . '/data/DisallowUseOfGlobalTypesErrors.php');

        $this->assertSame(6, $file->getErrorCount());

        $this->assertSniffError($file, 5, DisallowUseOfGlobalTypesSniff::CODE_USE_CONTAINS_GLOBAL_TYPE);
        $this->assertSniffError($file, 6, DisallowUseOfGlobalTypesSniff::CODE_USE_CONTAINS_GLOBAL_TYPE);
        $this->assertSniffError($file, 7, DisallowUseOfGlobalTypesSniff::CODE_USE_CONTAINS_GLOBAL_TYPE);

        $this->assertSniffError($file, 9, DisallowUseOfGlobalTypesSniff::CODE_USE_CONTAINS_GLOBAL_TYPE);
        $this->assertSniffError($file, 10, DisallowUseOfGlobalTypesSniff::CODE_USE_CONTAINS_GLOBAL_TYPE);
        $this->assertSniffError($file, 11, DisallowUseOfGlobalTypesSniff::CODE_USE_CONTAINS_GLOBAL_TYPE);
    }
}
