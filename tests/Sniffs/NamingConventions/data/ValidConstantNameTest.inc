<?php
use Exception as My_Exception, foo\bar, baz;
namespace foo;
namespace foo\bar;
namespace bar\foo\baz;

define('SCREAMING_SNAKE_CASE', true);
define('camelCase', true);
define('PascalCase', true);
define('bar\foo\baz\SCREAMING_SNAKE_CASE_WITH_NAMESPACE', true);
define('bar\foo\baz\camelCaseWithNamespace', true);
define("bar\foo\baz\PascalCaseWithNamespace", true);

class TestClass extends MyClass implements MyInterface, YourInterface
{

    const SCREAMING_SNAKE_CASE = 'hello';
    const camelCase = 'hello';
    const PascalCase = 'hello';
}

$foo->define('bar');
$foo->getBar()->define('foo');
Foo::define('bar');
