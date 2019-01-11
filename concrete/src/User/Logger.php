<?php
namespace Concrete\Core\User;

use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\Entry\EntryInterface;
use Concrete\Core\Logging\Entry\User\ActivateUser;
use Concrete\Core\Logging\Entry\User\AddUser;
use Concrete\Core\Logging\Entry\User\ChangeUserPassword;
use Concrete\Core\Logging\Entry\User\DeactivateUser;
use Concrete\Core\Logging\Entry\User\ResetUserPassword;
use Concrete\Core\Logging\Entry\User\UpdateUser;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;

class Logger implements LoggerAwareInterface
{

    public function getLoggerChannel()
    {
        return Channels::CHANNEL_USERS;
    }

    use LoggerAwareTrait;

    private function log(EntryInterface $entry)
    {
        $this->logger->info($entry->getMessage(), $entry->getContext());
    }

    public function logAdd(User $user, User $applier = null)
    {
        $entry = new AddUser($user, $applier);
        $this->log($entry);
    }

    public function logChangePassword(User $user, User $applier = null)
    {
        $entry = new ChangeUserPassword($user, $applier);
        $this->log($entry);
    }

    public function logResetPassword(User $user, User $applier = null)
    {
        $entry = new ResetUserPassword($user, $applier);
        $this->log($entry);
    }

    public function logUpdateUser(User $user, User $applier = null)
    {
        $entry = new UpdateUser($user, $applier);
        $this->log($entry);
    }

    public function logActivateUser(User $user, User $applier = null)
    {
        $entry = new ActivateUser($user, $applier);
        $this->log($entry);
    }


    public function logDeactivateUser(User $user, User $applier = null)
    {
        $entry = new DeactivateUser($user, $applier);
        $this->log($entry);
    }

}
