<?php

namespace Concrete\Tests\Notification;

use Concrete\Core\Workflow\Type;
use Concrete\Core\Workflow\Workflow;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;
use PHPUnit_Framework_TestCase;
use Concrete\Core\Notification\Alert\Filter\FilterListFactory;
use Concrete\Core\Notification\Alert\Filter\FilterList;
use Concrete\Core\Notification\Alert\Filter\FilterInterface;

class NotificationFilterTest extends ConcreteDatabaseTestCase
{

    protected $fixtures = [];
    protected $tables = [
        'Workflows',
        'WorkflowTypes',
    ];

    protected function getFilters()
    {
        $app = \Core::make('app');
        $factory = $app->make(FilterListFactory::class);
        $list = $factory->createList();
        $this->assertInstanceOf(FilterList::class, $list);
        $filters = $list->getFilters();
        return $filters;
    }

    public function testFilterListFactory()
    {
        $filters = $this->getFilters();
        $this->assertTrue(is_array($filters));
        $this->assertCount(6, $filters);
        $filter = $filters[0];
        $this->assertInstanceOf(FilterInterface::class, $filter);
        $this->assertEquals('concrete5 Updates', $filter->getName());
    }

    public function testFilterListFactoryWorkflow()
    {
        $type = Type::add('basic', 'Basic Workflow');
        Workflow::add($type, 'Page Approval');
        $filters = $this->getFilters();
        $this->assertCount(7, $filters);
        Workflow::add($type, 'Calendar Approval');
        $filters = $this->getFilters();
        $this->assertCount(8, $filters);
        $this->assertEquals('Workflow: Page Approval', $filters[7]->getName());

    }



}
