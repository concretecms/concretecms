<?php
namespace Concrete\Core\Application\Service;

use Concrete\Core\Session\SessionValidator;
use Loader;
use TaskPermission;

class User
{
    /**
     * @param $uo \Concrete\Core\User\User|int
     * @param bool $showSpacer
     *
     * @return mixed
     */
    public function getOnlineNow($uo, $showSpacer = true)
    {
        $ul = 0;
        if (is_object($uo)) {
            // user object
            $ul = $uo->getLastOnline();
        } elseif (is_numeric($uo)) {
            $db = Loader::db();
            $ul = $db->getOne("select uLastOnline from Users where uID = {$uo}");
        }
        $onlineTimeout = app(SessionValidator::class)->getUserActivityThreshold();
        $online = (time() - $ul) <= $onlineTimeout;

        if ($online) {
            return ONLINE_NOW_SRC_ON;
        } else {
            if ($showSpacer) {
                return ONLINE_NOW_SRC_OFF;
            }
        }
    }

    /**
     * @param string $password
     * @param null|\Concrete\Core\Error\ErrorList\ErrorList $errorObj
     *
     * @return bool
     *
     * @deprecated Use `app('validator/password')->isValid($password, $error);`
     */
    public function validNewPassword($password, $errorObj = null)
    {
        return \Core::make('validator/password')->isValid($password, $errorObj);
    }

    /**
     * @return bool
     */
    public function canAccessUserSearchInterface()
    {
        $tp = new TaskPermission();

        return $tp->canAccessUserSearch();
    }

    /**
     * @param $email
     */
    public function generateUsernameFromEmail($email)
    {
        $db = \Database::connection();
        $prefix = substr($email, 0, strpos($email, '@'));
        $numberOfUsers = 1;
        while ($numberOfUsers > 0) {
            $letters = '123456789abcdefghijklmnopqrstuvwxyz';
            $letters = str_repeat($letters, 3);
            $suffix = substr(str_shuffle($letters), 0, 3);
            $uName = $prefix . $suffix;
            $numberOfUsers = $db->GetOne('select count(uID) from Users where uName = ?', [$uName]);
        }

        return $uName;
    }
}
