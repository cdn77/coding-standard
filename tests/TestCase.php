<?php

declare(strict_types=1);

namespace Cdn77;

use SlevomatCodingStandard\Sniffs\TestCase as SlevomatTestCase;

use function assert;
use function class_exists;
use function str_replace;
use function strlen;
use function substr;

abstract class TestCase extends SlevomatTestCase
{
    protected static function getSniffClassName(): string
    {
        $class = str_replace('\\Tests\\', '\\', substr(static::class, 0, -strlen('Test')));
        assert(class_exists($class));

        return $class;
    }
}
