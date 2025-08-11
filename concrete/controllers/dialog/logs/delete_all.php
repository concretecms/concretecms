<?php

namespace Concrete\Controller\Dialog\Logs;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\EditResponse;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

class DeleteAll extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/logs/delete_all';
    protected $controllerActionPath = '/ccm/system/dialogs/logs/delete_all';

    protected function canAccess()
    {
        $key = Key::getByHandle("delete_log_entries");
        return $key->validate();
    }

    public function view()
    {
    }

    public function submit()
    {
        /** @var ResponseFactory $responseFactory */
        $responseFactory = $this->app->make(ResponseFactory::class);
        /** @var EditResponse $editResponse */
        $editResponse = new EditResponse();

        if ($this->validateAction()) {
            /** @var Connection $db */
            $db = $this->app->make(Connection::class);

            /** @noinspection PhpUnhandledExceptionInspection */
            /** @noinspection SqlDialectInspection */
            /** @noinspection SqlNoDataSourceInspection */
            $db->executeQuery("TRUNCATE TABLE Logs");

            $this->flash('success', t('Log cleared successfully.'));
            $editResponse->setRedirectURL((string) $this->app->make(ResolverManagerInterface::class)->resolve(['/dashboard/reports/logs']));
        } else {
            $editResponse->setMessage(t('Access denied'));
        }

        return $responseFactory->json($editResponse->getJSONObject());
    }
}
