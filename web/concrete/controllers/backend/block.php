<?php
namespace Concrete\Controller\Backend;

use Concrete\Controller\Backend\UserInterface\Block as BackendInterfaceBlockController;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Http\Request;
use Concrete\Core\View\DialogView;
use Concrete\Core\Block\Block as BlockObject;

class Block extends BackendInterfaceBlockController
{

    public function render()
    {
        $request = Request::getInstance();
        if (!$this->block && $request->get('bID')) {
            $this->block = BlockObject::getByID($request->get('bID'));
        }
        $btc = $this->block->getInstance();
        $btc->outputAutoHeaderItems();
        $bv = new BlockView($this->block);
        if (isset($_REQUEST['arEnableGridContainer']) && $_REQUEST['arEnableGridContainer'] == 1) {
            $this->area->enableGridContainer();
        }
        $bv->addScopeItems(array('c' => $this->page, 'a' => $this->area, 'dialogController' => $this));
        $this->set('bv', $bv);
        $this->view = new DialogView('/backend/block');
    }

    protected function canAccess()
    {
        return $this->permissions->canViewEditInterface();
    }

}

