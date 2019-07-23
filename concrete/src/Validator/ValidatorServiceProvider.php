<?php

namespace Concrete\Core\Validator;

use Concrete\Core\Foundation\Service\Provider;

class ValidatorServiceProvider extends Provider
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Foundation\Service\Provider::register()
     */
    public function register()
    {
        // Bind the manager interface to the default implementation
        $this->app->bind(ValidatorManagerInterface::class, ValidatorManager::class);
        $this->app->bind(ValidatorForSubjectInterface::class, ValidatorForSubjectManager::class);
    }
}
