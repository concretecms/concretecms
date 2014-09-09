<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Conversations;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Loader;

class Settings extends DashboardPageController {

	public function view() {
		$helperFile = Loader::helper('concrete/file');
		$fileAccessFileTypes = Config::get('conversations.files.allowed_types');
		//is nothing's been defined, display the constant value
		if (!$fileAccessFileTypes) {
			$fileAccessFileTypes = $helperFile->unserializeUploadFileExtensions(Config::get('concrete.upload.extensions'));
		}
		else {
			$fileAccessFileTypes = $helperFile->unserializeUploadFileExtensions($fileAccessFileTypes);
		}
		$fileAccessFileTypes = join(', ',$fileAccessFileTypes);
		$this->set('file_access_file_types', $fileAccessFileTypes);
		$this->set('maxFileSizeGuest', Config::get('conversations.files.guest.max_size'));
		$this->set('maxFileSizeRegistered', Config::get('conversations.files.registered.max_size'));
		$this->set('maxFilesGuest', Config::get('conversations.files.guest.max'));
		$this->set('maxFilesRegistered', Config::get('conversations.files.registered.max'));
		$this->set('fileExtensions', implode(',', $fileAccessFileTypes));
	}

	public function success() {
		$this->view();
		$this->set('message','Updated conversations settings.');
	}

	public function save() {
		$helper_file = Loader::helper('concrete/file');
		if ($this->post('maxFileSizeGuest')) {
			Config::save('conversations.files.guest.max_size', $this->post('maxFileSizeGuest'));
		}
		if($this->post('maxFileSizeRegistered')){
			Config::save('conversations.files.registered.max_size', $this->post('maxFileSizeRegistered'));
		}
		if($this->post('maxFilesGuest')) {
			Config::save('conversations.files.guest.max', $this->post('maxFilesGuest'));
		}
		if($this->post('maxFilesGuest')){
			Config::save('conversations.files.registered.max', $this->post('maxFilesRegistered')) ;
		}
		if ($this->post('fileExtensions')){
			$types = preg_split('{,}',$this->post('fileExtensions'),null,PREG_SPLIT_NO_EMPTY);
			$types = $helper_file->serializeUploadFileExtensions($types);
			Config::save('conversations.files.allowed_types',$types);
		}
		$this->success();
	}

}
