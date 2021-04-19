<?php
namespace Concrete\Core\Search\Field;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class ManagerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('manager/search_field/file', function($app) {
            $manager = $this->app->make('Concrete\Core\File\Search\Field\Manager');

            return $manager;
        });
        $this->app->singleton('manager/search_field/page', function($app) {
            $manager = $this->app->make('Concrete\Core\Page\Search\Field\Manager');

            return $manager;
        });
        $this->app->singleton('manager/search_field/user', function($app) {
            $manager = $this->app->make('Concrete\Core\User\Search\Field\Manager');

            return $manager;
        });
        $this->app->singleton('manager/search_field/express', function($app) {
            $manager = $this->app->make('Concrete\Core\Express\Search\Field\Manager');
            return $manager;
        });
        $this->app->singleton('manager/search_field/calendar_event', function($app) {
            $manager = $this->app->make('Concrete\Core\Calendar\Event\Search\Field\Manager');

            return $manager;
        });
        $this->app->singleton('manager/search_field/logging', function($app) {
            $manager = $this->app->make('Concrete\Core\Logging\Search\Field\Manager');

            return $manager;
        });
        $this->app->singleton('manager/search_field/group', function($app) {
            $manager = $this->app->make('Concrete\Core\User\Group\Search\Field\Manager');

            return $manager;
        });
    }
}
