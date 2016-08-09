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
        $this->set('isMasterCollection', $this->page->isMasterCollection());
        if ($this->page->isMasterCollection()) {
            $this->set('submitAction', $this->action('submit_master'));
        } else {
            $this->set('submitAction', $this->action('submit'));
        }
    }

    public function submit()
    {
        if ($this->validateAction()) {
            if ($this->permissions->canDeleteBlock() && !$this->page->isMasterCollection()) {
                $b = $this->getBlockToEdit();
                $pr = $this->getEditResponse($b);

                $b->deleteBlock();

                $event = new BlockDelete($b, $this->page);
                \Events::dispatch('on_block_delete', $event);

                $b->getBlockCollectionObject()->rescanDisplayOrder($_REQUEST['arHandle']);


                $pr->setMessage(t('Block deleted successfully.'));
                $pr->outputJSON();
            }
        }
    }
}
