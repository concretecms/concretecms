<?php
namespace Concrete\Controller\Dialog\Page\Bulk;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Messenger\Batch\BatchProcessor;
use Concrete\Core\Messenger\Batch\BatchProcessorResponseFactory;
use Concrete\Core\Page\Command\DeletePageCommand;
use Page;
use Permissions;

class Delete extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/page/bulk/delete';
    protected $pages;
    protected $canEdit = false;

    protected function canAccess()
    {
        $this->populatePages();

        return $this->canEdit;
    }

    protected function populatePages()
    {
        if (!isset($this->pages)) {
            if (is_array($_REQUEST['item'])) {
                foreach ($_REQUEST['item'] as $cID) {
                    $c = Page::getByID($cID);
                    if (is_object($c) && !$c->isError()) {
                        $this->pages[] = $c;
                    }
                }
            }
        }

        if (count($this->pages) > 0) {
            $this->canEdit = true;
            foreach ($this->pages as $c) {
                $cp = new Permissions($c);
                if (!$cp->canDeletePage()) {
                    $this->canEdit = false;
                }
            }
        } else {
            $this->canEdit = false;
        }

        return $this->canEdit;
    }

    public function view()
    {
        $this->populatePages();
        $this->set('form', $this->app->make('helper/form'));
        $this->set('dh', $this->app->make('helper/date'));
        $this->set('pages', $this->pages);
    }

    public function submit()
    {
        if ($this->canAccess()) {
            $u = new \User();
            $processor = $this->app->make(BatchProcessor::class);
            $uID = $u->getUserID();
            $pages = $this->pages;
            $batch = $processor->createBatch(function() use ($uID, $pages) {
                foreach ($pages as $page) {
                    yield new DeletePageCommand($page->getCollectionID(), $uID);
                }
            }, t('Delete Pages'));
            $batchProcess = $processor->dispatch($batch);
            $responseFactory = $this->app->make(BatchProcessorResponseFactory::class);
            return $responseFactory->createResponse($batchProcess);
        }
    }


}
