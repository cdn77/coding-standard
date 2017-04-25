<?php

declare(strict_types=1);

namespace Cdn77CodingStandard\Sniffs;

use SlevomatCodingStandard\Sniffs\TestCase as SlevomatTestCase;

abstract class TestCase extends SlevomatTestCase
{
    protected function getSniffPath() : string
    {
        [$namespace, $sniffName] = explode('.', $this->getSniffName(), 2);

        $className = sprintf('%s\Sniffs\%sSniff', $namespace, strtr($sniffName, '.', '\\'));

        return (new \ReflectionClass($className))->getFileName();
    }
}
