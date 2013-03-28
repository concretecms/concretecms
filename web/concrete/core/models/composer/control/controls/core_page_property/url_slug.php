<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_UrlSlugCorePagePropertyComposerControl extends CorePagePropertyComposerControl {
	
	public function __construct() {
		$this->setCorePagePropertyHandle('url_slug');
		$this->setComposerControlName(t('URL Slug'));
		$this->setComposerControlIconSRC(ASSETS_URL . '/models/attribute/types/text/icon.png');
	}

}