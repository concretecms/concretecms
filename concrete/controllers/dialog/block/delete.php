<?php
namespace Concrete\Controller\Dialog\Block;

use Concrete\Controller\Backend\UserInterface\Block as BackendInterfaceBlockController;
use Concrete\Core\Block\Command\DeleteBlockCommand;
use Concrete\Core\Block\Events\BlockDelete;
use Concrete\Core\Foundation\Queue\QueueService;
use Concrete\Core\Foundation\Queue\Response\EnqueueItemsResponse;
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
                $queue = $this->app->make(QueueService::class);
                $q = $queue->get('delete_block');
                $blocks = $this->block->queueForDefaultsUpdate($_POST);
                foreach($blocks as $b) {
                    $command = new DeleteBlockCommand(
                        $b['bID'], $b['cID'], $b['cvID'], $b['arHandle']
                    );
                    $this->queueCommand($command);
                }


                $response = new EnqueueItemsResponse($q, [
                    'bID' => $this->block->getBlockID(),
                    'aID' => $this->area->getAreaID(),
                    'message' => t('All child blocks deleted successfully.')
                ]);
                return $response;
            }
        }
    }

}
