<?php

namespace Concrete\Core\Logging\Entry\Group;

/**
 * Log entry for users being added to groups
 */
class EnterGroup extends Group
{

    public function getMessage()
    {
        if ($this->applier && $this->applier->isRegistered()) {
            return t('User %s (ID %s) was added to group %s (ID %s) by %s (ID %s).',
                $this->user->getUserName(),
                $this->user->getUserID(),
                $this->group->getGroupName(),
                $this->group->getGroupID(),
                $this->applier->getUserName(),
                $this->applier->getUserID()
            );
        } else {
            return t('User %s (ID %s) was added to group %s (ID %s) by an automated process.',
                $this->user->getUserName(),
                $this->user->getUserID(),
                $this->group->getGroupName(),
                $this->group->getGroupID()
            );
        }
    }

    public function getContext()
    {
        $context = parent::getContext();
        $context['operation'] = 'enter_group';
        return $context;
    }
}
