<?php
namespace Concrete\Controller\Dialog\File\Bulk;
use \Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use FilePermissions;
use \Concrete\Core\Http\ResponseAssetGroup;
use \Concrete\Core\File\EditResponse as FileEditResponse;
use FileAttributeKey;
use Permissions;
use Loader;
use File;

class Properties extends BackendInterfaceController {

	protected $viewPath = '/dialogs/file/bulk/properties';
	protected $files = array();
	protected $canEdit = false;

	protected function canAccess() {
		$fp = FilePermissions::GetGlobal();
		$this->populateFiles();
		return $this->canEdit;
	}

	protected function populateFiles() {
		if (is_array($_REQUEST['fID'])) {
			foreach($_REQUEST['fID'] as $fID) {
				$f = File::getByID($fID);
				if (is_object($f) && !$f->isError()) {
					$this->files[] = $f;
				}
			}
		}

		if (count($this->files) > 0) {
			$this->canEdit = true;
			foreach($this->files as $f) {
				$fp = new Permissions($f);
				if (!$fp->canEditFileProperties()) {
					$this->canEdit = false;
				}
			}
		} else {
			$this->canEdit = false;
		}

		return $this->canEdit;
	}

	public function view() {
		$r = ResponseAssetGroup::get();
		$r->requireAsset('core/app/editable-fields');
		$this->populateFiles();
		$form = Loader::helper('form');
		$attribs = FileAttributeKey::getList();
		$this->set('files', $this->files);
		$this->set('attributes', $attribs);
	}

	public function updateAttribute() {
		$fr = new FileEditResponse();
		$ak = FileAttributeKey::get($_REQUEST['name']);
		if ($this->validateAction()) {
			$this->populateFiles();
			if ($this->canEdit) {
				foreach($this->files as $f) {
					$ak->saveAttributeForm($f);
					$f->reindex();
				}


				$fr->setFiles($this->files);
				$val = $f->getAttributeValueObject($ak);
				$fr->setAdditionalDataAttribute('value',  $val->getValue('displaySanitized','display'));
				$fr->setMessage(t('Files updated successfully.'));
			}
		}
		$fr->outputJSON();
	}

	public function clearAttribute() {
		$fr = new FileEditResponse();
		$ak = FileAttributeKey::get($_REQUEST['akID']);
		if ($this->validateAction()) {
			$this->populateFiles();
			if ($this->canEdit) {
				foreach($this->files as $f) {
					$f->clearAttribute($ak);			
					$f->reindex();
				}
				$fr->setFiles($this->files);
				$fr->setAdditionalDataAttribute('value',  false);
				$fr->setMessage(t('Attributes cleared successfully.'));
			}
		}
		$fr->outputJSON();

	}


}

