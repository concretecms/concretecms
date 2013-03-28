<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_DateTimeCorePagePropertyComposerControl extends CorePagePropertyComposerControl {
	
	public function __construct() {
		$this->setCorePagePropertyHandle('date_time');
		$this->setComposerControlName(t('Public Date/Time'));
		$this->setComposerControlIconSRC(ASSETS_URL . '/models/attribute/types/date_time/icon.png');
	}

}