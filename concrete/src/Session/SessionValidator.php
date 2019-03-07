<?php
namespace Concrete\Core\Session;

use Carbon\Carbon;
use Concrete\Controller\SinglePage\Dashboard\System\Registration\AutomatedLogout;
use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Http\Request;
use Concrete\Core\Permission\IPService;
use Concrete\Core\Utility\IPAddress;
use Psr\Log\LoggerAwareInterface;
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
    /** @var \Concrete\Core\Application\Application */
    private $app;

    /** @var \Concrete\Core\Config\Repository\Repository */
    private $config;

    /** @var \Concrete\Core\Http\Request */
    private $request;

    /** @var \Concrete\Core\Permission\IPService */
    private $ipService;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    public function __construct(Application $app, Repository $config, Request $request, IPService $ipService, LoggerInterface $logger = null)
    {
        $this->app = $app;
        $this->config = $config;
        $this->request = $request;
        $this->ipService = $ipService;
        $this->logger = $logger;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Session\Session $session
     * @return bool True if the session invalidated, false otherwise.
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
                $this->logger->notice('Session Invalidated. Session IP "{session}" did not match provided IP "{client}".',
                    array(
                        'session' => $ip,
                        'client' => $request_ip, ));
            }

            $invalidate = true;
        }

        // Validate the request user agent
        if ($this->shouldCompareAgent() && $agent && $agent != $request_agent) {
            if ($this->logger) {
                $this->logger->notice('Session Invalidated. Session user agent "{session}" did not match provided agent "{client}"',
                    array(
                        'session' => $agent,
                        'client' => $request_agent, ));
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

    public function shouldValidateUserActivity(SymfonySession $session)
    {
        return $this->config->get('concrete.security.session.invalidate_inactive_users.enabled') &&
            $session->has('uID') && $session->get('uID') > 0 && $session->has('uOnlineCheck') &&
            $session->get('uOnlineCheck') > 0;
    }

    public function getUserActivityThreshold()
    {
        return $this->config->get('concrete.security.session.invalidate_inactive_users.time');
    }

    public function hasActiveSession()
    {
        $cookie = $this->app['cookie'];
        return $cookie->has($this->config->get('concrete.session.name')) || $cookie->has('ccmAuthUserHash');
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
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
