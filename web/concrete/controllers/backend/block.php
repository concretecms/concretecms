<?
namespace Concrete\Controller\Backend;

use Concrete\Controller\Backend\UserInterface\Block as BackendInterfaceBlockController;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\View\View;

class Block extends BackendInterfaceBlockController {

    protected function canAccess()
    {
        return $this->permissions->canViewEditInterface();
    }

	public function render() {
        $btc = $this->block->getInstance();
        $btc->outputAutoHeaderItems();
        $bv = new BlockView($this->block);
        if (isset($_REQUEST['arEnableGridContainer']) && $_REQUEST['arEnableGridContainer'] == 1) {
            $this->area->enableGridContainer();
        }
        $bv->addScopeItems(array('c' => $this->page, 'a' => $this->area, 'dialogController' => $this));
        $this->set('bv', $bv);
        $this->view = new View('/backend/block');
	}

}

