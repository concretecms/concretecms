<?php
namespace Concrete\Core\User;

use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\Entry\EntryInterface;
use Concrete\Core\Logging\Entry\User\ActivateUser;
use Concrete\Core\Logging\Entry\User\AddUser;
use Concrete\Core\Logging\Entry\User\ChangeUserPassword;
use Concrete\Core\Logging\Entry\User\DeactivateUser;
use Concrete\Core\Logging\Entry\User\DeleteUser;
use Concrete\Core\Logging\Entry\User\ResetUserPassword;
use Concrete\Core\Logging\Entry\User\UpdateUser;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;
use Concrete\Core\User\Event\UserInfo as UserInfoEvent;
use Concrete\Core\User\Event\UserInfoWithPassword;
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
            'on_user_activate' => 'onUserActivate',
            'on_user_add' => 'onUserAdd',
            'on_user_change_password' => 'onUserChangePassword',
            'on_user_deactivate' => 'onUserDeactivate',
            'on_user_deleted' => 'onUserDeleted',
            'on_user_reset_password' => 'onUserResetPassword',
            'on_user_update' => 'onUserUpdate',
        ];
    }

    protected function log(EntryInterface $entry)
    {
        $this->logger->info($entry->getMessage(), $entry->getContext());
    }

    public function onUserActivate(UserInfoEvent $event)
    {
        $this->log(new ActivateUser($event->getUserInfoObject()->getUserObject(), $event->getApplier()));
    }

    public function onUserDeactivate(UserInfoEvent $event)
    {
        $this->log(new DeactivateUser($event->getUserInfoObject()->getUserObject(), $event->getApplier()));
    }

    public function onUserAdd(UserInfoWithPassword $event)
    {
        $this->log(new AddUser($event->getUserInfoObject()->getUserObject(), $event->getApplier()));
    }

    public function onUserUpdate(UserInfoEvent $event)
    {
        $this->log(new UpdateUser($event->getUserInfoObject()->getUserObject(), $event->getApplier()));
    }

    public function onUserDeleted(UserInfoEvent $event)
    {
        $this->log(new DeleteUser($event->getUserInfoObject()->getUserObject(), $event->getApplier()));
    }

    public function onUserResetPassword(UserInfoEvent $event)
    {
        $this->log(new ResetUserPassword($event->getUserInfoObject()->getUserObject(), $event->getApplier()));
    }

    public function onUserChangePassword(UserInfoWithPassword $event)
    {
        $this->log(new ChangeUserPassword($event->getUserInfoObject()->getUserObject(), $event->getApplier()));
    }



}
