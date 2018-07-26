<?php

namespace Concrete\Core\Validator;

use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Validator\String\MaximumLengthValidator;
use Concrete\Core\Validator\String\MinimumLengthValidator;

class PasswordValidatorServiceProvider extends Provider
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Foundation\Service\Provider::register()
     */
    public function register()
    {
        $this->app->singleton('validator/password', function (Application $app) {
            $config = $app->make('config');
            $manager = $app->make(ValidatorManagerInterface::class);

            $minimumLengthValidator = null;
            $maximumLengthValidator = null;

            $error = function ($validator, $code, $password) use (&$minimumLengthValidator, &$maximumLengthValidator) {
                if ($minimumLengthValidator && $maximumLengthValidator) {
                    return t('A password must be between %s and %s characters long.', $minimumLengthValidator->getMinimumLength(), $maximumLengthValidator->getMaximumLength());
                } elseif ($minimumLengthValidator) {
                    return t('A password must be at least %s characters long.', $minimumLengthValidator->getMinimumLength());
                } elseif ($maximumLengthValidator) {
                    return t('A password can be at most %s characters long.', $maximumLengthValidator->getMaximumLength());
                }

                return t('Invalid password.');
            };

            $requirements = function ($validator, $code) use (&$minimumLengthValidator, &$maximumLengthValidator) {
                if ($minimumLengthValidator && $maximumLengthValidator) {
                    return t('Must be between %s and %s characters long.', $minimumLengthValidator->getMinimumLength(), $maximumLengthValidator->getMaximumLength());
                } elseif ($minimumLengthValidator) {
                    return t('Must be at least %s characters long.', $minimumLengthValidator->getMinimumLength());
                } elseif ($maximumLengthValidator) {
                    return t('Must be at most %s characters long.', $maximumLengthValidator->getMaximumLength());
                }
            };

            $minimumLength = $config->get('concrete.user.password.minimum', 5);
            if ($minimumLength) {
                $minimumLengthValidator = $app->make(MinimumLengthValidator::class, [$minimumLength]);
                $minimumLengthValidator->setRequirementString($minimumLengthValidator::E_TOO_SHORT, $requirements);
                $minimumLengthValidator->setErrorString($minimumLengthValidator::E_TOO_SHORT, $error);
                $manager->setValidator('minimum_length', $minimumLengthValidator);
            }

            $maximumLength = $config->get('concrete.user.password.maximum');
            if ($maximumLength) {
                $maximumLengthValidator = $app->make(MaximumLengthValidator::class, [$maximumLength]);
                $maximumLengthValidator->setRequirementString($maximumLengthValidator::E_TOO_LONG, $requirements);
                $maximumLengthValidator->setErrorString($maximumLengthValidator::E_TOO_LONG, $error);
                $manager->setValidator('maximum_length', $maximumLengthValidator);
            }

            return $manager;
        });
    }
}
