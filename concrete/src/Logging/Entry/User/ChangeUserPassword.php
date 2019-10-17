<?php

namespace Concrete\Core\Logging\Entry\User;

class ChangeUserPassword extends User
{

    public function getEntryMessage()
    {
        return t('Password for user %1$s (ID %2$s) was changed by code or an automated process.',
            $this->user->getUserName(),
            $this->user->getUserID()
        );
    }

    public function getEntryMessageWithApplier()
    {
        return t('Password for user %1$s (ID %2$s) was changed by %3$s (ID %4$s).',
            $this->user->getUserName(),
            $this->user->getUserID(),
            $this->applier->getUserName(),
            $this->applier->getUserID()
        );
    }

    public function getEntryOperation()
    {
        return 'change_password';
    }
}
