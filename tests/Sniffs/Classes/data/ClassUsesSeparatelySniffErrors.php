<?php

class Foo
{
    use A, B;
    use C, D {
        f as g;
    }
    use E,
        F;
}

new class {
    use A, B;
    use C, D {
        f as g;
    }
    use E,
        F;
};

class Bar
{
    public function abc()
    {
        new class {
            use A, B;
        };
    }
}
