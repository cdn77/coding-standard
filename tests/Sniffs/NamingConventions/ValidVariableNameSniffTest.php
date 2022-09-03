<?php

declare(strict_types=1);

namespace Cdn77\Sniffs\NamingConventions;

use Cdn77\TestCase;

use function array_keys;
use function is_array;
use function json_encode;

use const JSON_THROW_ON_ERROR;

class ValidVariableNameSniffTest extends TestCase
{
    public function testErrors(): void
    {
        $file = self::checkFile(__DIR__ . '/data/ValidVariableNameSniffTest.inc');

        $errorTypesPerLine = [
            3 => ValidVariableNameSniff::CodeDoesNotMatchPattern,
            5 => ValidVariableNameSniff::CodeDoesNotMatchPattern,
            10 => ValidVariableNameSniff::CodeMemberDoesNotMatchPattern,
            12 => ValidVariableNameSniff::CodeMemberDoesNotMatchPattern,
            15 => ValidVariableNameSniff::CodeMemberDoesNotMatchPattern,
            17 => ValidVariableNameSniff::CodeMemberDoesNotMatchPattern,
            19 => ValidVariableNameSniff::CodeMemberDoesNotMatchPattern,
            20 => ValidVariableNameSniff::CodeMemberDoesNotMatchPattern,
            21 => ValidVariableNameSniff::CodeMemberDoesNotMatchPattern,
            26 => ValidVariableNameSniff::CodeDoesNotMatchPattern,
            28 => ValidVariableNameSniff::CodeDoesNotMatchPattern,
            31 => ValidVariableNameSniff::CodeStringDoesNotMatchPattern,
            32 => ValidVariableNameSniff::CodeStringDoesNotMatchPattern,
            34 => ValidVariableNameSniff::CodeStringDoesNotMatchPattern,
            37 => ValidVariableNameSniff::CodeDoesNotMatchPattern,
            39 => ValidVariableNameSniff::CodeDoesNotMatchPattern,
            48 => ValidVariableNameSniff::CodeMemberDoesNotMatchPattern,
            50 => ValidVariableNameSniff::CodeMemberDoesNotMatchPattern,
            53 => ValidVariableNameSniff::CodeMemberDoesNotMatchPattern,
            55 => ValidVariableNameSniff::CodeMemberDoesNotMatchPattern,
            57 => ValidVariableNameSniff::CodeMemberDoesNotMatchPattern,
            58 => ValidVariableNameSniff::CodeDoesNotMatchPattern,
            59 => ValidVariableNameSniff::CodeDoesNotMatchPattern,
            62 => ValidVariableNameSniff::CodeDoesNotMatchPattern,
            76 => ValidVariableNameSniff::CodeStringDoesNotMatchPattern,
            100 => ValidVariableNameSniff::CodeMemberDoesNotMatchPattern,
            101 => ValidVariableNameSniff::CodeMemberDoesNotMatchPattern,
            102 => ValidVariableNameSniff::CodeMemberDoesNotMatchPattern,
            117 => ValidVariableNameSniff::CodeMemberDoesNotMatchPattern,
            118 => ValidVariableNameSniff::CodeMemberDoesNotMatchPattern,
            128 => ValidVariableNameSniff::CodeMemberDoesNotMatchPattern,
            132 => ValidVariableNameSniff::CodeDoesNotMatchPattern,
            134 => ValidVariableNameSniff::CodeDoesNotMatchPattern,
            135 => ValidVariableNameSniff::CodeDoesNotMatchPattern,
            140 => ValidVariableNameSniff::CodeMemberDoesNotMatchPattern,
            142 => ValidVariableNameSniff::CodeMemberDoesNotMatchPattern,
            144 => [
                ValidVariableNameSniff::CodeDoesNotMatchPattern,
                ValidVariableNameSniff::CodeDoesNotMatchPattern,
            ],
            146 => ValidVariableNameSniff::CodeDoesNotMatchPattern,
        ];
        $possibleLines = array_keys($errorTypesPerLine);

        $errors = $file->getErrors();
        foreach ($errors as $line => $error) {
            self::assertContains($line, $possibleLines, json_encode($error, JSON_THROW_ON_ERROR));

            $errorTypes = $errorTypesPerLine[$line];
            if (! is_array($errorTypes)) {
                $errorTypes = [$errorTypes];
            }

            foreach ($errorTypes as $errorType) {
                self::assertSniffError($file, $line, $errorType);
            }
        }

        self::assertSame(41, $file->getErrorCount());
    }
}
