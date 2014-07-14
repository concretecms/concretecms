<?
namespace Concrete\Controller\Dialog\Block;
use Concrete\Controller\Backend\UserInterface\Block as BackendInterfaceBlockController;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\View\View;

class Design extends BackendInterfaceBlockController {

    protected $viewPath = '/dialogs/block/design';

    protected function canAccess()
    {
        return $this->permissions->canEditBlockDesign() || $this->permissions->canEditBlockCustomTemplate();
    }

	public function view() {
        $btc = $this->block->getInstance();
        $btc->outputAutoHeaderItems();
        $bv = new BlockView($this->block);
        $bv->addScopeItems(array('c' => $this->page, 'a' => $this->area, 'dialogController' => $this));
        $this->set('bv', $bv);
	}

}

