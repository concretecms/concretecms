<?

class DashboardThemesController extends Controller {

	protected $helpers = array('html');

	public function view() {
		
		$tArray = array();
		$tArray2 = array();
		
		$tArray = PageTheme::getList();
		$tArray2 = PageTheme::getAvailableThemes();
		
		$this->set('tArray', $tArray);
		$this->set('tArray2', $tArray2);
		
		$this->set('siteTheme', PageTheme::getSiteTheme());
		$this->set('activate', View::url('/dashboard/themes', 'activate'));		
		$this->set('install', View::url('/dashboard/themes', 'install'));		
	}

	public function remove($ptID) {
		$v = Loader::helper('validation/error');
		try {
			$pl = PageTheme::getByID($ptID);
			if (!is_object($pl)) {
				throw new Exception('Invalid theme.');
			}
			if ($pl->getPackageID() > 0) {
				throw new Exception(t('You may not uninstall a packaged theme.'));
			}
			
			$pl->uninstall();
			$this->set('message', t('Theme uninstalled.'));
		} catch (Exception $e) {
			$v->add($e);
			$this->set('error', $v);
		}
		$this->view();
	}
	
	public function activate($ptID) {
		$this->set('activate_confirm', View::url('/dashboard/themes', 'activate_confirm', $ptID));	
	}

	public function install($ptHandle = null) {
		$th = PageTheme::getByFileHandle($ptHandle);
		if ($ptHandle == null) {
			$this->redirect('/dashboard/themes');
		}
		
		$v = Loader::helper('validation/error');
		try {
			if (is_object($th)) {
				$t = PageTheme::add($ptHandle);
				$this->redirect('/dashboard/themes/inspect', $t->getThemeID(), 1);
				
			} else {
				throw new Exception(t('Invalid Theme'));
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
	
	public function activate_confirm($ptID) {
		$l = PageTheme::getByID($ptID);
		$val = Loader::helper('validation/error');
		if (!is_object($l)) {
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