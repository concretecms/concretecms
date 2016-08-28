<?php

namespace Concrete\Core\Page\Collection\Response;

use Concrete\Core\Foundation\Service\Provider;

class ServiceProvider extends Provider
{

    /**
     * Registers the services provided by this provider.
     */
    public function register()
    {
        $this->app->bind(ResponseFactoryInterface::class, CollectionResponseFactory::class);
    }

}
