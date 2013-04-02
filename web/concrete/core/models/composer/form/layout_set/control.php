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

	public function getPermissionObjectIdentifier() {
		return $this->getComposerFormLayoutSetControlID();
	}

	public function render() {
		$control = $this->getComposerControlObject();
		$control->setComposerFormLayoutSetControlObject($this);
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

	public function updateFormLayoutSetControlCustomTemplate($template) {
		$db = Loader::db();
		$db->Execute('update ComposerFormLayoutSetControls set cmpFormLayoutSetControlCustomTemplate = ? where cmpFormLayoutSetControlID = ?', array(
			$template, $this->cmpFormLayoutSetControlID
		));
		$this->cmpFormLayoutSetControlCustomTemplate = $template;
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from ComposerFormLayoutSetControls where cmpFormLayoutSetControlID = ?', array($this->cmpFormLayoutSetControlID));
		$db->Execute('delete from ComposerOutputControls where cmpFormLayoutSetControlID = ?', array($this->cmpFormLayoutSetControlID));
		$composer = $this->getComposerFormLayoutSetObject();
		$composer->rescanFormLayoutSetControlDisplayOrder();
	}

}