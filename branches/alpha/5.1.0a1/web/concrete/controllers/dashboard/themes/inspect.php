<?

class DashboardThemesInspectController extends Controller {

	protected $helpers = array('html');

	// grab all the page types from within a theme	
	public function view($ptID = null, $isOnInstall = false) {
		if (!$ptID) {
			$this->redirect('/dashboard/themes/');
		}
		
		$v = Loader::helper('validation/error');
		$pt = PageTheme::getByID($ptID);
		if (is_object($pt)) {
			$files = $pt->getFilesInTheme();
			$this->set('files', $files);
			$this->set('ptID', $ptID);
		} else {
			$v->add('Invalid Theme');
		}	
		
		if ($isOnInstall) {
			$this->set('message', "Theme installed. You may automatically create page types from template files contained in your theme using the form below, or click \"return to themes\" to return to your theme list.");
		}
		
		if ($v->has()) {
			$this->set('error', $v);
		}
	}
	
	public function activate_files($ptID) {
		try {
			Loader::model("collection_types");
			$pt = PageTheme::getByID($ptID);
			$txt = Loader::helper('text');
			if (!is_array($this->post('pageTypes'))) {
				throw new Exception("You must specify at least one template to make into a page type.");
			}
			
			foreach($this->post('pageTypes') as $ptHandle) {
				$data['ctName'] = $txt->unhandle($ptHandle);
				$data['ctHandle'] = $ptHandle;
				$ct = CollectionType::add($data);
			}
			$this->set('message', 'Files in the theme were activated successfully. <a href="' . View::url('/dashboard/themes/') . '">Click here to return to your list of themes.</a>');
		} catch(Exception $e) {
			$this->set('error', $e);
		}
		$this->view($ptID);
	}


	

}

?>