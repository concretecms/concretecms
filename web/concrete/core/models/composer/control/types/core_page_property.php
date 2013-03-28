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

	protected function getCorePropertyNameFromIdentifier($identifier) {
		$properties = array(
			'name' => t('Page Name'),
			'url_slug' => t('URL Slug'),
			'date' => t('Date/Time'),
			'description' => t('Short Description'),
			'user' => t('User')
		);
		return $properties[$identifier];
	}

	public function getComposerControlByIdentifier($identifier) {
		return new CorePagePropertyComposerControl($identifier, $this->getCorePropertyNameFromIdentifier($identifier), ASSETS_URL . '/models/attribute/types/text/icon.png');
	}

	
}