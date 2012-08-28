<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_Pages_Themes extends Controller {

	protected $helpers = array('html');

	public function view() {
		
		$tArray = array();
		$tArray2 = array();
		
		$tArray = PageTheme::getList();
		$tArray2 = PageTheme::getAvailableThemes();
		
		$this->set('tArray', $tArray);
		$this->set('tArray2', $tArray2);
		$siteThemeID = 0;
		$obj = PageTheme::getSiteTheme();
		if (is_object($obj)) {
			$siteThemeID = $obj->getThemeID();
		}
		
		$this->set('siteThemeID', $siteThemeID);
		$this->set('activate', View::url('/dashboard/pages/themes', 'activate'));		
		$this->set('install', View::url('/dashboard/pages/themes', 'install'));		
	}
	
	public function save_mobile_theme() {
		Config::save('MOBILE_THEME_ID', $this->post('MOBILE_THEME_ID'));
		$this->redirect('/dashboard/pages/themes', 'mobile_theme_saved');
	}
	
	public function mobile_theme_saved() {
		$this->set('success', t('Mobile theme saved.'));
		$this->view();
	}
	
	public function on_start() {
		$this->set('disableThirdLevelNav', true);
	}
	
	public function remove($ptID, $token = '') {
		$v = Loader::helper('validation/error');
		try {
			$valt = Loader::helper('validation/token');
			if (!$valt->validate('remove', $token)) {
				throw new Exception($valt->getErrorMessage());
			}
			$pl = PageTheme::getByID($ptID);
			if (!is_object($pl)) {
				throw new Exception(t('Invalid theme.'));
			}
			/*
			if ($pl->getPackageID() > 0) {
				throw new Exception('You may not uninstall a packaged theme.');
			}
			*/
			
			$localUninstall = true;
			if ($pl->getPackageID() > 0) {
				// then we check to see if this is the only theme in that package. If so, we uninstall the package too
				$pkg = Package::getByID($pl->getPackageID());
				$items = $pkg->getPackageItems();
				if (count($items) == 1) {
					$_pl = $items[0];
					if ($_pl instanceof PageTheme && $_pl->getThemeID() == $ptID) {
						$pkg->uninstall();
						$localUninstall = false;						
					}
				}
			}
			if ($localUninstall) {
				$pl->uninstall();
			}
			$this->set('message', t('Theme uninstalled.'));
		} catch (Exception $e) {
			$v->add($e);
			$this->set('error', $v);
		}
		$this->view();
	}
	
	public function activate($ptID) {
		$valt = Loader::helper('validation/token');
		$this->set('activate_confirm', View::url('/dashboard/pages/themes', 'activate_confirm', $ptID, $valt->generate('activate')));	
	}
	
	public function marketplace() {
		$this->redirect('/dashboard/install/browse', 'themes');
	}

	public function install($ptHandle = null) {
		$th = PageTheme::getByFileHandle($ptHandle);
		if ($ptHandle == null) {
			$this->redirect('/dashboard/pages/themes');
		}
		
		$v = Loader::helper('validation/error');
		try {
			if (is_object($th)) {
				$t = PageTheme::add($ptHandle);
				$this->redirect('/dashboard/pages/themes/inspect', $t->getThemeID(), 1);
				
			} else {
				throw new Exception('Invalid Theme');
			}
		} catch(Exception $e) {
			switch($e->getMessage()) {
				case PageTheme::E_THEME_INSTALLED:
					$v->add(t('That theme has already been installed.'));
					break;
				default:
					$v->add($e->getMessage());
					break;
			}
			
			$this->set('error', $v);
		}
		$this->view();
	}
	
	// this can be run from /layouts/add/ or /layouts/edit/ or /layouts/ - anything really
	
	public function activate_confirm($ptID, $token) {
		$l = PageTheme::getByID($ptID);
		$val = Loader::helper('validation/error');
		$valt = Loader::helper('validation/token');
		if (!$valt->validate('activate', $token)) {
			$val->add($valt->getErrorMessage());
			$this->set('error', $val);
		} else if (!is_object($l)) {
			$val->add('Invalid Theme');
			$this->set('error', $val);
		} else {
			$l->applyToSite();
			$this->set('message', t('Theme activated'));
		}
		$this->view();
	}
	

}

?>