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
            20 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            22 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            24 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            25 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            26 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            31 => ValidVariableNameSniff::CODE_DOES_NOT_MATCH_PATTERN,
            33 => ValidVariableNameSniff::CODE_DOES_NOT_MATCH_PATTERN,
            36 => ValidVariableNameSniff::CODE_STRING_DOES_NOT_MATCH_PATTERN,
            37 => ValidVariableNameSniff::CODE_STRING_DOES_NOT_MATCH_PATTERN,
            39 => ValidVariableNameSniff::CODE_STRING_DOES_NOT_MATCH_PATTERN,
            42 => ValidVariableNameSniff::CODE_DOES_NOT_MATCH_PATTERN,
            44 => ValidVariableNameSniff::CODE_DOES_NOT_MATCH_PATTERN,
            53 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            55 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            58 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            60 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            62 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            63 => ValidVariableNameSniff::CODE_DOES_NOT_MATCH_PATTERN,
            64 => ValidVariableNameSniff::CODE_DOES_NOT_MATCH_PATTERN,
            67 => ValidVariableNameSniff::CODE_DOES_NOT_MATCH_PATTERN,
            81 => ValidVariableNameSniff::CODE_STRING_DOES_NOT_MATCH_PATTERN,
            106 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            107 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            108 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            123 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            124 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            134 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            138 => ValidVariableNameSniff::CODE_DOES_NOT_MATCH_PATTERN,
            140 => ValidVariableNameSniff::CODE_DOES_NOT_MATCH_PATTERN,
            141 => ValidVariableNameSniff::CODE_DOES_NOT_MATCH_PATTERN,
            146 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            148 => ValidVariableNameSniff::CODE_MEMBER_DOES_NOT_MATCH_PATTERN,
            150 => [
                ValidVariableNameSniff::CODE_DOES_NOT_MATCH_PATTERN,
                ValidVariableNameSniff::CODE_DOES_NOT_MATCH_PATTERN,
            ],
            152 => ValidVariableNameSniff::CODE_DOES_NOT_MATCH_PATTERN,
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

        self::assertSame(43, $file->getErrorCount());
    }
}
