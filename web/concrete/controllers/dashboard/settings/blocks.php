<?

defined('C5_EXECUTE') or die("Access Denied.");
class DashboardSettingsBlocksController extends Controller {
	
	public function __construct() {
		$this->error = Loader::helper('validation/error');
	}
		
	public function on_start() {
		$addFuncSelected = true;
		$updateSelected = false;
		
		$subnav = array(
			array(View::url('/dashboard/install'), t('Installed and Available'), true),
			array(View::url('/dashboard/install', 'browse', 'themes'), t('More Themes'), false),
			array(View::url('/dashboard/install', 'browse', 'addons'), t('More Add-Ons'), false)
		);
		$this->set('subnav', $subnav);
		Loader::library('marketplace');

	}
	
	public function browse($what = 'themes') {
		
		$tp = new TaskPermission();
		
		$subnav = array(
			array(View::url('/dashboard/install'), t('Installed and Available'), false),
			array(View::url('/dashboard/install', 'browse', 'themes'), t('More Themes'), $what == 'themes'),
			array(View::url('/dashboard/install', 'browse', 'addons'), t('More Add-Ons'), $what == 'addons')
		);
		
		if ($tp->canInstallPackages()) { 
			Loader::model('marketplace_remote_item');
			
			$mri = new MarketplaceRemoteItemList();
			$mri->setItemsPerPage(9);
			if ($what == 'addons') {
				$sets = MarketplaceRemoteItemList::getItemSets('addons');
			} else { 
				// themes
				$sets = MarketplaceRemoteItemList::getItemSets('themes');
			}
			
			$setsel = array('' => t('All Items'), 'FEATURED' => t('Featured Items'));
			if (is_array($sets)) {
				foreach($sets as $s) {
					$setsel[$s->getMarketplaceRemoteSetID()] = $s->getMarketplaceRemoteSetName();
				}
			}
			
			$mri->setIncludeInstalledItems(false);
			if (isset($_REQUEST['marketplaceRemoteItemSetID'])) {
				$set = $_REQUEST['marketplaceRemoteItemSetID'];
			}
	
			if (isset($_REQUEST['marketplaceRemoteItemKeywords'])) {
				$keywords = $_REQUEST['marketplaceRemoteItemKeywords'];
			}
			
			if ($keywords != '') {
				$mri->filterByKeywords($keywords);
			}
			
			if ($set == 'FEATURED') {
				$mri->filterByIsFeaturedRemotely(1);
			} else if ($set > 0) {
				$mri->filterBySet($set);
			}
			
			$mri->setType($what);
			$mri->execute();
			
			$items = $mri->getPage();
	
			$this->set('selectedSet', $set);
			$this->set('list', $mri);
			$this->set('items', $items);
			$this->set('form', Loader::helper('form'));
			$this->set('sets', $setsel);
			$this->set('type', $what);
		}
		$this->set('subnav', $subnav);

	}
	
	public function view($status = false) {
		if ($status == 'community_connect_success') {
			$this->set('message', t('Your site is now connected to the concrete5 community.'));
		}
	}
	
	public function update($pkgHandle = false) {
		$tp = new TaskPermission();
		if ($tp->canInstallPackages()) { 
			if ($pkgHandle) {
				$tests = Package::testForInstall($pkgHandle, false);
				if (is_array($tests)) {
					$tests = Package::mapError($tests);
					$this->set('error', $tests);
				} else {
					$p = Package::getByHandle($pkgHandle);
					try {
						$p->upgradeCoreData();
						$p->upgrade();
						$this->set('message', t('The package has been updated successfully.'));
					} catch(Exception $e) {
						$this->set('error', $e);
					}
				}
			} else {
				$mi = Marketplace::getInstance();
				if ($mi->isConnected()) {
					Marketplace::checkPackageUpdates();
				}
			}
		}
	}
	
