<?php

namespace Concrete\Core\Logging\Entry\User;

class DeactivateUser extends User
{

    public function getMessage()
    {
        if ($this->applier && $this->applier->isRegistered()) {
            return t('User %s (ID %s) was deactivated by %s (ID %s).',
                $this->user->getUserName(),
                $this->user->getUserID(),
                $this->applier->getUserName(),
                $this->applier->getUserID()
            );
        } else {
            return t('User %s (ID %s) was deactivated by code or an automated process.',
                $this->user->getUserName(),
                $this->user->getUserID()
            );
        }
    }

    public function getContext()
    {
        $context = parent::getContext();
        $context['operation'] = 'deactivate_user';
        return $context;
    }
}
