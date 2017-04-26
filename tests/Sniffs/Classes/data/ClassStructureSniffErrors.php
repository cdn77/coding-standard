<?php

class A
{
    private $some;

    const SOME = 1;

    private $any;
}

class B
{
    private $some;

    private $any;

    public const SOME = 1;

    public function __get($name)
    {
    }
}

class C
{
    public const SOME = 1;

    public function __construct()
    {
    }

    private $foo;

    public function getFoo()
    {
    }
}

class D
{
    private $foo;

    public function __get($name)
    {
    }

    public function __construct()
    {
    }

    public function getFoo()
    {
    }

    private const SOME = 1;
}
