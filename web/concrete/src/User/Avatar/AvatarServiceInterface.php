<?php
namespace Concrete\Core\User\Avatar;

use Concrete\Core\User\UserInfo;

interface AvatarServiceInterface
{
    public function userHasAvatar(UserInfo $ui);
    public function getAvatar(UserInfo $ui);
    public function removeAvatar(UserInfo $ui);
}
