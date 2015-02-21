<?php
namespace Concrete\Core\Application\Service;

use Loader;
use Config;

class Validation
{

    /**
     * Checks whether a passed username is unique or if a user of this name already exists
     * @param string $uName
     * @return bool
     */
    public function isUniqueUsername($uName)
    {
        $db = Loader::db();
        $q = "select uID from Users where uName = ?";
        $r = $db->getOne($q, array($uName));
        if ($r) {
            return false;
        } else {
            return true;
        }
    }


    /**
     * Checks whether a passed email address is unique
     * @return bool
     * @param string $uEmail
     */
    public function isUniqueEmail($uEmail)
    {
        $db = Loader::db();
        $q = "select uID from Users where uEmail = ?";
        $r = $db->getOne($q, array($uEmail));
        if ($r) {
            return false;
        } else {
            return true;
        }
    }


    /**
     * Returns true if this is a valid password.
     * @param string $pass
     * @return bool
     */
    public function password($pass)
    {
        $hu = \Core::make('helper/concrete/user');
        return $hu->validNewPassword($pass);
    }

    /**
     * Returns true if this is a valid username.
     * Valid usernames can only contain letters, numbers, dots (only in the middle), underscores (only in the middle) and optionally single spaces
     * @param string $username
     * @return bool
     */
    public function username($username)
    {
        $username = trim($username);
        if (strlen($username) < Config::get('concrete.user.username.minimum')) {
            return false;
        }
        if (strlen($username) > Config::get('concrete.user.username.maximum')) {
            return false;
        }
        $rxBoundary = '[A-Za-z0-9]';
        if (Config::get('concrete.user.username.allow_spaces')) {
            $rxMiddle = '[A-Za-z0-9_. ]';
        } else {
            $rxMiddle = '[A-Za-z0-9_.]';
        }
        if (strlen($username) < 3) {
            if (!preg_match('/^' . $rxBoundary . '+$/', $username)) {
                return false;
            }
        } else {
            if (!preg_match('/^' . $rxBoundary . $rxMiddle . '+' . $rxBoundary . '$/', $username)) {
                return false;
            }
        }
        return true;
    }

}
