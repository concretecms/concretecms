<?php

namespace Concrete\Core\Validator;

use ArrayAccess;
use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Validator\String\MaximumLengthValidator;
use Concrete\Core\Validator\String\MinimumLengthValidator;
use Concrete\Core\Validator\String\RegexValidator;
use Concrete\Core\Validator\String\ReuseValidator;


class PasswordValidatorServiceProvider extends Provider
{
    /**
     * The config repository we're using to register
     *
     * @var \Concrete\Core\Config\Repository\Repository
     */
    protected $config;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Foundation\Service\Provider::register()
     */
    public function register()
    {
        $this->app->singleton('validator/password', function () {
            $this->config = $this->app->make('config');
            $manager = $this->app->make(ValidatorForSubjectManager::class);

            $this->applyLengthValidators($manager);
            $this->applyStringRequirementValidators($manager);
            $this->applyRegexRequirements($manager);
            $this->applyPasswordReuseValidator($manager);

            return $manager;
        });
    }

    /**
     *
     *
     * @param ValidatorManagerInterface $manager
     */
    protected function applyPasswordReuseValidator(ValidatorManagerInterface $manager)
    {
        $trackUse = $this->config->get('concrete.user.password.reuse', 5);
        if ($trackUse) {
            $reuseValidator = $this->app->make(ReuseValidator::class, ['maxReuse' => $trackUse]);
            $reuseValidator->setErrorString($reuseValidator::E_PASSWORD_RECENTLY_USED, t("You've recently used this password, please use a unique password."));
            $reuseValidator->setRequirementString($reuseValidator::E_PASSWORD_RECENTLY_USED, t('Must not have been recently used by this account.'));
            $manager->setValidator('reuse', $reuseValidator);
        }
    }

    /**
     * Apply configured password length validators
     *
     * @param $manager
     */
    protected function applyLengthValidators(ValidatorManagerInterface $manager)
    {
        $minimum = $this->getMinimumRequirement();
        $maximum = $this->getMaximumRequirement();
        $this->applyMinMaxStrings($minimum, $maximum);

        // Set validators
        if ($maximum) {
            $manager->setValidator('maximum_length', $maximum);
        }
        if ($minimum) {
            $manager->setValidator('minimum_length', $minimum);
        }
    }

    /**
     * Get maximum length validator
     *
     * @return \Concrete\Core\Validator\String\MaximumLengthValidator|null
     */
    protected function getMaximumRequirement()
    {
        $maximumLength = $this->config->get('concrete.user.password.maximum');

        return $maximumLength ? $this->app->make(MaximumLengthValidator::class, ['maximum_length' => $maximumLength]) : null;
    }

    /**
     * Get minimum length validator
     *
     * @return \Concrete\Core\Validator\String\MinimumLengthValidator|null
     */
    protected function getMinimumRequirement()
    {
        $minimumLength = $this->config->get('concrete.user.password.minimum', 8);
        return $minimumLength ? $this->app->make(MinimumLengthValidator::class, ['minimum_length' => $minimumLength]) : null;
    }

    /**
     * Apply translatable strings to minimum and maximum requirements
     *
     * @param $minimum
     * @param $maximum
     */
    protected function applyMinMaxStrings($minimum, $maximum)
    {
        if ($minimum && $maximum) {
            $errorString = t('A password must be between %s and %s characters long.', $minimum->getMinimumLength(), $maximum->getMaximumLength());
            $requirement = t('Must be between %s and %s characters long.', $minimum->getMinimumLength(), $maximum->getMaximumLength());
        } elseif ($minimum) {
            $errorString = t('Must be at least %s characters long.', $minimum->getMinimumLength());
            $requirement = t('A password must be at least %s characters long.', $minimum->getMinimumLength());
        } elseif ($maximum) {
            $errorString = t('A password must be at most %s characters long.', $maximum->getMaximumLength());
            $requirement = t('Must be at most %s characters long.', $maximum->getMaximumLength());
        } else {
            $errorString = t('Invalid Password.');
            $requirement = '';
        }

        $errorHandler = function ($validator, $code, $password) use ($errorString) { return $errorString; };
        $requirementHandler = function ($validator, $code) use (&$requirement) { return $requirement; };
        $minimum->setRequirementString($minimum::E_TOO_SHORT, $requirementHandler);
        $minimum->setErrorString($minimum::E_TOO_SHORT, $errorHandler);

        if (is_object($maximum)) {
            $maximum->setRequirementString($maximum::E_TOO_LONG, $requirementHandler);
            $maximum->setErrorString($maximum::E_TOO_LONG, $errorHandler);
        }
    }

