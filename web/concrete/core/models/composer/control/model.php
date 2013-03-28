<?php defined('C5_EXECUTE') or die("Access Denied.");

abstract class Concrete5_Model_ComposerControl extends Object {

	protected $cmpControlIdentifier;
	protected $cmpControlName;
	protected $cmpControlIconSRC;
	protected $cmpControl;

	abstract public function getComposerControlCustomTemplates();

	public function setComposerControlName($cmpControlName) {
		$this->cmpControlName = $cmpControlName;
	}
	
	public function getComposerControlName() {
		return $this->cmpControlName;
	}

	public function setComposerControlIconSRC($cmpControlIconSRC) {
		$this->cmpControlIconSRC = $cmpControlIconSRC;
	}
	
	public function getComposerControlIconSRC() {
		return $this->cmpControlIconSRC;
	}

	public function setComposerControlIdentifier($cmpControlIdentifier) {
		$this->cmpControlIdentifier = $cmpControlIdentifier;
	}

	public function getComposerControlIdentifier() {
		return $this->cmpControlIdentifier;
	}

	public function getComposerControlTypeObject() {
		return ComposerControlType::getByHandle($this->cmpControlTypeHandle);
	}
	
}