<?php

namespace Concrete\Core\Logging\Entry\User;

use Concrete\Core\Logging\Entry\EntryInterface;
use Concrete\Core\User\User as CoreUser;

/**
 * Log entry for user actions
 */
abstract class User implements EntryInterface
{

    /**
     * The user being added
     *
     * @var \Concrete\Core\User\User
     */
    protected $user;

    /**
     * The user performing the operation
     *
     * @var User | null
     */
    protected $applier;

    public function __construct(CoreUser $user, CoreUser $applier = null)
    {
        $this->user = $user;
        $this->applier = $applier;
    }

    public function getContext()
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
