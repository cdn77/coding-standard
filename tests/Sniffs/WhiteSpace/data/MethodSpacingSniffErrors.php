<?php

class A
{
    public function some()
    {
    }
    public function any()
    {
    }
}

abstract class B
{
    public function some()
    {
    }
    public static function any()
    {
    }
    abstract protected static function none();
    public function all()
    {
    }
}

class C
{
    public function some()
    {
    }
    private $some;
    public function any()
    {
    }
    public function none()
    {
    }
    private $none;
    public function all()
    {
    }
}
