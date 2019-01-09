<?php

declare(strict_types=1);

namespace Cdn77CodingStandard\Tests\Sniffs\Classes;

use Cdn77CodingStandard\Sniffs\Classes\SingleLineMethodDeclarationWhenPossibleSniff;
use Cdn77CodingStandard\Tests\TestCase;

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

        $code = SingleLineMethodDeclarationWhenPossibleSniff::CODE_UNNECESSARY_MULTI_LINE_METHOD;
        self::assertSniffError($file, 5, $code);
        self::assertSniffError($file, 11, $code);
        self::assertSniffError($file, 20, $code);
        self::assertSniffError($file, 25, $code);

        self::assertAllFixedInFile($file);
    }
}
