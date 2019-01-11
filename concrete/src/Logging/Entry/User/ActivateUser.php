<?php

namespace Concrete\Core\Logging\Entry\User;

class ActivateUser extends User
{

    public function getMessage()
    {
        if ($this->applier && $this->applier->isRegistered()) {
            return t('User %s (ID %s) was activated by %s (ID %s).',
                $this->user->getUserName(),
                $this->user->getUserID(),
                $this->applier->getUserName(),
                $this->applier->getUserID()
            );
        } else {
            return t('User %s (ID %s) was activated by code or an automated process.',
                $this->user->getUserName(),
                $this->user->getUserID()
            );
        }
    }

    public function getContext()
    {
        $context = parent::getContext();
        $context['operation'] = 'activate_user';
        return $context;
    }
}
