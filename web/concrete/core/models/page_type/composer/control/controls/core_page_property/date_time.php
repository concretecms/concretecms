<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_DateTimeCorePagePropertyComposerControl extends CorePagePropertyComposerControl {
	
	public function __construct() {
		$this->setCorePagePropertyHandle('date_time');
		$this->setComposerControlName(t('Public Date/Time'));
		$this->setComposerControlIconSRC(ASSETS_URL . '/models/attribute/types/date_time/icon.png');
	}

	public function publishToPage(ComposerDraft $d, $data, $controls) {
		$this->addComposerControlRequestValue('cDatePublic', Loader::helper('form/date_time')->translate('date_time', $data));
		parent::publishToPage($d, $data, $controls);
	}

	public function validate($data, ValidationErrorHelper $e) {
		$date = Loader::helper('form/date_time')->translate('date_time', $data);
		if (!strtotime($date)) {
			$e->add(t('You must specify a valid date/time for this page.'));
		}
	}

	public function getComposerControlDraftValue() {
		$c = $this->cmpDraftObject->getComposerDraftCollectionObject();
		return $c->getCollectionDatePublic();
	}
	


}