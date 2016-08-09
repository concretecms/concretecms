<?php
namespace Concrete\Core\User\Avatar;

use Concrete\Core\Application\Application;
use Concrete\Core\File\StorageLocation\StorageLocation;
use Concrete\Core\User\UserInfo;
use HtmlObject\Image;

class StandardAvatar implements AvatarInterface
{
    protected $userInfo;
    protected $application;

    public function __construct(UserInfo $userInfo, Application $application)
    {
        $this->userInfo = $userInfo;
        $this->application = $application;
    }

    public function getPath()
    {
        $fsl = StorageLocation::getDefault();
        $configuration = $fsl->getConfigurationObject();
        $src = $configuration->getPublicURLToFile(REL_DIR_FILES_AVATARS . '/' . $this->userInfo->getUserID() . '.jpg');

        return $src;
    }

    public function output()
    {
        $img = new Image();
        $width = $this->application['config']->get('concrete.icons.user_avatar.width');
        $height = $this->application['config']->get('concrete.icons.user_avatar.height');
        $img->src($this->getPath())->class('u-avatar')->width($width)->height($height)->alt($this->userInfo->getUserName());

        return (string) $img;
    }
}
