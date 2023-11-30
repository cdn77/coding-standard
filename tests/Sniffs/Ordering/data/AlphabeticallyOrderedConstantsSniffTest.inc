<?php

declare(strict_types=1);

namespace Cdn77\Sniffs\Ordering\data;

final class TestClass
{
    public const C = 'c';
    public const A = 'a';

    public const
        IgnoredMultiline1 = 1,
        IgnoredMultiline2 = 2;

    public const D = [123, 'test'];
    public const B = 'b';

    protected const H = 'h';
    protected const E = 'e';
    protected const G = 'g';
    protected const F = 'f';

    private const K = 'k';
    private const J = 'j';
    private const I = 'i';
    private const L = 'l';
}
