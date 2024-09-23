<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Basics;

use Concrete\Core\Marketplace\Exception\InvalidConnectResponseException;
use Concrete\Core\Marketplace\Exception\UnableToConnectException;
use Concrete\Core\Marketplace\PackageRepositoryInterface;
use Concrete\Core\Marketplace\PurchaseConnectionCoordinator;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Permission\Checker;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class Marketplace extends DashboardPageController
{
    public function view(): void
    {
        if ($errors = $this->app->make('session')->getFlashBag()->get('errors')) {
            foreach ($errors as $error) {
                $this->error->addError($error);
            }
        }

        $marketplace = $this->app->make(PackageRepositoryInterface::class);
        $connection = $marketplace->getConnection();

        $config = $this->app->make('config');
        $this->set('permissions', new Checker());
        $this->set('marketplace', $marketplace);
        $this->set('dbConfig', $this->app->make('config/database'));
        $this->set('config', $config);
        $this->set('connection', $connection);
        if ($connection) {
            $projectPageUrl = $config->get('concrete.urls.marketplace')
                . $config->get('concrete.urls.paths.marketplace.projects')
                . '/' . $connection->getPublic();

            $coordinator = $this->app->make(PurchaseConnectionCoordinator::class);
            $this->set(
                'launchProjectPageUrl',
                $coordinator->createPurchaseConnectionUrl(
                    $connection,
                    $projectPageUrl,
                    \URL::to('/dashboard/system/basics/marketplace')
                )
            );
        }
    }

    public function update_connection_settings(): ?RedirectResponse
    {
        if (!$this->token->validate('update_connection_settings')) {
            $this->error->add($this->token->getErrorMessage());
        }
        $checker = new Checker();
        if (!$checker->canInstallPackages()) {
            $this->error->add(t('You do not have access to set community configuration values.'));
        }
        if (!$this->error->has()) {
            $dbConfig = $this->app->make('config/database');
            $dbConfig->save('concrete.marketplace.key.public', $this->request->request->get('publicKey'));
            $dbConfig->save('concrete.marketplace.key.private', $this->request->request->get('privateKey'));
            return $this->buildRedirect($this->action('view'));
        }

        $this->view();
        return null;
    }

    public function do_connect(): ?RedirectResponse
    {
        $this->view();
        if (!$this->token->validate('do_connect')) {
            $this->error->add($this->token->getErrorMessage());
            return null;
        }

        $repository = $this->app->make(PackageRepositoryInterface::class);
        $current = $repository->getConnection();

        if ($current && $repository->validate($current)) {
            $this->error->add(t('This site is already connected.'));
            return null;
        }

        $checker = new Checker();
        if (!$checker->canInstallPackages()) {
            $this->error->add(t('You do not have access to set community configuration values.'));
            return null;
        }

        try {
            if ($this->request->request->get('connect') === 'connect_url') {
                // The site URL does not match what we have in the record, so we need to add this URL to the
                // connection.
                $connection = $repository->getConnection();
                $repository->registerUrl($connection);
            } else {
                $repository->connect();
            }
        } catch (UnableToConnectException $e) {
            $this->error->add(t('Unable to connect: ' . $e->getMessage()));
        } catch (InvalidConnectResponseException $e) {
            $this->error->add(t('Connection failed, try again later.'));
        }

        if ($this->error->has()) {
            $this->app->make('session')->getFlashBag()->set('errors', $this->error->getList());
        }

        return $this->buildRedirect('/dashboard/system/basics/marketplace');
    }

}
