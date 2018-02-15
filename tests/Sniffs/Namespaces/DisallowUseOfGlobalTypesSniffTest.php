<?php

declare(strict_types=1);

namespace Cdn77CodingStandard\Sniffs\Namespaces;

use Cdn77CodingStandard\Sniffs\TestCase;

class DisallowUseOfGlobalTypesSniffTest extends TestCase
{
    public function testNoErrors() : void
    {
        self::assertNoSniffErrorInFile(self::checkFile(__DIR__ . '/data/DisallowUseOfGlobalTypesNoErrors.php'));
    }

    public function testErrors() : void
    {
        $file = self::checkFile(__DIR__ . '/data/DisallowUseOfGlobalTypesErrors.php');

        self::assertSame(2, $file->getErrorCount());

        self::assertSniffError($file, 5, DisallowUseOfGlobalTypesSniff::CODE_USE_CONTAINS_GLOBAL_TYPE);
        self::assertSniffError($file, 6, DisallowUseOfGlobalTypesSniff::CODE_USE_CONTAINS_GLOBAL_TYPE);
    }
}
