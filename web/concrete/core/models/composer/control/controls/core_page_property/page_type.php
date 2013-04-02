<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_PageTypeCorePagePropertyComposerControl extends CorePagePropertyComposerControl {
	
	public function __construct() {
		$this->setCorePagePropertyHandle('page_type');
		$this->setComposerControlName(t('Page Type'));
		$this->setComposerControlIconSRC(ASSETS_URL . '/models/attribute/types/select/icon.png');
	}

	public function composerFormControlSupportsValidation() {
		return false;
	}



}