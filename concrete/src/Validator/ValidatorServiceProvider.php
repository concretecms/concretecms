<?php

namespace Concrete\Core\Validator;

use Concrete\Core\Foundation\Service\Provider;

class ValidatorServiceProvider extends Provider
{
    /**
     * Registers the services provided by this provider.
     * @return void
     */
    public function register()
    {
        // Bind the manager interface to the default implementation
        $this->app->bind(
            '\Concrete\Core\Validator\ValidatorManagerInterface',
            '\Concrete\Core\Validator\ValidatorManager');
    }

}
