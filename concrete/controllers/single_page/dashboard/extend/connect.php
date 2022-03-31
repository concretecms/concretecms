<?php
namespace Concrete\Controller\SinglePage\Dashboard\Extend;

use Concrete\Core\Marketplace\Marketplace;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Permission\Checker;

class Connect extends DashboardPageController
{

    public function view()
    {
        $config = $this->app->make('config');
        $this->set('permissions', new Checker());
        $this->set('marketplace', Marketplace::getInstance());
        $this->set('dbConfig', $this->app->make('config/database'));
        $this->set('config', $config);
        $this->set('projectPageURL', $config->get('concrete.urls.concrete_secure') . $config->get('concrete.urls.paths.marketplace.projects'));
    }

    public function do_connect()
    {
        if (!$this->token->validate('do_connect')) {
            $this->error->add($this->token->getErrorMessage());
        }
        $checker = new Checker();
        if (!$checker->canInstallPackages()) {
            $this->error->add(t('You do not have access to set community configuration values.'));
        }
        if (!$this->error->has()) {
            $config = $this->app->make('config/database');
            if ($this->request->request->has('disconnect')) {
                $config->save('concrete.marketplace.token', null);
                $config->save('concrete.marketplace.url_token', null);
                $this->flash('success', t('The site has been disconnected from the marketplace.'));
            } else {
                $config->save('concrete.marketplace.token', $this->request->request->get('csToken'));
                $config->save('concrete.marketplace.url_token', $this->request->request->get('csURLToken'));
                $this->flash('success', t('Marketplace configuration saved successfully.'));
            }
            $this->redirect('/dashboard/extend/connect');
        }
    }

}
