<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_UrlSlugCorePagePropertyComposerControl extends CorePagePropertyComposerControl {
	
	public function __construct() {
		$this->setCorePagePropertyHandle('url_slug');
		$this->setComposerControlName(t('URL Slug'));
		$this->setComposerControlIconSRC(ASSETS_URL . '/models/attribute/types/text/icon.png');
	}

	public function publishToPage(ComposerDraft $d, $data, $controls) {
		$this->addComposerControlRequestValue('cHandle', $data['url_slug']);
		parent::publishToPage($d, $data, $controls);
	}

	public function validate($data, ValidationErrorHelper $e) {
		$vt = Loader::helper('validation/strings');
		if (!($vt->notempty($data['url_slug']))) {
			$e->add(t('You must specify a URL slug.'));
		}
	}


}