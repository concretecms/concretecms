<?
defined('C5_EXECUTE') or die("Access Denied.");
use \Concrete\Core\Tree\Tree;
use \Concrete\Core\Tree\Node\Node as TreeNode;

$form = Loader::helper('form');
$treeID = Loader::helper('security')->sanitizeInt($_REQUEST['treeID']);
$tree = Tree::getByID($treeID);
if (!is_object($tree)) {
	exit;
}

if ($_REQUEST['treeNodeSelectedIDs']) {
	// starting multiple node stuff
    $node = TreeNode::getByID($nID);
    if (is_object($node) && $node->getTreeID() == $tree->getTreeID()) {
        $tree->setSelectedTreeNodeIDs($_REQUEST['treeNodeSelectedIDs']);
    }
}

$tree->setRequest($_REQUEST);
$result = $tree->getJSON();
print Loader::helper('ajax')->sendResult($result);