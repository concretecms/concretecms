<?php

namespace Concrete\Core\Logging\Entry\Group;

use Concrete\Core\Logging\Entry\ApplierEntry;
use Concrete\Core\User\User;

/**
 * Log entry for group actions
 */
abstract class Group extends ApplierEntry
{

    /**
     * The group being added or removed
     *
     * @var \Concrete\Core\User\Group\Group
     */
    protected $group;

    public function __construct(\Concrete\Core\User\Group\Group $group, User $applier = null)
    {
        $this->group = $group;
        parent::__construct($applier);
    }

    public function getEntryContext()
    {
        $context = parent::getEntryContext();
        $context['group_id'] = $this->group->getGroupID();
        $context['group_name'] = $this->group->getGroupName();
        return $context;
    }

}
