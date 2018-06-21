<?php

namespace Concrete\Core\Application\Service;

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\Validation\UsernameValidator;
use Loader;

class Validation
{
    /**
     * @deprecated Use \Core::make(\Concrete\Core\User\Validation\UsernameValidator::class)->check()
     *
     * @param string $uName
     *
     * @return bool
     */
    public function isUniqueUsername($uName)
    {
        $app = Application::getFacadeApplication();
        $validator = $app->make(UsernameValidator::class);

        return ($validator->checkUnique($uName) & $validator::E_IN_USE) === 0;
    }

    /**
     * Checks whether a passed email address is unique.
     *
     *
     * @param string $uEmail
     *
     * @return bool
     */
    public function isUniqueEmail($uEmail)
    {
        $db = Loader::db();
        $q = 'select uID from Users where uEmail = ?';
        $r = $db->getOne($q, [$uEmail]);
        if ($r) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Returns true if this is a valid password.
     *
     * @param string $pass
     *
     * @return bool
     *
     * @deprecated Use `\Core::make('validator/password')->isValid($pass, $error);`
     */
    public function password($pass)
    {
        return \Core::make('validator/password')->isValid($pass);
    }

    /**
     * @deprecated Use \Core::make(\Concrete\Core\User\Validation\UsernameValidator::class)->check()
     *
     * @param string $username
     *
     * @return bool
     */
    public function username($username)
    {
        $app = Application::getFacadeApplication();
        $validator = $app->make(UsernameValidator::class);

        return ($validator->checkLength($username) | $validator->checkCharacters($username)) === $validator::E_OK;
    }
}
