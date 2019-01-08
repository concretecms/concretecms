<?php

namespace Concrete\Tests\User\Event;

use Carbon\Carbon;
use Concrete\Core\Entity\User\User;
use Concrete\Core\User\Event\DeactivateUser;
use Concrete\Core\User\UserInfo;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit_Framework_TestCase;
use Mockery as M;

class DeactivateUserTest extends PHPUnit_Framework_TestCase
{
    use MockeryPHPUnitIntegration;

    public function testStaticFactory()
    {
        $userInfo = M::mock(UserInfo::class);

        $user = M::mock(User::class);
        $user->shouldReceive('getUserInfoObject')->twice()->andReturn($userInfo);

        $actor = M::mock(User::class);

        // Test event without actor
        $event = DeactivateUser::create($user);
        $this->assertInstanceOf(DeactivateUser::class, $event);
        $this->assertEquals($event->getUserEntity(), $user);
        $this->assertEquals($event->getUserInfoObject(), $userInfo);
        $this->assertNull($event->getActorEntity());

        // Test event with actor
        $event = DeactivateUser::create($user, $actor);
        $this->assertInstanceOf(DeactivateUser::class, $event);
        $this->assertEquals($event->getActorEntity(), $actor);
        $this->assertEquals($event->getUserEntity(), $user);
        $this->assertEquals($event->getUserInfoObject(), $userInfo);

        // Test event with datetime
        $date = Carbon::create(1992, 12, 10);

        $event = DeactivateUser::create($user, $actor, $date);
        $this->assertInstanceOf(DeactivateUser::class, $event);
        $this->assertEquals($event->getActorEntity(), $actor);
        $this->assertEquals($event->getUserEntity(), $user);
    }

    public function testSubjectInterface()
    {
        $user = M::mock(User::class);
        $actor = M::mock(User::class);
        $created = Carbon::create(1992, 12, 10);

        /** @var \Concrete\Core\Notification\Subject\SubjectInterface $event */
        $event = new DeactivateUser($user, $actor, $created);

        $this->assertEquals([$user], $event->getUsersToExcludeFromNotification());
        $this->assertEquals($created, $event->getNotificationDate());
    }

}
