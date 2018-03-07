<?php

use Shouldnt\Matter\At\All;

class A
{
    use SomeTraitA;
    use SomeTraitB;
    public function someFunction() : void
    {
    }
}

class B
{
    use SomeTraitA;
    use SomeTraitB;
    /** @var null */
    private $property;
}

class C
{
    use SomeTraitA, SomeTraitB {
        SomeTraitA::foo insteadof SomeTraitB;
        SomeTraitB::bar insteadof SomeTraitA;
        SomeTraitB::foobar as barfoo;
    }
    public const CONST_HERE = 1;
}
