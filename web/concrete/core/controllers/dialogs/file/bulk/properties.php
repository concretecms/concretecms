<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dialogs_File_Bulk_Properties extends BackendInterfaceController {

	protected $viewPath = '/system/dialogs/file/bulk/properties';
	protected $files = array();
	protected $canEdit = false;

	protected function canAccess() {
		$fp = FilePermissions::GetGlobal();
		$this->populateFiles();
		return $this->canEdit;
	}

	protected function populateFiles() {
		if (is_array($_REQUEST['item'])) {
			foreach($_REQUEST['item'] as $fID) {
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



/*	public function update_extended_attribute() {
		$fakID = $_REQUEST['cakID'];
		$value = ''; 
		
		$ak = CollectionAttributeKey::get($cakID);
		foreach($files as $c) {
			$cp = new Permissions($c);
			if ($cp->canEditFileProperties($ak)) {
				$ak->saveAttributeForm($c);
				$c->reindex();
			}
		}
		$val = $c->getAttributeValueObject($ak);
		print $val->getValue('display');	
		exit;
	} 

	public function clear_extended_attribute() {
		$cakID = $_REQUEST['cakID'];
		$value = ''; 
		
		$ak = CollectionAttributeKey::get($cakID);
		foreach($files as $c) {
			$cp = new Permissions($c);
			if ($cp->canEditFileProperties($ak)) {
				$c->clearAttribute($ak);
				$c->reindex();
			}
		}
		
		print '<div class="ccm-attribute-field-none">' . t('None') . '</div>';
		exit;
	}
	*/



}

