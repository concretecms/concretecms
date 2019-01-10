<?php

namespace Concrete\Tests\User;

use Concrete\Core\Logging\Entry\Group\EnterGroup;
use Concrete\Core\Logging\Entry\Group\ExitGroup;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\User;
use Mockery as M;

class GroupLoggingTest extends \PHPUnit_Framework_TestCase
{

    public function testGroupEntryLogging()
    {
        $user = M::mock(User::class);
        $group = M::mock(Group::class);
        $applier = M::mock(User::class);

        $user->shouldReceive('getUserName')->andReturn('andrew');
        $user->shouldReceive('getUserID')->andReturn(33);
        $group->shouldReceive('getGroupID')->andReturn(5);
        $group->shouldReceive('getGroupName')->andReturn('Editors');

        $entry = new EnterGroup($user, $group);
        $this->assertEquals('User andrew (ID 33) was added to group Editors (ID 5) by an automated process.',
            $entry->getMessage());
        $this->assertEquals([
            'user_id' => 33,
            'user_name' => 'andrew',
            'group_id' => 5,
            'group_name' => 'Editors',
            'operation' => 'enter_group'
        ], $entry->getContext());

        $applier->shouldReceive('getUserName')->andReturn('admin');
        $applier->shouldReceive('getUserID')->andReturn(1);

        $entry = new EnterGroup($user, $group, $applier);
        $this->assertEquals('User andrew (ID 33) was added to group Editors (ID 5) by admin (ID 1).',
            $entry->getMessage());
        $this->assertEquals([
            'user_id' => 33,
            'user_name' => 'andrew',
            'group_id' => 5,
            'group_name' => 'Editors',
            'applier_id' => 1,
            'applier_name' => 'admin',
            'operation' => 'enter_group'
        ], $entry->getContext());
    }

    public function testGroupExitLogging()
    {
        $user = M::mock(User::class);
        $group = M::mock(Group::class);
        $applier = M::mock(User::class);

        $user->shouldReceive('getUserName')->andReturn('andrew');
        $user->shouldReceive('getUserID')->andReturn(33);
        $group->shouldReceive('getGroupID')->andReturn(5);
        $group->shouldReceive('getGroupName')->andReturn('Editors');

        $entry = new ExitGroup($user, $group);
        $this->assertEquals('User andrew (ID 33) was removed from group Editors (ID 5) by an automated process.',
            $entry->getMessage());
        $this->assertEquals([
            'user_id' => 33,
            'user_name' => 'andrew',
            'group_id' => 5,
            'group_name' => 'Editors',
            'operation' => 'exit_group'
        ], $entry->getContext());

        $applier->shouldReceive('getUserName')->andReturn('admin');
        $applier->shouldReceive('getUserID')->andReturn(1);

        $entry = new ExitGroup($user, $group, $applier);
        $this->assertEquals('User andrew (ID 33) was removed from group Editors (ID 5) by admin (ID 1).',
            $entry->getMessage());
        $this->assertEquals([
            'user_id' => 33,
            'user_name' => 'andrew',
            'group_id' => 5,
            'group_name' => 'Editors',
            'applier_id' => 1,
            'applier_name' => 'admin',
            'operation' => 'exit_group'
        ], $entry->getContext());
    }
}
