<?php

namespace Notification\Type;

use Concrete\Core\Application\Application;
use Concrete\Core\Notification\Alert\Filter\StandardFilter;
use Concrete\Core\Notification\Notifier\StandardNotifier;
use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Notification\Subscription\StandardSubscription;
use Concrete\Core\Notification\Type\UserDeactivatedType;
use Concrete\Tests\TestCase;
use Mockery as M;

class UserDeactivatedTypeTest extends TestCase
{


    public function testPassingDefaultValues()
    {
        $app = M::mock(Application::class);
        $notifier = M::mock(StandardNotifier::class);
        $subscription = M::mock(StandardSubscription::class);
        $filter = M::mock(StandardFilter::class);
        $subject = M::mock(SubjectInterface::class);

        $type = new UserDeactivatedType($app, $notifier, $subscription, $filter);
        $this->assertEquals($subscription, $type->getSubscription($subject));
        $this->assertEquals([$subscription], $type->getAvailableSubscriptions());
        $this->assertEquals([$filter], $type->getAvailableFilters());
    }

    public function testStandardFunctionality()
    {
        $app = M::mock(Application::class);
        $notifier = M::mock(StandardNotifier::class);
        $subject = M::mock(SubjectInterface::class);
        $subscription = M::mock(StandardSubscription::class);
        $filter = M::mock(StandardFilter::class);
        $type = null;

        $app->shouldReceive('make')->andReturn($filter);

        $type = new UserDeactivatedType($app, $notifier);
        $this->assertInstanceOf(StandardSubscription::class, $type->getSubscription($subject));
        $subscriptions = $type->getAvailableSubscriptions();
        $this->assertInstanceOf(StandardSubscription::class, $subscriptions[0]);
        $this->assertEquals([$filter], $type->getAvailableFilters());
        $this->assertEquals($notifier, $type->getNotifier());
    }

    public function testUnsupportedFactoryMethod()
    {
        $this->expectException(\RuntimeException::class);
        $app = M::mock(Application::class);
        $notifier = M::mock(StandardNotifier::class);
        $subject = M::mock(SubjectInterface::class);

        $type = new UserDeactivatedType($app, $notifier);
        $type->createNotification($subject);
    }
}
