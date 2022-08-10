<?php

namespace Concrete\Core\Notification;

use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Notification\Events\MercureService;
use Concrete\Core\Notification\Type\UserDeactivatedType;
use Symfony\Component\Mercure\Hub;
use Symfony\Component\Mercure\PublisherInterface;

class NotificationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(
            'Concrete\Core\Notification\Type\Manager',
            function ($app) {
                $manager = new Type\Manager($app);
                $manager->driver('core_update');
                $manager->driver('new_conversation_message');
                $manager->driver('new_form_submission');
                $manager->driver('new_private_message');
                $manager->driver('user_signup');
                $manager->driver('workflow_progress');
                $manager->driver('group_signup_request');
                $manager->driver('group_signup');
                $manager->driver('group_role_change');
                $manager->driver('group_create');
                $manager->driver('group_signup_request_accept');
                $manager->driver('group_signup_request_decline');
                $manager->driver(UserDeactivatedType::IDENTIFIER);
                return $manager;
            }
        );

        $this->app->singleton(
            'manager/notification/types',
            function ($app) {
                return $app->make('Concrete\Core\Notification\Type\Manager');
            }
        );

        $this->app->singleton(
            'manager/notification/subscriptions',
            function ($app) {
                return $app->make('Concrete\Core\Notification\Subscription\Manager');
            }
        );
    }
}
