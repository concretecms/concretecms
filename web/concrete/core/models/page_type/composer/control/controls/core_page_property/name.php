<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_NameCorePagePropertyComposerControl extends CorePagePropertyComposerControl {
	
	protected $cmpControlRequiredByDefault = true;

	public function __construct() {
		$this->setCorePagePropertyHandle('name');
		$this->setComposerControlName(t('Page Name'));
		$this->setComposerControlIconSRC(ASSETS_URL . '/models/attribute/types/text/icon.png');
	}

	public function publishToPage(ComposerDraft $d, $data, $controls) {
		$this->addComposerControlRequestValue('cName', $data['name']);
		parent::publishToPage($d, $data, $controls);
	}

	public function validate($data, ValidationErrorHelper $e) {
		$vt = Loader::helper('validation/strings');
		if (!($vt->notempty($data['name']))) {
			$e->add(t('You must specify a page name.'));
		}
	}

	public function getRequestValue() {
		$data = parent::getRequestValue();
		$data['name'] = Loader::helper('security')->sanitizeString($data['name']);
		return $data;
	}

	public function getComposerControlDraftValue() {
		if (is_object($this->cmpDraftObject)) {
			$c = $this->cmpDraftObject->getComposerDraftCollectionObject();
			return $c->getCollectionName();
		}
	}
	

}