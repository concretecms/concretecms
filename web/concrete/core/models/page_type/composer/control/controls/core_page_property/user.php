<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_UserCorePagePropertyPageTypeComposerControl extends CorePagePropertyPageTypeComposerControl {
	
	public function __construct() {
		$this->setCorePagePropertyHandle('user');
		$this->setPageTypeComposerControlName(t('User'));
		$this->setPageTypeComposerControlIconSRC(ASSETS_URL . '/models/attribute/types/text/icon.png');
	}

	public function publishToPage(PageTypeComposerDraft $d, $data, $controls) {
		$this->addPageTypeComposerControlRequestValue('uID', $data['user']);
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

	public function getPageTypeComposerControlDraftValue() {
		if (is_object($this->pDraftObject)) {
			$c = $this->pDraftObject->getPageDraftCollectionObject();
			return $c->getCollectionUserID();
		}
	}
	


}