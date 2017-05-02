<?php

/** @var $foo null */
/** @var $foo Foo */
/** @var $foo Foo|Bar */
/** @var $foo Foo|null */
/** @var $foo Foo|Bar|null */
/** @var $foo Foo|null comment*/
/**
 * @var $foo Foo|Bar|null
 */

/** @var null $foo */
/** @var Foo $foo */
/** @var Foo|Bar $foo */
/** @var Foo|null $foo */
/** @var Foo|Bar|null $foo */
/** @var Foo|Bar|null $foo comment */

/** @var */

class Foo
{
    /** @var int|null */
    private $a;

    /** @var int|string|null */
    private $b;

    /** @var bool|null comment */
    private $c;

    /** @var NULL */
    private $d;

    /** @var Null */
    private $e;

    /**
     * @var string|null
     */
    private $f;

    /**
     * abc
     * @var string|null test
     */
    private $g;

    /**
     * @param int $a
     * @param int|null $b
     * @param int|Foo|null $c
     * @param int|null $d abc
     * @param int|NULL $e
     * @param $f
     * @param    int|null       $g
     */
    private function a($a, $b, $c, $d, $e, $f, $g)
    {
    }

    /**
     * @return int
     */
    private function b()
    {
    }

    /**
     * @return int|null
     */
    private function c()
    {
    }

    /**
     * @return int|string|null comment
     */
    private function d()
    {
    }

    /**
     * @return
     */
    private function e()
    {
    }
}
