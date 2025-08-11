<?php
namespace Concrete\Controller\Dialog\Page\Bulk;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Command\Batch\Batch;
use Concrete\Core\Controller\Traits\DashboardBulkUpdaterTrait;
use Concrete\Core\Page\Command\DeletePageCommand;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;

class Delete extends BackendInterfaceController
{
    use DashboardBulkUpdaterTrait;
    protected $viewPath = '/dialogs/page/bulk/delete';

    protected function getObjectFromRequestId(string $id)
    {
        $page = Page::getByID($id, 'RECENT');
        if ($page && !$page->isError()) {
            return $page;
        }
        return null;
    }

    public function canPerformOperationOnObject($object): bool
    {
        $checker = new Checker($object);
        return $checker->canDeletePage();
    }

    public function view()
    {
        $this->populateItemsFromRequest();
        $this->set('form', $this->app->make('helper/form'));
        $this->set('dh', $this->app->make('helper/date'));
        $this->set('pages', $this->items);
    }

    public function submit()
    {
        if ($this->canAccess()) {
            $u = new \User();
            $uID = $u->getUserID();
            $pages = $this->items;
            $batch = Batch::create(t('Delete Pages'), function() use ($uID, $pages) {
                foreach ($pages as $page) {
                    yield new DeletePageCommand($page->getCollectionID(), $uID);
                }
            });
            return $this->dispatchBatch($batch);
        }
    }


}
