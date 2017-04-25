<?php

class Foo
{
    public $noCommentPublic;

    protected $noCommentProtected;

    private $noCommentPrivate;

    var $noCommentOldStyle;

    /** @var int */
    public $withVarOnly;

    /** @var int */
    public $withVarOnlyOldStyle;

    /**
     * Foo
     * @var int
     */
    public $withVarAndDescription;

    /**
     * Foo
     */
    public $withDescriptionOnly;

    /**
     * @see http://example.com/
     */
    public $withSomeAnnotationOnly;

    /**
     * @internal
     * @var foo
     */
    public $withVarAndAnotherAnnotation;

    /**
     * @var int
     * Foo
     */
    public $withVarAndTextOnNextLine;
}
