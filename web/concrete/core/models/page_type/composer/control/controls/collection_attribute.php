<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_CollectionAttributePageTypeComposerControl extends PageTypeComposerControl {
	
	protected $akID;
	protected $ak = false;
	protected $ptComposerControlTypeHandle = 'collection_attribute';
	
	public function setAttributeKeyID($akID) {
		$this->akID = $akID;
		$this->setPageTypeComposerControlIdentifier($akID);
	}

	public function pageTypeComposerFormControlSupportsValidation() {
		return true;
	}

	public function export($node) {
		$ak = $this->getAttributeKeyObject();
		$node->addAttribute('handle', $ak->getAttributeKeyHandle());
	}

	public function removePageTypeComposerControlFromDraft() {
		$ak = $this->getAttributeKeyObject();
		$c = $this->pDraftObject->getPageDraftCollectionObject();
		$c->clearAttribute($ak);
	}

	public function getAttributeKeyObject() {
		if (!$this->ak) {
			$this->ak = CollectionAttributeKey::getByID($this->akID);
		}
		return $this->ak;
	}

	public function getAttributeKeyID() {
		return $this->akID;
	}

	public function getPageTypeComposerControlCustomTemplates() {
		return array();
	}

	public function canPageTypeComposerControlSetPageName() {
		$ak = $this->getAttributeKeyObject();
		if ($ak->getAttributeKeyHandle() == 'meta_title') {
			return true;
		}
		return false;
	}

	public function getPageTypeComposerControlPageNameValue(Page $c) {
		$ak = $this->getAttributeKeyObject();
		return $c->getAttribute($ak->getAttributeKeyHandle());
	}

	public function getPageTypeComposerControlDraftValue() {
		if (is_object($this->pDraftObject)) {
			$ak = $this->getAttributeKeyObject();
			$c = $this->pDraftObject->getPageDraftCollectionObject();
			return $c->getAttributeValueObject($ak);
		}
	}
	
	public function shouldPageTypeComposerControlStripEmptyValuesFromDraft() {
		return true;
	}

	public function isPageTypeComposerControlDraftValueEmpty() {
		$ak = $this->getAttributeKeyObject();
		$c = $this->pDraftObject->getPageDraftCollectionObject();
		return ($c->getAttribute($ak) == '');
	}

	public function render($label, $customTemplate) {
		$ak = $this->getAttributeKeyObject();
		$env = Environment::get();
		$set = $this->getPageTypeComposerFormLayoutSetControlObject()->getPageTypeComposerFormLayoutSetObject();
		$control = $this;
		$template = $env->getPath(DIRNAME_ELEMENTS . '/' . DIRNAME_COMPOSER . '/' . DIRNAME_COMPOSER_ELEMENTS_CONTROLS . '/' . $this->ptComposerControlTypeHandle . '.php');
		include($template);
	}

	public function publishToPage(PageDraft $d, $data, $controls) {
		$c = $d->getPageDraftCollectionObject();
		// the data for this actually doesn't come from $data. Attributes have their own way of gettin data.
		$ak = $this->getAttributeKeyObject();
		$ak->saveAttributeForm($c);				
	}

	public function validate($data, ValidationErrorHelper $e) {
		$ak = $this->getAttributeKeyObject();
		$e1 = $ak->validateAttributeForm();
		if ($e1 == false) {
			$e->add(t('The field "%s" is required', $ak->getAttributeKeyName()));
		} else if ($e1 instanceof ValidationErrorHelper) {
			$e->add($e1);
		}
	}

	public function addAssetsToRequest(Controller $cnt) {
		$ak = $this->getAttributeKeyObject();
		$akc = $ak->getController();
		$akc->setupAndRun('composer');
	}

}