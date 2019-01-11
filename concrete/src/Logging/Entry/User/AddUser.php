<?php

namespace Concrete\Core\Logging\Entry\User;

class AddUser extends User
{

    public function getEntryMessage()
    {
        return t('User %s (ID %s) was added by code or an automated process.',
            $this->user->getUserName(),
            $this->user->getUserID()
        );
    }

    public function getEntryMessageWithApplier()
    {
        return t('User %s (ID %s) was added by %s (ID %s).',
            $this->user->getUserName(),
            $this->user->getUserID(),
            $this->applier->getUserName(),
            $this->applier->getUserID()
        );
    }

    public function getEntryOperation()
    {
        return 'add_user';
    }
}
