<?php
namespace Concrete\Core\User;

use stdclass;

class EditResponse extends \Concrete\Core\Application\EditResponse
{

    protected $users = array();

    public function setUser(UserInfo $user)
    {
        $this->users[] = $user;
    }

    public function setUsers($users)
    {
        $this->users = $users;
    }

    public function getJSONObject()
    {
        $o = parent::getBaseJSONObject();
        foreach ($this->users as $user) {
            $uo = new stdClass;
            $uo->uID = $user->getUserID();
            $uo->displayName = $user->getUserDisplayName();
            $o->users[] = $uo;
        }
        return $o;
    }

}
