<?php defined('C5_EXECUTE') or die("Access Denied.");

abstract class Concrete5_Model_CorePagePropertyComposerControl extends ComposerControl {

	protected $propertyHandle;
	protected $cmpControlTypeHandle = 'core_page_property';
	private static $cmpSaveRequest = null;
	private static $cmpRequestControlsProcessed = array();
	
	public function addAssetsToRequest(Controller $cnt) {

	}

	public static function getComposerSaveRequest() {
		if (null === self::$cmpSaveRequest) {
			self::$cmpSaveRequest = array();
		}
		return self::$cmpSaveRequest;
	}

	public function composerFormControlSupportsValidation() {
		return true;
	}

	public function addComposerControlRequestValue($key, $value) {
		self::$cmpSaveRequest[$key] = $value;
	}	

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

	public function publishToPage(ComposerDraft $d, $data, $controls) {
		$c = $d->getComposerDraftCollectionObject();
		array_push(self::$cmpRequestControlsProcessed, $this);
		// now we check to see if we have any more core controls to process in this request
		$coreControls = array();
		foreach($controls as $cnt) {
			if ($cnt->getComposerControlTypeHandle() == $this->cmpControlTypeHandle) {
				$coreControls[] = $controls;
			}
		}
		if (count(self::$cmpRequestControlsProcessed) == count($coreControls)) {
			// this was the last one. so we're going to loop through our saved request
			// and do the page update once, rather than four times.
			$c->update(self::$cmpSaveRequest);
		}
	}


	public function render($label, $customTemplate) {
		$env = Environment::get();
		$form = Loader::helper('form');
		$set = $this->getComposerFormLayoutSetControlObject()->getComposerFormLayoutSetObject();
		$control = $this;
		
		if ($customTemplate) {
			$rec = $env->getRecord(DIRNAME_ELEMENTS . '/' . DIRNAME_COMPOSER . '/' . DIRNAME_COMPOSER_ELEMENTS_CONTROLS . '/' . $this->cmpControlTypeHandle . '/' . $this->propertyHandle . '/' . DIRNAME_BLOCK_TEMPLATES_COMPOSER . '/' . $customTemplate);
			if ($rec->exists()) {
				$template = $rec->file;
			}
		}

		if (!isset($template)) {
			$template = $env->getPath(DIRNAME_ELEMENTS . '/' . DIRNAME_COMPOSER . '/' . DIRNAME_COMPOSER_ELEMENTS_CONTROLS . '/' . $this->cmpControlTypeHandle . '/' . $this->propertyHandle . '.php');
		}

		include($template);
	}

	public function validate($data, ValidationErrorHelper $e) {}

}