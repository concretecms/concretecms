<?
defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardFilesAccessController extends Controller {

	var $helpers = array('form','concrete/interface','validation/token', 'concrete/file');
	
	public function view($updated=false) {
		$helper_file = Loader::helper('concrete/file');
		
		$file_access_file_types = Config::get('UPLOAD_FILE_EXTENSIONS_ALLOWED');
		//is nothing's been defined, display the constant value
		if (!$file_access_file_types) {
			$file_access_file_types = $helper_file->unserializeUploadFileExtensions(UPLOAD_FILE_EXTENSIONS_ALLOWED);
		}
		else {
			$file_access_file_types = $helper_file->unserializeUploadFileExtensions($file_access_file_types);		
		}
		$file_access_file_types = join(', ',$file_access_file_types);		
		$this->set('file_access_file_types', $file_access_file_types);		
		
		switch ($updated) {
			case 'extensions-saved':
				$this->set('message',t('Changes Saved'));
				break;
		}
	}
	
	public function file_access_extensions(){
		$helper_file = Loader::helper('concrete/file');
		$validation_token = Loader::helper('validation/token');
		
		if (!$validation_token->validate("file_access_extensions")) {
			$this->set('error', array($validation_token->getErrorMessage()));
			return;
		}
		
		$types = preg_split('{,}',$this->post('file-access-file-types'),null,PREG_SPLIT_NO_EMPTY);
		$types = $helper_file->serializeUploadFileExtensions($types);
		Config::save('UPLOAD_FILE_EXTENSIONS_ALLOWED',$types);
		$this->redirect('/dashboard/files/access','extensions-saved');
	}
}

?>