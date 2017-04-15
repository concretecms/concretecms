<?php
namespace Concrete\Tests\Core\Form;

use Concrete\Core\Express\Form\Context\DashboardFormContext;
use Concrete\Core\Express\Form\Context\FrontendFormContext;
use Concrete\Core\Express\Form\Context\ViewContext;
use Core;
use Concrete\Core\Form\Context\Registry\ContextRegistry;

class TestFrontendFormContext extends FrontendFormContext
{

}

class TestDashboardFormContext extends DashboardFormContext
{

}

class ContextTest extends \PHPUnit_Framework_TestCase
{

    public function testContextManager()
    {
        $registry = new ContextRegistry();
        $context = $registry->getContext(new FrontendFormContext());
        $this->assertEquals(new FrontendFormContext(), $context);
        $registry->register(FrontendFormContext::class, TestFrontendFormContext::class);

        $context = $registry->getContext(new FrontendFormContext());
        $this->assertEquals(new TestFrontendFormContext(), $context);
        $this->assertInstanceOf(FrontendFormContext::class, $context);
    }

    public function testMultipleRegisters()
    {
        $registry = new ContextRegistry();
        $registry->register(DashboardFormContext::class, TestDashboardFormContext::class);
        $registry->register(FrontendFormContext::class, TestFrontendFormContext::class);

        $context1 = $registry->getContext(new FrontendFormContext());
        $context2 = $registry->getContext(new ViewContext());
        $context3 = $registry->getContext(new DashboardFormContext());

        $this->assertEquals(new TestFrontendFormContext(), $context1);
        $this->assertEquals(new ViewContext(), $context2);
        $this->assertEquals(new TestDashboardFormContext(), $context3);
    }
}
