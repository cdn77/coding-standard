<?php

class A
{
}

class B
{
    const SOME = 1;
}

class C
{
    private $some;
}

class D
{
    public function __construct()
    {
    }
}

class E
{
    public function getSome()
    {
    }
}

class F
{
    public function __get($name)
    {
    }
}

class G
{
    const SOME_CONSTANT = 1;

    private $someProperty;

    public function __construct()
    {
    }

    public function getSome()
    {
    }

    public function __get($name)
    {
    }
}

class H
{
    const FIRST_CONSTANT = 1;
    public const SECOND_CONSTANT = 2;

    private $firstProperty;
    var $secondProperty;

    public function __construct()
    {
    }

    public function getSome()
    {
    }

    public function getAny()
    {
    }

    public function __get($name)
    {
    }

    public function __set($name, $value)
    {
    }
}

class I
{
    private $some;

    public function getSome()
    {
    }
}
