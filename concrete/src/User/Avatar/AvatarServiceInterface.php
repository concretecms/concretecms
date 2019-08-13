<?php
namespace Concrete\Core\User\Avatar;

use Concrete\Core\User\UserInfo;

/**
 * @since 5.7.5.4
 */
interface AvatarServiceInterface
{
    public function userHasAvatar(UserInfo $ui);
    public function getAvatar(UserInfo $ui);
    public function removeAvatar(UserInfo $ui);
}
