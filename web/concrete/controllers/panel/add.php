<?
namespace Concrete\Controller\Panel;
use \Concrete\Controller\Backend\UI\Page as BackendInterfacePageController;
use BlockTypeList;
use Loader;
use Session;
use BlockType;

class Add extends BackendInterfacePageController {

	protected $viewPath = '/panels/add';
	protected $pagetypes = array();

	protected function canAccess() {
		return $this->permissions->canAddBlocks(); //canEditPageContents
	}

	public function view() {
		$btl = new BlockTypeList();
		$blockTypes = $btl->get();
		$dsh = Loader::helper('concrete/dashboard');
		$dashboardBlockTypes = array();
		if ($dsh->inDashboard()) {
			$dashboardBlockTypes = BlockTypeList::getDashboardBlockTypes();
		}
		$blockTypes = array_merge($blockTypes, $dashboardBlockTypes);
		if ($this->page->isMasterCollection()) {
			$bt = BlockType::getByHandle(BLOCK_HANDLE_PAGE_TYPE_OUTPUT_PROXY);
			$blockTypes[] = $bt;
		}
		if ($_REQUEST['tab']) {
			Session::set('panels_page_add_block_tab', $_REQUEST['tab']);
			$tab = $_REQUEST['tab'];
		} else {
			$tab = Session::get('panels_page_add_block_tab');
		}

		$this->set('tab', $tab);
		$this->set('blockTypes', $blockTypes);
		$this->set('ih', Loader::helper('concrete/ui'));
		$this->set('ci', Loader::helper('concrete/urls'));
	}

}

