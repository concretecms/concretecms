<?php
namespace Concrete\Controller\SinglePage\Dashboard\Extend;

use Concrete\Core\Marketplace\Exception\InvalidConnectResponseException;
use Concrete\Core\Marketplace\Exception\UnableToConnectException;
use Concrete\Core\Marketplace\Marketplace;
use Concrete\Core\Marketplace\PackageRepositoryInterface;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Permission\Checker;

class Connect extends DashboardPageController
{

    public function view()
    {
        if ($errors = $this->app->make('session')->getFlashBag()->get('errors')) {
            foreach ($errors as $error) {
                $this->error->addError($error);
            }
        }

        $config = $this->app->make('config');
        $this->set('permissions', new Checker());
        $this->set('marketplace', $this->app->make(PackageRepositoryInterface::class));
        $this->set('dbConfig', $this->app->make('config/database'));
        $this->set('config', $config);
        $this->set('projectPageURL', $config->get('concrete.urls.concrete_secure') . $config->get('concrete.urls.paths.marketplace.projects'));
    }

    public function do_connect()
    {
        $this->view();
        if (!$this->token->validate('do_connect')) {
            $this->error->add($this->token->getErrorMessage());
            return;
        }

        $repository = $this->app->make(PackageRepositoryInterface::class);
        $current = $repository->getConnection();

        if ($current && $repository->validate($current)) {
            $this->error->add(t('This site is already connected.'));
            return;
        }

        $checker = new Checker();
        if (!$checker->canInstallPackages()) {
            $this->error->add(t('You do not have access to set community configuration values.'));
            return;
        }

        try {
            $repository->connect();
        } catch (UnableToConnectException $e) {
            $this->error->add(t('Unable to connect: ' . $e->getMessage()));
        } catch (InvalidConnectResponseException $e) {
            $this->error->add(t('Connection failed, try again later.'));
        }

        if ($this->error->has()) {
            $this->app->make('session')->getFlashBag()->set('errors', $this->error->getList());
        }

        return $this->buildRedirect('/dashboard/extend/connect');
    }

}
