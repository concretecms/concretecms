<?php
namespace Concrete\Core\User;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\User\RegistrationService;

class UserServiceProvider extends ServiceProvider
{

    public function register()
    {
        $app = $this->app;
        $this->app->bindShared('user/registration', function() use ($app) {
            return $app->make('Concrete\Core\User\RegistrationService');
        });
        $this->app->bindShared('user/avatar', function() use ($app) {
            return $app->make('Concrete\Core\User\Avatar\AvatarService');
        });
        $this->app->bind('Concrete\Core\User\RegistrationServiceInterface', function() use ($app) {
            return $app->make('user/registration');
        });
        $this->app->bind('Concrete\Core\User\Avatar\AvatarServiceInterface', function() use ($app) {
            return $app->make('user/avatar');
        });
    }

}
