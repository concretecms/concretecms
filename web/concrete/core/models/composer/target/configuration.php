<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_ComposerTargetConfiguration extends Object {

	public function getComposerTargetTypeID() {return $this->cmpTargetTypeID;}

	public function __construct(ComposerTargetType $type) {
		$this->cmpTargetTypeID = $type->getComposerTargetTypeID();
	}
	
}
