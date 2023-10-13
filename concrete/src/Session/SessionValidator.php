<?php

namespace Concrete\Core\Session;

use Carbon\Carbon;
use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\User\User as UserEntity;
use Concrete\Core\Http\Request;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;
use Concrete\Core\User\PersistentAuthentication\CookieService;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManagerInterface;
use IPLib\Address\AddressInterface;
use IPLib\Factory;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\Session as SymfonySession;

/**
 * Class SessionValidator
 * Base concrete5 session validator, validates the IP and the agent across requests.
 *
 * \@package Concrete\Core\Session
 */
class SessionValidator implements SessionValidatorInterface, LoggerAwareInterface
{
    public const CONFIGKEY_IP_MISMATCH = 'concrete.security.session.invalidate_on_ip_mismatch';

    public const CONFIGKEY_IP_MISMATCH_ALLOWLIST = 'concrete.security.session.ignored_ip_mismatches';

    public const CONFIGKEY_ENABLE_USERSPECIFIC_IP_MISMATCH_ALLOWLIST = 'concrete.security.session.enable_user_specific_ignored_ip_mismatches';
    
    public const CONFIGKEY_USERAGENT_MISMATCH = 'concrete.security.session.invalidate_on_user_agent_mismatch';

    public const CONFIGKEY_SESSION_INVALIDATE = 'concrete.session.valid_since';

    public const CONFIGKEY_INVALIDATE_INACTIVE_USERS = 'concrete.security.session.invalidate_inactive_users.enabled';

    public const CONFIGKEY_INVALIDATE_INACTIVE_USERS_TIME = 'concrete.security.session.invalidate_inactive_users.time';

    use LoggerAwareTrait;

    /** @var \Concrete\Core\Application\Application */
    private $app;

    /** @var \Concrete\Core\Config\Repository\Repository */
    private $config;

    /** @var \Concrete\Core\Http\Request */
    private $request;

