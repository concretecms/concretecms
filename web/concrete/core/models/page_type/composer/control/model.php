<?php defined('C5_EXECUTE') or die("Access Denied.");

abstract class Concrete5_Model_PageTypeComposerControl extends Object {

	protected $ptComposerControlIdentifier;
	protected $ptComposerControlName;
	protected $ptComposerControlIconSRC;
	protected $ptComposerControl;
	protected $ptComposerControlRequiredByDefault = false;
	protected $ptComposerControlRequiredOnThisRequest = false;

	abstract public function getPageTypeComposerControlCustomTemplates();
	abstract public function render($label, $customTemplate);
	abstract public function publishToPage(PageDraft $d, $data, $controls);
	abstract public function validate($data, ValidationErrorHelper $e);
	abstract public function getPageTypeComposerControlDraftValue();
	abstract public function addAssetsToRequest(Controller $cnt);
	abstract public function export($node);
	abstract public function shouldPageTypeComposerControlStripEmptyValuesFromDraft();
	abstract public function isPageTypeComposerControlDraftValueEmpty();
	abstract public function removePageTypeComposerControlFromDraft();

	public function pageTypeComposerFormControlSupportsValidation() {
		return false;
	}

	public function setPageTypeComposerControlName($ptComposerControlName) {
		$this->ptComposerControlName = $ptComposerControlName;
	}

	public function setPageTypeComposerFormControlRequired($req) {
		$this->ptComposerControlRequiredOnThisRequest = $req;
	}
	
	public function setPageTypeComposerDraftObject($ptComposerDraftObject) {
		$this->pDraftObject = $pDraftObject;
	}

	public function getPageTypeComposerDraftObject() {
		return $this->pDraftObject;
	}

	public function isPageTypeComposerFormControlRequiredOnThisRequest() {
		return $this->ptComposerControlRequiredOnThisRequest;
	}

	public function getPageTypeComposerControlName() {
		return $this->ptComposerControlName;
	}

	public function setPageTypeComposerControlIconSRC($ptComposerControlIconSRC) {
		$this->ptComposerControlIconSRC = $ptComposerControlIconSRC;
	}
	
	public function getPageTypeComposerControlIconSRC() {
		return $this->ptComposerControlIconSRC;
	}

	public function setPageTypeComposerControlIdentifier($ptComposerControlIdentifier) {
		$this->ptComposerControlIdentifier = $ptComposerControlIdentifier;
	}

	public function getPageTypeComposerControlIdentifier() {
		return $this->ptComposerControlIdentifier;
	}

	public function getPageTypeComposerControlTypeObject() {
		return ComposerControlType::getByHandle($this->ptComposerControlTypeHandle);
	}

	public function getPageTypeComposerControlTypeHandle() {
		return $this->ptComposerControlTypeHandle;
	}

	public function setPageTypeComposerFormLayoutSetControlObject(PageTypeComposerFormLayoutSetControl $setcontrol) {
		$this->ptComposerFormLayoutSetControlObject = $setcontrol;
	}

	public function getPageTypeComposerFormLayoutSetControlObject() {
		return $this->ptComposerFormLayoutSetControlObject;
	}

	public function field($key) {
		return 'ptComposer[' . $this->ptPageTypeComposerFormLayoutSetControlObject->getPageTypeComposerFormLayoutSetControlID(). '][' . $key . ']';
	}

	public function getRequestValue($args = false) {
		if (!$args) {
			$args = $_POST;
		}
		return $args['ptComposer'][$this->ptPageTypeComposerFormLayoutSetControlObject->getPageTypeComposerFormLayoutSetControlID()];
	}

	public function addToPageTypeComposerFormLayoutSet(PageTypeComposerFormLayoutSet $set) {
		$db = Loader::db();
		$displayOrder = $db->GetOne('select count(ptComposerFormLayoutSetControlID) from ComposerFormLayoutSetControls where ptComposerFormLayoutSetID = ?', array($set->getPageTypeComposerFormLayoutSetID()));
		if (!$displayOrder) {
			$displayOrder = 0;
		}
		$ptComposerFormLayoutSetControlRequired = 0;
		if ($this->isPageTypeComposerControlRequiredByDefault) {
			$ptComposerFormLayoutSetControlRequired = 1;
		}
		$controlType = $this->getPageTypeComposerControlTypeObject();
		$db->Execute('insert into PageTypeComposerFormLayoutSetControls (ptComposerFormLayoutSetID, ptComposerControlTypeID, ptComposerControlObject, ptComposerFormLayoutSetControlDisplayOrder, ptComposerFormLayoutSetControlRequired) values (?, ?, ?, ?, ?)', array(
			$set->getPageTypeComposerFormLayoutSetID(), $controlType->getPageTypeComposerControlTypeID(), serialize($this), $displayOrder, $ptComposerFormLayoutSetControlRequired
		));	
		return PageTypeComposerFormLayoutSetControl::getByID($db->Insert_ID());
	}

	public function canPageTypeComposerControlSetPageName() {
		return false;
	}

	public function getPageTypeComposerControlPageNameValue(Page $c) {
		return false;
	}

	public static function getList(PageTypeComposer $composer) {
		$sets = PageTypeComposerFormLayoutSet::getList($composer);
		$controls = array();
		foreach($sets as $s) {
			$setControls = PageTypeComposerFormLayoutSetControl::getList($s);
			foreach($setControls as $sc) {
				$cnt = $sc->getPageTypeComposerControlObject();
				$cnt->setPageTypeComposerFormLayoutSetControlObject($sc);
				$cnt->setPageTypeComposerFormControlRequired($sc->isPageTypeComposerFormLayoutSetControlRequired());
				$controls[] = $cnt;
			}
		}
		return $controls;
	}
	
}