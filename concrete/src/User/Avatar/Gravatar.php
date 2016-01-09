<?php
namespace Concrete\Core\User\Avatar;

use Concrete\Core\Application\Application;
use Concrete\Core\File\StorageLocation\StorageLocation;
use Concrete\Core\User\UserInfo;
use HtmlObject\Image;

class Gravatar extends StandardAvatar
{

    protected $size = 80;
    protected $imageSet = 'mm';
    protected $rating = 'g';

    public function getPath()
    {
        $url = '//www.gravatar.com/avatar/';
        $url .= md5(strtolower(trim($this->userInfo->getUserEmail())));
        $url .= "?s={$this->size}&d={$this->imageSet}&r={$this->rating}";
        return $url;
    }

}