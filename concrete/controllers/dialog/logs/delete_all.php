<?php

namespace Concrete\Controller\Dialog\Logs;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\EditResponse;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker as Permissions;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\User;


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

        if ($this->canAccess()) {
            /** @var Connection $db */
            $db = $this->app->make(Connection::class);

            /** @noinspection PhpUnhandledExceptionInspection */
            /** @noinspection SqlDialectInspection */
            /** @noinspection SqlNoDataSourceInspection */
            $db->executeQuery("TRUNCATE TABLE Logs");

            $editResponse->setMessage(t('Log cleared successfully.'));
        } else {
            $editResponse->setMessage(t('Access denied'));
        }

        return $responseFactory->json($editResponse->getJSONObject());
    }
}
