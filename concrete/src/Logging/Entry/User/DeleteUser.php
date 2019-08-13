<?php

namespace Concrete\Core\Logging\Entry\User;

/**
 * @since 8.5.0
 */
class DeleteUser extends User
{

    public function getEntryMessage()
    {
        return t('User %1$s (ID %2$s) was deleted by code or an automated process.',
            $this->user->getUserName(),
            $this->user->getUserID()
        );
    }

    public function getEntryMessageWithApplier()
    {
        return t('User %1$s (ID %2$s) was deleted by %3$s (ID %4$s).',
            $this->user->getUserName(),
            $this->user->getUserID(),
            $this->applier->getUserName(),
            $this->applier->getUserID()
        );
    }

    public function getEntryOperation()
    {
        return 'delete_user';
    }

}
