<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dialogs_File_Properties extends BackendInterfaceFileController {

	protected $viewPath = '/system/dialogs/file/properties';

	protected function canAccess() {
		return $this->permissions->canViewFileInFileManager();
	}

	public function view() {
		$r = ResponseAssetGroup::get();
		$r->requireAsset('core/app/editable-fields');

		if (isset($_REQUEST['fvID'])) {
			$fv = $this->file->getVersion(Loader::helper('security')->sanitizeInt($_REQUEST['fvID']));
		} else {
			$fv = $this->file->getApprovedVersion();
		}

		$previewMode = false;
		$this->set('previewMode', $previewMode);
		$this->set('fv', $fv);
		$this->set('dateHelper', Loader::helper('date'));
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

