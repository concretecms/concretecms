<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_PageTemplateCorePagePropertyComposerControl extends CorePagePropertyComposerControl {
	
	public function __construct() {
		$this->setCorePagePropertyHandle('page_template');
		$this->setComposerControlName(t('Page Template'));
		$this->setComposerControlIconSRC(ASSETS_URL . '/models/attribute/types/select/icon.png');
	}

	public function composerFormControlSupportsValidation() {
		return false;
	}


	public function publishToPage(ComposerDraft $d, $data, $controls) {
		$this->addComposerControlRequestValue('pTemplateID', $_POST['cmpPageTemplateID']);
		parent::publishToPage($d, $data, $controls);
	}

	public function getComposerControlDraftValue() {
		if (is_object($this->cmpDraftObject)) {
			$c = $this->cmpDraftObject->getComposerDraftCollectionObject();
			return $c->getPageTemplateID();
		}
	}
	

}