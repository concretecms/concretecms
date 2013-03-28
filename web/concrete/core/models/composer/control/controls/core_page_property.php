<?php defined('C5_EXECUTE') or die("Access Denied.");

abstract class Concrete5_Model_CorePagePropertyComposerControl extends ComposerControl {

	protected $propertyHandle;
	protected $cmpControlTypeHandle = 'core_page_property';

	public function setCorePagePropertyHandle($propertyHandle) {
		$this->setComposerControlIdentifier($propertyHandle);
		$this->propertyHandle = $propertyHandle;
	}

	public function getCorePagePropertyHandle() {
		return $this->propertyHandle;
	}
	
	public function getComposerControlCustomTemplates() {
		return array();
	}

	public function render($label, $customTemplate) {
		$env = Environment::get();
		$form = Loader::helper('form');
		if ($customTemplate) {
			$rec = $env->getRecord(DIRNAME_ELEMENTS . '/' . DIRNAME_COMPOSER . '/' . DIRNAME_COMPOSER_ELEMENTS_CONTROLS . '/' . $this->cmpControlTypeHandle . '/' . $this->propertyHandle . '/' . DIRNAME_BLOCK_TEMPLATES_COMPOSER . '/' . $customTemplate);
			if ($rec->exists()) {
				$template = $rec->getPath();
			}
		}

		if (!isset($template)) {
			$template = $env->getPath(DIRNAME_ELEMENTS . '/' . DIRNAME_COMPOSER . '/' . DIRNAME_COMPOSER_ELEMENTS_CONTROLS . '/' . $this->cmpControlTypeHandle . '/' . $this->propertyHandle . '.php');
		}

		include($template);
	}

}