<?php

namespace Concrete\Core\Page;

use Concrete\Core\Foundation\Service\Provider;

class PageServiceProvider extends Provider
{
    public function register()
    {
        $this->app->singleton(HandleGenerator::class);
    }
}