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
     * @param mixed $params
     * @param null|\Concrete\Core\Error\Error $errorObj
     * @return bool
     */
    public function validNewPassword($params, $errorObj = null)
    {
        $valid = true;

        if (is_array($params)) {
            $password = $params['password'];
            $passwordConfirm = $params['passwordConfirm'];
        } else {
            // Add user does not require password confirmation. Also useful for AJAX pre-checks?
            $password = $params;
        }

        if ((strlen($password) < Config::get('concrete.user.password.minimum')) || (strlen($password) >  Config::get('concrete.user.password.maximum'))) {
            if ($errorObj) {
                $errorObj->add(
                    t(
                        'A password must be between %s and %s characters',
                        Config::get('concrete.user.password.minimum'),
                        Config::get('concrete.user.password.maximum')
                    )
                );
            }
            $valid = false;
        }

        // Only check equality if password confirm was supplied
        if ($password && isset($passwordConfirm) && $password != $passwordConfirm) {
            if ($errorObj) {
                $errorObj->add(
                    t('The two passwords provided do not match.')
                );
            }
            $valid = false;
        }

        return $valid;
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
