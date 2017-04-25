<?php

class A
{
    private $some;
}

class B
{
    public function some()
    {
    }
}

abstract class C
{
    abstract public function some();
}

class D
{
    public static function some()
    {
    }
}

class E
{
    public function some()
    {
    }

    public function any()
    {
    }
}

class F
{
    public function some()
    {
    }

    public function any()
    {
    }

    public function none()
    {
    }
}

class G
{
    private $some;

    public function getSome()
    {
    }
}

class H
{
    private $some;

    public function getSome()
    {
    }

    private $any;

    public function getAny()
    {
    }
}


class I
{
    public function some()
    {
    }
    private $some;
}

class J
{
    private $some;
    public function getSome()
    {
    }

    private $any;
    public function getAny()
    {
    }
}
