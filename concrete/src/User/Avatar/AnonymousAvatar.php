<?php
namespace Concrete\Core\User\Avatar;

use Concrete\Core\Application\Application;

use HtmlObject\Image;

class AnonymousAvatar implements AvatarInterface
{
    protected $application;
    protected $alt;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * @return mixed
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * @param mixed $alt
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;
    }

    public function getPath()
    {
        return $this->application['config']->get('concrete.icons.user_avatar.default');
    }

    public function output()
    {
        $img = new Image();
        $img->src($this->getPath())->class('u-avatar')->alt($this->getAlt());
        return (string) $img;
    }
}
