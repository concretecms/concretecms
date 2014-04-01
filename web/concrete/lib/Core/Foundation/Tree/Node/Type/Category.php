<?
namespace Concrete\Core\Foundation\Tree\Node\Type;
use Concrete\Core\Foundation\Tree\Node\Node;
abstract class Category extends Node {

	public function getTreeNodeDisplayName() {
		if ($this->treeNodeCategoryName) {
			return $this->treeNodeCategoryName;
		} else if ($this->treeNodeParentID == 0) {
			return t('Categories');
		}
	}

	public function loadDetails() {
		$db = Loader::db();
		$r = $db->GetRow('select * from TreeCategoryNodes where treeNodeID = ?', array($this->treeNodeID));
		$this->setPropertiesFromArray($r);
	}

	public function deleteDetails() {
		$db = Loader::db();
		$db->Execute('delete from TreeCategoryNodes where treeNodeID = ?', array($this->treeNodeID));
	}

	public function duplicate($parent = false) {
		$node = $this::add($this->treeNodeCategoryName, $parent);
		$this->duplicateChildren($node);
		return $node;
	}

	public function getTreeNodeJSON() {
		$obj = parent::getTreeNodeJSON();
		if (is_object($obj)) {
			$obj->isFolder = true;
			return $obj;
		}
	}

	public function setTreeNodeCategoryName($treeNodeCategoryName) {
		$db = Loader::db();
		$db->Replace('TreeCategoryNodes', array('treeNodeID' => $this->getTreeNodeID(), 'treeNodeCategoryName' => $treeNodeCategoryName), array('treeNodeID'), true);
		$this->treeNodeCategoryName = $treeNodeCategoryName;
	}

	public static function add($treeNodeCategoryName = '', $parent = false) {
		$db = Loader::db();
		$node = parent::add($parent);
		$node->setTreeNodeCategoryName($treeNodeCategoryName);
		return $node;
	}

}