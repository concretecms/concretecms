<?php

namespace Concrete\Core\Logging\Entry\User;

class UpdateUser extends User
{

    public function getMessage()
    {
        if ($this->applier && $this->applier->isRegistered()) {
            return t('User %s (ID %s) was updated by %s (ID %s).',
                $this->user->getUserName(),
                $this->user->getUserID(),
                $this->applier->getUserName(),
                $this->applier->getUserID()
            );
        } else {
            return t('User %s (ID %s) was updated by code or an automated process.',
                $this->user->getUserName(),
                $this->user->getUserID()
            );
        }
    }

    public function getContext()
    {
        $context = parent::getContext();
        $context['operation'] = 'update_user';
        return $context;
    }
}
