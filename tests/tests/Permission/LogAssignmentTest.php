<?php

namespace Concrete\Tests\Permission;

use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\Configuration\AdvancedConfiguration;
use Concrete\Core\Logging\Configuration\SimpleDatabaseConfiguration;
use Concrete\Core\Logging\Configuration\SimpleFileConfiguration;
use Concrete\Core\Logging\Entry\Permission\Assignment\Assignment;
use Concrete\Core\Logging\LoggerFactory;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Access\Entity\GroupEntity;
use Concrete\Core\Permission\Access\Entity\UserEntity;
use Concrete\Core\Permission\Access\ListItem\ListItem;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Permission\Logger;
use Concrete\Core\User\User;
use Mockery as M;
use Psr\Log\LoggerInterface;

class LogAssignmentTest extends \PHPUnit_Framework_TestCase
{
    use M\Adapter\Phpunit\MockeryPHPUnitIntegration;

    public function testShouldLogPermissionAssignment()
    {
        // Do not log if the level is higher than debug.
        $assignment = M::mock(Assignment::class);
        $factory = M::mock(LoggerFactory::class);
        $configuration = M::mock(SimpleFileConfiguration::class);
        $configuration->shouldReceive('getCoreLevel')->andReturn(\Monolog\Logger::NOTICE);
        $factory->shouldReceive('getConfiguration')->andReturn($configuration);
        $logger = new Logger($factory);
        $this->assertFalse($logger->shouldLogAssignment($assignment));

        // Do log if the level is debug.
        $assignment = M::mock(Assignment::class);
        $factory = M::mock(LoggerFactory::class);
        $configuration = M::mock(SimpleDatabaseConfiguration::class);
        $configuration->shouldReceive('getCoreLevel')->andReturn(\Monolog\Logger::INFO);
        $factory->shouldReceive('getConfiguration')->andReturn($configuration);
        $logger = new Logger($factory);
        $this->assertTrue($logger->shouldLogAssignment($assignment));

        // Do log if it is advanced.
        // NOTE: If the advanced throws the log entries away that's ok – this is determining whether the logger
        // code runs at all, because the logger code is potentially not performant, so we have these intermediate
        // checks to save some performance.
        $configuration = M::mock(AdvancedConfiguration::class);
        $factory = M::mock(LoggerFactory::class);
        $factory->shouldReceive('getConfiguration')->andReturn($configuration);
        $logger = new Logger($factory);
        $this->assertTrue($logger->shouldLogAssignment($assignment));
    }

    public function testLoggingPermissionAssignment()
    {
        $userAccessEntity = M::mock(UserEntity::class);
        $groupAccessEntity = M::mock(GroupEntity::class);
        $listItem1 = M::mock(ListItem::class);
        $listItem2 = M::mock(ListItem::class);

        $factory = M::mock(LoggerFactory::class);
        $configuration = M::mock(SimpleDatabaseConfiguration::class);
        $user = M::mock(User::class);
        $key = M::mock(Key::class);
        $page = M::mock(Page::class);
        $access = M::mock(Access::class);
        $loggerInterface = M::mock(LoggerInterface::class);

        $configuration->shouldReceive('getCoreLevel')->andReturn(\Monolog\Logger::INFO);
        $factory->shouldReceive('getConfiguration')->andReturn($configuration);
        $factory->shouldReceive('createLogger')
            ->once()
            ->with(Channels::CHANNEL_PERMISSIONS)
            ->andReturn($loggerInterface);

        $user->shouldReceive('getUserName')->andReturn('andrew');
        $key->shouldReceive('getPermissionKeyCategoryHandle')->andReturn('page');
        $key->shouldReceive('getPermissionKeyHandle')->andReturn('view_page_versions');
        $key->shouldReceive('getPermissionObject')->andReturn($page);

        $page->shouldReceive('getPermissionObjectIdentifier')->andReturn(4); // page ID 4

        $groupAccessEntity->shouldReceive('getAccessEntityID')->andReturn(10);
        $groupAccessEntity->shouldReceive('getAccessEntityLabel')->andReturn('Administrators');
        $groupAccessEntity->shouldReceive('getAccessEntityTypeHandle')->andReturn('group');
        $userAccessEntity->shouldReceive('getAccessEntityLabel')->andReturn('testuser');
        $userAccessEntity->shouldReceive('getAccessEntityID')->andReturn(20);
        $userAccessEntity->shouldReceive('getAccessEntityTypeHandle')->andReturn('user');
        $listItem1->shouldReceive('getAccessEntityObject')->andReturn($userAccessEntity);
        $listItem2->shouldReceive('getAccessEntityObject')->andReturn($groupAccessEntity);
        $listItem1->shouldReceive('getAccessType')->andReturn(Key::ACCESS_TYPE_INCLUDE);
        $listItem2->shouldReceive('getAccessType')->andReturn(Key::ACCESS_TYPE_INCLUDE);

        $access->shouldReceive('getAccessListItems')->with(Key::ACCESS_TYPE_ALL)->andReturn([$listItem1, $listItem2]);

        $loggerInterface->shouldReceive('info')->once()
            ->withArgs(['Permission assignment applied for permission view_page_versions on object 4 by user andrew', [
                'applier' => 'andrew',
                'handle' => 'view_page_versions',
                'category' => 'page',
                'object_id' => 4,
                'entities' => [[
                    'id' => 20,
                    'access_type' => 10,
                    'entity_type' => 'user',
                    'entity_name' => 'testuser',
                ], [
                    'id' => 10,
                    'access_type' => 10,
                    'entity_type' => 'group',
                    'entity_name' => 'Administrators',
                ]],
            ]]);

        $entry = new Assignment($user, $key, $access);
        $logger = new Logger($factory);
        $logger->log($entry);
    }
}
