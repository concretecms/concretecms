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
        $this->set('form', \Core::make('helper/form'));
        $this->set('cbOverrideBlockTypeCacheSettings', $this->block->overrideBlockTypeCacheSettings());
        $this->set('btCacheBlockOutput', $this->block->cacheBlockOutput());
        $this->set('btCacheBlockOutputOnPost', $this->block->cacheBlockOutputOnPost());
        $this->set('btCacheBlockOutputForRegisteredUsers', $this->block->cacheBlockOutputForRegisteredUsers());
        $this->set('btCacheBlockOutputLifetime', $this->block->getBlockOutputCacheLifetime());
    }

    public function submit()
    {
        if ($this->validateAction()) {

            $b = $this->getBlockToEdit();
            if ($this->permissions->canEditBlockName()) {
                $b->setName($this->request->request->get('bName'));
            }

            if ($this->permissions->canEditBlockCacheSettings()) {
                $b->resetCustomCacheSettings();
                if ($this->request->request->get('cbOverrideBlockTypeCacheSettings')) {
                    $b->setCustomCacheSettings(
                        $this->request->request->get('btCacheBlockOutput'),
                        $this->request->request->get('btCacheBlockOutputOnPost'),
                        $this->request->request->get('btCacheBlockOutputForRegisteredUsers'),
                        $this->request->request->get('btCacheBlockOutputLifetime')
                    );
                }
            }

            $pr = new EditResponse();
            $pr->setPage($this->page);
            $pr->setAdditionalDataAttribute('aID', $this->area->getAreaID());
            $pr->setAdditionalDataAttribute('arHandle', $this->area->getAreaHandle());
            $pr->setAdditionalDataAttribute('bID', $b->getBlockID());
            $pr->setMessage(t('Advanced block settings saved successfully.'));
            $pr->outputJSON();
        }
    }

    protected function canAccess()
    {
        return $this->permissions->canEditBlockName() || $this->permissions->canEditBlockCacheSettings();
    }

}

