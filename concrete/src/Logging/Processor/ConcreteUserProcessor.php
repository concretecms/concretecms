<?php

namespace Concrete\Core\Logging\Processor;

use Concrete\Core\Application\Application;
use Concrete\Core\User\User;

/**
 * A processor for adding the Concrete user into the extra log info
 */
class ConcreteUserProcessor
{

    /**
     * @var Application
     */
    protected $app;

    /**
     * The cached user instance
     *
     * @var User|null
     */
    protected $user;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Invoke this processor
     *
     * @param array $record The given monolog record
     *
     * @return array The modified record
     */
    public function __invoke(array $record)
    {
        $user = $this->getLoggedInUser();

        if ($user && $user->isRegistered()) {
            $record['extra']['user'] = [$user->getUserID(), $user->getUserName()];
        }

        return $record;
    }

    /**
     * Resolve a user intance from the IOC container and cache it
     *
     * @return User|mixed
     */
    protected function getLoggedInUser()
    {
        if (!$this->user) {
            $this->user = $this->app->make(User::class);
        }

        return $this->user;
    }

}


