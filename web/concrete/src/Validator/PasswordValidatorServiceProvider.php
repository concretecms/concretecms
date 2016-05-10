<?php
namespace Concrete\Core\Validator;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Validator\String\MaximumLengthValidator;
use Concrete\Core\Validator\String\MinimumLengthValidator;

class PasswordValidatorServiceProvider extends Provider
{
    /**
     * Registers the services provided by this provider.
     */
    public function register()
    {
        $this->app->bindShared('validator/password', function ($app) {
            /** @var Repository $config */
            $config = $app['config'];
            /** @var \Concrete\Core\Validator\ValidatorManagerInterface $manager */
            $manager = $app->make('\Concrete\Core\Validator\ValidatorManagerInterface');

            $minimum_length = $config->get('concrete.user.password.minimum', 5);
            $maximum_length = $config->get('concrete.user.password.maximum');

            /** @var MinimumLengthValidator $minimum */
            $minimum = null;
            /** @var MaximumLengthValidator $maximum */
            $maximum = null;

            $error_closure = function ($validator, $code, $password) use (&$minimum, &$maximum) {
                if ($minimum && $maximum) {
                    return t('A password must be between %s and %s characters long.',
                        $minimum->getMinimumLength(),
                        $maximum->getMaximumLength());
                } elseif ($minimum) {
                    return t('A password must be at least %s characters long.', $minimum->getMinimumLength());
                } elseif ($maximum) {
                    return t('A password can be at most %s characters long.', $maximum->getMaximumLength());
                }

                return t('Invalid password.');
            };

            $requirement_closure = function ($validator, $code) use (&$minimum, &$maximum) {
                if ($minimum && $maximum) {
                    return t('Must be between %s and %s characters long.',
                        $minimum->getMinimumLength(),
                        $maximum->getMaximumLength());
                } elseif ($minimum) {
                    return t('Must be at least %s characters long.', $minimum->getMinimumLength());
                } elseif ($maximum) {
                    return t('Must be at most %s characters long.', $maximum->getMaximumLength());
                }
            };

            if ($minimum_length) {
                $minimum = $app->make('\Concrete\Core\Validator\String\MinimumLengthValidator', array($minimum_length));

                $minimum->setRequirementString($minimum::E_TOO_SHORT, $requirement_closure);
                $minimum->setErrorString($minimum::E_TOO_SHORT, $error_closure);

                $manager->setValidator('minimum_length', $minimum);
            }

            if ($maximum_length) {
                $maximum = $app->make('\Concrete\Core\Validator\String\MaximumLengthValidator', array($maximum_length));

                $maximum->setRequirementString($maximum::E_TOO_LONG, $requirement_closure);
                $maximum->setErrorString($maximum::E_TOO_LONG, $error_closure);

                $manager->setValidator('maximum_length', $maximum);
            }

            return $manager;
        });
    }
}
