<?php
namespace Concrete\Job;

use Concrete\Core\Job\Job;
use Concrete\Core\User\Event\DeleteUser;
use Concrete\Core\User\UserInfo;
use Concrete\Core\User\UserList;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DeleteInvalidatedUsers extends Job
{
    /**
     * The event dispatcher we use to report that a user is being deactivated.
     *
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->dispatcher = $eventDispatcher;
    }

    /**
     * @return string
     */
    public function getJobName()
    {
        return t('Delete Invalidated Users');
    }

    /**
     * @return string
     */
    public function getJobDescription()
    {
        return t('Delete invalidated users, if email validation is enabled.');
    }

    /**
     * @return string
     */
    public function run()
    {
        $config = app('config');
        if ($config->get('concrete.user.registration.type', 'disabled') == 'validate_email') {
            $userList = new UserList();
            $userList->filterByIsValidated(0);
            $users = $userList->getResults();
            /** @var UserInfo $user */
            foreach ($users as $user) {
                $this->deleteUser($user);
            }

            return t2('%s user deleted.', '%s users deleted.', count($users));
        } else {
            return t('Email validation or user registration is disabled. Job aborted.');
        }
    }

    /**
     * @param UserInfo $userInfo
     */
    protected function deleteUser(UserInfo $userInfo)
    {
        $event = new DeleteUser($userInfo);
        $this->dispatcher->dispatch('on_before_user_delete', $event);

        $userInfo->delete();

        $this->dispatcher->dispatch('on_after_user_delete', $event);
    }
}
