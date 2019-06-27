<?php

namespace Concrete\Tests\Workflow;

use Concrete\Core\Entity\Package;
use Concrete\Core\Workflow\Type;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as M;

class WorkflowTypeTest extends ConcreteDatabaseTestCase
{
    use MockeryPHPUnitIntegration;

    protected $tables = [
        'WorkflowTypes',
        'Workflows',
    ];

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
    }

    public function testAdd()
    {
        $type = Type::add('new1', 'New type 1');
        $this->assertInstanceOf(Type::class, $type);
        $this->assertSame(0, $type->getPackageID());
        $pkg = M::mock(Package::class);
        $pkg->shouldReceive('getPackageID')->andReturn(123);
        $type = Type::add('new2', 'New type 2', $pkg);
        $this->assertInstanceOf(Type::class, $type);
        $this->assertSame(123, $type->getPackageID());
    }

    public function testGetByID()
    {
        $this->assertNull(Type::getByID(1));
        $type = Type::add('new', 'New type');
        $this->assertInstanceOf(Type::class, Type::getByID($type->getWorkflowTypeID()));
    }

    public function testGetByHandle()
    {
        $this->assertNull(Type::getByHandle('new'));
        Type::add('new', 'New type');
        $this->assertInstanceOf(Type::class, Type::getByHandle('new'));
    }

    public function testGetList()
    {
        $this->assertSame([], Type::getList());
        Type::add('sample', 'Example');
        $this->assertCount(1, Type::getList());
    }

    public function testGetListByPackage()
    {
        $pkg = M::mock(Package::class);
        $pkg->shouldReceive('getPackageID')->andReturn(1);
        $this->assertSame([], Type::getListByPackage($pkg));
        Type::add('new1', 'New type 1');
        $this->assertCount(0, Type::getListByPackage($pkg));
        Type::add('new2', 'New type 2', $pkg);
        $this->assertCount(1, Type::getListByPackage($pkg));
    }

    public function testProperties()
    {
        $pkg = M::mock(Package::class);
        $pkg->shouldReceive('getPackageID')->andReturn(321);
        $type = Type::add('wft_handle', 'WFT Name', $pkg);
        $this->assertSame('wft_handle', $type->getWorkflowTypeHandle());
        $this->assertSame('WFT Name', $type->getWorkflowTypeName());
        $this->assertSame(321, $type->getPackageID());
    }

    protected function setUp()
    {
        parent::setUp();
        $this->connection()->exec('delete from Workflows');
        $this->connection()->exec('delete from WorkflowTypes');
    }
}
