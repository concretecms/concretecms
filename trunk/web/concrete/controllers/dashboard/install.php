<?

defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardInstallController extends Controller {
	
	protected $errorText = array();
	
	public function __construct() {
		$this->errorText[E_PACKAGE_INSTALLED] = 'You\'ve already installed that package.';		
		$this->errorText[E_PACKAGE_NOT_FOUND] = 'This is not a valid package.';		
		$this->error = Loader::helper('validation/error');
	}
	
	private function mapError($testResults) {
		$testResultsText = array();
		foreach($testResults as $result) {
			$testResultsText[] = $this->errorText[$result];
		}
		return $testResultsText;
	}
	
	public function view() {
		$this->set('pkgArray', Package::getInstalledList());
		$this->set('pkgAvailableArray', Package::getAvailablePackages());
	}
	
	public function refresh_block_type($btID = 0) {
		if ($btID > 0) {
			$bt = BlockType::getByID($btID);
		}
		
		if (isset($bt) && ($bt instanceof BlockType)) {
			$this->set('message', 'Block Type Refreshed. Any database schema changes have been applied.');
			try {
				BlockType::installBlockType($bt->getBlockTypeHandle(), $btID);			
			} catch(Exception $e) {
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
			$this->set('message', 'Block Type Installed!');
		}
	}
	
	public function uninstall_block_type($btID = 0) {
		if ($btID > 0) {
			$bt = BlockType::getByID($btID);
		}
		
		if (isset($bt) && ($bt instanceof BlockType)) {
			if ($bt->canUnInstall()) {
				$bt->delete();
				$this->redirect('/dashboard/install', 'block_type_deleted');
			} else {
				$this->error->add('This block type is either internal, or is being used in your website. It cannot be uninstalled.');
			}
		} else {
			$this->error->add('Invalid block type.');
		}
		$this->inspect_block_type($btID);

	}

	public function on_before_render() {
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
				$this->set('message', 'The package has been installed');
			} catch(Exception $e) {
				$this->set('error', $e);
			}
		}
		$this->view();
	}
	

	

}

?>