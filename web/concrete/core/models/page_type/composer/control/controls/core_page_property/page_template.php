<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_PageTemplateCorePagePropertyPageTypeComposerControl extends CorePagePropertyPageTypeComposerControl {
	
	public function __construct() {
		$this->setCorePagePropertyHandle('page_template');
		$this->setPageTypeComposerControlName(t('Page Template'));
		$this->setPageTypeComposerControlIconSRC(ASSETS_URL . '/models/attribute/types/select/icon.png');
	}

	public function pageTypeComposerFormControlSupportsValidation() {
		return false;
	}


	public function publishToPage(PageDraft $d, $data, $controls) {
		$this->addPageTypeComposerControlRequestValue('pTemplateID', $_POST['ptComposerPageTemplateID']);
		parent::publishToPage($d, $data, $controls);
	}

	public function getPageTypeComposerControlDraftValue() {
		if (is_object($this->pDraftObject)) {
			$c = $this->pDraftObject->getPageDraftCollectionObject();
			return $c->getPageTemplateID();
		}
	}
	

}