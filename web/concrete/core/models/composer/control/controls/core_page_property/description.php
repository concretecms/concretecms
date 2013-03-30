<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_DescriptionCorePagePropertyComposerControl extends CorePagePropertyComposerControl {
	
	public function __construct() {
		$this->setCorePagePropertyHandle('description');
		$this->setComposerControlName(t('Description'));
		$this->setComposerControlIconSRC(ASSETS_URL . '/models/attribute/types/textarea/icon.png');
	}

	public function publishToPage(Page $c, $data, $controls) {
		$this->addComposerControlRequestValue('cDescription', $data['description']);
		parent::publishToPage($c, $data, $controls);
	}


}