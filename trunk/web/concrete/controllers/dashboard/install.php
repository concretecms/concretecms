<?

defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardInstallController extends Controller {
	
	protected $errorText = array();
	
	public function __construct() {
		$this->errorText[E_PACKAGE_INSTALLED] = t("You've already installed that package.");		
		$this->errorText[E_PACKAGE_NOT_FOUND] = t("Invalid Package.");
		$this->errorText[E_PACKAGE_VERSION] = t("This package requires concrete version %s or greater.");
		$this->error = Loader::helper('validation/error');
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
				$bt->refresh();
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
	}

	public function remote_addon($pkgName=null)
	{
		$this->install_remote('addon', $pkgName);
	}

	public function remote_theme($pkgName=null)
	{
		$this->install_remote('theme', $pkgName);
	}

	private function install_remote($type, $pkgName=null)
	{
		if (empty($pkgName)) {
			$this->error->add(t('No package name provided.'));
			return;
		}

	    $helper = Loader::helper($type == 'addon' ? 'concrete/marketplace/blocks' : 'concrete/marketplace/themes');
    	$featured = $helper->getPreviewableList();
        foreach ($featured as $fi) {
			if ($pkgName == $fi->getHandle()) {
				break;
			}
		}

		$fileURL = $fi ? $fi->getRemoteFileURL() : '';
		$file = $this->download_remote_package($fileURL);
		if (empty($file)) {
			$this->error->add(t('Not a recognized package.'));
			return;
		}

		try {
			Loader::model('package_archive');
			$am = new PackageArchive($fi->getHandle());
			$am->install($file, true);
		} catch (Exception $e) {
			$this->error->add(t('Error installing package.'));
			$this->set('message', $e->getMessage());
			return;
		}

        $tests = Package::testForInstall($fi->getHandle());
        if (is_array($tests)) {
            $tests = $this->mapError($tests);
            $this->set('error', $tests);
        } else {
            $p = Loader::package($fi->getHandle());
            try {
                $p->install();
                $this->set('message', t('The package has been installed.'));
            } catch(Exception $e) {
                $this->error->add('error', $e);
            }
        }
 
		$msg = "The " . ($type == 'addon' ? 'add-on' : 'theme') . " '" . $fi->getName() . "' was successfully installed.";
		$this->set('message', $msg);
	}

	private function download_remote_package($fileURL)
	{
		if (empty($fileURL)) return;

		$fh = Loader::helper('file');
		$pkg = $fh->getContents($fileURL);

		$file = time();
		// Use the same method as the Archive library to build a temporary file name.
		$tmpFile = $fh->getTemporaryDirectory() . '/' . $file . '.zip';
		$fp = fopen($tmpFile, "wb");
		fwrite($fp, $pkg);
		fclose($fp);

		return $file;
	}
}
