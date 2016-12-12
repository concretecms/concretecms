<?php
namespace Concrete\Core\Notification;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app['Concrete\Core\Notification\Type\Manager'] = $this->app->share(function ($app) {
            $manager = new Type\Manager($app);
            $manager->driver('core_update');
            $manager->driver('new_conversation_message');
            $manager->driver('new_form_submission');
            $manager->driver('new_private_message');
            $manager->driver('user_signup');
            $manager->driver('workflow_progress');
            return $manager;
        });

        $this->app['manager/notification/types'] = $this->app->share(function ($app) {
            return $app->make('Concrete\Core\Notification\Type\Manager');
        });
        $this->app['manager/notification/subscriptions'] = $this->app->share(function ($app) {
            return $app->make('Concrete\Core\Notification\Subscription\Manager');
        });
    }
}
