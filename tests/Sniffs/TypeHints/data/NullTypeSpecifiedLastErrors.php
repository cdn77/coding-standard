<?php

/** @var $foo null|Foo */
/** @var $foo Foo|null|Bar */
/** @var $foo Foo|Bar|null|bool comment*/
/**
 * @var $foo Foo|null|Bar
 */

/** @var null|Foo $foo */
/** @var Foo|null|Bar $foo */
/** @var Foo|Bar|null|bool $foo */

/** @var */

class Foo
{
    /** @var null|int */
    private $a;

    /** @var int|null|string */
    private $b;

    /** @var null|bool comment */
    private $c;

    /** @var NULL|bool */
    private $d;

    /**
     * @var null|string
     */
    private $f;

    /**
     * abc
     * @var null|string test
     */
    private $g;

    /**
     * @param null|int $a
     * @param int|null|Foo $b
     */
    private function a($a, $b)
    {
    }

    /**
     * @return null|int
     */
    private function b()
    {
    }

    /**
     * @return int|null|Foo
     */
    private function c()
    {
    }
}
