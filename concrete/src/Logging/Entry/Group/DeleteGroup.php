<?php

namespace Concrete\Core\Logging\Entry\Group;

class DeleteGroup extends Group
{

    public function getEntryMessage()
    {
        return t('Group %1$s (ID %2$s) was deleted by an automated process.',
            $this->group->getGroupName(),
            $this->group->getGroupID()
        );
    }

    public function getEntryMessageWithApplier()
    {
        return t('User %1$s (ID %2$s) deleted group %3$s (ID %4$s).',
            $this->applier->getUserName(),
            $this->applier->getUserID(),
            $this->group->getGroupName(),
            $this->group->getGroupID()
        );
    }

    public function getEntryOperation()
    {
        return 'delete_group';
    }

}
