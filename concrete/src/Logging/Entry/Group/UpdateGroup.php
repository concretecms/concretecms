<?php

namespace Concrete\Core\Logging\Entry\Group;

class UpdateGroup extends Group
{

    public function getEntryMessage()
    {
        return t('Group %s (ID %s) was updated by an automated process.',
            $this->group->getGroupName(),
            $this->group->getGroupID()
        );
    }

    public function getEntryMessageWithApplier()
    {
        return t('User %s (ID %s) updated group %s (ID %s).',
            $this->applier->getUserName(),
            $this->applier->getUserID(),
            $this->group->getGroupName(),
            $this->group->getGroupID()
        );
    }

    public function getEntryOperation()
    {
        return 'update_group';
    }

}
