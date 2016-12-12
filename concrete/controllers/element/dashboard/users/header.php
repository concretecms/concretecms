<?php
namespace Concrete\Controller\Element\Dashboard\Users;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\User\UserInfo;

class Header extends ElementController
{

    public function getElement()
    {
        return 'dashboard/users/header';
    }

    public function __construct(UserInfo $user)
    {
        parent::__construct();
        $this->user = $user;
    }

    public function view()
    {
        $this->set('token_validator', \Core::make('token'));
        $this->set('user', $this->user);
    }

}
