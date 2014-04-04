<?
namespace Concrete\Core\Page\Type\Composer;
use \Concrete\Core\Foundation\Object;
use Loader;
class FormLayoutSet extends Object {

	public function getPageTypeComposerFormLayoutSetID() {return $this->ptComposerFormLayoutSetID;}
	public function getPageTypeComposerFormLayoutSetName() {return $this->ptComposerFormLayoutSetName;}
	public function getPageTypeComposerFormLayoutSetDisplayOrder() {return $this->ptComposerFormLayoutSetDisplayOrder;}
	public function getPageTypeID() {return $this->ptID;}
	public function getPageTypeObject() {return PageType::getByID($this->ptID);}

	public static function getList(PageType $pagetype) {
		$db = Loader::db();
		$ptComposerFormLayoutSetIDs = $db->GetCol('select ptComposerFormLayoutSetID from PageTypeComposerFormLayoutSets where ptID = ? order by ptComposerFormLayoutSetDisplayOrder asc', array($pagetype->getPageTypeID()));
		$list = array();
		foreach($ptComposerFormLayoutSetIDs as $ptComposerFormLayoutSetID) {
			$set = PageTypeComposerFormLayoutSet::getByID($ptComposerFormLayoutSetID);
			if (is_object($set)) {
				$list[] = $set;
			}
		}
		return $list;
	}

	public static function getByID($ptComposerFormLayoutSetID) {
		$db = Loader::db();
		$r = $db->GetRow('select * from PageTypeComposerFormLayoutSets where ptComposerFormLayoutSetID = ?', array($ptComposerFormLayoutSetID));
		if (is_array($r) && $r['ptComposerFormLayoutSetID']) {
			$set = new PageTypeComposerFormLayoutSet;
			$set->setPropertiesFromArray($r);
			return $set;
		}
	}

	public function export($fxml) {
		$node = $fxml->addChild('set');
		$node->addAttribute('name', $this->getPageTypeComposerFormLayoutSetName());
		$controls = PageTypeComposerFormLayoutSetControl::getList($this);
		foreach($controls as $con) {
			$con->export($node);
		}
	}

	public function updateFormLayoutSetName($ptComposerFormLayoutSetName) {
		$db = Loader::db();
		$db->Execute('update PageTypeComposerFormLayoutSets set ptComposerFormLayoutSetName = ? where ptComposerFormLayoutSetID = ?', array(
			$ptComposerFormLayoutSetName, $this->ptComposerFormLayoutSetID
		));
		$this->ptComposerFormLayoutSetName = $ptComposerFormLayoutSetName;
	}


	public function updateFormLayoutSetDisplayOrder($displayOrder) {
		$db = Loader::db();
		$db->Execute('update PageTypeComposerFormLayoutSets set ptComposerFormLayoutSetDisplayOrder = ? where ptComposerFormLayoutSetID = ?', array(
			$displayOrder, $this->ptComposerFormLayoutSetID
		));
		$this->ptComposerFormLayoutSetDisplayOrder = $displayOrder;
	}

	public function delete() {
		$controls = PageTypeComposerFormLayoutSetControl::getList($this);
		foreach($controls as $control) {
			$control->delete();
		}
		$db = Loader::db();
		$db->Execute('delete from PageTypeComposerFormLayoutSets where ptComposerFormLayoutSetID = ?', array($this->ptComposerFormLayoutSetID));
		$pagetype = $this->getPageTypeObject();
		$pagetype->rescanFormLayoutSetDisplayOrder();
	}

	public function rescanFormLayoutSetControlDisplayOrder() {
		$sets = PageTypeComposerFormLayoutSetControl::getList($this);
		$displayOrder = 0;
		foreach($sets as $s) {
			$s->updateFormLayoutSetControlDisplayOrder($displayOrder);
			$displayOrder++;
		}
	}


}