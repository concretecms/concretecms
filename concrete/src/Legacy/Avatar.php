<?php
namespace Concrete\Core\Legacy;

use Core;

/**
 * Class Avatar.
 *
 * @deprecated
 *
 * \@package Concrete\Core\Application\Service
 */
class Avatar
{
    /**
     * @param \UserInfo $uo
     * @param bool $suppressNone
     * @param float $aspectRatio
     *
     * @return string
     */
    public function outputUserAvatar($uo, $suppressNone = false, $aspectRatio = 1.0)
    {
        if (!$uo->hasAvatar()) {
            if ($suppressNone) {
                return '';
            }
        }

        $avatar = $uo->getUserAvatar();

        return $avatar->output();
    }

    /**
     * gets the image path for a users avatar.
     *
     * @param \UserInfo $uo
     * @param bool $withNoCacheStr
     *
     * @return bool|string $src
     */
    public function getImagePath($uo, $withNoCacheStr = true)
    {
        if (!$uo->hasAvatar()) {
            return false;
        }

        $avatar = $uo->getUserAvatar();

        return $avatar->getPath();
    }

    /**
     * What to show if the user has no avatar.
     *
     * @param float $aspectRatio
     *
     * @return string $str
     */
    public function outputNoAvatar($aspectRatio = 1.0)
    {
        $avatar = Core::make('Concrete\Core\User\Avatar\EmptyAvatar');

        return $avatar->output();
    }

    /**
     * Removes the avatar for the given user.
     *
     * @param \User $ui
     */
    public function removeAvatar($ui)
    {
        $service = Core::make('user/avatar');
        $service->removeAvatar($ui);
    }

    /**
     * Get either a Gravatar URL or complete image tag for a specified email address.
     *
     * @deprecated
     *
     * @param string $email The email address
     * @param int $s Size in pixels, defaults to 80px [ 1 - 512 ]
     * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
     * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
     * @param bool $img True to return a complete IMG tag False for just the URL
     * @param array $atts Optional, additional key/value attributes to include in the IMG tag
     *
     * @return string containing either just a URL or a complete image tag
     * @source http://gravatar.com/site/implement/images/php/
     */
    public function getGravatar($email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array())
    {
        $url = '//www.gravatar.com/avatar/';
        $url .= md5(strtolower(trim($email)));
        $url .= "?s=$s&d=$d&r=$r";
        if ($img) {
            $url = '<img src="' . $url . '"';
            foreach ($atts as $key => $val) {
                $url .= ' ' . $key . '="' . $val . '"';
            }
            $url .= ' />';
        }

        return $url;
    }

    /**
     * @deprecated
     *
     * @param $email
     * @param int $s
     * @param string $d
     * @param string $r
     * @param bool $img
     * @param array $atts
     *
     * @return string
     */
    public function get_gravatar($email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array())
    {
        return self::getGravatar($email, $s, $d, $r, $img, $atts);
    }
}
