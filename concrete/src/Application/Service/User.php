<?php
namespace Concrete\Core\Application\Service;

use Loader;
use TaskPermission;
use Config;

class User
{

    /**
     * @param $uo \User
     * @param bool $showSpacer
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

        $online = (time() - $ul) <= ONLINE_NOW_TIMEOUT;

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
     * @param null|\Concrete\Core\Error\Error $errorObj
     * @return bool
     * @deprecated Use `\Core::make('validator/password')->isValid($password, $error);`
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
}
