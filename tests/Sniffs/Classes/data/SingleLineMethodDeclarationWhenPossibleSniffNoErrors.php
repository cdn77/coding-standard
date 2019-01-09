<?php

class A
{
    public function singleLine(int $abc, string $efg) : void
    {
    }

    public function singleLineWithNoReturnType(int $abc, string $efg)
    {
    }

    public function multiLine(
        \DateTimeImmutable $someLongNameHere,
        \DateTimeImmutable $andAnotherLongNameOverThere
    ) : void {
    }

    public function multiLineWithNoReturnType(
        \DateTimeImmutable $someLongNameHere,
        \DateTimeImmutable $andAnotherLongNameOverThere
    ) {
    }
}

interface B
{
    public function singleLine(int $abc, string $efg) : void;

    public function singleLineWithNoReturnType(int $abc, string $efg);

    public function multiLine(
        \DateTimeImmutable $someLongNameHere,
        \DateTimeImmutable $andAnotherLongNameOverThere
    ) : void;

    public function multiLineWithNoReturnType(
        \DateTimeImmutable $someLongNameHere,
        \DateTimeImmutable $andAnotherLongNameOverThere
    );
}

function thisSniffOnlyAppliesToMethodsSoFunctionShouldBeIgnored(
    \DateTimeImmutable $someLongNameHere,
    \DateTimeImmutable $andAnotherLongNameOverThere
) : void {
}
