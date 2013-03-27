<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_CorePagePropertyComposerControlType extends ComposerControlType {

	public function getComposerControlObjects() {
		$objects = array(
			new CorePagePropertyComposerControl('name', t('Page Name'), ASSETS_URL . '/models/attribute/types/text/icon.png'),
			new CorePagePropertyComposerControl('url_slug', t('URL Slug'), ASSETS_URL . '/models/attribute/types/text/icon.png'),
			new CorePagePropertyComposerControl('date', t('Date/Time'), ASSETS_URL . '/models/attribute/types/text/icon.png'),
			new CorePagePropertyComposerControl('description', t('Short Description'), ASSETS_URL . '/models/attribute/types/text/icon.png'),
			new CorePagePropertyComposerControl('user', t('User'), ASSETS_URL . '/models/attribute/types/text/icon.png'),
		);
		return $objects;
	}

	public function getComposerControlByIdentifier($identifier) {

	}
	
}