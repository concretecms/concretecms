<?php

namespace Notification\Type;

use Concrete\Core\Application\Application;
use Concrete\Core\Notification\Alert\Filter\StandardFilter;
use Concrete\Core\Notification\Notifier\StandardNotifier;
use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Notification\Subscription\StandardSubscription;
use Concrete\Core\Notification\Type\UserDeactivatedType;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit_Framework_TestCase;
use Mockery as M;

class UserDeactivatedTypeTest extends PHPUnit_Framework_TestCase
{

    use MockeryPHPUnitIntegration;

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

        $app->shouldReceive('make')->once()->withArgs([StandardSubscription::class, ['user_deactivated', 'User Deactivated']])->andReturn($subscription);
        $app->shouldReceive('make')->once()->withArgs([StandardFilter::class, [&$type, 'user_deactivated', 'User Deactivated', 'userdeactivatednotification']])->andReturn($filter);

        $type = new UserDeactivatedType($app, $notifier);
        $this->assertEquals($subscription, $type->getSubscription($subject));
        $this->assertEquals([$subscription], $type->getAvailableSubscriptions());
        $this->assertEquals([$filter], $type->getAvailableFilters());
        $this->assertEquals($notifier, $type->getNotifier());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testUnsupportedFactoryMethod()
    {
        $app = M::mock(Application::class);
        $notifier = M::mock(StandardNotifier::class);
        $subject = M::mock(SubjectInterface::class);

        $type = new UserDeactivatedType($app, $notifier);
        $type->createNotification($subject);
    }
}
