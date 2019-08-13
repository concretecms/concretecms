<?php
namespace Concrete\Core\User\Event;

/**
 * @since 5.7.3
 */
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
