<?php
namespace Concrete\Core\Job;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class JobServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Supply the deprecated static session accessor with a real application object
        Job::setApplicationObject($this->app);

        $this->app->bind('job', JobFactory::class);
    }
}