    public function __construct(Application $app, Repository $config, Request $request, LoggerInterface $logger = null)
    {
        $this->app = $app;
        $this->config = $config;
        $this->request = $request;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Logging\LoggerAwareInterface::getLoggerChannel()
     */
    public function getLoggerChannel()
    {
        return Channels::CHANNEL_AUTHENTICATION;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Session\Session $session
     *
     * @return bool true if the session invalidated, false otherwise
     */
    public function handleSessionValidation(SymfonySession $session)
    {
        $invalidate = false;
        $requestIP = $this->app->make(AddressInterface::class);
        $requestAgent = $this->request->server->get('HTTP_USER_AGENT');
        $previousIP = Factory::parseAddressString($session->get('CLIENT_REMOTE_ADDR'));
        $previousAgent = $session->get('CLIENT_HTTP_USER_AGENT');

        // Validate against the current uOnlineCheck. This will determine if the user has been inactive for too long.
        if ($this->shouldValidateUserActivity($session)) {
            $threshold = $this->getUserActivityThreshold();
            if ((time() - $session->get('uOnlineCheck')) > $threshold) {
                $this->logger->notice(t('Session Invalidated. Session was inactive for more than %s seconds', $threshold));
                $invalidate = true;
            }
        }

        // Validate against the `valid_since` config item
        $validSinceTimestamp = (int) $this->config->get(static::CONFIGKEY_SESSION_INVALIDATE);
        if ($validSinceTimestamp) {
            $validSince = Carbon::createFromTimestamp($validSinceTimestamp, 'utc');
            $created = Carbon::createFromTimestamp($session->getMetadataBag()->getCreated());
            if ($created->lessThan($validSince)) {
                $this->logger->notice('Session Invalidated. Session was created before "valid_since" setting.');
                $invalidate = true;
            }
        }

        // Validate the request IP
        if ($this->shouldCompareIP() && $this->considerIPChanged($requestIP, $previousIP)) {
            if ($this->logger) {
                $this->logger->notice(
                    'Session Invalidated. Session IP "{session}" did not match provided IP "{client}".',
                    [
                        'session' => $previousIP,
                        'client' => (string) $requestIP,
                    ]
                );
            }
            $invalidate = true;
        }

        // Validate the request user agent
        if ($this->shouldCompareAgent() && $previousAgent && $previousAgent != $requestAgent) {
            if ($this->logger) {
                $this->logger->notice(
                    'Session Invalidated. Session user agent "{session}" did not match provided agent "{client}"',
                    [
                        'session' => $previousAgent,
                        'client' => $requestAgent,
                    ]
                );
            }
            $invalidate = true;
        }

        if ($invalidate) {
            $session->invalidate();
        } else {
            $session->set('CLIENT_REMOTE_ADDR', (string) $requestIP);
            if (!$previousAgent && $requestAgent) {
                $session->set('CLIENT_HTTP_USER_AGENT', $requestAgent);
            }
        }

        return $invalidate;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Session\Session $session
     *
     * @return bool
     */
    public function shouldValidateUserActivity(SymfonySession $session)
    {
        return $this->config->get(static::CONFIGKEY_INVALIDATE_INACTIVE_USERS) &&
            $session->has('uID') && $session->get('uID') > 0 && $session->has('uOnlineCheck') &&
            $session->get('uOnlineCheck') > 0;
    }

    /**
     * @return int
     */
    public function getUserActivityThreshold()
    {
        return $this->config->get(static::CONFIGKEY_INVALIDATE_INACTIVE_USERS_TIME);
    }

    /**
     * Check if there is an active session.
     *
     * @return bool
     */
    public function hasActiveSession()
    {
        if ($this->app['cookie']->has($this->config->get('concrete.session.name'))) {
            return true;
        }
        if ($this->app->make(CookieService::class)->getCookie() !== null) {
            return true;
        }

        return false;
    }

    /**
     * Get the current session (if it exists).
     *
     * @param bool $start set to true to initialize the current session if it's not already started
     *
     * @return \Symfony\Component\HttpFoundation\Session\Session|null Returns NULL if $start is falsy and the session is not already started
     */
    public function getActiveSession($start = false)
    {
        if ($start || $this->hasActiveSession()) {
            return $this->app->make('session');
        }

        return null;
    }

    /**
     * @return bool
     */
    private function shouldCompareIP()
    {
        return $this->config->get(static::CONFIGKEY_IP_MISMATCH, true);
    }

    private function considerIPChanged(AddressInterface $currentIP, ?AddressInterface $previousIP): bool
    {
        if ($previousIP === null) {
            return false;
        }
        if ((string) $currentIP === (string) $previousIP) {
            return false;
        }
        $rangeStrings = (array) $this->config->get(self::CONFIGKEY_IP_MISMATCH_ALLOWLIST);
        if ($this->config->get(static::CONFIGKEY_ENABLE_USERSPECIFIC_IP_MISMATCH_ALLOWLIST)) {
            $user = $this->app->make(User::class);
            if ($user->isRegistered()) {
                $em = $this->app->make(EntityManagerInterface::class);
                $userEntity = $em->find(UserEntity::class, $user->getUserID());
                if ($userEntity !== null) {
                    $rangeStrings = array_unique(array_merge($rangeStrings, $userEntity->getIgnoredIPMismatches()));
                }
            }
        }
        $currentIPFound = false;
        $previousIPFound = false;
        foreach ($rangeStrings as $rangeString) {
            $rangeObject = Factory::parseRangeString($rangeString);
            if ($rangeObject === null) {
                continue;
            }
            $currentIPFound = $currentIPFound || $rangeObject->contains($currentIP);
            $previousIPFound = $previousIPFound || $rangeObject->contains($previousIP);
            if ($currentIPFound && $previousIPFound) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    private function shouldCompareAgent()
    {
        return $this->config->get(static::CONFIGKEY_USERAGENT_MISMATCH, true);
    }

    /**
     * Sets a logger instance on the object.
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
