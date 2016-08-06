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

    public function output()
    {
        $img = new Image();
        $img->src($this->getPath())->class('u-avatar')->alt(t('guest'));
        return (string) $img;
    }
