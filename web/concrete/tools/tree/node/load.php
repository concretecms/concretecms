<?
defined('C5_EXECUTE') or die("Access Denied.");
$node = TreeNode::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['treeNodeParentID']));
$selectedNodeIDs = Loader::helper('security')->sanitizeString($_REQUEST['treeNodeSelectedID']);
if (is_object($node)) {
	$np = new Permissions($node);
	if ($np->canViewTreeNode()) {
		$node->populateDirectChildrenOnly();
		$r = array();
		if($selectedNodeIDs) {
			$selectedIDs = explode(',', $selectedNodeIDs);
			foreach($selectedIDs as $match) {
				$node->selectChildrenNodesByID($match);
			}
		}
		foreach($node->getChildNodes() as $childnode) {
			$json = $childnode->getTreeNodeJSON();
			if ($json) {
				$r[] = $json;
			}
		}
		print Loader::helper('ajax')->sendResult($r);
	}
}