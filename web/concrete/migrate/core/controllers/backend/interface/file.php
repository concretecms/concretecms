<?php
defined('C5_EXECUTE') or die("Access Denied.");

abstract class Concrete5_Controller_Backend_Interface_File extends BackendInterfaceController {

	protected $page;

	public function __construct() {
		parent::__construct();
		$request = $this->request;
		$fID = Loader::helper('security')->sanitizeInt($request->query->get('fID'));
		if ($fID) {
			$file = File::getByID($fID);
			if (is_object($file) && !$file->isError()) {
				$this->setFileObject($file);
			} else {
				throw new Exception(t('Invalid file.'));
			}
		}
	}

	public function setFileObject(File $f) {
		$this->file = $f;
		$this->permissions = new Permissions($this->file);		
		$this->set('f', $this->file);
		$this->set('fp', $this->permissions);
	}

	public function getViewObject() {
		if ($this->permissions->canViewFileInFileManager()) {
			return parent::getViewObject();
		}
		throw new Exception(t('Access Denied'));
	}

	public function action() {
		$url = call_user_func_array('parent::action', func_get_args());
		$url .= '&fID=' . $this->file->getFileID();
		return $url;
	}

}
	
