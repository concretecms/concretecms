<?php
namespace Concrete\Core\Job;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class JobServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Supply the deprecated static session accessor with a real application object
        Job::setApplicationObject($this->app);

        $this->app->bind('Concrete\Core\Job\Factory', 'Concrete\Core\Job\Factory');
        $this->app->bind('Concrete\Core\Job\Service', 'Concrete\Core\Job\Service');

        $this->app->singleton('job', function ($app) {
            return $app->make('Concrete\Core\Job\Factory');
        });
    }
}
