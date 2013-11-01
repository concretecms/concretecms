<?
class Concrete5_Model_GroupTree extends Tree {

	public function getTreeDisplayName() {return t('Groups Tree');}

	public static function get() {
		$db = Loader::db();
		$treeTypeID = $db->GetOne('select treeTypeID from TreeTypes where treeTypeHandle = ?', array('group'));
		$treeID = $db->GetOne('select treeID from Trees where treeTypeID = ?', array($treeTypeID));
		return Tree::getByID($treeID);
	}

	protected function deleteDetails() {}

	public static function add() {
		// copy permissions from the other node.
		$rootNode = GroupTreeNode::add();
		$treeID = parent::add($rootNode);
		$tree = self::getByID($treeID);
		return $tree;
	}


	protected function loadDetails() {}

	public static function ensureGroupNodes() {
		$db = Loader::db();
		$tree = GroupTree::get();
		$rootNode = $tree->getRootTreeNodeObject();
		$rows = $db->GetCol('select Groups.gID from Groups left join TreeGroupNodes on Groups.gID = TreeGroupNodes.gID where TreeGroupNodes.gID is null');
		foreach($rows as $gID) {
			$g = Group::getByID($gID);
			GroupTreeNode::add($g, $rootNode);
		}
	}


}