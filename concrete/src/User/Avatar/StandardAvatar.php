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
        $img->src($this->getPath())->class('u-avatar')->alt(h($this->userInfo->getUserName()));

        return (string) $img;
    }
}
