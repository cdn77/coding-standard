<?php

declare(strict_types=1);

namespace Cdn77;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Files\LocalFile;
use PHP_CodeSniffer\Runner;
use PHP_CodeSniffer\Sniffs\Sniff;
use ReflectionClass;
use SlevomatCodingStandard\Sniffs\TestCase as SlevomatTestCase;

use function array_merge;
use function assert;
use function class_exists;
use function count;
use function define;
use function defined;
use function in_array;
use function sprintf;
use function str_replace;
use function strlen;
use function strpos;
use function substr;

abstract class TestCase extends SlevomatTestCase
{
    protected static function getSniffClassName(): string
    {
        $class = str_replace('\\Tests\\', '\\', substr(static::class, 0, -strlen('Test')));
        assert(class_exists($class));

        return $class;
    }

    // phpcs:ignore
    protected static function checkFile(string $filePath, array $sniffProperties = [], array $codesToCheck = [], array $cliArgs = [], array $sniffConfig = []): File
    {
        if (defined('PHP_CODESNIFFER_CBF') === false) {
            // phpcs:ignore
            define('PHP_CODESNIFFER_CBF', false);
        }

        $codeSniffer = new Runner();
        $codeSniffer->config = new Config(array_merge(['-s'], $cliArgs));
        $codeSniffer->init();

        if (count($sniffConfig) > 0) {
            $codeSniffer->ruleset->ruleset[self::getSniffName()] = $sniffConfig;
        }

        if (count($sniffProperties) > 0) {
            $codeSniffer->ruleset->ruleset[self::getSniffName()]['properties'] = $sniffProperties;
        }

        $sniffClassName = static::getSniffClassName();
        $sniff = new $sniffClassName();
        assert($sniff instanceof Sniff);

        $codeSniffer->ruleset->sniffs = [$sniffClassName => $sniff];

        if (count($codesToCheck) > 0) {
            foreach (self::getSniffClassReflection()->getConstants() as $constantName => $constantValue) {
                if (strpos($constantName, 'CODE_') !== 0 || in_array($constantValue, $codesToCheck, true)) {
                    continue;
                }

                $codeSniffer->ruleset->ruleset[sprintf('%s.%s', self::getSniffName(), $constantValue)]['severity'] = 0;
            }
        }

        $codeSniffer->ruleset->populateTokenListeners();

        $file = new LocalFile($filePath, $codeSniffer->ruleset, $codeSniffer->config);
        $file->process();

        return $file;
    }

    private static function getSniffClassReflection(): ReflectionClass
    {
        static $reflections = [];

        $className = static::getSniffClassName();

        return $reflections[$className] ?? $reflections[$className] = new ReflectionClass($className);
    }
}
