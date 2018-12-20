<?php
namespace Concrete\Core\User;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\User\Event\DeactivateUser;
use Concrete\Core\User\Notification\UserNotificationEventHandler;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
        $this->app->bindShared('user/status', function() use ($app) {
            return $app->make('Concrete\Core\User\StatusService');
        });
        $this->app->bind('Concrete\Core\User\RegistrationServiceInterface', function() use ($app) {
            return $app->make('user/registration');
        });
        $this->app->bind('Concrete\Core\User\StatusServiceInterface', function() use ($app) {
            return $app->make('user/status');
        });
        $this->app->bind('Concrete\Core\User\Avatar\AvatarServiceInterface', function() use ($app) {
            return $app->make('user/avatar');
        });

        // Handle binding events
        if ($this->app->resolved(EventDispatcher::class)) {
            $this->bindEvents($this->app->make(EventDispatcher::class));
        } else {
            $this->app->extend(EventDispatcher::class, function(EventDispatcher $director) {
                $this->bindEvents($director);
                return $director;
            });
        }

        $this->app->singleton(User::class);
    }

    protected function bindEvents(EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addListener('on_after_user_deactivate', function($e) {
            $this->app->call([$this, 'handleEvent'], ['event' => $e]);
        });
    }

    /**
     * Handle routing bound events
     *
     * @param \Symfony\Component\EventDispatcher\Event $event
     * @param \Concrete\Core\User\Notification\UserNotificationEventHandler $service
     */
    public function handleEvent(Event $event, UserNotificationEventHandler $service)
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
