<?
defined('C5_EXECUTE') or die("Access Denied.");
class DashboardBlocksTypesController extends Controller {

	public function view() {
		$btAvailableArray = BlockTypeList::getAvailableList();
		$btArray = BlockTypeList::getInstalledList();		
		$coreBlockTypes = array();
		$webBlockTypes = array();		
		foreach($btArray as $_bt) {
			if ($_bt->getPackageID() == 0) {
				if ($_bt->isCoreBlockType()) {
					$coreBlockTypes[] = $_bt;
				} else {
					$webBlockTypes[] = $_bt;
				}
			}
		}
		$this->set('webBlockTypes', $webBlockTypes);
		$this->set('coreBlockTypes', $coreBlockTypes);
		$this->set('availableBlockTypes', $btAvailableArray);
	}

	public function on_start() {
		$this->set('ci', Loader::helper('concrete/urls'));
		$this->set('ch', Loader::helper('concrete/interface'));
		$this->set("valt", Loader::helper('validation/token'));
		$this->error = Loader::helper('validation/error');
	}

	public function on_before_render() {
		$this->set('error', $this->error);
	}
	
	public function refresh($btID = 0) {
		if ($btID > 0) {
			$bt = BlockType::getByID($btID);
		}
		
		if (isset($bt) && ($bt instanceof BlockType)) {
			try {
				$bt->refresh();
				$this->set('message', t('Block Type Refreshed. Any database schema changes have been applied.'));

			} catch(Exception $e) {
				$this->set('error', $e);
			}
			$this->inspect($btID);
		}
	}
	
	public function install($btHandle = null) {
		$tp = new TaskPermission();
		if ($tp->canInstallPackages()) { 
			try {
				$resp = BlockType::installBlockType($btHandle);
				
				if ($resp != '') {
					$this->error->add($resp);
				} else {
					$this->set('message', t('Block Type Installed.'));
				}
			} catch(Exception $e) {
				$this->error->add($e);
				$this->set('error', $this->error);
			}
		} else {
			$this->error->add(t('You do not have permission to install custom block types or add-ons.'));
			$this->set('error', $this->error);
		}		
		$this->view();
	}
	
	public function uninstall($btID = 0, $token = '') {
		$valt = Loader::helper('validation/token');

		if ($btID > 0) {
			$bt = BlockType::getByID($btID);
		}
		
		$u = new User();
		if (!$u->isSuperUser()) {
			$this->error->add(t('Only the super user may remove block types.'));
		} else if (isset($bt) && ($bt instanceof BlockType)) {
			if (!$valt->validate('uninstall', $token)) {
				$this->error->add($valt->getErrorMessage());
			} else if ($bt->canUnInstall()) {
				$bt->delete();
				$this->redirect('/dashboard/blocks/types', 'block_type_deleted');
			} else {
				$this->error->add(t('This block type is internal. It cannot be uninstalled.'));
			}
		} else {
			$this->error->add('Invalid block type.');
		}
		
		if ($this->error->has()) {
			$this->set('error', $this->error);
		}
		$this->inspect($btID);
	}

	public function inspect($btID = 0) { 
		if ($btID > 0) {
			$bt = BlockType::getByID($btID);
		}
		
		if (isset($bt) && ($bt instanceof BlockType)) {
			$this->set('bt', $bt);
			$this->set('num', $bt->getCount());
		} else {
			$this->redirect('/dashboard/blocks/types');
		}
	}


	public function block_type_deleted() {
		$this->set('message', t('The block type has been removed.'));
		$this->view();
	}


	
}