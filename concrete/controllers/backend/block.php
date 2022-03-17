<?php
namespace Concrete\Controller\Backend;

use Concrete\Controller\Backend\UserInterface\Block as BackendInterfaceBlockController;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\View\DialogView;
use Concrete\Core\Localization\Localization;

/**
 * Class Block is a backend controller for ajax requests
 *
 * path: /ccm/system/block
 */
class Block extends BackendInterfaceBlockController
{
    /**
     * Renders the block view, normally called via an ajax request
     * path: /ccm/system/block/render
     * @return void
     */
    public function render()
    {
        $loc = Localization::getInstance();
        $loc->setActiveContext(Localization::CONTEXT_SITE);

        $dl = app('multilingual/detector');
        $dl->setupSiteInterfaceLocalization($this->page);
        // If this has a temporary filename then validate it
        if ($this->request->query->has('tempFilename') && preg_match('/^[A-Za-z0-9_-]+(?:\.php)?$/i', $this->request->query->get('tempFilename')))
        {
            // With a valid filename clear the block cache and set our new filename
            $this->block->setTempFilename($this->request->query->get('tempFilename'));
            $this->block->temporaryClearBlockCache();
        }
        $bv = new BlockView($this->block);
        if ($this->request->query->has('arEnableGridContainer') && $this->request->query->get('arEnableGridContainer') == 1) {
            $this->area->enableGridContainer();

        }

        $this->area->forceControlsToDisplay(); // we always want to show them controls.
        $bv->addScopeItems(['c' => $this->page, 'a' => $this->area, 'dialogController' => $this]);
        $this->set('bv', $bv);
        $this->view = new DialogView('/backend/block');
    }


    protected function canAccess()
    {
        return $this->permissions->canViewEditInterface();
    }
}
