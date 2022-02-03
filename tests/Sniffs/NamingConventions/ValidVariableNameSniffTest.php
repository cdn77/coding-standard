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
            3 => ValidVariableNameSniff::CODE_DOES_NOT_MATCH_PATTERN,
            5 => ValidVariableNameSniff::CODE_DOES_NOT_MATCH_PATTERN,
            10 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            12 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            15 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            17 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            19 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            20 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            21 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            26 => ValidVariableNameSniff::CODE_DOES_NOT_MATCH_PATTERN,
            28 => ValidVariableNameSniff::CODE_DOES_NOT_MATCH_PATTERN,
            31 => ValidVariableNameSniff::CODE_STRING_DOES_NOT_MATCH_PATTERN,
            32 => ValidVariableNameSniff::CODE_STRING_DOES_NOT_MATCH_PATTERN,
            34 => ValidVariableNameSniff::CODE_STRING_DOES_NOT_MATCH_PATTERN,
            37 => ValidVariableNameSniff::CODE_DOES_NOT_MATCH_PATTERN,
            39 => ValidVariableNameSniff::CODE_DOES_NOT_MATCH_PATTERN,
            48 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            50 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            53 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            55 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            57 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            58 => ValidVariableNameSniff::CODE_DOES_NOT_MATCH_PATTERN,
            59 => ValidVariableNameSniff::CODE_DOES_NOT_MATCH_PATTERN,
            62 => ValidVariableNameSniff::CODE_DOES_NOT_MATCH_PATTERN,
            76 => ValidVariableNameSniff::CODE_STRING_DOES_NOT_MATCH_PATTERN,
            100 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            101 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            102 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            117 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            118 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            128 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            132 => ValidVariableNameSniff::CODE_DOES_NOT_MATCH_PATTERN,
            134 => ValidVariableNameSniff::CODE_DOES_NOT_MATCH_PATTERN,
            135 => ValidVariableNameSniff::CODE_DOES_NOT_MATCH_PATTERN,
            140 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            142 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            144 => [
                ValidVariableNameSniff::CODE_DOES_NOT_MATCH_PATTERN,
                ValidVariableNameSniff::CODE_DOES_NOT_MATCH_PATTERN,
            ],
            146 => ValidVariableNameSniff::CODE_DOES_NOT_MATCH_PATTERN,
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
