<?php

namespace Foo;

use A;
use A\B;

class Bar
{
    use A;
    use B\C;
    use D {
        D::f insteadof g;
    }
}

new class {
    use A;
    use B\C;
    use D {
        D::f insteadof g;
    }
};

class Baz
{
    use A;

    public function abc()
    {
        new class {
            use B;
        };
    }
}

function () use ($a): void {
};
