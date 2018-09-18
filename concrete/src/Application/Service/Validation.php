<?php

namespace Concrete\Core\Application\Service;

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Validator\String\UniqueUserEmailValidator;
use Concrete\Core\Validator\String\UniqueUserNameValidator;

class Validation
{
    /**
     * @deprecated Use the Concrete\Core\Validator\String\UniqueUserNameValidator validator
     *
     * @param string $uName
     *
     * @return bool
     */
    public function isUniqueUsername($uName)
    {
        $app = Application::getFacadeApplication();

        return $app->make(UniqueUserNameValidator::class)->isValid($uName);
    }

    /**
     * @deprecated Use \Concrete\Core\Validator\String\UniqueUserEmailValidator
     *
     * @param string $uEmail
     *
     * @return bool
     */
    public function isUniqueEmail($uEmail)
    {
        $app = Application::getFacadeApplication();

        return $app->make(UniqueUserEmailValidator::class)->isValid($uEmail);
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
        $app = Application::getFacadeApplication();

        return $app->make('validator/password')->isValid($pass);
    }

    /**
     * @deprecated use the 'validator/user/name' validator
     *
     * @param string $username
     *
     * @return bool
     */
    public function username($username)
    {
        $app = Application::getFacadeApplication();
        $validator = $app->make('validator/user/name');
        foreach ($validator->getValidators() as $handle => $validator) {
            if ($handle !== 'unique_username') {
                if (!$validator->isValid($username)) {
                    return false;
                }
            }
        }

        return true;
    }
}
