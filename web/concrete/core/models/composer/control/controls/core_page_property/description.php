<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_DescriptionCorePagePropertyComposerControl extends CorePagePropertyComposerControl {
	
	public function __construct() {
		$this->setCorePagePropertyHandle('description');
		$this->setComposerControlName(t('Description'));
		$this->setComposerControlIconSRC(ASSETS_URL . '/models/attribute/types/textarea/icon.png');
	}

	public function publishToPage(ComposerDraft $d, $data, $controls) {
		$this->addComposerControlRequestValue('cDescription', $data['description']);
		parent::publishToPage($d, $data, $controls);
	}

	public function validate($data, ValidationErrorHelper $e) {
		$vt = Loader::helper('validation/strings');
		if (!($vt->notempty($data['description']))) {
			$e->add(t('You must specify a page description.'));
		}
	}

	public function getRequestValue() {
		$data = parent::getRequestValue();
		$data['description'] = Loader::helper('security')->sanitizeString($data['description']);
		return $data;
	}

	public function getComposerControlDraftValue() {
		if (is_object($this->cmpDraftObject)) {
			$c = $this->cmpDraftObject->getComposerDraftCollectionObject();
			return $c->getCollectionDescription();
		}
	}
	


}