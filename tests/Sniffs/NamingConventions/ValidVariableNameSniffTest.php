<?php

declare(strict_types=1);

namespace Cdn77\Sniffs\NamingConventions;

use Cdn77\TestCase;

class ValidVariableNameSniffTest extends TestCase
{
    public const CODE_NOT_CAMEL_CAPS = 'NotCamelCaps';
    public const CODE_MEMBER_NOT_CAMEL_CAPS = 'MemberNotCamelCaps';
    public const CODE_PUBLIC_HAS_UNDERSCORE = 'PublicHasUnderscore';
    public const CODE_PRIVATE_NO_UNDERSCORE = 'PrivateNoUnderscore';
    public const CODE_STRING_NOT_CAMEL_CAPS = 'StringNotCamelCaps';

    public function testErrors(): void
    {
        $file = self::checkFile(__DIR__ . '/data/ValidVariableNameSniffTest.inc');

        self::assertSame(36, $file->getErrorCount());

        self::assertSniffError($file, 3, self::CODE_NOT_CAMEL_CAPS);
        self::assertSniffError($file, 5, self::CODE_NOT_CAMEL_CAPS);
        self::assertSniffError($file, 10, self::CODE_MEMBER_NOT_CAMEL_CAPS);
        self::assertSniffError($file, 12, self::CODE_PUBLIC_HAS_UNDERSCORE);
        self::assertSniffError($file, 15, self::CODE_MEMBER_NOT_CAMEL_CAPS);
        self::assertSniffError($file, 17, self::CODE_PUBLIC_HAS_UNDERSCORE);
        self::assertSniffError($file, 20, self::CODE_MEMBER_NOT_CAMEL_CAPS);
        self::assertSniffError($file, 22, self::CODE_PUBLIC_HAS_UNDERSCORE);
        self::assertSniffError($file, 25, self::CODE_MEMBER_NOT_CAMEL_CAPS);
        self::assertSniffError($file, 27, self::CODE_PRIVATE_NO_UNDERSCORE);
        self::assertSniffError($file, 31, self::CODE_NOT_CAMEL_CAPS);
        self::assertSniffError($file, 33, self::CODE_NOT_CAMEL_CAPS);
        self::assertSniffError($file, 36, self::CODE_STRING_NOT_CAMEL_CAPS);
        self::assertSniffError($file, 37, self::CODE_STRING_NOT_CAMEL_CAPS);
        self::assertSniffError($file, 39, self::CODE_STRING_NOT_CAMEL_CAPS);
        self::assertSniffError($file, 42, self::CODE_NOT_CAMEL_CAPS);
        self::assertSniffError($file, 44, self::CODE_NOT_CAMEL_CAPS);
        self::assertSniffError($file, 53, self::CODE_MEMBER_NOT_CAMEL_CAPS);
        self::assertSniffError($file, 58, self::CODE_MEMBER_NOT_CAMEL_CAPS);
        self::assertSniffError($file, 62, self::CODE_MEMBER_NOT_CAMEL_CAPS);
        self::assertSniffError($file, 63, self::CODE_NOT_CAMEL_CAPS);
        self::assertSniffError($file, 64, self::CODE_NOT_CAMEL_CAPS);
        self::assertSniffError($file, 67, self::CODE_NOT_CAMEL_CAPS);
        self::assertSniffError($file, 81, self::CODE_STRING_NOT_CAMEL_CAPS);
        self::assertSniffError($file, 106, self::CODE_PUBLIC_HAS_UNDERSCORE);
        self::assertSniffError($file, 107, self::CODE_PUBLIC_HAS_UNDERSCORE);
        self::assertSniffError($file, 107, self::CODE_MEMBER_NOT_CAMEL_CAPS);
        self::assertSniffError($file, 108, self::CODE_PUBLIC_HAS_UNDERSCORE);
        self::assertSniffError($file, 111, self::CODE_PRIVATE_NO_UNDERSCORE);
        self::assertSniffError($file, 112, self::CODE_PRIVATE_NO_UNDERSCORE);
        self::assertSniffError($file, 113, self::CODE_PRIVATE_NO_UNDERSCORE);
        self::assertSniffError($file, 114, self::CODE_PRIVATE_NO_UNDERSCORE);
        self::assertSniffError($file, 123, self::CODE_PUBLIC_HAS_UNDERSCORE);
        self::assertSniffError($file, 138, self::CODE_NOT_CAMEL_CAPS);
        self::assertSniffError($file, 141, self::CODE_NOT_CAMEL_CAPS);
        self::assertSniffError($file, 146, self::CODE_MEMBER_NOT_CAMEL_CAPS);
    }
}
