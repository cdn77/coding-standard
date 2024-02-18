<?php

declare(strict_types=1);

namespace Cdn77\Sniffs\NamingConventions;

use Cdn77\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

use function array_keys;
use function count;
use function json_encode;

use const JSON_THROW_ON_ERROR;
use const PHP_VERSION_ID;

#[CoversClass(ValidConstantNameSniff::class)]
class ValidConstantNameSniffTest extends TestCase
{
    public function testErrors(): void
    {
        $file = self::checkFile(__DIR__ . '/data/ValidConstantNameTest.inc');

        $errorTypesPerLine = [
            7 => ValidConstantNameSniff::CodeConstantNotMatchPattern,
            8 => ValidConstantNameSniff::CodeConstantNotMatchPattern,
            10 => ValidConstantNameSniff::CodeConstantNotMatchPattern,
            11 => ValidConstantNameSniff::CodeConstantNotMatchPattern,
            17 => ValidConstantNameSniff::CodeClassConstantNotMatchPattern,
            18 => ValidConstantNameSniff::CodeClassConstantNotMatchPattern,
        ];
        $possibleLines = array_keys($errorTypesPerLine);

        $errors = $file->getErrors();
        foreach ($errors as $line => $error) {
            self::assertContains($line, $possibleLines, json_encode($error, JSON_THROW_ON_ERROR));

            $errorType = $errorTypesPerLine[$line];

            self::assertSniffError($file, $line, $errorType);
        }

        self::assertSame(count($errorTypesPerLine), $file->getErrorCount());
    }

    public function testErrorsConstantType(): void
    {
        if (PHP_VERSION_ID < 80300) {
            self::markTestSkipped('Test requires PHP 8.3');
        }

        $file = self::checkFile(__DIR__ . '/data/ValidConstantNameWithTypeTest.inc');

        $errorTypesPerLine = [
            6 => ValidConstantNameSniff::CodeClassConstantNotMatchPattern,
        ];
        $possibleLines = array_keys($errorTypesPerLine);

        $errors = $file->getErrors();
        foreach ($errors as $line => $error) {
            self::assertContains($line, $possibleLines, json_encode($error, JSON_THROW_ON_ERROR));

            $errorType = $errorTypesPerLine[$line];

            self::assertSniffError($file, $line, $errorType);
        }

        self::assertSame(count($errorTypesPerLine), $file->getErrorCount());
    }
}
