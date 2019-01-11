<?php

namespace Concrete\Core\Logging\Entry\User;

class ResetUserPassword extends User
{

    public function getEntryMessage()
    {
        return t('Password for user %s (ID %s) was reset by code or an automated process.',
            $this->user->getUserName(),
            $this->user->getUserID()
        );
    }

    public function getEntryMessageWithApplier()
    {
        return t('Password for user %s (ID %s) was reset by %s (ID %s).',
            $this->user->getUserName(),
            $this->user->getUserID(),
            $this->applier->getUserName(),
            $this->applier->getUserID()
        );
    }

    public function getEntryOperation()
    {
        return 'reset_password';
    }

}
