<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_ComposerTargetConfiguration extends Object {

	public function getComposerTargetTypeID() {return $this->cmpTargetTypeID;}
	public function getComposerTargetTypeHandle() {return $this->cmpTargetTypeHandle;}

	public function __construct(ComposerTargetType $type) {
		$this->cmpTargetTypeID = $type->getComposerTargetTypeID();
		$this->cmpTargetTypeHandle = $type->getComposerTargetTypeHandle();
		$this->pkgHandle = $type->getPackageHandle();
	}

	public function export($cxml) {
		$target = $cxml->addChild('target');
		$target->addAttribute('handle', $this->getComposerTargetTypeHandle());
		$target->addAttribute('package', $this->pkgHandle);
		return $target;
	}


	public function includeChooseTargetForm($composer = false, $draft = false) {
		Loader::element(DIRNAME_COMPOSER . '/' . DIRNAME_COMPOSER_ELEMENTS_TARGET_TYPES . '/' . DIRNAME_COMPOSER_ELEMENTS_TARGET_TYPES_FORM . '/' . $this->getComposerTargetTypeHandle(), array('draft' => $draft, 'composer' => $composer), $this->pkgHandle);
	}

	/** 
	 * If this composer target object supports a single page ID (currently only the parent_page target object supports this) it is returned here.
	 */
	public function getComposerConfiguredTargetParentPageID() {
		return 0;
	}
	
}
