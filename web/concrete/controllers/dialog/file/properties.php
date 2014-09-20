<?php
namespace Concrete\Controller\Dialog\File;

use \Concrete\Controller\Backend\UserInterface\File as BackendInterfaceFileController;
use \Concrete\Core\Http\ResponseAssetGroup;
use Permissions;
use File;
use FileAttributeKey;
use \Concrete\Core\File\EditResponse as FileEditResponse;
use Loader;
use Exception;

class Properties extends BackendInterfaceFileController {

	protected $viewPath = '/dialogs/file/properties';

	protected function canAccess() {
		return $this->permissions->canViewFileInFileManager();
	}

	public function view() {
		$r = ResponseAssetGroup::get();
		$r->requireAsset('core/app/editable-fields');
		
		if (isset($_REQUEST['fvID'])) {
			$fv = $this->file->getVersion(Loader::helper('security')->sanitizeInt($_REQUEST['fvID']));
			$this->set('previewMode', true);
		} else {
			$fv = $this->file->getApprovedVersion();
			$this->set('previewMode', false);
		}

		$this->set('fv', $fv);
		$this->set('form', Loader::helper('form'));
		$this->set('dateHelper', Loader::helper('date'));
	}

	public function clear_attribute() {
		if ($this->validateAction()) {
			$fp = new Permissions($this->file);
			if ($fp->canEditFileProperties()) {
				$fv = $this->file->getVersionToModify();

				$ak = FileAttributeKey::get($_REQUEST['akID']);
				$fv->clearAttribute($ak);

				$sr = new FileEditResponse();
				$sr->setFile($this->file);
				$sr->setMessage(t('Attribute cleared successfully.'));
				$sr->outputJSON();
			}
		}

		throw new Exception(t('Access Denied'));

	}

	public function update_attribute() {
		if ($this->validateAction()) {
			$fp = new Permissions($this->file);
			if ($fp->canEditFileProperties()) {
				$fv = $this->file->getVersionToModify();

				$ak = FileAttributeKey::get($_REQUEST['name']);
				$ak->saveAttributeForm($fv);

				$file = File::getByID($this->file->getFileID());
				$val = $file->getAttributeValueObject($ak); // ugh this is some kind of race condition or cache issue.

				$sr = new FileEditResponse();
				$sr->setFile($this->file);
				$sr->setMessage(t('Attribute saved successfully.'));
				$sr->setAdditionalDataAttribute('value',  $val->getValue('displaySanitized','display'));
				$sr->outputJSON();
			}
		}

		throw new Exception(t('Access Denied'));

	}

	public function save() {
		if ($this->validateAction()) {
			$fp = new Permissions($this->file);
			if ($fp->canEditFileProperties()) {
				$fv = $this->file->getVersionToModify();
				$value = $this->request->request->get('value');
				switch($this->request->request->get('name')) {
					case 'fvTitle':
						$fv->updateTitle($value);
						break;
					case 'fvDescription':
						$fv->updateDescription($value);
						break;
					case 'fvTags':
						$fv->updateTags($value);
						break;
				}

				$sr = new FileEditResponse();
				$sr->setFile($this->file);
				$sr->setMessage(t('File updated successfully.'));
				$sr->setAdditionalDataAttribute('value', $value);
				$sr->outputJSON();


			} else {
				throw new Exception(t('Access Denied.'));
			}
		} else {
			throw new Exception(t('Access Denied.'));
		}
	}

}

