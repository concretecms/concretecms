<?php
namespace Concrete\Controller\Dialog\Block;

use Concrete\Controller\Backend\UserInterface\Block as BackendInterfaceBlockController;
use Concrete\Core\Block\Events\BlockDelete;
use Concrete\Core\Page\EditResponse as PageEditResponse;
use Concrete\Core\Page\EditResponse;

class Delete extends BackendInterfaceBlockController
{
    protected $viewPath = '/dialogs/block/delete';
    protected $controllerActionPath = '/ccm/system/dialogs/block/delete';

    protected function canAccess()
    {
        return $this->permissions->canDeleteBlock();
    }

    public function view()
    {
        if ($this->page->isMasterCollection()) {
            if ($this->block->getBlockTypeHandle() == BLOCK_HANDLE_LAYOUT_PROXY) {
                $this->set('defaultsMessage', t('Warning! This layout is contained in the page type defaults. Anywhere this layout is used may have content deleted. This cannot be undone.'));
            } else {
                $this->set('defaultsMessage', t('Warning! This block is contained in the page type defaults. Any blocks aliased from this block in the site will be deleted. This cannot be undone.'));
            }
        }
        if ($this->block->getBlockTypeHandle() == BLOCK_HANDLE_LAYOUT_PROXY) {
            $this->set('message', t('Are you sure you wish to delete this layout? It will remove the blocks that are contained within it.'));
        } else {
            $this->set('message', t('Are you sure you wish to delete this %s block?', $this->block->getBlockTypeObject()->getBlockTypeName()));
        }
        $this->set('isMasterCollection', $this->page->isMasterCollection());
        $this->set('deleteAllAction', $this->action('submit_all'));
        $this->set('deleteAction', $this->action('submit'));
    }

    public function submit()
    {
        if ($this->validateAction()) {
            if ($this->permissions->canDeleteBlock()) {
                $b = $this->getBlockToEdit();
                $pr = $this->getEditResponse($b);

                // Since we have the OLD block ID in the DOM, we need to override that bID
                $pr->setAdditionalDataAttribute('bID', $this->block->getBlockID());

                $b->deleteBlock();

                $event = new BlockDelete($b, $this->page);
                \Events::dispatch('on_block_delete', $event);

                $b->getBlockCollectionObject()->rescanDisplayOrder($_REQUEST['arHandle']);


                $pr->setMessage(t('Block deleted successfully.'));
                $pr->outputJSON();
            }
        }
    }

    public function submit_all()
    {
        if ($this->validateAction()) {
            if ($this->permissions->canDeleteBlock() && $this->page->isMasterCollection()) {
                $name = sprintf('delete_block_%s', $this->block->getBlockID());
                $queue = \Queue::get($name);

                if ($_POST['process']) {
                    $obj = new \stdClass();
                    $messages = $queue->receive(20);
                    foreach ($messages as $key => $p) {
                        $block = unserialize($p->body);

                        $page = \Page::getByID($block['cID'], $block['cvID']);
                        $b = \Block::getByID($block['bID'], $page, $block['arHandle']);
                        if (is_object($b) && !$b->isError()) {
                            $b->deleteBlock();
                        }
                        $queue->deleteMessage($p);

                    }
                    $obj->totalItems = $queue->count();
                    if ($queue->count() == 0) {
                        $queue->deleteQueue($name);
                    }
                    $obj->bID = $this->block->getBlockID();
                    $obj->aID = $this->area->getAreaID();
                    $obj->message = t('All child blocks deleted successfully.');
                    echo json_encode($obj);
                    $this->app->shutdown();
                } else {
                    $queue = $this->block->queueForDefaultsUpdate($_POST, $queue);
                }

                $totalItems = $queue->count();
                \View::element('progress_bar', array('totalItems' => $totalItems, 'totalItemsSummary' => t2("%d block", "%d blocks", $totalItems)));
                $this->app->shutdown();
            }
        }
    }

}
