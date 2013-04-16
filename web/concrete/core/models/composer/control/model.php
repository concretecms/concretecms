<?php defined('C5_EXECUTE') or die("Access Denied.");

abstract class Concrete5_Model_ComposerControl extends Object {

	protected $cmpControlIdentifier;
	protected $cmpControlName;
	protected $cmpControlIconSRC;
	protected $cmpControl;
	protected $cmpControlRequiredByDefault = false;
	protected $cmpControlRequiredOnThisRequest = false;
	protected $cmpDraftObject;

	abstract public function getComposerControlCustomTemplates();
	abstract public function render($label, $customTemplate);
	abstract public function publishToPage(ComposerDraft $d, $data, $controls);
	abstract public function validate($data, ValidationErrorHelper $e);
	abstract public function getComposerControlDraftValue();
	abstract public function addAssetsToRequest(Controller $cnt);
	
	public function composerFormControlSupportsValidation() {
		return false;
	}

	public function setComposerControlName($cmpControlName) {
		$this->cmpControlName = $cmpControlName;
	}

	public function setComposerFormControlRequired($req) {
		$this->cmpControlRequiredOnThisRequest = $req;
	}
	
	public function setComposerDraftObject($cmpDraftObject) {
		$this->cmpDraftObject = $cmpDraftObject;
	}

	public function getComposerDraftObject() {
		return $this->cmpDraftObject;
	}

	public function isComposerFormControlRequiredOnThisRequest() {
		return $this->cmpControlRequiredOnThisRequest;
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

	public function getComposerControlTypeHandle() {
		return $this->cmpControlTypeHandle;
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

	public function getRequestValue($args = false) {
		if (!$args) {
			$args = $_POST;
		}
		return $args['cmp'][$this->cmpFormLayoutSetControlObject->getComposerFormLayoutSetControlID()];
	}

	public function addToComposerFormLayoutSet(ComposerFormLayoutSet $set) {
		$db = Loader::db();
		$displayOrder = $db->GetOne('select count(cmpFormLayoutSetControlID) from ComposerFormLayoutSetControls where cmpFormLayoutSetID = ?', array($set->getComposerFormLayoutSetID()));
		if (!$displayOrder) {
			$displayOrder = 0;
		}
		$cmpFormLayoutSetControlRequired = 0;
		if ($this->cmpControlRequiredByDefault) {
			$cmpFormLayoutSetControlRequired = 1;
		}
		$controlType = $this->getComposerControlTypeObject();
		$db->Execute('insert into ComposerFormLayoutSetControls (cmpFormLayoutSetID, cmpControlTypeID, cmpControlObject, cmpFormLayoutSetControlDisplayOrder, cmpFormLayoutSetControlRequired) values (?, ?, ?, ?, ?)', array(
			$set->getComposerFormLayoutSetID(), $controlType->getComposerControlTypeID(), serialize($this), $displayOrder, $cmpFormLayoutSetControlRequired
		));	
		return ComposerFormLayoutSetControl::getByID($db->Insert_ID());
	}

	public function canComposerControlSetPageName() {
		return false;
	}

	public function getComposerControlPageNameValue(Page $c) {
		return false;
	}

	public static function getList(Composer $composer) {
		$sets = ComposerFormLayoutSet::getList($composer);
		$controls = array();
		foreach($sets as $s) {
			$setControls = ComposerFormLayoutSetControl::getList($s);
			foreach($setControls as $sc) {
				$cnt = $sc->getComposerControlObject();
				$cnt->setComposerFormLayoutSetControlObject($sc);
				$cnt->setComposerFormControlRequired($sc->isComposerFormLayoutSetControlRequired());
				$controls[] = $cnt;
			}
		}
		return $controls;
	}
	
}