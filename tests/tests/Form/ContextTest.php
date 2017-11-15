<?php

namespace Concrete\Tests\Core\Form;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Express\Controller\ControllerInterface;
use Concrete\Core\Express\Controller\StandardController;
use Concrete\Core\Express\Entry\Notifier\NotificationProviderInterface;
use Concrete\Core\Express\Form\Context\DashboardFormContext;
use Concrete\Core\Express\Form\Context\DashboardViewContext;
use Concrete\Core\Express\Form\Context\FrontendFormContext;
use Concrete\Core\Form\Context\ContextFactory;
use Concrete\Core\Form\Context\Registry\ContextRegistry;
use Symfony\Component\HttpFoundation\Request;

class TestFrontendFormContext extends FrontendFormContext
{
}

class TestDashboardFormContext extends DashboardFormContext
{
}

class TestController implements ControllerInterface
{
    public function getContextRegistry()
    {
        return new ContextRegistry([
            FrontendFormContext::class => new TestFrontendFormContext(),
            DashboardFormContext::class => new TestDashboardFormContext(),
        ]);
    }

    public function getFormProcessor()
    {
        return null;
    }

    public function getEntryManager(Request $request)
    {
        return null;
    }

    public function getNotifier(NotificationProviderInterface $provider = null)
    {
        return null;
    }
}

class ContextTest extends \PHPUnit_Framework_TestCase
{
    public function testController()
    {
        $entity = $this->getMockBuilder(Entity::class)
            ->getMock();

        $express = \Core::make('express');
        $controller = $express->getEntityController($entity);
        $this->assertInstanceOf(ControllerInterface::class, $controller);
        $this->assertInstanceOf(StandardController::class, $controller);
        $factory = new ContextFactory($controller);
        $context = $factory->getContext(new FrontendFormContext());
        $this->assertEquals(new FrontendFormContext(), $context);

        $controller2 = new TestController();
        $factory = new ContextFactory($controller2);
        $context = $factory->getContext(new FrontendFormContext());
        $this->assertEquals(new TestFrontendFormContext(), $context);
        $this->assertEquals(new DashboardViewContext(), $factory->getContext(new DashboardViewContext()));
        $this->assertEquals(new TestFrontendFormContext(), $factory->getContext(new FrontendFormContext()));
        $this->assertEquals(new TestDashboardFormContext(), $factory->getContext(new DashboardFormContext()));
    }

    public function testFormProcessor()
    {
        $entity = $this->getMockBuilder(Entity::class)
            ->getMock();
        $express = \Core::make('express');
        $controller = $express->getEntityController($entity);
        $processor = $controller->getFormProcessor();

        $this->assertInstanceOf('Concrete\Core\Express\Form\Processor\ProcessorInterface', $processor);

        $validator = $processor->getValidator(Request::createFromGlobals());
        $this->assertInstanceOf('Concrete\Core\Express\Form\Validator\ValidatorInterface', $validator);
        $this->assertInstanceOf('Concrete\Core\Express\Form\Validator\StandardValidator', $validator);
    }
}
