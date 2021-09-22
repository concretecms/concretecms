<?php

namespace Concrete\Controller\Dialog\Logs\Bulk;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\EditResponse;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\User;

class Delete extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/logs/bulk/delete';
    protected $pages;
    protected $canEdit = false;

    protected function canAccess()
    {
        $key = Key::getByHandle("delete_log_entries");
        return $key->validate();
    }

    public function view()
    {
        /** @var Request $request */
        $request = $this->app->make(Request::class);
        $logItems = (array)$request->query->get("item", []);
        $this->set('logItems', $logItems);
    }

    public function submit()
    {
        if ($this->canAccess()) {
            /** @var Request $request */
            $request = $this->app->make(Request::class);
            /** @var Connection $db */
            $db = $this->app->make(Connection::class);
            /** @var ResponseFactory $responseFactory */
            $responseFactory = $this->app->make(ResponseFactory::class);
            $logItems = (array)$request->request->get("logItem", []);

            foreach ($logItems as $logItem) {
                /** @noinspection PhpUnhandledExceptionInspection */
                /** @noinspection SqlDialectInspection */
                /** @noinspection SqlNoDataSourceInspection */
                $db->executeQuery("DELETE FROM Logs WHERE logID = ?", [$logItem]);
            }

            $this->flash('success', t('Log entries successfully deleted.')); // It will be displayed on page reload

            $editResponse = new EditResponse();
            return $responseFactory->json($editResponse->getJSONObject());
        }
    }


}
