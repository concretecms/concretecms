<?php

namespace Concrete\Tests\Search\Column;

use Concrete\Core\Search\Column\Column;
use Concrete\Tests\TestCase;

class ColumnTest extends TestCase
{
    /**
     * Regression test: a column with no callback configured (an empty
     * getColumnCallback(), which returns null) must degrade to an empty value
     * instead of throwing a TypeError from call_user_func([$obj, null]) and
     * fataling the entire search results page.
     *
     * @see https://github.com/concretecms/concretecms/issues/12961
     */
    public function testNullCallbackReturnsEmptyString()
    {
        $column = new Column('someKey', 'Some Name', null);
        $this->assertSame('', $column->getColumnValue(new \stdClass()));
    }

    public function testEmptyStringCallbackReturnsEmptyString()
    {
        $column = new Column('someKey', 'Some Name', '');
        $this->assertSame('', $column->getColumnValue(new \stdClass()));
    }

    /**
     * A string callback is invoked as a method on the item itself.
     */
    public function testStringCallbackInvokesMethodOnItem()
    {
        $column = new Column('someKey', 'Some Name', 'itemValue');
        $this->assertSame('item-value', $column->getColumnValue(new ColumnTestCallbackFixture()));
    }

    /**
     * An array callback pointing at a static method is invoked with the item
     * passed as the argument (e.g. NameColumn -> Available::getName).
     */
    public function testArrayCallbackInvokesStaticMethodWithItem()
    {
        $column = new Column('someKey', 'Some Name', [ColumnTestCallbackFixture::class, 'renderStatic']);
        $this->assertSame('static:x', $column->getColumnValue('x'));
    }

    /**
     * An array callback pointing at a non-static (instance) method is resolved
     * by instantiating the class first (e.g. FileIDColumn -> getFileID). This
     * is the PHP 8 path guarded by the is_callable() check in getColumnValue().
     */
    public function testArrayCallbackInstantiatesForInstanceMethod()
    {
        $column = new Column('someKey', 'Some Name', [ColumnTestCallbackFixture::class, 'renderInstance']);
        $this->assertSame('instance:x', $column->getColumnValue('x'));
    }
}

class ColumnTestCallbackFixture
{
    public static function renderStatic($obj)
    {
        return 'static:' . $obj;
    }

    public function renderInstance($obj)
    {
        return 'instance:' . $obj;
    }

    public function itemValue()
    {
        return 'item-value';
    }
}
