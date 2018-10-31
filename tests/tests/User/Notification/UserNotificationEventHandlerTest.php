<?php

namespace Concrete\Tests\User\Notification;

use Carbon\Carbon;
use Concrete\Core\Entity\Notification\UserDeactivatedNotification;
use Concrete\Core\Entity\User\User;
use Concrete\Core\Notification\Notifier\NotifierInterface;
use Concrete\Core\Notification\Notifier\StandardNotifier;
use Concrete\Core\Notification\Subscription\SubscriptionInterface;
use Concrete\Core\Notification\Type\Manager;
use Concrete\Core\Notification\Type\UserDeactivatedType;
use Concrete\Core\User\Event\DeactivateUser;
use Concrete\Core\User\Notification\UserNotificationEventHandler;
use Hamcrest\Core\IsEqual;
use Hamcrest_Core_IsEqual;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit_Framework_TestCase;
use Mockery as M;

class UserNotificationEventHandlerTest extends PHPUnit_Framework_TestCase
{

    use MockeryPHPUnitIntegration;

    public function testHandlingEvent()
    {
        if (!class_exists(IsEqual::class)) {
            if (class_exists(Hamcrest_Core_IsEqual::class)) {
                // Oh merciful lord, why??
                class_alias(Hamcrest_Core_IsEqual::class, IsEqual::class);
            } else {
                $this->markTestSkipped('Unable to detect proper hamcrest IsEqual class');
            }
        }

        $user = M::mock(User::class);
        $user->shouldReceive('getUserID')->atLeast()->once()->andReturn(44); // This is called once when we create a new UserDeactivatedNotification below

        $now = Carbon::create(1992, 12, 10);
        $event = M::mock(DeactivateUser::class);
        $event->shouldReceive('getUserEntity')->atLeast()->once()->andReturn($user);
        $event->shouldReceive('getActorEntity')->atLeast()->once()->andReturn(null);
        $event->shouldReceive('getNotificationDate')->atLeast()->once()->andReturn($now);

        $notifier = M::mock(StandardNotifier::class);
        $notifier->shouldReceive('notify')->once()->withArgs([['foo'], IsEqual::equalTo(new UserDeactivatedNotification($event))]);
        $notifier->shouldReceive('getUsersToNotify')->once()->andReturn(['foo']);

        $type = M::mock(UserDeactivatedType::class)->shouldIgnoreMissing();
        $type->shouldReceive('getNotifier')->once()->andReturn($notifier);
        $type->shouldReceive('getSubscription')->once()->andReturn(M::mock(SubscriptionInterface::class));

        $manager = M::mock(Manager::class);
        $manager->shouldReceive('driver')->once()->withArgs([UserDeactivatedType::IDENTIFIER])->andReturn($type);

        $service = new UserNotificationEventHandler($manager);

        $service->deactivated($event);
    }

    public function testAwkwardNotifier()
    {
        $event = M::mock(DeactivateUser::class);
        $notifier = M::mock(NotifierInterface::class); // Notifier without a `notify` method
        $type = M::mock(UserDeactivatedType::class)->shouldIgnoreMissing();
        $type->shouldReceive('getNotifier')->once()->andReturn($notifier);

        $manager = M::mock(Manager::class);
        $manager->shouldReceive('driver')->once()->withArgs([UserDeactivatedType::IDENTIFIER])->andReturn($type);

        $service = new UserNotificationEventHandler($manager);

        // Not much should happen. The handler should see the notifier can't notify and should give up.
        $service->deactivated($event);
    }

}
