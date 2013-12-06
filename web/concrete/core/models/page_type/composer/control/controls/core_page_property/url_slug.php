<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_UrlSlugCorePagePropertyPageTypeComposerControl extends CorePagePropertyPageTypeComposerControl {
	
	public function __construct() {
		$this->setCorePagePropertyHandle('url_slug');
		$this->setPageTypeComposerControlName(t('URL Slug'));
		$this->setPageTypeComposerControlIconSRC(ASSETS_URL . '/models/attribute/types/text/icon.png');
	}

	public function publishToPage(Page $c, $data, $controls) {
		$this->addPageTypeComposerControlRequestValue('cHandle', $data['url_slug']);
		parent::publishToPage($c, $data, $controls);
	}

	public function validate() {
		$e = Loader::helper('validation/error');
		$handle = $this->getPageTypeComposerControlDraftValue();
		if (!$handle) {
			$e->add(t('You must specify a URL slug.'));
			return $e;
		}
	}

	public function getPageTypeComposerControlDraftValue() {
		if (is_object($this->page)) {
			$c = $this->page;
			return $c->getCollectionHandle();
		}
	}
	


}