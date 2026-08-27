<?php

declare(strict_types=1);

namespace Concrete\Core\User;

use Concrete\Core\Application\Application;
use Concrete\Core\Events\EventDispatcher;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\User\Event\DeactivateUser;
use Concrete\Core\User\Group\LogSubscriber as GroupLogSubscriber;
use Concrete\Core\User\LogSubscriber as UserLogSubscriber;
use Concrete\Core\User\Notification\UserNotificationEventHandler;
use Concrete\Core\User\Password\PasswordChangeEventHandler;
use Concrete\Core\User\Password\PasswordUsageTracker;
use Concrete\Core\User\Search\MembershipsProvider;

defined('C5_EXECUTE') or die('Access Denied.');

class UserServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->bindContainer($this->app);

        // Handle binding events
        $subscribers = [
            $this->app->make(UserLogSubscriber::class),
            $this->app->make(GroupLogSubscriber::class),
        ];
        if ($this->app->resolved(EventDispatcher::class)) {
            $this->bindEvents($this->app->make(EventDispatcher::class), $subscribers);
        } else {
            $this->app->extend(EventDispatcher::class, function (EventDispatcher $director) use ($subscribers) {
                $this->bindEvents($director, $subscribers);

                return $director;
            });
        }

        $this->app->singleton(User::class);
        $this->app->singleton(MembershipsProvider::class);
    }

    /**
     * Bind things to the container.
     */
    protected function bindContainer(Application $app)
    {
        $this->app->when(PasswordUsageTracker::class)->needs('$maxReuse')->give(function () {
            return $this->app['config']->get('concrete.user.password.reuse.track', 5);
        });

        $this->app->bindShared('user/registration', static function () use ($app) {
            return $app->make('Concrete\Core\User\RegistrationService');
        });
        $this->app->bindShared('user/avatar', static function () use ($app) {
            return $app->make('Concrete\Core\User\Avatar\AvatarService');
        });
        $this->app->bindShared('user/status', static function () use ($app) {
            return $app->make('Concrete\Core\User\StatusService');
        });
        $this->app->bind('Concrete\Core\User\RegistrationServiceInterface', static function () use ($app) {
            return $app->make('user/registration');
        });
        $this->app->bind('Concrete\Core\User\StatusServiceInterface', static function () use ($app) {
            return $app->make('user/status');
        });
        $this->app->bind('Concrete\Core\User\Avatar\AvatarServiceInterface', static function () use ($app) {
            return $app->make('user/avatar');
        });
    }

    protected function bindEvents(EventDispatcher $dispatcher, $subscribers)
    {
        $dispatcher->addListener('on_after_user_deactivate', function ($e) {
            $this->app->call([$this, 'handleEvent'], ['event' => $e]);
        });

        $dispatcher->addListener('on_user_change_password', function ($event) {
            $this->app->make(PasswordChangeEventHandler::class)->handleEvent($event);
        });

        foreach ($subscribers as $subscriber) {
            $dispatcher->addSubscriber($subscriber);
        }
    }

    /**
     * Handle routing bound events.
     *
     * @param object $event
     *
     * @internal
     */
    public function handleEvent($event, UserNotificationEventHandler $service)
    {
        // If our event is the wrong type, just do a null/void return
        if (!$event instanceof DeactivateUser) {
            return;
        }

        $entity = $event->getUserEntity();

        if ($entity) {
            $service->deactivated($event);
        }
    }
}
