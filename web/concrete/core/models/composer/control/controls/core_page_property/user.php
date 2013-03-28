<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_UserCorePagePropertyComposerControl extends CorePagePropertyComposerControl {
	
	public function __construct() {
		$this->setCorePagePropertyHandle('user');
		$this->setComposerControlName(t('User'));
		$this->setComposerControlIconSRC(ASSETS_URL . '/models/attribute/types/text/icon.png');
	}

}