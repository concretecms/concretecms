<?

defined('C5_EXECUTE') or die("Access Denied.");
class DashboardSystemAppearanceIconsController extends DashboardBaseController {

	public function view() {
		$this->set('site', SITE);
	}

	public function favicon_saved() {
		$this->set('message', t("Icons updated successfully."));	
		$this->view();
	}

	public function favicon_removed() {
		$this->set('message', t("Icon removed successfully."));	
		$this->view();
	}
	
	function update_favicon(){
		Loader::library('file/importer');
		if ($this->token->validate("update_favicon")) { 
		
			if(intval($this->post('remove_favicon'))==1){
				Config::save('FAVICON_FID',0);
					$this->redirect('/dashboard/system/appearance/icons/', 'favicon_removed');
			} else {
				$fi = new FileImporter();
				$resp = $fi->import($_FILES['favicon_file']['tmp_name'], $_FILES['favicon_file']['name'], $fr);
				if (!($resp instanceof FileVersion)) {
					switch($resp) {
						case FileImporter::E_FILE_INVALID_EXTENSION:
							$this->error->add(t('Invalid file extension.'));
							break;
						case FileImporter::E_FILE_INVALID:
							$this->error->add(t('Invalid file.'));
							break;
						
					}
				} else {
				
					Config::save('FAVICON_FID', $resp->getFileID());
					$filepath=$resp->getPath();  
					//@copy($filepath, DIR_BASE.'/favicon.ico');
					$this->redirect('/dashboard/system/appearance/icons/', 'favicon_saved');

				}
			}		
			
		}else{
			$this->set('error', array($this->token->getErrorMessage()));
		}
	}

}