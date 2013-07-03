<?php defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_System_Conversations_Settings extends Controller {

	public function view() {
		$helperFile = Loader::helper('concrete/file');
		$fileAccessFileTypes = Config::get('CONVERSATIONS_ALLOWED_FILE_TYPES');
		//is nothing's been defined, display the constant value
		if (!$fileAccessFileTypes) {
			$fileAccessFileTypes = $helperFile->unserializeUploadFileExtensions(UPLOAD_FILE_EXTENSIONS_ALLOWED);
		}
		else {
			$fileAccessFileTypes = $helperFile->unserializeUploadFileExtensions($fileAccessFileTypes);
		}
		$fileAccessFileTypes = join(', ',$fileAccessFileTypes);
		$this->set('file_access_file_types', $fileAccessFileTypes);
		$this->set('maxFileSizeGuest', Config::get('CONVERSATIONS_MAX_FILE_SIZE_GUEST'));
		$this->set('maxFileSizeRegistered', Config::get('CONVERSATIONS_MAX_FILE_SIZE_REGISTERED'));
		$this->set('maxFilesGuest', Config::get('CONVERSATIONS_MAX_FILES_GUEST'));
		$this->set('maxFilesRegistered', Config::get('CONVERSATIONS_MAX_FILES_REGISTERED'));
		$this->set('fileExtensions', implode(',', $fileAccessFileTypes));
	}

	public function success() {
		$this->view();
		$this->set('message','Updated conversations settings.');
	}

	public function save() {
		$helper_file = Loader::helper('concrete/file');
		if ($this->post('maxFileSizeGuest')) {
			Config::save('CONVERSATIONS_MAX_FILE_SIZE_GUEST', $this->post('maxFileSizeGuest')); 
		}
		if($this->post('maxFileSizeRegistered')){
			Config::save('CONVERSATIONS_MAX_FILE_SIZE_REGISTERED', $this->post('maxFileSizeRegistered'));
		}
		if($this->post('maxFilesGuest')) {
			Config::save('CONVERSATIONS_MAX_FILES_GUEST', $this->post('maxFilesGuest'));
		}
		if($this->post('maxFilesGuest')){
			Config::save('CONVERSATIONS_MAX_FILES_REGISTERED', $this->post('maxFilesRegistered')) ;
		}
		if ($this->post('fileExtensions')){
			$types = preg_split('{,}',$this->post('fileExtensions'),null,PREG_SPLIT_NO_EMPTY);
			$types = $helper_file->serializeUploadFileExtensions($types);
			Config::save('CONVERSATIONS_ALLOWED_FILE_TYPES',$types);
		} 
		$this->success();
	}

}