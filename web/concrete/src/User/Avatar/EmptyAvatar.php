<?php
namespace Concrete\Core\User\Avatar;

use Concrete\Core\Application\Application;
use Concrete\Core\File\StorageLocation\StorageLocation;
use Concrete\Core\User\UserInfo;
use HtmlObject\Image;

class EmptyAvatar extends StandardAvatar
{

    public function getPath()
    {
        return $this->application['config']->get('concrete.icons.user_avatar.default');
    }

}