<?php
/**
 * @author: Biplob Hossain <biplob.ice@gmail.com>
 */

namespace Concrete\Tests\Search\Column;

use Concrete\Core\Search\Column\Set;
use Concrete\Tests\TestCase;

class SetTest extends TestCase
{
    public function testAddColumnAfterKey()
    {
        $set = new Set();
        $set->addColumn($this->getDummyColumn('a'));
        $set->addColumn($this->getDummyColumn('b'));
        $set->addColumn($this->getDummyColumn('c'));
        $set->addColumnAfterKey($this->getDummyColumn('d'), 'b');
        $columns = $set->getColumns();
        $this->assertEquals('a', $columns[0]->getColumnKey());
        $this->assertEquals('b', $columns[1]->getColumnKey());
        $this->assertEquals('d', $columns[2]->getColumnKey());
        $this->assertEquals('c', $columns[3]->getColumnKey());
    }

    public function testAddColumnBeforeKey()
    {
        $set = new Set();
        $set->addColumn($this->getDummyColumn('a'));
        $set->addColumn($this->getDummyColumn('b'));
        $set->addColumn($this->getDummyColumn('c'));
        $set->addColumnBeforeKey($this->getDummyColumn('d'), 'b');
        $columns = $set->getColumns();
        $this->assertEquals('a', $columns[0]->getColumnKey());
        $this->assertEquals('d', $columns[1]->getColumnKey());
        $this->assertEquals('b', $columns[2]->getColumnKey());
        $this->assertEquals('c', $columns[3]->getColumnKey());
    }

    public function testRemoveColumnByKey()
    {
        $set = new Set();
        $set->addColumn($this->getDummyColumn('a'));
        $set->addColumn($this->getDummyColumn('b'));
        $set->addColumn($this->getDummyColumn('c'));
        $set->removeColumnByKey('b');
        $columns = array_values($set->getColumns());
        $this->assertEquals('a', $columns[0]->getColumnKey());
        $this->assertEquals('c', $columns[1]->getColumnKey());
    }

    private function getDummyColumn($key)
    {
        $mock = $this->getMockBuilder('\Concrete\Core\Search\Column\Column')
            ->getMock();
        $mock->method('getColumnKey')
            ->willReturn($key);
        return $mock;
    }
}