    /**
     * Apply validators that require specific substrings
     *
     * @param \Concrete\Core\Validator\ValidatorManagerInterface $manager
     */
    protected function applyStringRequirementValidators(ValidatorManagerInterface $manager)
    {
        $specialCharacters = (int) $this->config->get('concrete.user.password.required_special_characters', 0);
        $lowerCase = (int) $this->config->get('concrete.user.password.required_lower_case', 0);
        $upperCase = (int) $this->config->get('concrete.user.password.required_upper_case', 0);

        if ($specialCharacters) {
            $regex = "/([^a-zA-Z0-9].*){{$specialCharacters},}/";
            $requirement = t2('Must contain at least %d special character.', 'Must contain at least %d special characters.', $specialCharacters);

            $manager->setValidator('required_special_characters', $this->regexValidator($regex, $requirement));
        }

        if ($lowerCase) {
            $regex = "/([a-z].*){{$lowerCase},}/";
            $requirement = t2('Must contain at least %d lowercase character.', 'Must contain at least %d lowercase characters.', $lowerCase);

            $manager->setValidator('required_lower_case', $this->regexValidator($regex, $requirement));
        }

        if ($upperCase) {
            $regex = "/([A-Z].*){{$upperCase},}/";
            $requirement = t2('Must contain at least %d uppercase character.', 'Must contain at least %d uppercase characters.', $upperCase);

            $manager->setValidator('required_upper_case', $this->regexValidator($regex, $requirement));
        }
    }

    /**
     * Create a regex validator
     *
     * @param string $regex
     * @param string $requirement
     *
     * @return RegexValidator
     */
    protected function regexValidator($regex, $requirement)
    {
        $validator = $this->app->make(RegexValidator::class, ['pattern' => $regex]);
        $validator->setRequirementString(RegexValidator::E_DOES_NOT_MATCH, $requirement);
        $validator->setErrorString(RegexValidator::E_DOES_NOT_MATCH, $requirement);

        return $validator;
    }

    protected function applyRegexRequirements(ValidatorManagerInterface $manager)
    {
        $specialCharacters = (array) $this->config->get('concrete.user.password.custom_regex', []);
        $i = 0;

        foreach ($specialCharacters as $regex => $requirementString) {
            $validator = $this->wrappedRegexValidator($regex, $requirementString);

            $i++;
            $manager->setValidator('custom_validator_' . $i, $validator);
        }
    }

    /**
     * Create a closure validator that wraps a regex validator and handles all errors
     * If the given regex is invalid, we will deny all passwords!
     *
     * @param $regex
     * @param $requirementString
     *
     * @return \Concrete\Core\Validator\ClosureValidator|mixed
     */
    protected function wrappedRegexValidator($regex, $requirementString)
    {
        $regexValidator = $this->regexValidator($regex, $requirementString);
        $validator = $this->app->make(ClosureValidator::class, ['validator_closure' =>
            function (ClosureValidator $validator, $string, ArrayAccess $error = null) use ($regexValidator) {
                try {
                    $regexValidator->isValid($string, $error);
                } catch (\RuntimeException $e) {
                    if ($error) {
                        $error[] = $regexValidator::E_DOES_NOT_MATCH;
                    }

                    return false;
                }
            },
            function () use ($regexValidator, $requirementString) {
                return [
                    $regexValidator::E_DOES_NOT_MATCH => $requirementString
                ];
            }
        ]);

        return $validator;

    }
}
