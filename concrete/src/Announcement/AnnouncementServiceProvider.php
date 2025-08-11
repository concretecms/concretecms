<?php

namespace Concrete\Core\Announcement;

use Concrete\Core\Foundation\Service\Provider;

class AnnouncementServiceProvider extends Provider
{

    public function register()
    {
        $this->app->singleton(Manager::class);
    }
}
