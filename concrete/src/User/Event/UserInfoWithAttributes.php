<?php
namespace Concrete\Core\User\Event;

class UserInfoWithAttributes extends UserInfo
{
    protected $keys = array();

    public function setAttributes($keys)
    {
        $this->keys = $keys;
    }

    public function getAttributes()
    {
        return $this->keys;
    }
}
