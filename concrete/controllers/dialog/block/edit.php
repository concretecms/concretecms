<?php
namespace Concrete\Controller\Dialog\Block;

use Concrete\Controller\Backend\UserInterface\Block as BackendInterfaceBlockController;
use Concrete\Core\Block\Block;
use Concrete\Core\Block\Events\BlockEdit;
use Concrete\Core\Block\View\BlockView;
use BlockType;
use Area;
use Concrete\Core\Foundation\Queue\Queue;
use Concrete\Core\Page\Page;
use Concrete\Core\View\View;
use Events;

class Edit extends BackendInterfaceBlockController
{
    protected $viewPath = '/dialogs/block/edit';

    public function view()
    {
        $bv = new BlockView($this->block);
        if (isset($_REQUEST['arGridMaximumColumns'])) {
            $this->area->setAreaGridMaximumColumns(intval($_REQUEST['arGridMaximumColumns']));
        }
        if (isset($_REQUEST['arEnableGridContainer']) && $_REQUEST['arEnableGridContainer'] == 1) {
            $this->area->enableGridContainer();
        }
        $bv->addScopeItems(array('c' => $this->page, 'a' => $this->area, 'dialogController' => $this));
        $this->set('bv', $bv);
    }

    protected function validateBlock($b)
    {
        $bi = $b->getInstance();
        if ($b->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) {
            $_b = \Block::getByID($bi->getOriginalBlockID());
            $bi = $_b->getInstance(); // for validation
        }

        $e = $bi->validate($_POST);
        return $e;
    }

    /*
    public function submit_master()
    {
        if ($this->validateAction() && $this->canAccess() && $this->page->isMasterCollection()) {
            $b = $this->getBlockToEdit();
            $e = $this->validateBlock($b);
            $pr = $this->getEditResponse($b, $e);

            if (!is_object($e) || ($e instanceof \Concrete\Core\Error\ErrorList\ErrorList && !$e->has())) {

                $name = sprintf('update_defaults_%s', $b->getBlockID());
                $queue = Queue::get($name);

                if ($_POST['process']) {
                    $obj = new \stdClass();
                    $messages = $queue->receive(20);
                    foreach ($messages as $key => $p) {
                        $block = unserialize($p->body);
                        // Get a block object for the related block
                        $page = Page::getByID($block['cID'], $block['cvID']);
                        $approve = $page->getVersionObject()->isApproved();
                        $child = Block::getByID($block['bID'], $page, $block['arHandle']);
                        $nvc = $page->getVersionToModify();
                        $child->loadNewCollection($nvc);

                        // Update the block on the page.
                        if ($child->isAlias()) {
                            $nb = $child->duplicate($nvc);
                            $child->deleteBlock();
                            $child = $nb;
                        }

                        if ($approve) {
                            $nvc->getVersionObject()->approve();
                        }

                        $child->update($block['data']);
                        $queue->deleteMessage($p);

                    }
                    $obj->totalItems = $queue->count();
                    if ($queue->count() == 0) {
                        $queue->deleteQueue($name);
                    }
                    $obj->bID = $b->getBlockID();
                    $obj->aID = $this->area->getAreaID();
                    echo json_encode($obj);
                    $this->app->shutdown();
                } else {
                    $queue = $b->queueForDefaultsUpdate($_POST, $queue);
                }

                $totalItems = $queue->count();
                View::element('progress_bar', array('totalItems' => $totalItems, 'totalItemsSummary' => t2("%d block", "%d blocks", $totalItems)));
                $this->app->shutdown();
            }
        }
    }
    */

    public function submit()
    {
        if ($this->validateAction() && $this->canAccess()) {
            $b = $this->getBlockToEdit();
            $e = $this->validateBlock($b);
            $pr = $this->getEditResponse($b, $e);

            if (!is_object($e) || ($e instanceof \Concrete\Core\Error\ErrorList\ErrorList && !$e->has())) {
                // we can update the block that we're submitting
                $b->update($_POST);
                $event = new BlockEdit($b, $this->page);
                Events::dispatch('on_block_edit', $event);
            }

            $pr->outputJSON();
        }
    }

    protected function canAccess()
    {
        return $this->permissions->canEditBlock();
    }
}
