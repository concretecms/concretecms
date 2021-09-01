<?php

namespace Concrete\Tests\User\Password;

use Concrete\Core\File\Event\FileVersion;
use Concrete\Core\User\Event\UserInfoWithPassword;
use Concrete\Core\User\Logger;
use Concrete\Core\User\Password\PasswordChangeEventHandler;
use Concrete\Core\User\Password\PasswordUsageTracker;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfo;
use Concrete\Tests\TestCase;
use Mockery as M;

class PasswordChangeEventHandlerTest extends TestCase
{

    public function testHandlingSimpleEvent()
    {
        $password = 'blah';

        $user = M::mock(User::class);
        $userinfo = M::mock(UserInfo::class);
        $tracker = M::mock(PasswordUsageTracker::class);
        $tracker->shouldReceive('trackUse')->once()->with($password, $userinfo);

        $userinfo->shouldReceive('getUserObject')->andReturn($user);

        $event = M::mock(UserInfoWithPassword::class);
        $event->shouldReceive([
            'getUserPassword' => $password,
            'getUserInfoObject' => $userinfo
        ]);

        $handler = new PasswordChangeEventHandler($tracker);
        $handler->handleEvent($event);
    }

    public function testInvalidEventType()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid event type provided. Event type must be \"UserInfoWithPassword\".");
        $tracker = M::mock(PasswordUsageTracker::class);
        $event = M::mock(FileVersion::class);

        $handler = new PasswordChangeEventHandler($tracker);
        $handler->handleEvent($event);
    }

}
