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
    public const CODE_NOT_CAMEL_CAPS = 'NotCamelCaps';
    public const CODE_MEMBER_NOT_CAMEL_CAPS = 'MemberNotCamelCaps';
    public const CODE_PUBLIC_HAS_UNDERSCORE = 'PublicHasUnderscore';
    public const CODE_STRING_NOT_CAMEL_CAPS = 'StringNotCamelCaps';

    public function testErrors(): void
    {
        $file = self::checkFile(__DIR__ . '/data/ValidVariableNameSniffTest.inc');

        $errorTypesPerLine = [
            3 => self::CODE_NOT_CAMEL_CAPS,
            5 => self::CODE_NOT_CAMEL_CAPS,
            10 => self::CODE_MEMBER_NOT_CAMEL_CAPS,
            12 => self::CODE_PUBLIC_HAS_UNDERSCORE,
            15 => self::CODE_MEMBER_NOT_CAMEL_CAPS,
            17 => self::CODE_PUBLIC_HAS_UNDERSCORE,
            20 => self::CODE_MEMBER_NOT_CAMEL_CAPS,
            22 => self::CODE_PUBLIC_HAS_UNDERSCORE,
            25 => self::CODE_MEMBER_NOT_CAMEL_CAPS,
            31 => self::CODE_NOT_CAMEL_CAPS,
            33 => self::CODE_NOT_CAMEL_CAPS,
            36 => self::CODE_STRING_NOT_CAMEL_CAPS,
            37 => self::CODE_STRING_NOT_CAMEL_CAPS,
            39 => self::CODE_STRING_NOT_CAMEL_CAPS,
            42 => self::CODE_NOT_CAMEL_CAPS,
            44 => self::CODE_NOT_CAMEL_CAPS,
            53 => self::CODE_MEMBER_NOT_CAMEL_CAPS,
            58 => self::CODE_MEMBER_NOT_CAMEL_CAPS,
            62 => self::CODE_MEMBER_NOT_CAMEL_CAPS,
            63 => self::CODE_NOT_CAMEL_CAPS,
            64 => self::CODE_NOT_CAMEL_CAPS,
            67 => self::CODE_NOT_CAMEL_CAPS,
            81 => self::CODE_STRING_NOT_CAMEL_CAPS,
            106 => self::CODE_PUBLIC_HAS_UNDERSCORE,
            107 => self::CODE_PUBLIC_HAS_UNDERSCORE,
            108 => self::CODE_PUBLIC_HAS_UNDERSCORE,
            123 => self::CODE_PUBLIC_HAS_UNDERSCORE,
            138 => self::CODE_NOT_CAMEL_CAPS,
            141 => self::CODE_NOT_CAMEL_CAPS,
            146 => self::CODE_MEMBER_NOT_CAMEL_CAPS,
            150 => [self::CODE_NOT_CAMEL_CAPS, self::CODE_NOT_CAMEL_CAPS],
            152 => self::CODE_NOT_CAMEL_CAPS,
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

        self::assertSame(33, $file->getErrorCount());
    }
}
