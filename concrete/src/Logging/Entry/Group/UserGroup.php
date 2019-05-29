<?php

namespace Concrete\Core\Logging\Entry\Group;

use Concrete\Core\Logging\Entry\EntryInterface;
use Concrete\Core\User\User;

/**
 * Log entry for group actions for a user
 */
abstract class UserGroup extends Group
{

    /**
     * The user having the group added or removed
     *
     * @var User
     */
    protected $user;

    public function __construct(User $user, \Concrete\Core\User\Group\Group $group, User $applier = null)
    {
        $this->user = $user;
        parent::__construct($group, $applier);
    }

    public function getEntryContext()
    {
        $context = parent::getEntryContext();
        $context['user_id'] = $this->user->getUserID();
        $context['user_name'] = $this->user->getUserName();
        return $context;
    }

}
