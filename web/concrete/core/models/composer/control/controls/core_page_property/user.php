<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_UserCorePagePropertyComposerControl extends CorePagePropertyComposerControl {
	
	public function __construct() {
		$this->setCorePagePropertyHandle('user');
		$this->setComposerControlName(t('User'));
		$this->setComposerControlIconSRC(ASSETS_URL . '/models/attribute/types/text/icon.png');
	}

	public function publishToPage(ComposerDraft $d, $data, $controls) {
		$this->addComposerControlRequestValue('uID', $data['user']);
		parent::publishToPage($d, $data, $controls);
	}

	public function validate($data, ValidationErrorHelper $e) {
		if (Loader::helper('validation/numbers')->integer($data['user'])) {
			$ux = UserInfo::getByID($data['user']);
		}
		if (!is_object($ux)) {
			$e->add(t('You must specify a valid user.'));
		}
	}


}