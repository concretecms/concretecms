<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Registration;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Page;

class Postlogin extends DashboardPageController
{
    public function view($message = null)
    {
        $config = $this->app->make('config');
        $this->set('pageSelector', $this->app->make('helper/form/page_selector'));
        $loginRedirect = $config->get('concrete.misc.login_redirect');
        if (!in_array($loginRedirect, $this->getAvailableLoginRedirects(), true)) {
            $loginRedirect = 'HOMEPAGE';
        }
        $this->set('loginRedirect', $loginRedirect);
        $this->set('loginRedirectCID', ((int) $config->get('concrete.misc.login_redirect_cid')) ?: null);
    }

    public function update_login_redirect()
    {
        $config = $this->app->make('config');
        $post = $this->request->request;
        if (!$this->token->validate('update_login_redirect')) {
            $this->error->add($this->token->getErrorMessage());
        }
        $loginRedirect = $post->get('login_redirect');
        if (!in_array($loginRedirect, $this->getAvailableLoginRedirects(), true)) {
            $this->error->add(t('Please specify the login destination.'));
        }
        if ($loginRedirect === 'CUSTOM') {
            $loginRedirectCID = (int) $post->get('login_redirect_cid');
            $loginRedirectPage = $loginRedirectCID === 0 ? null : Page::getByID($loginRedirectCID);
            if ($loginRedirectPage === null || $loginRedirectPage->isError()) {
                $this->error->add(t('Please specify the custom login destination.'));
            }
        }
        if ($this->error->has()) {
            return $this->view();
        }
        if (!in_array($loginRedirect, $this->getAvailableLoginRedirects(), true)) {
            $loginRedirect = 'HOMEPAGE';
        }
        $config->save('concrete.misc.login_redirect', $loginRedirect);
        if ($loginRedirect === 'CUSTOM') {
            $config->save('concrete.misc.login_redirect_cid', $loginRedirectCID);
        }

        $this->flash('success', t('Login redirection saved.'));

        return $this->buildRedirect($this->action());
    }

    /**
     * @return string[]
     */
    protected function getAvailableLoginRedirects(): array
    {
        return [
            'HOMEPAGE',
            'DESKTOP',
            'CUSTOM',
        ];
    }
}
