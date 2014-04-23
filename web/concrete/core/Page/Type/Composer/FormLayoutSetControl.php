<?
namespace Concrete\Core\Page\Type\Composer;
use Loader;
use PageTemplate;
use \Concrete\Core\Foundation\Object;
use \Concrete\Core\Page\Type\Composer\OutputControl as PageTypeComposerOutputControl;
use \Concrete\Core\Page\Type\Composer\FormLayoutSet as PageTypeComposerFormLayoutSet;
use \Concrete\Core\Page\Type\Composer\Control\Type\Type as PageTypeComposerControlType;

class FormLayoutSetControl extends Object {

	public function getPageTypeComposerFormLayoutSetControlID() {return $this->ptComposerFormLayoutSetControlID;}
	public function getPageTypeComposerFormLayoutSetID() {return $this->ptComposerFormLayoutSetID;}
	public function getPageTypeComposerControlTypeID() {return $this->ptComposerControlTypeID;}
	public function getPageTypeComposerControlObject() {return $this->ptComposerControlObject;}
	public function getPageTypeComposerControlTypeObject() {return PageTypeComposerControlType::getByID($this->ptComposerControlTypeID);}
	public function getPageTypeComposerFormLayoutSetObject() {return PageTypeComposerFormLayoutSet::getByID($this->ptComposerFormLayoutSetID);}
	public function getPageTypeComposerFormLayoutSetControlCustomLabel() {return $this->ptComposerFormLayoutSetControlCustomLabel;}
	public function getPageTypeComposerFormLayoutSetControlCustomTemplate() {return $this->ptComposerFormLayoutSetControlCustomTemplate;}
	public function isPageTypeComposerFormLayoutSetControlRequired() {return $this->ptComposerFormLayoutSetControlRequired;}

	public function setPageObject($page) {
		$this->page = $page;
	}
	
	public function render() {
		$control = $this->getPageTypeComposerControlObject();
		$control->setPageTypeComposerFormLayoutSetControlObject($this);
		$control->setPageObject($this->page);
		$control->render($this->getPageTypeComposerControlLabel(), $this->getPageTypeComposerFormLayoutSetControlCustomTemplate());
	}


	public function getPageTypeComposerControlLabel() {
		if ($this->getPageTypeComposerFormLayoutSetControlCustomLabel()) {
			return $this->getPageTypeComposerFormLayoutSetControlCustomLabel();
		} else {
			$control = $this->getPageTypeComposerControlObject();
			return $control->getPageTypeComposerControlName();
		}
	}

	public function ensureOutputControlExists() {
		$set = $this->getPageTypeComposerFormLayoutSetObject();
		$pt = $set->getPageTypeObject();
		$pagetemplates = $pt->getPageTypePageTemplateObjects();
		foreach($pagetemplates as $pt) {
			$outputcontrol = PageTypeComposerOutputControl::getByPageTypeComposerFormLayoutSetControl($pt, $this);
			if (!is_object($outputcontrol)) {
				PageTypeComposerOutputControl::add($this, $pt);
			}
		}
	}

	public function export($fxml) {
		$node = $fxml->addChild('control');
		$node->addAttribute('custom-template', $this->getPageTypeComposerFormLayoutSetControlCustomTemplate());
		if ($this->isPageTypeComposerFormLayoutSetControlRequired()) {
			$node->addAttribute('required', true);
		}
		$node->addAttribute('custom-label', $this->getPageTypeComposerFormLayoutSetControlCustomLabel());
		$db = Loader::db();
		$cnt = $db->GetOne('select count(*) from PageTypeComposerOutputControls where ptComposerFormLayoutSetControlID = ?', array($this->ptComposerFormLayoutSetControlID));
		if ($cnt > 0) {
			$ptComposerControlTemporaryID = Loader::helper('validation/identifier')->getString(8);
			ContentExporter::addPageTypeComposerOutputControlID($this, $ptComposerControlTemporaryID);
			$node->addAttribute('output-control-id', $ptComposerControlTemporaryID);
		}
		$typeo = $this->getPageTypeComposerControlTypeObject();
		$node->addAttribute('type', $typeo->getPageTypeComposerControlTypeHandle());
		$to = $this->getPageTypeComposerControlObject();
		$to->export($node);
	}


