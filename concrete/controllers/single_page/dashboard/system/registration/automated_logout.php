<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Registration;

use Carbon\Carbon;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Url\Resolver\Manager\ResolverManager;
use Concrete\Core\Utility\Service\Validation\Numbers;

class AutomatedLogout extends DashboardPageController
{
    public const ITEM_IP = 'concrete.security.session.invalidate_on_ip_mismatch';

    public const ITEM_USER_AGENT = 'concrete.security.session.invalidate_on_user_agent_mismatch';

    public const ITEM_SESSION_INVALIDATE = 'concrete.session.valid_since';

    public const ITEM_INVALIDATE_INACTIVE_USERS = 'concrete.security.session.invalidate_inactive_users.enabled';

    public const ITEM_INVALIDATE_INACTIVE_USERS_TIME = 'concrete.security.session.invalidate_inactive_users.time';

    /**
     * The response factory we use to generate responses.
     *
     * @var \Concrete\Core\Config\Repository\Repository
     */
    protected $factory;

    /**
     * The config repository we save to.
     *
     * @var \Concrete\Core\Config\Repository\Repository
     */
    protected $config;

    /**
     * The URL manager that tells us how to get to the page we're looking for.
     *
     * @var \Concrete\Core\Url\Resolver\Manager\ResolverManager
     */
    protected $urls;

    public function __construct(Page $c, ResponseFactory $factory, Repository $config, ResolverManager $urls)
    {
        parent::__construct($c);
        $this->factory = $factory;
        $this->config = $config;
        $this->urls = $urls;
    }

    /**
     * Main view function for the controller. This method is called when no other action matches the request.
     *
     * @return \Concrete\Core\Http\Response|void
     */
    public function view()
    {
        $this->set('trustedProxyUrl', $this->urls->resolve(['/dashboard/system/permissions/trusted_proxies']));
        $this->set('invalidateOnIPMismatch', (bool) $this->config->get(static::ITEM_IP));
        $this->set('invalidateOnUserAgentMismatch', (bool) $this->config->get(static::ITEM_USER_AGENT));
        $this->set('invalidateInactiveUsers', (bool) $this->config->get(static::ITEM_INVALIDATE_INACTIVE_USERS));
        $this->set('inactiveTime', ((int) $this->config->get(static::ITEM_INVALIDATE_INACTIVE_USERS_TIME)) ?: null);
        $this->set('confirmInvalidateString', $this->getConfirmInvalidateString());
    }

    /**
     * An action for saving the Session Security form
     * This method will manage saving settings and redirecting to the appropriate results page.
     *
     * @return \Concrete\Core\Http\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function save()
    {
        $post = $this->request->request;
        if (!$this->token->validate('save_automated_logout')) {
            $this->error->add($this->token->getErrorMessage());
        }

        $invalidateInactiveUsers = (bool) $post->get('invalidateInactiveUsers');
        if ($invalidateInactiveUsers) {
            $inactiveTime = $post->get('inactiveTime');
            if ($this->app->make(Numbers::class)->integer($inactiveTime, 15)) {
                $inactiveTime = (int) $inactiveTime;
            } else {
                $this->error->add(t('Please specify the inactive timeout.'));
            }
        }
        if ($this->error->has()) {
            return $this->view();
        }

        // Save the posted settings
        $this->config->save(static::ITEM_IP, (bool) $post->get('invalidateOnIPMismatch'));
        $this->config->save(static::ITEM_USER_AGENT, (bool) $post->get('invalidateOnUserAgentMismatch'));
        $this->config->save(static::ITEM_INVALIDATE_INACTIVE_USERS, $invalidateInactiveUsers);
        if ($invalidateInactiveUsers) {
            $this->config->save(static::ITEM_INVALIDATE_INACTIVE_USERS_TIME, $inactiveTime);
        }

        $this->flash('message', t('Successfully saved Session Security settings'));

        return $this->buildRedirect($this->action());
    }

    /**
     * An action for invalidating all active sessions.
     *
     * @param string $token The CSRF token used to validate this request
     *
     * @return \Concrete\Core\Http\Response|void
     */
    public function invalidate_sessions($token = '')
    {
        if (!$this->token->validate('invalidate_sessions', $token)) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (mb_strtolower($this->request->request->get('confirmation', '')) !== mb_strtolower($this->getConfirmInvalidateString())) {
            $this->error->add(t('Please confirm you really want to invalidate all sessions.'));
        }

        if ($this->error->has()) {
            return $this->view();
        }

        // Invalidate all sessions
        $this->invalidateSessions();

        // Set a message for the login page
        $this->flash('error', t('All sessions have been invalidated. You must log back in to continue.'));

        // Redirect to the login page
        return $this->factory->redirect($this->urls->resolve(['/login']));
    }

    /**
     * Invalidate all user sessions.
     *
     * @todo move this to a proper service class that manages common user functions
     *       like registration `$user->registration->register()` and locating the logged in user `$user->loggedIn()->getID()`
     */
    protected function invalidateSessions()
    {
        // Save the configuration that invalidates sessions
        $this->config->save(static::ITEM_SESSION_INVALIDATE, Carbon::now('utc')->getTimestamp());

        // Invalidate the current session explicitly so that we get back to the login page with a nice error message
        $this->app->make('session')->invalidate();
    }

    protected function getConfirmInvalidateString(): string
    {
        return t('invalidate');
    }
}
