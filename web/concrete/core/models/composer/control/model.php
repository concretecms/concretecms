<?php defined('C5_EXECUTE') or die("Access Denied.");

abstract class Concrete5_Model_ComposerControl extends Object {

	protected $cmpControlIdentifier;
	protected $cmpControlName;
	protected $cmpControlIconSRC;
	protected $cmpControl;

	abstract public function getComposerControlCustomTemplates();
	abstract public function render($label, $customTemplate);

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

	public function setComposerFormLayoutSetControlObject(ComposerFormLayoutSetControl $setcontrol) {
		$this->cmpFormLayoutSetControlObject = $setcontrol;
	}

	public function getComposerFormLayoutSetControlObject() {
		return $this->cmpFormLayoutSetControlObject;
	}

	public function field($key) {
		return 'cmp[' . $this->cmpFormLayoutSetControlObject->getComposerFormLayoutSetControlID(). '][' . $key . ']';
	}

	public function addToComposerFormLayoutSet(ComposerFormLayoutSet $set) {
		$db = Loader::db();
		$displayOrder = $db->GetOne('select count(cmpFormLayoutSetControlID) from ComposerFormLayoutSetControls where cmpFormLayoutSetID = ?', array($set->getComposerFormLayoutSetID()));
		if (!$displayOrder) {
			$displayOrder = 0;
		}
		$controlType = $this->getComposerControlTypeObject();
		$db->Execute('insert into ComposerFormLayoutSetControls (cmpFormLayoutSetID, cmpControlTypeID, cmpControlObject, cmpFormLayoutSetControlDisplayOrder) values (?, ?, ?, ?)', array(
			$set->getComposerFormLayoutSetID(), $controlType->getComposerControlTypeID(), serialize($this), $displayOrder
		));	
		return ComposerFormLayoutSetControl::getByID($db->Insert_ID());

	}
	
}