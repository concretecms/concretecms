<?php
namespace Concrete\Core\User\Avatar;

use Concrete\Core\Application\Application;

class EmptyAvatar extends StandardAvatar
{
    public function getPath()
    {
        return $this->application['config']->get('concrete.icons.user_avatar.default');
    }
}
