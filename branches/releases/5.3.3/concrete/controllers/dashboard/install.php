<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardInstallController extends Controller {
	
	public function __construct() {
		$this->error = Loader::helper('validation/error');
	}
		
	public function on_start() {
		$addFuncSelected = true;
		$updateSelected = false;
		
		if ($this->getTask() == 'update') {
			$updateSelected = true;
			$addFuncSelected = false;
		}
		
		$subnav = array(
			array(View::url('/dashboard/install'), t('Installed and Available'), $addFuncSelected),
			array(View::url('/dashboard/install', 'update'), t('Update Add-Ons'), $updateSelected)
		);
		$this->set('subnav', $subnav);
	}

	public function view() {

	}
	
	public function update($pkgHandle = false) {
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

	public function uninstall_package($pkgID = 0, $token = '') {
		$valt = Loader::helper('validation/token');

		if ($pkgID > 0) {
			$pkg = Package::getByID($pkgID);
		}
		
		$u = new User();
		if (!$u->isSuperUser()) {
			$this->error->add(t('Only the super user may remove packages.'));
		} else if (isset($pkg) && ($pkg instanceof Package)) {
			if (!$valt->validate('uninstall', $token)) {
				$this->error->add($valt->getErrorMessage());
			} else {
				$pkg->uninstall();
				$this->redirect('/dashboard/install', 'package_uninstalled');
			}
		} else {
			$this->error->add('Invalid package.');
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
	}

    public function remote_purchase($remoteCID=null){
    	$ph = Loader::helper('package');
    	$errors = $ph->install_remote('purchase', $remoteCID, false);
		if (is_array($errors)) {
			$errors = Package::mapError($errors);
			$this->set('error', $errors);
		}
    }

    public function remote_upgrade($remoteCID, $pkgHandle){
    	$ph = Loader::helper('package');
    	$errors = $ph->prepare_remote_upgrade('purchase', $remoteCID, $pkgHandle);
		if (is_array($errors)) {
			$errors = Package::mapError($errors);
			$this->set('error', $errors);
		} else {
			$this->redirect('/dashboard/install', 'update', $pkgHandle);
		}
    }

}
