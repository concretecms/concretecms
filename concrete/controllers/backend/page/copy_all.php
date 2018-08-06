<?php
namespace Concrete\Controller\Backend\Page;

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Foundation\Queue\Batch\Processor;
use Concrete\Core\Foundation\Queue\QueueService;
use Concrete\Core\Foundation\Queue\Response\EnqueueItemsResponse;
use Concrete\Core\Page\Command\CopyPageBatchProcessFactory;
use Concrete\Core\Page\Command\CopyPageCommand;
use Concrete\Core\Page\Page;

class CopyAll extends AbstractController
{

    public function canAccess()
    {
        $dh = $this->app->make('helper/concrete/dashboard/sitemap');
        return $dh->canRead();
    }

    public function fillQueue()
    {
        $includeParent = true;
        if (isset($_REQUEST['copyChildrenOnly']) && $_REQUEST['copyChildrenOnly']) {
            $includeParent = false;
        }
        $isMultilingual = false;
        if (isset($_REQUEST['multilingual']) && $_REQUEST['multilingual']) {
            $isMultilingual = true;
        }
        if ($this->canAccess()) {
            if (isset($_REQUEST['origCID']) && strpos($_REQUEST['origCID'], ',') > -1) {
                $ocs = explode(',', $_REQUEST['origCID']);
                foreach ($ocs as $ocID) {
                    $oc = Page::getByID($ocID);
                    if (is_object($oc) && !$oc->isError()) {
                        $originalPages[] = $oc;
                    }
                }
            } else {
                $oc = isset($_REQUEST['origCID']) ? Page::getByID($_REQUEST['origCID']) : null;
                if (is_object($oc) && !$oc->isError()) {
                    $originalPages[] = $oc;
                }
            }

            $queue = $this->app->make(QueueService::class);
            $q = $queue->get('copy_page');

            $dc = isset($_REQUEST['destCID']) ? Page::getByID($_REQUEST['destCID']) : null;
            if (count($originalPages) > 0 && is_object($dc) && !$dc->isError()) {
                $u = new \User();
                if ($u->isSuperUser() && $oc->canMoveCopyTo($dc)) {
                    foreach ($originalPages as $oc) {
                        $pages = [];
                        $pages = $oc->populateRecursivePages($pages, ['cID' => $oc->getCollectionID()], $oc->getCollectionParentID(), 0, $includeParent);
                        // we want to order the pages by level, which should get us no funny
                        // business if the queue dies.
                        usort($pages, ['\Concrete\Core\Page\Page', 'queueForDuplicationSort']);

                        $ids = [];

                        foreach ($pages as $page) {
                            $ids[] = $page['cID'];
                        }


                        $factory = new CopyPageBatchProcessFactory($dc, $isMultilingual);
                        $processor = $this->app->make(Processor::class);
                        return $processor->process($factory, $ids);
                    }
                }
            }

            return new EnqueueItemsResponse($q);
        } else {
            throw new UserMessageException(t('Access Denied.'));
        }
    }
}
