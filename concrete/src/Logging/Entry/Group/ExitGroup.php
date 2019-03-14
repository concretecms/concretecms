<?php

namespace Concrete\Core\Logging\Entry\Group;

/**
 * Log entry for users being removed from groups
 */
class ExitGroup extends UserGroup
{

    public function getEntryMessage()
    {
        return t('User %s (ID %s) was removed from group %s (ID %s) by an automated process.',
            $this->user->getUserName(),
            $this->user->getUserID(),
            $this->group->getGroupName(),
            $this->group->getGroupID()
        );
    }

    public function getEntryMessageWithApplier()
    {
        return t('User %s (ID %s) was removed from group %s (ID %s) by %s (ID %s).',
            $this->user->getUserName(),
            $this->user->getUserID(),
            $this->group->getGroupName(),
            $this->group->getGroupID(),
            $this->applier->getUserName(),
            $this->applier->getUserID()
        );
    }

    public function getEntryOperation()
    {
        return 'exit_group';
    }

}
