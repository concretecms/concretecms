<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_System_Basics_Icons extends DashboardBaseController {

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

	public function iphone_icon_saved() {
		$this->set('message', t("iPhone icon updated successfully."));	
		$this->view();
	}

	public function iphone_icon_removed() {
		$this->set('message', t("iPhone icon removed successfully."));	
		$this->view();
	}

	public function modern_icon_saved() {
		$this->set('message', t('Windows 8 icon updated successfully.'));
		$this->view();
	}

	public function modern_icon_removed() {
		$this->set('message', t('Windows 8 icon removed successfully.'));
		$this->view();
	}

	function update_modern_thumbnail() {
		if($this->token->validate('update_modern_thumbnail')) {
			if(intval($this->post('remove_icon')) == 1) {
				Config::save('MODERN_TILE_THUMBNAIL_FID', 0);
				$this->redirect('/dashboard/system/basics/icons/', 'modern_icon_removed');
			}
			else {
				Loader::library('file/importer');
				$fi = new FileImporter();
				$resp = $fi->import($_FILES['favicon_file']['tmp_name'], $_FILES['favicon_file']['name'], $fr);
				if(!($resp instanceof FileVersion)) {
					switch($resp) {
						case FileImporter::E_FILE_INVALID_EXTENSION:
							$this->error->add(t('Invalid file extension.'));
							break;
						case FileImporter::E_FILE_INVALID:
							$this->error->add(t('Invalid file.'));
							break;
					}
				}
				else {
					Config::save('MODERN_TILE_THUMBNAIL_FID', $resp->getFileID());
					Config::save('MODERN_TILE_THUMBNAIL_BGCOLOR', Loader::helper('security')->sanitizeString($this->post('favicon_bgcolor')));
					$this->redirect('/dashboard/system/basics/icons/', 'modern_icon_saved');
				}
			}
		}
		else {
			$this->set('error', array($this->token->getErrorMessage()));
		}
	}

	function update_iphone_thumbnail(){
		Loader::library('file/importer');
		if ($this->token->validate("update_iphone_thumbnail")) { 
		
			if(intval($this->post('remove_icon'))==1){
				Config::save('IPHONE_HOME_SCREEN_THUMBNAIL_FID',0);
					$this->redirect('/dashboard/system/basics/icons/', 'iphone_icon_removed');
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
				
					Config::save('IPHONE_HOME_SCREEN_THUMBNAIL_FID', $resp->getFileID());
					$filepath=$resp->getPath();  
					$this->redirect('/dashboard/system/basics/icons/', 'iphone_icon_saved');

				}
			}		
			
		}else{
			$this->set('error', array($this->token->getErrorMessage()));
		}
	}
	
	function update_favicon(){
		Loader::library('file/importer');
		if ($this->token->validate("update_favicon")) { 
		
			if(intval($this->post('remove_favicon'))==1){
				Config::save('FAVICON_FID',0);
					$this->redirect('/dashboard/system/basics/icons/', 'favicon_removed');
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
					$this->redirect('/dashboard/system/basics/icons/', 'favicon_saved');

				}
			}		
			
		}else{
			$this->set('error', array($this->token->getErrorMessage()));
		}
	}

}