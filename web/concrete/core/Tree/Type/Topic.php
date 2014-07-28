<?
namespace Concrete\Core\Tree\Type;
use \Concrete\Core\Tree\Tree;
use \Concrete\Core\Tree\Node\Type\TopicCategory as TopicCategoryTreeNode;
use Loader;
use Group as UserGroup;
use \Concrete\Core\Permission\Access\Entity\GroupEntity as GroupPermissionAccessEntity;
use \Concrete\Core\Permission\Key\TopicCategoryTreeNodeKey as TopicCategoryTreeNodePermissionKey;
use PermissionAccess;
class Topic extends Tree {

	public function getTreeDisplayName() {return $this->topicTreeName;}

	public static function getDefault() {
		$db = Loader::db();
		$treeID = $db->GetOne('select treeID from TopicTrees order by treeID asc');
		return Tree::getByID($treeID);
	}

	protected function deleteDetails() {
		$db = Loader::db();
		$db->Execute('delete from TopicTrees where treeID = ?', array($this->treeID));
	}

	public static function add($name) {
		// copy permissions from the other node.
		$rootNode = TopicCategoryTreeNode::add();
		$treeID = parent::add($rootNode);
		$tree = self::getByID($treeID);
		$tree->setTopicTreeName($name);

		// by default, topic trees are viewable by all
		$guestGroupEntity = GroupPermissionAccessEntity::getOrCreate(UserGroup::getByID(GUEST_GROUP_ID));
		$pk = TopicCategoryTreeNodePermissionKey::getByHandle('view_topic_category_tree_node');
		$pk->setPermissionObject($rootNode);
		$pa = PermissionAccess::create($pk);
		$pa->addListItem($guestGroupEntity);
		$pt = $pk->getPermissionAssignmentObject();
		$pt->assignPermissionAccess($pa);

		return $tree;
	}

    public function exportDetails(\SimpleXMLElement $sx)
    {
        $default = self::getDefault();
        if (is_object($default) && $default->getTreeID() == $this->getTreeID()) {
            $sx->addAttribute('default', 1);
        }
    }

    public static function importDetails(\SimpleXMLElement $sx)
    {
        $isDefault = (string) $sx['default'];
        if ($isDefault) {
            return static::getDefault();
        } else {
            $name = (string) $sx['name'];
            return static::add($name);
        }
    }


    protected function loadDetails() {
		$db = Loader::db();
		$row = $db->GetRow('select treeID, topicTreeName from TopicTrees where treeID = ?', array($this->treeID));
		if (is_array($row) && $row['treeID']) {
			$this->setPropertiesFromArray($row);
			return $tree;
		}
	}

	public function setTopicTreeName($name) {
		$db = Loader::db();
		$db->Replace('TopicTrees', array('treeID' => $this->getTreeID(), 'topicTreeName' => $name), array('treeID'), true);
		$this->topicTreeName = $name;
	}

	public static function getList() {
		$db = Loader::db();
		$treeIDs = $db->GetCol('select TopicTrees.treeID from TopicTrees inner join Trees on TopicTrees.treeID = Trees.treeID order by treeDateAdded asc');
		$trees = array();
		foreach($treeIDs as $treeID) {
			$tree = self::getByID($treeID);
			if (is_object($tree)) {
				$trees[] = $tree;
			}
		}
		return $trees;
	}

}