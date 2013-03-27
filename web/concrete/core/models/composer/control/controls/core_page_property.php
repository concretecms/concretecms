<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_CorePagePropertyComposerControl extends ComposerControl {

	protected $propertyHandle;
	
	public function __construct($propertyHandle, $cmpControlName, $cmpControlIconSRC) {
		$this->setCorePagePropertyHandle($propertyHandle);
		$this->setComposerControlName($cmpControlName);
		$this->setComposerControlIconSRC($cmpControlIconSRC);
	}

	public function setCorePagePropertyHandle($propertyHandle) {
		$this->setComposerControlIdentifier($propertyHandle);
		$this->propertyHandle = $propertyHandle;
	}

	public function getCorePagePropertyHandle() {
		return $this->propertyHandle;
	}
	
}