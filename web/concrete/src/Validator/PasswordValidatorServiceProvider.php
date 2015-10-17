<?php

namespace Concrete\Core\Validator;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Error\Error;
use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Validator\String\MaximumLengthValidator;
use Concrete\Core\Validator\String\MinimumLengthValidator;

class PasswordValidatorServiceProvider extends Provider
{

    /**
     * Registers the services provided by this provider.
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('validator/password', function($app) {
            /** @type Repository $config */
            $config = $app['config'];
            /** @type \Concrete\Core\Validator\ValidatorManagerInterface $manager */
            $manager = $app->make('\Concrete\Core\Validator\ValidatorManagerInterface');

            $minimum_length = $config->get('concrete.user.password.minimum', 5);
            $maximum_length = $config->get('concrete.user.password.maximum');

            if ($minimum_length) {
                $minimum = new MinimumLengthValidator($minimum_length);

                // Set the requirement string
                $minimum->setRequirementString($minimum::E_TOO_SHORT, function(MinimumLengthValidator $validator, $code) {
                    return t('Password must be at least %s characters long.', $validator->getMinimumLength());
                });

                // Set the error string
                $minimum->setErrorString($minimum::E_TOO_SHORT, function(MinimumLengthValidator $validator, $code, $passed) {
                    return t('Password too short. Must be at least %s characters long.', $validator->getMinimumLength());
                });

                $manager->setValidator('minimum_length', $minimum);
            }

            if ($maximum_length) {
                $maximum = new MaximumLengthValidator($maximum_length);

                // Set the requirement string
                $maximum->setRequirementString($maximum::E_TOO_LONG, function(MaximumLengthValidator $validator, $code) {
                    return t('Password must be at most %s characters long.', $validator->getMaximumLength());
                });

                // Set the error string
                $maximum->setErrorString($maximum::E_TOO_LONG, function(MaximumLengthValidator $validator, $code, $passed) {
                    return t('Password too long. Must be at most %s characters long.', $validator->getMaximumLength());
                });

                $manager->setValidator('maximum_length', $maximum);
            }

            return $manager;
        });
    }

}
