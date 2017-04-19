<?php
namespace Concrete\Controller\Backend;

use Concrete\Controller\Backend\UserInterface\Block as BackendInterfaceBlockController;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\View\DialogView;
use Core;
use Concrete\Core\Localization\Localization;

class Block extends BackendInterfaceBlockController
{
    public function render()
    {
        $loc = Localization::getInstance();
        $loc->setActiveContext(Localization::CONTEXT_SITE);

        $c = $this->block->getBlockCollectionObject();
        $dl = Core::make('multilingual/detector');
        $dl->setupSiteInterfaceLocalization($this->page);

        $btc = $this->block->getInstance();
        $bv = new BlockView($this->block);
        if (isset($_REQUEST['arEnableGridContainer']) && $_REQUEST['arEnableGridContainer'] == 1) {
            $this->area->enableGridContainer();
        }
        $this->area->forceControlsToDisplay(); // we always want to show them controls.
        $bv->addScopeItems(array('c' => $this->page, 'a' => $this->area, 'dialogController' => $this));
        $this->set('bv', $bv);
        $this->view = new DialogView('/backend/block');
    }


    protected function canAccess()
    {
        return $this->permissions->canViewEditInterface();
    }
}
