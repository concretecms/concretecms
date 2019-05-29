<?php

namespace Concrete\Core\Logging\Entry\User;

class UpdateUser extends User
{
    public function getEntryMessage()
    {
        return t('User %1$s (ID %2$s) was updated by code or an automated process.',
            $this->user->getUserName(),
            $this->user->getUserID()
        );
    }

    public function getEntryMessageWithApplier()
    {
        return t('User %1$s (ID %2$s) was updated by %3$s (ID %4$s).',
            $this->user->getUserName(),
            $this->user->getUserID(),
            $this->applier->getUserName(),
            $this->applier->getUserID()
        );
    }

    public function getEntryOperation()
    {
        return 'update_user';
    }
}
