<?php
namespace Concrete\Controller\Dialog\Block;

use Concrete\Controller\Backend\UserInterface\Block as BackendInterfaceBlockController;
use Concrete\Core\Page\EditResponse;

class Cache extends BackendInterfaceBlockController
{
    protected $viewPath = '/dialogs/block/cache';

    public function view()
    {
        $this->set('bName', $this->block->getBlockName());
    }

    public function submit()
    {
        if ($this->validateAction()) {
            if ($this->permissions->canEditBlockName()) {
                $b = $this->getBlockToEdit();
                $b->setName($this->request->request->get('bName'));
            }

            $pr = new EditResponse();
            $pr->setPage($this->page);
            $pr->setMessage(t('Advanced block settings saved successfully.'));
            $pr->outputJSON();
        }
    }

    protected function canAccess()
    {
        return $this->permissions->canEditBlockName() || $this->permissions->canEditBlockCacheSettings();
    }

}

