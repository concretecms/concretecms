<?php

namespace Concrete\Tests\Workflow;

use Concrete\Core\Workflow\Progress\Progress;
use Concrete\Core\Workflow\Request\Request;
use Concrete\Core\Workflow\Type;
use Concrete\Core\Workflow\Workflow;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as M;

class WorkflowTest extends ConcreteDatabaseTestCase
{
    use MockeryPHPUnitIntegration;

    protected $tables = [
        'WorkflowTypes',
        'Workflows',
        'WorkflowProgress',
    ];

    protected function setUp()
    {
        parent::setUp();
        $this->connection()->exec('delete from Workflows');
        $this->connection()->exec('delete from WorkflowTypes');
    }

    public function testBasicProperties()
    {
        $name = 'Test ' . time() . '@' . mt_rand();
        $this->assertNull(Workflow::getByName($name));
        $wt = Type::add('basic', 'WF Type');
        $wf = Workflow::add($wt, $name);
        $this->assertInstanceOf(Workflow::class, $wf);
        $this->assertGreaterThan(0, $wf->getWorkflowID());
        $this->assertSame($name, $wf->getWorkflowName());
        $this->assertSame($name, $wf->getWorkflowDisplayName());
        $this->assertSame($name, $wf->getWorkflowDisplayName('text'));
        $this->assertSame(['cancel', 'approve'], $wf->getAllowedTasks());
        $this->assertEquals($wt, $wf->getWorkflowTypeObject());
        $this->assertSame([], $wf->getRestrictedToPermissionKeyHandles());
        $this->assertInternalType('string', $wf->getPermissionResponseClassName());
        $this->assertNotEmpty($wf->getPermissionResponseClassName());
        //$this->assertTrue(class_exists($wf->getPermissionResponseClassName()), sprintf('Checking that class %s exists.', $wf->getPermissionResponseClassName()));
        $this->assertInternalType('string', $wf->getPermissionAssignmentClassName());
        $this->assertNotEmpty($wf->getPermissionAssignmentClassName());
        $this->assertTrue(class_exists($wf->getPermissionAssignmentClassName()), sprintf('Checking that class %s exists.', $wf->getPermissionAssignmentClassName()));
        $this->assertInternalType('string', $wf->getPermissionObjectKeyCategoryHandle());
        $this->assertNotEmpty($wf->getPermissionObjectKeyCategoryHandle());
        $this->assertSame($wf->getWorkflowID(), $wf->getPermissionObjectIdentifier());
    }

    public function testDelete()
    {
        $this->assertSame([], Type::getList());
        $this->assertSame([], Workflow::getList());
        $type = Type::add('empty', 'Example');
        $this->assertEquals([$type], Type::getList());
        Type::add('basic', 'Example 2');
        $this->assertCount(2, Type::getList());
        $this->assertSame([], Workflow::getList());
        $wf = Workflow::add($type, 'Name');
        $this->assertEquals([$wf], Workflow::getList());
        $this->assertEquals([$wf], $type->getWorkflows());
        $wf->delete();
        $this->assertEquals([], $type->getWorkflows());
        $this->assertCount(2, Type::getList());
    }

    public function testRename()
    {
        $type = Type::add('empty', 'Example');
        $wf = Workflow::add($type, 'WF 1');
        $this->assertSame('WF 1', $wf->getWorkflowName());
        $wf->updateName('WF 2');
        $this->assertSame('WF 2', $wf->getWorkflowName());
        $this->assertSame('WF 2', Workflow::getByName('WF 2')->getWorkflowName());
    }

    public function testGetWorkflowProgressCurrentStatusNum()
    {
        $wf = Workflow::add(Type::add('empty', 'Example'), 'Name');
        $wp = M::mock(Progress::class);
        $wp->shouldReceive('getWorkflowRequestObject')->andReturn(null);
        $this->assertEmpty($wf->getWorkflowProgressCurrentStatusNum($wp));
        $wr = M::mock(Request::class);
        $wr->shouldReceive('getWorkflowRequestStatusNum')->andReturn(123);
        $wp = M::mock(Progress::class);
        $wp->shouldReceive('getWorkflowRequestObject')->andReturn($wr);
        $this->assertSame(123, $wf->getWorkflowProgressCurrentStatusNum($wp));
    }

    public function testGetList()
    {
        $type = Type::add('empty', 'Example');
        $wf1 = Workflow::add($type, 'WF 1');
        $wf3 = Workflow::add($type, 'WF 3');
        $wf2 = Workflow::add($type, 'WF 2');
        $this->assertEquals([$wf1, $wf2, $wf3], Workflow::getList());
    }
}
