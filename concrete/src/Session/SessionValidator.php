<?php

namespace Concrete\Core\Session;

use Carbon\Carbon;
use Concrete\Controller\SinglePage\Dashboard\System\Registration\AutomatedLogout;
use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Http\Request;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;
use Concrete\Core\Permission\IPService;
use Concrete\Core\User\PersistentAuthentication\CookieService;
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
    use LoggerAwareTrait;

    /** @var \Concrete\Core\Application\Application */
    private $app;

    /** @var \Concrete\Core\Config\Repository\Repository */
    private $config;

    /** @var \Concrete\Core\Http\Request */
    private $request;

    /** @var \Concrete\Core\Permission\IPService */
    private $ipService;

    public function __construct(Application $app, Repository $config, Request $request, IPService $ipService, LoggerInterface $logger = null)
    {
        $this->app = $app;
        $this->config = $config;
        $this->request = $request;
        $this->ipService = $ipService;
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
        $request_ip = (string) $this->ipService->getRequestIPAddress();

        $invalidate = false;

        $ip = $session->get('CLIENT_REMOTE_ADDR');
        $agent = $session->get('CLIENT_HTTP_USER_AGENT');
        $request_agent = $this->request->server->get('HTTP_USER_AGENT');

        // Validate against the current uOnlineCheck. This will determine if the user has been inactive for too long.
        if ($this->shouldValidateUserActivity($session)) {
            $threshold = $this->getUserActivityThreshold();
            if ((time() - $session->get('uOnlineCheck')) > $threshold) {
                $this->logger->notice(t('Session Invalidated. Session was inactive for more than %s seconds', $threshold));
                $invalidate = true;
            }
        }

        // Validate against the `valid_since` config item
        $validSinceTimestamp = (int) $this->config->get(AutomatedLogout::ITEM_SESSION_INVALIDATE);

        if ($validSinceTimestamp) {
            $validSince = Carbon::createFromTimestamp($validSinceTimestamp, 'utc');
            $created = Carbon::createFromTimestamp($session->getMetadataBag()->getCreated());

            if ($created->lessThan($validSince)) {
                $this->logger->notice('Session Invalidated. Session was created before "valid_since" setting.');
                $invalidate = true;
            }
        }

        // Validate the request IP
        if ($this->shouldCompareIP() && $ip && $ip != $request_ip) {
            if ($this->logger) {
                $this->logger->notice(
                    'Session Invalidated. Session IP "{session}" did not match provided IP "{client}".',
                    [
                        'session' => $ip,
                        'client' => $request_ip,
                    ]
                );
            }

            $invalidate = true;
        }

        // Validate the request user agent
        if ($this->shouldCompareAgent() && $agent && $agent != $request_agent) {
            if ($this->logger) {
                $this->logger->notice(
                    'Session Invalidated. Session user agent "{session}" did not match provided agent "{client}"',
                    [
                        'session' => $agent,
                        'client' => $request_agent,
                    ]
                );
            }

            $invalidate = true;
        }

        if ($invalidate) {
            $session->invalidate();
        } else {
            if (!$ip && $request_ip) {
                $session->set('CLIENT_REMOTE_ADDR', $request_ip);
            }

            if (!$agent && $request_agent) {
                $session->set('CLIENT_HTTP_USER_AGENT', $request_agent);
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
        return $this->config->get('concrete.security.session.invalidate_inactive_users.enabled') &&
            $session->has('uID') && $session->get('uID') > 0 && $session->has('uOnlineCheck') &&
            $session->get('uOnlineCheck') > 0;
    }

    /**
     * @return int
     */
    public function getUserActivityThreshold()
    {
        return $this->config->get('concrete.security.session.invalidate_inactive_users.time');
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
        return $this->config->get('concrete.security.session.invalidate_on_ip_mismatch', true);
    }

    /**
     * @return bool
     */
    private function shouldCompareAgent()
    {
        return $this->config->get('concrete.security.session.invalidate_on_user_agent_mismatch', true);
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
