<?php

namespace Concrete\Core\User\Login;

use Carbon\Carbon;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\User\LoginAttempt;
use Concrete\Core\Entity\User\User;
use Concrete\Core\User\Event\DeactivateUser;
use Concrete\Core\User\User as LegacyUser;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Concrete\Core\Events\EventDispatcher;

/**
 * A service for tracking and acting upon login attempts
 * This service is NOT in charge of logging in users, it is meant to react to attempts
 *
 * @todo Replace this with a more general login service
 */
class LoginAttemptService
{

    /**
     * The entitymanager we're tracking attempts with
     *
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * The config repository that has configuration for this service
     *
     * @var \Concrete\Core\User\Login\Repository
     */
    protected $config;

    /**
     * The event dispatcher we use to send out events
     *
     * @var EventDispatcher
     */
    protected $director;

    /**
     * The currently waiting login attempts
     *
     * @var LoginAttempt[]
     */
    protected $batch = [];

    /**
     * A cache for known users
     *
     * @var array
     */
    protected $knownUsers = [];

    public function __construct(EntityManagerInterface $entityManager, Repository $config, EventDispatcher $director)
    {
        $this->entityManager = $entityManager;
        $this->config = $config;
        $this->director = $director;
    }

    /**
     * Track a login attempt for a user
     *
     * @param string $username
     * @param string $password
     *
     * @return $this
     */
    public function trackAttempt($username, $password)
    {
        if (!$this->config->get('concrete.user.deactivation.authentication_failure.enabled', false)) {
            return $this;
        }

        $user = $this->resolveUser($username);
        if (!$user) {
            return;
        }

        $attempt = new LoginAttempt();
        $attempt->setUtcDate(Carbon::now('UTC'));
        $attempt->setUserId($user->getUserID());
        $this->entityManager->persist($attempt);

        // Make sure we get flushed with the next batch
        $this->batch[] = $attempt;

        // Prune old attempts
        $this->pruneAttempts();

        return $this;
    }

    /**
     * Determine the amount of attempts remaining for a username
     *
     * @param $username
     * @return int
     */
    public function remainingAttempts($username)
    {
        $config = $this->config->get('concrete.user.deactivation.authentication_failure');

        $allowed = (int) array_get($config, 'amount', 0);
        $duration = (int) array_get($config, 'duration', 0);
        $after = Carbon::now('UTC')->subSeconds($duration);

        $user = $this->resolveUser($username);
        if (!$user) {
            return $allowed;
        }

        /** @var \Concrete\Core\Entity\User\LoginAttemptRepository $repository */
        $repository = $this->entityManager->getRepository(LoginAttempt::class);
        $attempts = $repository->after($after, $user, true);

        return max(0, $allowed - $attempts);
    }

    /**
     * Deactivate a given username
     *
     * @param $username
     */
    public function deactivate($username)
    {
        if (!$this->config->get('concrete.user.deactivation.authentication_failure.enabled', false)) {
            return;
        }

        $user = $this->resolveUser($username);
        if (!$user) {
            throw new InvalidArgumentException(t('Unable to find and deactivate given user'));
        }

        $event = DeactivateUser::create($user);
        $this->director->dispatch('on_before_user_deactivate', $event);

        // Here we could retrieve the user entity from the event and allow an event handler to change which user gets
        // deactivated, but we don't want to support that at the moment
        // $user = $event->getUserEntity();

        // Set the user to inactive
        $this->entityManager->transactional(function(EntityManagerInterface $localManager) use ($user) {
            $user = $localManager->merge($user);
            $user->setUserIsActive(false);
        });

        // Send out a `user_deactivate` event
        $this->director->dispatch('on_after_user_deactivate', $event);
    }

    /**
     * Prune old login attempts
     *
     * @param \DateTime|null $before The date to prune before. This MUST be in UTC, if not passed this value is derived
     *                               from config
     */
    public function pruneAttempts(DateTime $before = null)
    {
        if (!$before) {
            $duration = (int) $this->config->get('concrete.user.deactivation.authentication_failure.duration');
            $before = Carbon::now('UTC')->subSeconds($duration);
        }

        // Load our repository and get entries before our date
        $repository = $this->entityManager->getRepository(LoginAttempt::class);
        $results = $repository->before($before);

        // Loop through and remove those entries
        $batch = [];
        $max = 50;

        foreach ($results as $result) {
            $this->entityManager->remove($result);
            $batch[] = $result;

            // Handle the batch
            $batch = $this->manageBatch($batch, $max);
        }

        // Close off the batch
        $this->manageBatch($batch);
    }

    /**
     * Manage a batched ORM operation
     *
     * @param array $batch
     * @param int $max
     *
     * @return array
     */
    private function manageBatch(array $batch, $max = 0)
    {
        if (count($batch) >= $max) {
            $this->entityManager->flush();
            $batch = [];
        }

        return $batch;
    }

    /**
     * Resolve the user from username / user email
     *
     * @param $username
     * @return User|null
     */
    private function resolveUser($username)
    {
        if (isset($this->knownUsers[strtolower($username)])) {
            return $this->knownUsers[strtolower($username)];
        }

        $repository = $this->entityManager->getRepository(User::class);
        $user = $repository->findOneBy(['uName' => $username]);

        // If we couldn't find the user by username, let's try to get it by email
        if (!$user) {
            $user = $repository->findOneBy(['uEmail' => $username]);
        }

        // No user to actually track against.
        if (!$user) {
            return null;
        }

        /**
         * I don't know what detach is doing or why it's here, but it's causing an exception on UserSignup
         * not being a known entity. Much like drugs, `detach` is not the answer
         */
        //$this->entityManager->detach($user);
        $this->knownUsers[strtolower($username)] = $user;
        return $user;
    }

}
