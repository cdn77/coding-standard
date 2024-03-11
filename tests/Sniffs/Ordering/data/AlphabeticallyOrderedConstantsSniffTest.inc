<?php

declare(strict_types=1);

namespace Cdn77\Sniffs\Ordering\data;

final class TestClass
{
    public const C = 'c' . PHP_EOL;
    public const A = 'a';

    public const
        IgnoredMultiline1 = 1,
        IgnoredMultiline2 = 2;

    public const D = [123, 'test'];
    public const B = 'b';

    protected const G = 'g';
    protected const string E = 'e';
    protected const int F = 123;
    protected const HB = 'h';
    protected const Ha = 'h';

    private const K = 'k';
    private const J = 'j';
    private const I = 'i';
    private const L = 'l';
}
