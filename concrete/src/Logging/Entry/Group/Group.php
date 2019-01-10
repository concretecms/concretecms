<?php

namespace Concrete\Core\Logging\Entry\Group;

use Concrete\Core\Logging\Entry\EntryInterface;
use Concrete\Core\User\User;

/**
 * Log entry for group actions
 */
abstract class Group implements EntryInterface
{

    /**
     * The user having the group added or removed
     *
     * @var User
     */
    protected $user;

    /**
     * The group being added or removed
     *
     * @var \Concrete\Core\User\Group\Group
     */
    protected $group;

    /**
     * The user performing the operation
     *
     * @var User | null
     */
    protected $applier;

    public function __construct(User $user, \Concrete\Core\User\Group\Group $group, User $applier = null)
    {
        $this->user = $user;
        $this->group = $group;
        $this->applier = $applier;
    }

    public function getContext()
    {
        $context = [];
        $context['user_id'] = $this->user->getUserID();
        $context['user_name'] = $this->user->getUserName();
        $context['group_id'] = $this->group->getGroupID();
        $context['group_name'] = $this->group->getGroupName();
        if ($this->applier) {
            $context['applier_id'] = $this->applier->getUserID();
            $context['applier_name'] = $this->applier->getUserName();
        }
        return $context;
    }

}
