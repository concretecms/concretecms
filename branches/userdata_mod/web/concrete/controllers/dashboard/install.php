<?

class DashboardInstallController extends Controller {
	
	protected $errorText = array();
	
	public function __construct() {
		$this->errorText[E_PACKAGE_INSTALLED] = 'You\'ve already installed that package.';		
		$this->errorText[E_PACKAGE_NOT_FOUND] = 'This is not a valid package.';		
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