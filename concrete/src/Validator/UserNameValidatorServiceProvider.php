<?php

namespace Concrete\Core\Validator;

use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Validator\String\MaximumLengthValidator;
use Concrete\Core\Validator\String\MinimumLengthValidator;
use Concrete\Core\Validator\String\RegexValidator;
use Concrete\Core\Validator\String\UniqueUserNameValidator;

class UserNameValidatorServiceProvider extends Provider
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Foundation\Service\Provider::register()
     */
    public function register()
    {
        $this->app->singleton('validator/user/name', function (Application $app) {
            $config = $app->make('config');
            $manager = $app->make(ValidatorForSubjectInterface::class);

            $minimumLengthValidator = null;
            $maximumLengthValidator = null;
            $lengthError = function ($validator, $code, $username) use (&$minimumLengthValidator, &$maximumLengthValidator) {
                if ($minimumLengthValidator && $maximumLengthValidator) {
                    return t('A username must be between %s and %s characters long.', $minimumLengthValidator->getMinimumLength(), $maximumLengthValidator->getMaximumLength());
                } elseif ($minimumLengthValidator) {
                    return t('A username must be at least %s characters long.', $minimumLengthValidator->getMinimumLength());
                } elseif ($maximumLengthValidator) {
                    return t('A username can be at most %s characters long.', $maximumLengthValidator->getMaximumLength());
                }

                return t('Invalid username.');
            };
            $lengthRequirements = function ($validator, $code) use (&$minimumLengthValidator, &$maximumLengthValidator) {
                if ($minimumLengthValidator && $maximumLengthValidator) {
                    return t('Must be between %s and %s characters long.', $minimumLengthValidator->getMinimumLength(), $maximumLengthValidator->getMaximumLength());
                } elseif ($minimumLengthValidator) {
                    return t('Must be at least %s characters long.', $minimumLengthValidator->getMinimumLength());
                } elseif ($maximumLengthValidator) {
                    return t('Must be at most %s characters long.', $maximumLengthValidator->getMaximumLength());
                }
            };
            $minimumLength = $config->get('concrete.user.username.minimum', 1);
            if ($minimumLength) {
                $minimumLengthValidator = $app->make(MinimumLengthValidator::class, [$minimumLength]);
                $minimumLengthValidator->setRequirementString($minimumLengthValidator::E_TOO_SHORT, $lengthRequirements);
                $minimumLengthValidator->setErrorString($minimumLengthValidator::E_TOO_SHORT, $lengthError);
                $manager->setValidator('minimum_length', $minimumLengthValidator);
            }
            $maximumLength = $config->get('concrete.user.username.maximum');
            if ($maximumLength) {
                $maximumLengthValidator = $app->make(MaximumLengthValidator::class, [$maximumLength]);
                $maximumLengthValidator->setRequirementString($maximumLengthValidator::E_TOO_LONG, $lengthRequirements);
                $maximumLengthValidator->setErrorString($maximumLengthValidator::E_TOO_LONG, $lengthError);
                $manager->setValidator('maximum_length', $maximumLengthValidator);
            }

            $rxBoundary = '[' . $config->get('concrete.user.username.allowed_characters.boundary') . ']';
            $rxMiddle = '[' . $config->get('concrete.user.username.allowed_characters.middle') . ']';
            $rx = "/^({$rxBoundary}({$rxMiddle}*{$rxBoundary})?)?$/";
            $rxValidator = $app->make(RegexValidator::class, [$rx]);
            $rxValidator->setRequirementString($rxValidator::E_DOES_NOT_MATCH, function($validator, $code) use ($config) {
                return t($config->get('concrete.user.username.allowed_characters.requirement_string'));
            });
            $rxValidator->setErrorString($rxValidator::E_DOES_NOT_MATCH, function($validator, $code) use ($config)  {
                return t($config->get('concrete.user.username.allowed_characters.error_string'));
            });
            $manager->setValidator('valid_pattern', $rxValidator);

            $manager->setValidator('unique_username', $app->make(UniqueUserNameValidator::class));

            return $manager;
        });
    }
}
