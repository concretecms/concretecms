<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_Pages_Themes_Inspect extends Controller {

	protected $helpers = array('html');

	// grab all the page types from within a theme	
	public function view($ptID = null, $isOnInstall = false) {
		if (!$ptID) {
			$this->redirect('/dashboard/pages/themes/');
		}
		
		$v = Loader::helper('validation/error');
		$pt = PageTheme::getByID($ptID);
		if (is_object($pt)) {
			$files = $pt->getFilesInTheme();
			$this->set('files', $files);
			$this->set('ptID', $ptID);
			$this->set('pageTheme', $pt);
		} else {
			$v->add('Invalid Theme');
		}	
		
		if ($isOnInstall) {
			$this->set('message', t("Theme installed. You may automatically create page types from template files contained in your theme using the form below."));
		}
		
		if ($v->has()) {
			$this->set('error', $v);
		}

		$this->set('disableThirdLevelNav', true);

	}
	
	public function activate_files($ptID) {
		try {
			Loader::model("collection_types");
			$pt = PageTheme::getByID($ptID);
			$txt = Loader::helper('text');
			if (!is_array($this->post('pageTypes'))) {
				throw new Exception(t("You must specify at least one template to make into a page type."));
			}
			
			foreach($this->post('pageTypes') as $ptHandle) {
				$data['ctName'] = $txt->unhandle($ptHandle);
				$data['ctHandle'] = $ptHandle;
				$ct = CollectionType::add($data);
			}
			$this->set('message', t('Files in the theme were activated successfully.'));
		} catch(Exception $e) {
			$this->set('error', $e);
		}
		$this->view($ptID);
	}


	

}

?>