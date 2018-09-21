<?php

namespace Concrete\TestHelpers\Form;

use Concrete\Core\Express\Controller\ControllerInterface;
use Concrete\Core\Express\Entry\Notifier\NotificationProviderInterface;
use Concrete\Core\Express\Form\Context\DashboardFormContext;
use Concrete\Core\Express\Form\Context\FrontendFormContext;
use Concrete\Core\Form\Context\Registry\ContextRegistry;
use Symfony\Component\HttpFoundation\Request;

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
