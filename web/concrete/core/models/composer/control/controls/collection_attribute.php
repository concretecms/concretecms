<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_CollectionAttributeComposerControl extends ComposerControl {
	
	protected $akID;
	protected $cmpControlTypeHandle = 'collection_attribute';
	
	public function setAttributeKeyID($akID) {
		$this->akID = $akID;
		$this->setComposerControlIdentifier($akID);
	}

	public function getAttributeKeyID() {
		return $this->akID;
	}

	public function getComposerControlCustomTemplates() {
		return array();
	}

	public function render($label, $customTemplate) {
		$ak = CollectionAttributeKey::getByID($this->akID);
		$env = Environment::get();
		$template = $env->getPath(DIRNAME_ELEMENTS . '/' . DIRNAME_COMPOSER . '/' . DIRNAME_COMPOSER_ELEMENTS_CONTROLS . '/' . $this->cmpControlTypeHandle . '.php');
		include($template);
	}

	public function publishToPage(Page $c, $data, $controls) {
	}


}