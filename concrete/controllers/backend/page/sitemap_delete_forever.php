<?php
namespace Concrete\Controller\Backend\Page;

use Concrete\Core\Command\Batch\Batch;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Page\Command\DeletePageForeverCommand;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;

class SitemapDeleteForever extends AbstractController
{

    public function canAccess()
    {
        $checker = new Checker();
        return $checker->canEmptyTrash();
    }

    public function fillQueue()
    {
        if ($this->canAccess()) {
            $c = Page::getByID($_REQUEST['cID']);
            if (is_object($c) && !$c->isError()) {
                $cp = new \Permissions($c);
                if ($cp->canDeletePage()) {

                    $includeThisPage = true;
                    if ($c->getCollectionPath() == $this->app->make('config')->get('concrete.paths.trash')) {
                        // we're in the trash. we can't delete the trash. we're skipping over the trash node.
                        $includeThisPage = false;
                    }
                    $pages = $c->populateRecursivePages([], ['cID' => $c->getCollectionID()], $c->getCollectionParentID(), 0, $includeThisPage);
                    // business if the queue dies.
                    usort($pages, ['\Concrete\Core\Page\Page', 'queueForDeletionSort']);

                    foreach ($pages as $page) {
                        $ids[] = $page['cID'];
                    }

                    $batch = Batch::create(t('Delete Pages'), function() use ($pages) {
                        foreach ($pages as $page) {
                            yield new DeletePageForeverCommand($page['cID']);
                        }
                    });
                    return $this->dispatchBatch($batch);
                }
            }
        } else {
            throw new UserMessageException(t('Access Denied.'));
        }
    }
}