	public function refresh_block_type($btID = 0) {
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
			$this->inspect_block_type($btID);
		}
	}
	
	public function install_block_type($btHandle = null) {
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
		
	}
	
	public function uninstall_block_type($btID = 0, $token = '') {
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
				$this->redirect('/dashboard/install', 'block_type_deleted');
			} else {
				$this->error->add(t('This block type is internal. It cannot be uninstalled.'));
			}
		} else {
			$this->error->add('Invalid block type.');
		}
		
		if ($this->error->has()) {
			$this->set('error', $this->error);
		}
		$this->inspect_block_type($btID);

	}
	
	public function uninstall($pkgID) {
		$tp = new TaskPermission();
		if (!$tp->canUninstallPackages()) {
			return false;
		}
		
		$pkg = Package::getByID($pkgID);
		if (!is_object($pkg)) {
			$this->redirect("/dashboard/install");
		}
		$this->set('text', Loader::helper('text'));
		$this->set('pkg', $pkg);
		$this->set('items', $pkg->getPackageItems());
	}

	public function do_uninstall_package() {
		$pkgID = $this->post('pkgID');

		$valt = Loader::helper('validation/token');

		if ($pkgID > 0) {
			$pkg = Package::getByID($pkgID);
		}
		
		if (!$valt->validate('uninstall')) {
			$this->error->add($valt->getErrorMessage());
		}
		
		$tp = new TaskPermission();
		if (!$tp->canUninstallPackages()) {
			$this->error->add(t('You do not have permission to uninstall packages.'));
		}
		
		if (!is_object($pkg)) {
			$this->error->add(t('Invalid package.'));
		}
		
		if (!$this->error->has()) {
			$pkg->uninstall();
			if ($this->post('pkgMoveToTrash')) {
				$r = $pkg->backup();
				if (is_array($r)) {
					$pe = Package::mapError($r);
					foreach($pe as $ei) {
						$this->error->add($ei);
					}
				}
			}
			if (!$this->error->has()) { 
				$this->redirect('/dashboard/install', 'package_uninstalled');
			}
		}
		
		if ($this->error->has()) {
			$this->set('error', $this->error);
		}
		$this->inspect_package($pkgID);

	}


	public function inspect_block_type($btID = 0) { 
		if ($btID > 0) {
			$bt = BlockType::getByID($btID);
		}
		
		if (isset($bt) && ($bt instanceof BlockType)) {
			$this->set('bt', $bt);
			$this->set('num', $bt->getCount());
		} else {
			$this->redirect('/dashboard/install');
		}
	}

	public function inspect_package($pkgID = 0) { 
		if ($pkgID > 0) {
			$pkg = Package::getByID($pkgID);
		}
		
		if (isset($pkg) && ($pkg instanceof Package)) {
			$this->set('pkg', $pkg);
		} else {
			$this->redirect('/dashboard/install');
		}
	}
	
	public function block_type_deleted() {
		$this->set('message', t('The block type has been removed.'));
	}

	public function package_uninstalled() {
		$this->set('message', t('The package type has been uninstalled.'));
	}
	
	public function install_package($package) {
		$tp = new TaskPermission();
		if ($tp->canInstallPackages()) { 
			$tests = Package::testForInstall($package);
			if (is_array($tests)) {
				$tests = Package::mapError($tests);
				$this->set('error', $tests);
			} else {
				$p = Loader::package($package);
				try {
					$p->install();
					$this->set('message', t('The package has been installed.'));
				} catch(Exception $e) {
					$this->set('error', $e);
				}
			}
		} else {
			$this->error->add(t('You do not have permission to install add-ons.'));
			$this->set('error', $this->error);
		}
	}
	

    public function download($remoteMPID=null) {
		$tp = new TaskPermission();
		if ($tp->canInstallPackages()) { 
			Loader::model('marketplace_remote_item');
			$mri = MarketplaceRemoteItem::getByID($remoteMPID);
			
			if (!is_object($mri)) {
				$this->set('error', array(t('Invalid marketplace item ID.')));
				return;
			}
			
			$r = $mri->download();
			if ($r != false) {
				if (!is_array($r)) {
					$this->set('error', array($r));
				} else {
					$errors = Package::mapError($r);
					$this->set('error', $errors);
				}
			} else {
				$this->set('message', t('Marketplace item %s downloaded successfully.', $mri->getName()));
			}
		} else {
			$this->error->add(t('You do not have permission to download add-ons.'));
			$this->set('error', $this->error);
		}
    }

    public function prepare_remote_upgrade($remoteMPID){
		$tp = new TaskPermission();
		if ($tp->canInstallPackages()) { 
			Loader::model('marketplace_remote_item');
			$mri = MarketplaceRemoteItem::getByID($remoteMPID);
	
			if (!is_object($mri)) {
				$this->set('error', array(t('Invalid marketplace item ID.')));
				return;
			}
			
			$local = Package::getbyHandle($mri->getHandle());
			if (!is_object($local) || $local->isPackageInstalled() == false) {
				$this->set('error', array(Package::E_PACKAGE_NOT_FOUND));
				return;
			}		
			
			$r = $mri->downloadUpdate();
	
			if ($r != false) {
				if (!is_array($r)) {
					$this->set('error', array($r));
				} else {
					$errors = Package::mapError($r);
					$this->set('error', $errors);
				}
			} else {
				$this->redirect('/dashboard/install', 'update', $mri->getHandle());
			}
		}
    }

}