	public static function getList(PageTypeComposerFormLayoutSet $set) {
		$db = Loader::db();
		$ptComposerFormLayoutSetControlIDs = $db->GetCol('select ptComposerFormLayoutSetControlID from PageTypeComposerFormLayoutSetControls where ptComposerFormLayoutSetID = ? order by ptComposerFormLayoutSetControlDisplayOrder asc',
			array($set->getPageTypeComposerFormLayoutSetID())
		);
		$list = array();
		foreach($ptComposerFormLayoutSetControlIDs as $ptComposerFormLayoutSetControlID) {
			$control = static::getByID($ptComposerFormLayoutSetControlID);
			if (is_object($control)) {
				$list[] = $control;
			}
		}
		return $list;
	}

	public static function getByID($ptComposerFormLayoutSetControlID) {
		$db = Loader::db();
		$r = $db->GetRow('select * from PageTypeComposerFormLayoutSetControls where ptComposerFormLayoutSetControlID = ?', array($ptComposerFormLayoutSetControlID));
		if (is_array($r) && $r['ptComposerFormLayoutSetControlID']) {
			$control = new static;
			$control->setPropertiesFromArray($r);
			$control->ptComposerControlObject = unserialize($r['ptComposerControlObject']);
			return $control;
		}
	}

	public function updateFormLayoutSetControlDisplayOrder($displayOrder) {
		$db = Loader::db();
		$db->Execute('update PageTypeComposerFormLayoutSetControls set ptComposerFormLayoutSetControlDisplayOrder = ? where ptComposerFormLayoutSetControlID = ?', array(
			$displayOrder, $this->ptComposerFormLayoutSetControlID
		));
		$this->ptComposerFormLayoutSetControlDisplayOrder = $displayOrder;
	}

	public function updateFormLayoutSetControlCustomLabel($label) {
		$db = Loader::db();
		$db->Execute('update PageTypeComposerFormLayoutSetControls set ptComposerFormLayoutSetControlCustomLabel = ? where ptComposerFormLayoutSetControlID = ?', array(
			$label, $this->ptComposerFormLayoutSetControlID
		));
		$this->ptComposerFormLayoutSetControlCustomLabel = $label;
	}

	public function updateFormLayoutSetControlRequired($required) {
		$db = Loader::db();
		$db->Execute('update PageTypeComposerFormLayoutSetControls set ptComposerFormLayoutSetControlRequired = ? where ptComposerFormLayoutSetControlID = ?', array(
			intval($required), $this->ptComposerFormLayoutSetControlID
		));
		$this->ptComposerFormLayoutSetControlRequired = $required;
	}

	public function updateFormLayoutSetControlCustomTemplate($template) {
		$db = Loader::db();
		$db->Execute('update PageTypeComposerFormLayoutSetControls set ptComposerFormLayoutSetControlCustomTemplate = ? where ptComposerFormLayoutSetControlID = ?', array(
			$template, $this->ptComposerFormLayoutSetControlID
		));
		$this->ptComposerFormLayoutSetControlCustomTemplate = $template;
	}

	public function getPageTypeComposerOutputControlObject(PageTemplate $pt) {
		$db = Loader::db();
		$ptComposerOutputControlID = $db->GetOne('select ptComposerOutputControlID from PageTypeComposerOutputControls where ptComposerFormLayoutSetControlID = ? and pTemplateID = ?', array($this->ptComposerFormLayoutSetControlID, $pt->getPageTemplateID()));
		if ($ptComposerOutputControlID) {
			return PageTypeComposerOutputControl::getByID($ptComposerOutputControlID);
		}
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from PageTypeComposerFormLayoutSetControls where ptComposerFormLayoutSetControlID = ?', array($this->ptComposerFormLayoutSetControlID));
		$db->Execute('delete from PageTypeComposerOutputControls where ptComposerFormLayoutSetControlID = ?', array($this->ptComposerFormLayoutSetControlID));
		$set = $this->getPageTypeComposerFormLayoutSetObject();
		$set->rescanFormLayoutSetControlDisplayOrder();
	}

}