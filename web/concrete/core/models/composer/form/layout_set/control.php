<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_ComposerFormLayoutSetControl extends Object {

	public function getComposerFormLayoutSetControlID() {return $this->cmpFormLayoutSetControlID;}
	public function getComposerFormLayoutSetID() {return $this->cmpFormLayoutSetID;}
	public function getComposerControlTypeID() {return $this->cmpControlTypeID;}
	public function getComposerControlObject() {return $this->cmpControlObject;}
	public function getComposerControlTypeObject() {return ComposerControlType::getByID($this->cmpControlTypeID);}
	public function getComposerFormLayoutSetObject() {return ComposerFormLayoutSet::getByID($this->cmpFormLayoutSetID);}
	public function getComposerFormLayoutSetControlCustomLabel() {return $this->cmpFormLayoutSetControlCustomLabel;}
	public function getComposerFormLayoutSetControlCustomTemplate() {return $this->cmpFormLayoutSetControlCustomTemplate;}
	public function isComposerFormLayoutSetControlRequired() {return $this->cmpFormLayoutSetControlRequired;}

	public function setComposerDraftObject($cmpDraftObject) {
		$this->cmpDraftObject = $cmpDraftObject;
	}

	public function render() {
		$control = $this->getComposerControlObject();
		$control->setComposerFormLayoutSetControlObject($this);
		$control->setComposerDraftObject($this->cmpDraftObject);
		$control->render($this->getComposerControlLabel(), $this->getComposerFormLayoutSetControlCustomTemplate());
	}


	public function getComposerControlLabel() {
		if ($this->getComposerFormLayoutSetControlCustomLabel()) {
			return $this->getComposerFormLayoutSetControlCustomLabel();
		} else {
			$control = $this->getComposerControlObject();
			return $control->getComposerControlName();
		}
	}

	public function export($fxml) {
		$node = $fxml->addChild('control');
		$node->addAttribute('custom-template', $this->getComposerFormLayoutSetControlCustomTemplate());
		if ($this->isComposerFormLayoutSetControlRequired()) {
			$node->addAttribute('required', true);
		}
		$node->addAttribute('custom-label', $this->getComposerFormLayoutSetControlCustomLabel());
		$db = Loader::db();
		$cnt = $db->GetOne('select count(*) from ComposerOutputControls where cmpFormLayoutSetControlID = ?', array($this->cmpFormLayoutSetControlID));
		if ($cnt > 0) {
			$cmpControlTemporaryID = Loader::helper('validation/identifier')->getString(8);
			ContentExporter::addComposerOutputControlID($this, $cmpControlTemporaryID);
			$node->addAttribute('output-control-id', $cmpControlTemporaryID);
		}
		$typeo = $this->getComposerControlTypeObject();
		$node->addAttribute('type', $typeo->getComposerControlTypeHandle());
		$to = $this->getComposerControlObject();
		$to->export($node);
	}


	public static function getList(ComposerFormLayoutSet $set) {
		$db = Loader::db();
		$cmpFormLayoutSetControlIDs = $db->GetCol('select cmpFormLayoutSetControlID from ComposerFormLayoutSetControls where cmpFormLayoutSetID = ? order by cmpFormLayoutSetControlDisplayOrder asc',
			array($set->getComposerFormLayoutSetID())
		);
		$list = array();
		foreach($cmpFormLayoutSetControlIDs as $cmpFormLayoutSetControlID) {
			$control = ComposerFormLayoutSetControl::getByID($cmpFormLayoutSetControlID);
			if (is_object($control)) {
				$list[] = $control;
			}
		}
		return $list;
	}

	public static function getByID($cmpFormLayoutSetControlID) {
		$db = Loader::db();
		$r = $db->GetRow('select * from ComposerFormLayoutSetControls where cmpFormLayoutSetControlID = ?', array($cmpFormLayoutSetControlID));
		if (is_array($r) && $r['cmpFormLayoutSetControlID']) {
			$control = new ComposerFormLayoutSetControl;
			$control->setPropertiesFromArray($r);
			$control->cmpControlObject = unserialize($r['cmpControlObject']);
			return $control;
		}
	}

	public function updateFormLayoutSetControlDisplayOrder($displayOrder) {
		$db = Loader::db();
		$db->Execute('update ComposerFormLayoutSetControls set cmpFormLayoutSetControlDisplayOrder = ? where cmpFormLayoutSetControlID = ?', array(
			$displayOrder, $this->cmpFormLayoutSetControlID
		));
		$this->cmpFormLayoutSetControlDisplayOrder = $displayOrder;
	}

	public function updateFormLayoutSetControlCustomLabel($label) {
		$db = Loader::db();
		$db->Execute('update ComposerFormLayoutSetControls set cmpFormLayoutSetControlCustomLabel = ? where cmpFormLayoutSetControlID = ?', array(
			$label, $this->cmpFormLayoutSetControlID
		));
		$this->cmpFormLayoutSetControlCustomLabel = $label;
	}

	public function updateFormLayoutSetControlRequired($required) {
		$db = Loader::db();
		$db->Execute('update ComposerFormLayoutSetControls set cmpFormLayoutSetControlRequired = ? where cmpFormLayoutSetControlID = ?', array(
			$required, $this->cmpFormLayoutSetControlID
		));
		$this->cmpFormLayoutSetControlRequired = $label;
	}

	public function updateFormLayoutSetControlCustomTemplate($template) {
		$db = Loader::db();
		$db->Execute('update ComposerFormLayoutSetControls set cmpFormLayoutSetControlCustomTemplate = ? where cmpFormLayoutSetControlID = ?', array(
			$template, $this->cmpFormLayoutSetControlID
		));
		$this->cmpFormLayoutSetControlCustomTemplate = $template;
	}

	public function getComposerOutputControlObject(CollectionType $ct) {
		$db = Loader::db();
		$cmpOutputControlID = $db->GetOne('select cmpOutputControlID from ComposerOutputControls where cmpFormLayoutSetControlID = ? and ctID = ?', array($this->cmpFormLayoutSetControlID, $ct->getCollectionTypeID()));
		if ($cmpOutputControlID) {
			return ComposerOutputControl::getByID($cmpOutputControlID);
		}
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from ComposerFormLayoutSetControls where cmpFormLayoutSetControlID = ?', array($this->cmpFormLayoutSetControlID));
		$db->Execute('delete from ComposerOutputControls where cmpFormLayoutSetControlID = ?', array($this->cmpFormLayoutSetControlID));
		$composer = $this->getComposerFormLayoutSetObject();
		$composer->rescanFormLayoutSetControlDisplayOrder();
	}

}