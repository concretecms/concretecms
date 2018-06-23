<?php

namespace Concrete\Core\Validator;

use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Validator\String\UniqueUserEmailValidator;

class UserEmailValidatorServiceProvider extends Provider
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Foundation\Service\Provider::register()
     */
    public function register()
    {
        $this->app->singleton('validator/user/email', function (Application $app) {
            $config = $app->make('config');
            $manager = $app->make(ValidatorForSubjectInterface::class);

            $manager->setValidator(
                'unique_user_email',
                $app->make(
                    UniqueUserEmailValidator::class,
                    [
                        'testMXRecord' => $config->get('concrete.user.email.test_mx_record'),
                        'strict' => $config->get('concrete.user.email.strict'),
                    ]
                )
            );

            return $manager;
        });
    }
}
