<?php /** @noinspection PhpUnused */
/** @noinspection SqlDialectInspection */
/** @noinspection SqlNoDataSourceInspection */

namespace Concrete\Core\User\Group;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Notification\GroupSignupRequestAcceptNotification;
use Concrete\Core\Entity\Notification\GroupSignupRequestDeclineNotification;
use Concrete\Core\Entity\User\GroupSignupRequestAccept;
use Concrete\Core\Entity\User\GroupSignupRequestDecline;
use Concrete\Core\Foundation\ConcreteObject;
use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Notification\Type\GroupSignupRequestAcceptType;
use Concrete\Core\Notification\Type\GroupSignupRequestDeclineType;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\User;
use DateTime;

class GroupJoinRequest extends ConcreteObject implements SubjectInterface
{
    /** @var Group */
    protected $group;
    /** @var User */
    protected $user;

    public function __construct(Group $group, User $user)
    {
        $this->group = $group;
        $this->user = $user;
    }

    /**
     * @return Group
     */
    public function getGroup(): Group
    {
        return $this->group;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    public function accept()
    {
        $activeUser = new User();

        if ($this->group->hasUserManagerPermissions($activeUser)) {
            $app = Application::getFacadeApplication();
            /** @var Connection $db */
            $db = $app->make(Connection::class);

            $this->user->enterGroup($this->group);

            $db->executeQuery("DELETE FROM GroupJoinRequests WHERE uID = ? AND gID = ?", [$this->user->getUserID(), $this->group->getGroupID()]);

            /** @noinspection PhpUnhandledExceptionInspection */
            $subject = new GroupSignupRequestAccept($this->group, $this->user, $activeUser);
            /** @var GroupSignupRequestAcceptType $type */
            $type = $app->make('manager/notification/types')->driver('group_signup_request_accept');
            $notifier = $type->getNotifier();
            if (method_exists($notifier, 'notify')) {
                $subscription = $type->getSubscription($subject);
                $users = $notifier->getUsersToNotify($subscription, $subject);
                $notification = new GroupSignupRequestAcceptNotification($subject);
                $notifier->notify($users, $notification);
            }

            return true;
        } else {
            return false;
        }
    }

    public function decline()
    {
        $activeUser = new User();

        if ($this->group->hasUserManagerPermissions($activeUser)) {
            $app = Application::getFacadeApplication();
            /** @var Connection $db */
            $db = $app->make(Connection::class);
            $db->executeQuery("DELETE FROM GroupJoinRequests WHERE uID = ? AND gID = ?", [$this->user->getUserID(), $this->group->getGroupID()]);

            /** @noinspection PhpUnhandledExceptionInspection */
            $subject = new GroupSignupRequestDecline($this->group, $this->user, $activeUser);
            /** @var GroupSignupRequestDeclineType $type */
            $type = $app->make('manager/notification/types')->driver('group_signup_request_decline');
            $notifier = $type->getNotifier();
            if (method_exists($notifier, 'notify')) {
                $subscription = $type->getSubscription($subject);
                $users = $notifier->getUsersToNotify($subscription, $subject);
                $notification = new GroupSignupRequestDeclineNotification($subject);
                $notifier->notify($users, $notification);
            }

            return true;
        } else {
            return false;
        }
    }

    public function getNotificationDate()
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);

        $this->user->enterGroup($this->group);

        $row = $db->fetchAssoc("SELECT gjrRequested GroupJoinRequests WHERE uID = ? AND gID = ?", [$this->user->getUserID(), $this->group->getGroupID()]);

        if (isset($row)) {
            return DateTime::createFromFormat("Y-m-d H:i:s", $row["gjrRequested"]);
        }
    }

    public function getUsersToExcludeFromNotification()
    {
        return [];
    }
}