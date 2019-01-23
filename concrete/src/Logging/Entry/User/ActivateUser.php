<?php

namespace Concrete\Core\Logging\Entry\User;

class ActivateUser extends User
{

    public function getEntryMessage()
    {
        return t('User %s (ID %s) was activated by code or an automated process.',
            $this->user->getUserName(),
            $this->user->getUserID()
        );
    }

    public function getEntryMessageWithApplier()
    {
        return t('User %s (ID %s) was activated by %s (ID %s).',
            $this->user->getUserName(),
            $this->user->getUserID(),
            $this->applier->getUserName(),
            $this->applier->getUserID()
        );
    }

    public function getEntryOperation()
    {
        return 'activate_user';
    }

}
