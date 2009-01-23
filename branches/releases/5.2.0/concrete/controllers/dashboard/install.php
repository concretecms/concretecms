<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardInstallController extends Controller {
	
	protected $errorText = array();
	
	public function __construct() {
		$this->errorText[E_PACKAGE_INSTALLED] = t("You've already installed that package.");		
		$this->errorText[E_PACKAGE_NOT_FOUND] = t("Invalid Package.");
		$this->errorText[E_PACKAGE_VERSION] = t("This package requires concrete version %s or greater.");
		$this->error = Loader::helper('validation/error');
	}
	
	public function packages() {
		$this->set('nav', 'packages');
		$this->set('pkgArray', Package::getInstalledList());
		$this->set('pkgAvailableArray', Package::getAvailablePackages());
	}
	
	private function mapError($testResults) {
		$testResultsText = array();
		foreach($testResults as $result) {
			if (is_array($result)) {
				$et = $this->errorText[$result[0]];
				array_shift($result);
				$testResultsText[] = vsprintf($et, $result);
			} else {
				$testResultsText[] = $this->errorText[$result];
			}
		}
		return $testResultsText;
	}
	
	public function view() {

	}
	
	public function refresh_block_type($btID = 0) {
		if ($btID > 0) {
			$bt = BlockType::getByID($btID);
		}
		
		if (isset($bt) && ($bt instanceof BlockType)) {
			try {
				if ($bt->getPackageID() > 0) {
					$pkg = Package::getByID($bt->getPackageID());
					$resp = BlockType::installBlockTypeFromPackage($bt->getBlockTypeHandle(), $pkg, $btID);			
					if ($resp != '') {
						throw new Exception($resp);
					}
				} else {
					$resp = BlockType::installBlockType($bt->getBlockTypeHandle(), $btID);			
					if ($resp != '') {
						throw new Exception($resp);
					}
				}
				
				$this->set('message', t('Block Type Refreshed. Any database schema changes have been applied.'));

			} catch(Exception $e) {
				@ob_end_flush();
				$this->set('error', $e);
			}
			$this->inspect_block_type($btID);
		}
	}
	
	public function install_block_type($btHandle = null) {
		$resp = BlockType::installBlockType($btHandle);
		if ($resp != '') {
			$this->error->add($resp);
		} else {
			$this->set('message', t('Block Type Installed.'));
		}
	}
	
	public function uninstall_block_type($btID = 0, $token = '') {
		$valt = Loader::helper('validation/token');

		if ($btID > 0) {
			$bt = BlockType::getByID($btID);
		}
		
		if (isset($bt) && ($bt instanceof BlockType)) {
			if (!$valt->validate('uninstall', $token)) {
				$this->error->add($valt->getErrorMessage());
			} else if ($bt->canUnInstall()) {
				$bt->delete();
				$this->redirect('/dashboard/install', 'block_type_deleted');
			} else {
				$this->error->add(t('This block type is either internal, or is being used in your website. It cannot be uninstalled.'));
			}
		} else {
			$this->error->add('Invalid block type.');
		}
		$this->inspect_block_type($btID);

	}

	public function on_before_render() {
		$btSelected = false;
		$pkgsSelected = false;			
		switch($this->get('nav')) {
			case "packages":
			$pkgsSelected = true;
			break;
		default:
			$btSelected = true;
			break;
		}		
		$subnav = array(
			array(View::url('/dashboard/install'), t('Block Types'), $btSelected),
			array(View::url('/dashboard/install', 'packages'), t('Applications'), $pkgsSelected)
		);
		$this->set('subnav', $subnav);
		if ($this->error->has()) {
			$this->set('error', $this->error);	
		}
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
	
	public function install_package($package) {
		$tests = Package::testForInstall($package);
		if (is_array($tests)) {
			$tests = $this->mapError($tests);
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
		$this->packages();
	}
	

	

}

?>