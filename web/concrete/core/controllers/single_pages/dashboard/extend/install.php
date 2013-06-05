<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_Extend_Install extends Controller {
	
	public function on_start() {
		Loader::library('marketplace');
		$this->error = Loader::helper('validation/error');
	}
	
	public function uninstall($pkgID) {
		$tp = new TaskPermission();
		if (!$tp->canUninstallPackages()) {
			return false;
		}
		
		$pkg = Package::getByID($pkgID);
		if (!is_object($pkg)) {
			$this->redirect("/dashboard/extend/install");
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
				$this->redirect('/dashboard/extend/install', 'package_uninstalled');
			}
		}
		
		if ($this->error->has()) {
			$this->set('error', $this->error);
		}
		$this->inspect_package($pkgID);

	}

	public function inspect_package($pkgID = 0) { 
		if ($pkgID > 0) {
			$pkg = Package::getByID($pkgID);
		}
		
		if (isset($pkg) && ($pkg instanceof Package)) {
			$this->set('pkg', $pkg);
		} else {
			$this->redirect('/dashboard/extend/install');
		}
	}
	
	public function package_uninstalled() {
		$this->set('message', t('The package type has been uninstalled.'));
	}

	public function install_package($package) {
		$tp = new TaskPermission();
		if ($tp->canInstallPackages()) { 
			$p = Loader::package($package);
			if (is_object($p)) {
				if (
					(!$p->showInstallOptionsScreen()) ||
					Loader::helper('validation/token')->validate('install_options_selected')
				) {
					$tests = Package::testForInstall($package);
					if (is_array($tests)) {
						$tests = Package::mapError($tests);
						$this->set('error', $tests);
					} else {
						$currentLocale = Localization::activeLocale();
						if ($currentLocale != 'en_US') {
							// Prevent the database records being stored in wrong language
							Localization::changeLocale('en_US');
						}
						try {
							$u = new User();
							$pkg = $p->install($this->post());
							if ($u->isSuperUser() && $p->allowsFullContentSwap() && $this->post('pkgDoFullContentSwap')) { 
								$p->swapContent($this->post());
							}
							if ($currentLocale != 'en_US') {
								Localization::changeLocale($currentLocale);
							}
							$pkg = Package::getByHandle($p->getPackageHandle());
							$this->redirect('/dashboard/extend/install', 'package_installed', $pkg->getPackageID());
						} catch(Exception $e) {
							if ($currentLocale != 'en_US') {
								Localization::changeLocale($currentLocale);
							}
							if ($p->showInstallOptionsScreen()) {
								$this->set('showInstallOptionsScreen', true);
								$this->set('pkg', $p);
							}
							$this->set('error', $e);
						}
					}
				} else {
					$this->set('showInstallOptionsScreen', true);
					$this->set('pkg', $p);
				}
			}
		} else {
			$this->error->add(t('You do not have permission to install add-ons.'));
			$this->set('error', $this->error);
		}
	}

	public function package_installed($pkgID = 0) {
		$this->set('message', t('The package has been installed.'));
		$this->set('installedPKG', Package::getByID($pkgID));
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

}
