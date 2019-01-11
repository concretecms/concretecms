<?php

namespace Concrete\Core\Logging\Entry\User;

class ChangeUserPassword extends User
{

    public function getMessage()
    {
        if ($this->applier && $this->applier->isRegistered()) {
            return t('Password for user %s (ID %s) was changed by %s (ID %s).',
                $this->user->getUserName(),
                $this->user->getUserID(),
                $this->applier->getUserName(),
                $this->applier->getUserID()
            );
        } else {
            return t('Password for user %s (ID %s) was changed by code or an automated process.',
                $this->user->getUserName(),
                $this->user->getUserID()
            );
        }
    }

    public function getContext()
    {
        $context = parent::getContext();
        $context['operation'] = 'change_password';
        return $context;
    }
}
