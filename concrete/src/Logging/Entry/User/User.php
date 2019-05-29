<?php

namespace Concrete\Core\Logging\Entry\User;

use Concrete\Core\Logging\Entry\ApplierEntry;
use Concrete\Core\User\User as CoreUser;

/**
 * Log entry for user actions
 */
abstract class User extends ApplierEntry
{

    /**
     * The user being added
     *
     * @var \Concrete\Core\User\User
     */
    protected $user;

    public function __construct(CoreUser $user, CoreUser $applier = null)
    {
        $this->user = $user;
        parent::__construct($applier);
    }

    public function getEntryContext()
    {
        $context = [];
        $context['user_id'] = $this->user->getUserID();
        $context['user_name'] = $this->user->getUserName();
        if ($this->applier) {
            $context['applier_id'] = $this->applier->getUserID();
            $context['applier_name'] = $this->applier->getUserName();
        }
        return $context;
    }

}
