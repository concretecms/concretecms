<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Panel_Add extends BackendInterfacePageController {

	protected $viewPath = '/system/panels/add';
	protected $pagetypes = array();

	protected function canAccess() {
		return $this->permissions->canAddBlocks(); //canEditPageContents
	}

	public function view() {
		$btl = new BlockTypeList();
		$blockTypes = $btl->getBlockTypeList();
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
			$_SESSION['panels_page_add_block_tab'] = $_REQUEST['tab'];
			$tab = $_REQUEST['tab'];
		} else {
			$tab = $_SESSION['panels_page_add_block_tab'];
		}

		$this->set('tab', $tab);
		$this->set('blockTypes', $blockTypes);
		$this->set('ih', Loader::helper('concrete/interface'));
		$this->set('ci', Loader::helper('concrete/urls'));
	}

}

