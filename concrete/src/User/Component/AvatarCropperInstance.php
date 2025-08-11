<?php

namespace Concrete\Core\User\Component;

use Concrete\Core\Config\Repository\Repository;
use HtmlObject\Element;

class AvatarCropperInstance
{

    /**
     * @var string
     */
    protected $accessToken;

    /**
     * @var int
     */
    protected $width;

    /**
     * @var int
     */
    protected $height;

    /**
     * @var string
     */
    protected $uploadUrl;

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param mixed $accessToken
     */
    public function setAccessToken($accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return string
     */
    public function getUploadUrl(): string
    {
        return $this->uploadUrl;
    }

    /**
     * @param string $uploadUrl
     */
    public function setUploadUrl(string $uploadUrl): void
    {
        $this->uploadUrl = $uploadUrl;
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @param int $width
     */
    public function setWidth(int $width): void
    {
        $this->width = $width;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @param int $height
     */
    public function setHeight(int $height): void
    {
        $this->height = $height;
    }

    public function getTag(): Element
    {
        $lang = [
            'header' => t('Change Profile Picture'),
            'upload' => t('Click to choose or a drag a profile picture.'),
            'reset' => t('Reset Image'),
            'save' => t('Save'),
            'saveInProgress' => t('Saving...'),
        ];
        $tag = new Element('avatar-cropper');
        $tag->setAttribute(':lang', json_encode($lang));
        $tag->setAttribute('upload-url', $this->getUploadUrl());
        $tag->setAttribute('access-token', $this->getAccessToken());
        $tag->setAttribute(':width', $this->getWidth());
        $tag->setAttribute(':height', $this->getHeight());
        return $tag;
    }



}
