<?php
namespace Concrete\Controller\Backend\Page;

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Foundation\Queue\Batch\Processor;
use Concrete\Core\Foundation\Queue\QueueService;
use Concrete\Core\Foundation\Queue\Response\EnqueueItemsResponse;
use Concrete\Core\Page\Command\CopyPageCommand;
use Concrete\Core\Page\Command\DeletePageForeverBatchProcessFactory;
use Concrete\Core\Page\Command\DeletePageForeverCommand;
use Concrete\Core\Page\Page;

class SitemapDeleteForever extends AbstractController
{

    public function canAccess()
    {
        $dh = $this->app->make('helper/concrete/dashboard/sitemap');
        return $dh->canRead();
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

                    $ids = [];

                    foreach ($pages as $page) {
                        $ids[] = $page['cID'];
                    }

                    $factory = new DeletePageForeverBatchProcessFactory();
                    $processor = $this->app->make(Processor::class);
                    return $processor->process($factory, $ids);

                }
            }

            $q = $this->app->make(QueueService::class)->get('delete_page_forever');
            return new EnqueueItemsResponse($q);
        } else {
            throw new UserMessageException(t('Access Denied.'));
        }
    }
}
