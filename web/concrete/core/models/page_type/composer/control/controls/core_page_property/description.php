<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_DescriptionCorePagePropertyPageTypeComposerControl extends CorePagePropertyPageTypeComposerControl {
	
	public function __construct() {
		$this->setCorePagePropertyHandle('description');
		$this->setPageTypeComposerControlName(t('Description'));
		$this->setPageTypeComposerControlIconSRC(ASSETS_URL . '/models/attribute/types/textarea/icon.png');
	}

	public function publishToPage(Page $c, $data, $controls) {
		$this->addPageTypeComposerControlRequestValue('cDescription', $data['description']);
		parent::publishToPage($c, $data, $controls);
	}

	public function validate($data, ValidationErrorHelper $e) {
		$vt = Loader::helper('validation/strings');
		if (!($vt->notempty($data['description']))) {
			$e->add(t('You must specify a page description.'));
		}
	}

	public function getRequestValue() {
		$data = parent::getRequestValue();
		$data['description'] = Loader::helper('security')->sanitizeString($data['description']);
		return $data;
	}

	public function getPageTypeComposerControlDraftValue() {
		if (is_object($this->page)) {
			$c = $this->page;
			return $c->getCollectionDescription();
		}
	}
	


}