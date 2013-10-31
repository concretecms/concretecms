<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_NameCorePagePropertyPageTypeComposerControl extends CorePagePropertyPageTypeComposerControl {
	
	protected $ptComposerControlRequiredByDefault = true;

	public function __construct() {
		$this->setCorePagePropertyHandle('name');
		$this->setPageTypeComposerControlName(t('Page Name'));
		$this->setPageTypeComposerControlIconSRC(ASSETS_URL . '/models/attribute/types/text/icon.png');
	}

	public function publishToPage(Page $c, $data, $controls) {
		$this->addPageTypeComposerControlRequestValue('cName', $data['name']);
		parent::publishToPage($c, $data, $controls);
	}

	public function validate($data, ValidationErrorHelper $e) {
		$vt = Loader::helper('validation/strings');
		if (!($vt->notempty($data['name']))) {
			$e->add(t('You must specify a page name.'));
		}
	}

	public function getRequestValue() {
		$data = parent::getRequestValue();
		$data['name'] = Loader::helper('security')->sanitizeString($data['name']);
		return $data;
	}

	public function getPageTypeComposerControlDraftValue() {
		if (is_object($this->page)) {
			$c = $this->page;
			return $c->getCollectionName();
		}
	}
	

}