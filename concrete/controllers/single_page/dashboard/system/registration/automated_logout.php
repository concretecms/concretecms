<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Registration;

use Carbon\Carbon;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Url\Resolver\Manager\ResolverManager;

class AutomatedLogout extends DashboardPageController
{

    const ITEM_IP = 'concrete.security.session.invalidate_on_ip_mismatch';
    const ITEM_USER_AGENT = 'concrete.security.session.invalidate_on_user_agent_mismatch';
    const ITEM_SESSION_INVALIDATE = 'concrete.session.valid_since';
    const ITEM_INVALIDATE_INACTIVE_USERS = 'concrete.security.session.invalidate_inactive_users.enabled';
    const ITEM_INVALIDATE_INACTIVE_USERS_TIME = 'concrete.security.session.invalidate_inactive_users.time';

    /**
     * The response factory we use to generate responses
     *
     * @var ResponseFactory
     */
    protected $factory;

    /**
     * The config repository we save to
     *
     * @var \Concrete\Core\Config\Repository\Repository
     */
    protected $config;

    /**
     * The URL manager that tells us how to get to the page we're looking for
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
     * Main view function for the controller. This method is called when no other action matches the request
     *
     * @return \Concrete\Core\Http\Response|void
     */
    public function view()
    {

        $this->set('trustedProxyUrl', $this->urls->resolve(['/dashboard/system/permissions/trusted_proxies']));
        $this->set('invalidateOnIPMismatch', $this->config->get(self::ITEM_IP));
        $this->set('invalidateOnUserAgentMismatch', $this->config->get(self::ITEM_USER_AGENT));
        $this->set('invalidateInactiveUsers', $this->config->get(self::ITEM_INVALIDATE_INACTIVE_USERS));
        $this->set('inactiveTime', $this->config->get(self::ITEM_INVALIDATE_INACTIVE_USERS_TIME));

        $this->set('saveAction', $this->action('save'));
        $this->set('invalidateAction', $this->action('invalidate_sessions', $this->token->generate('invalidate_sessions')));
        $this->set('confirmInvalidateString', t('invalidate'));
    }

    /**
     * An action for saving the Session Security form
     * This method will manage saving settings and redirecting to the appropriate results page
     *
     * @return \Concrete\Core\Http\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function save()
    {
        if (!$this->token->validate('save_automated_logout')) {
            $this->error->add($this->token->getErrorMessage());

            $this->flash('error', $this->error);

            // Redirect away from this save endpoint
            return $this->factory->redirect($this->action());
        }

        $post = $this->request->request;
        // Save the posted settings
        $this->config->save(self::ITEM_IP, filter_var($post->get('invalidateOnIPMismatch'), FILTER_VALIDATE_BOOLEAN));
        $this->config->save(self::ITEM_USER_AGENT, filter_var($post->get('invalidateOnUserAgentMismatch'), FILTER_VALIDATE_BOOLEAN));
        $this->config->save(self::ITEM_INVALIDATE_INACTIVE_USERS, filter_var($post->get('invalidateInactiveUsers'), FILTER_VALIDATE_BOOLEAN));
        $this->config->save(self::ITEM_INVALIDATE_INACTIVE_USERS_TIME, filter_var($post->get('inactiveTime'), FILTER_VALIDATE_INT));

        $this->flash('message', t('Successfully saved Session Security settings'));

        // Redirect away from this save endpoint
        return $this->factory->redirect($this->action());
    }

    /**
     * An action for invalidating all active sessions
     *
     * @param string $token The CSRF token used to validate this request
     *
     * @return \Concrete\Core\Http\Response|void
     */
    public function invalidate_sessions($token = '')
    {
        if (!$this->token->validate('invalidate_sessions', $token)) {
            $this->error->add($this->token->getErrorMessage());

            $this->flash('error', $this->error);

            // Redirect away from this save endpoint
            return $this->factory->redirect($this->action());
        }

        // Invalidate all sessions
        $this->invalidateSessions();

        // Set a message for the login page
        $this->flash('error', t('All sessions have been invalidated. You must log back in to continue.'));

        // Redirect to the login page
        return $this->factory->redirect($this->urls->resolve(['/login']));
    }

    /**
     * Invalidate all user sessions
     *
     * @todo move this to a proper service class that manages common user functions
     *       like registration `$user->registration->register()` and locating the logged in user `$user->loggedIn()->getID()`
     */
    protected function invalidateSessions()
    {
        // Save the configuration that invalidates sessions
        $this->config->save(self::ITEM_SESSION_INVALIDATE, Carbon::now('utc')->getTimestamp());

        // Invalidate the current session explicitly so that we get back to the login page with a nice error message
        $this->app->make('session')->invalidate();
    }
}
