<?php

namespace Concrete\Core\User\Group;

use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\Entry\EntryInterface;
use Concrete\Core\Logging\Entry\Group\AddGroup;
use Concrete\Core\Logging\Entry\Group\DeleteGroup;
use Concrete\Core\Logging\Entry\Group\EnterGroup;
use Concrete\Core\Logging\Entry\Group\ExitGroup;
use Concrete\Core\Logging\Entry\Group\UpdateGroup;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\Event\UserGroup as UserGroupEvent;
use Concrete\Core\User\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LogSubscriber implements EventSubscriberInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function getLoggerChannel()
    {
        return Channels::CHANNEL_USERS;
    }

    public static function getSubscribedEvents()
    {
        return [
            'on_group_add' => 'onGroupAdd',
            'on_group_update' => 'onGroupUpdate',
            'on_group_delete' => 'onGroupDelete',
            'on_user_enter_group' => 'onUserEnterGroup',
            'on_user_exit_group' => 'onUserExitGroup',
        ];
    }

    public function onGroupAdd(Event $event)
    {
        $this->log(new AddGroup($event->getGroupObject(), $this->getCurrentUser()));
    }

    public function onGroupUpdate(Event $event)
    {
        $this->log(new UpdateGroup($event->getGroupObject(), $this->getCurrentUser()));
    }

    public function onGroupDelete(Event $event)
    {
        $this->log(new DeleteGroup($event->getGroupObject(), $this->getCurrentUser()));
    }

    public function onUserEnterGroup(UserGroupEvent $event)
    {
        $this->log(new EnterGroup($event->getUserObject(), $event->getGroupObject(), $this->getCurrentUser()));
    }

    public function onUserExitGroup(UserGroupEvent $event)
    {
        $this->log(new ExitGroup($event->getUserObject(), $event->getGroupObject(), $this->getCurrentUser()));
    }

    protected function log(EntryInterface $entry)
    {
        $this->logger->info($entry->getMessage(), $entry->getContext());
    }

    /**
     * @return \Concrete\Core\User\User
     */
    protected function getCurrentUser()
    {
        return Application::getFacadeApplication()->make(User::class);
    }
}
