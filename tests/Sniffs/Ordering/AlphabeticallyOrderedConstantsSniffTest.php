<?php

declare(strict_types=1);

namespace Cdn77\Sniffs\Ordering;

use Cdn77\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

use function array_keys;
use function json_encode;

use const JSON_THROW_ON_ERROR;

#[CoversClass(AlphabeticallyOrderedConstantsSniff::class)]
final class AlphabeticallyOrderedConstantsSniffTest extends TestCase
{
    public function testErrors(): void
    {
        $file = self::checkFile(
            __DIR__ . '/data/AlphabeticallyOrderedConstantsSniffTest.inc',
            sniffConfig: ['severity' => 5],
        );
        $expectedErrors = [
            9 => AlphabeticallyOrderedConstantsSniff::CodeIncorrectConstantOrder,
            19 => AlphabeticallyOrderedConstantsSniff::CodeIncorrectConstantOrder,
            25 => AlphabeticallyOrderedConstantsSniff::CodeIncorrectConstantOrder,
        ];
        $possibleLines = array_keys($expectedErrors);
        $errors = $file->getErrors();

        foreach ($errors as $line => $error) {
            self::assertContains($line, $possibleLines, json_encode($error, JSON_THROW_ON_ERROR));

            $expectedError = $expectedErrors[$line];
            self::assertSniffError($file, $line, $expectedError);
        }

        self::assertSame(3, $file->getErrorCount());

        $file->disableCaching();
        $file->fixer->fixFile();

        self::assertStringEqualsFile(
            __DIR__ . '/data/AlphabeticallyOrderedConstantsSniffTest.fixed.inc',
            $file->fixer->getContents(),
        );
    }
}
