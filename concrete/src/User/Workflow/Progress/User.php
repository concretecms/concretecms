<?php
namespace Concrete\Core\User\Workflow\Progress;

use Concrete\Core\User\UserInfo;
use Concrete\Core\Workflow\Progress\UserProgress;

class User
{

    public function __construct(UserInfo $u, UserProgress $wp)
    {
        $this->user = $u;
        $this->wp = $wp;
    }

    public function getUserObject()
    {
        return $this->user;
    }

    public function getWorkflowProgressObject()
    {
        return $this->wp;
    }
}