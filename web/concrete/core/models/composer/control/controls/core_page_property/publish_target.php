<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_PublishTargetCorePagePropertyComposerControl extends CorePagePropertyComposerControl {
	
	public function __construct() {
		$this->setCorePagePropertyHandle('publish_target');
		$this->setComposerControlName(t('Publish Target'));
		$this->setComposerControlIconSRC(ASSETS_URL . '/models/attribute/types/image_file/icon.png');
	}

}