<?php
namespace Concrete\Core\Tree\Node;

use Concrete\Core\Foundation\Object;
use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Access\Entity\GroupCombinationEntity;
use Concrete\Core\Permission\Access\Entity\GroupEntity;
use Concrete\Core\Permission\Access\Entity\UserEntity;
use Concrete\Core\Permission\AssignableObjectInterface;
use Concrete\Core\Permission\AssignableObjectTrait;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Permission\Key\TreeNodeKey;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Tree\Tree;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfo;
use Database;
use Concrete\Core\Tree\Node\NodeType as TreeNodeType;
use PermissionKey;
use Permissions;
use Core;
use stdClass;
use Gettext\Translations;
use Concrete\Core\Tree\Node\Exception\MoveException;

abstract class Node extends Object implements \Concrete\Core\Permission\ObjectInterface, AssignableObjectInterface
{

    use AssignableObjectTrait;

    abstract public function loadDetails();

    /** Returns the display name for this tree node (localized and escaped accordingly to $format)
     * @param  string $format = 'html' Escape the result in html format (if $format is 'html'). If $format is 'text' or any other value, the display name won't be escaped.
     *
     * @return string
     */
    abstract public function getTreeNodeDisplayName($format = 'html');
    abstract public function deleteDetails();
    abstract public function getTreeNodeTypeName();

    protected $childNodes = [];
    protected $childNodesLoaded = false;
    protected $treeNodeIsSelected = false;
    protected $tree;

    public function getTreeNodeTypeDisplayName($format = 'html')
    {
        $name = $this->getTreeNodeTypeName();
        switch ($format) {
            case 'html':
                return h($name);
            case 'text':
            default:
                return $name;
        }
    }

    public function getListFormatter()
    {
        return false;
    }

    public function getPermissionObjectIdentifier()
    {
        return $this->treeNodeID;
    }

    public function getTreeNodeID()
    {
        return $this->treeNodeID;
    }
    public function getTreeNodeParentID()
    {
        return $this->treeNodeParentID;
    }
    public function getTreeNodeParentObject()
    {
        return self::getByID($this->treeNodeParentID);
    }

    public function setTree(Tree $tree)
    {
        $this->tree = $tree;
    }

    public function getDateLastModified()
    {
        return $this->dateModified;
    }

    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    public function getTreeObject()
    {
        if (!isset($this->tree) && !empty($this->treeID)) {
            $this->tree = Tree::getByID($this->treeID);
        }

        return $this->tree;
    }

    public function setTreeNodeName($treeNodeName)
    {
        $db = Database::connection();
        $db->executeQuery('update TreeNodes set treeNodeName = ? where treeNodeID = ?', [$treeNodeName, $this->treeNodeID]);
        $this->treeNodeName = $treeNodeName;
    }

    public function getTreeNodeName()
    {
        return $this->treeNodeName;
    }

    public function getTreeID()
    {
        return $this->treeID;
    }
    public function getTreeNodeTypeID()
    {
        return $this->treeNodeTypeID;
    }
    public function getTreeNodeTypeObject()
    {
        if (!isset($this->treeNodeType)) {
            $this->treeNodeType = TreeNodeType::getByID($this->treeNodeTypeID);
        }

        return $this->treeNodeType;
    }

    public function getTreeNodeTypeHandle()
    {
        $type = $this->getTreeNodeTypeObject();
        if (is_object($type)) {
            return $type->getTreeNodeTypeHandle();
        }
    }
    public function getChildNodes()
    {
        return $this->childNodes;
    }
    public function overrideParentTreeNodePermissions()
    {
        return $this->treeNodeOverridePermissions;
    }
    public function getTreeNodePermissionsNodeID()
    {
        return $this->inheritPermissionsFromTreeNodeID;
    }

    public function getTreeNodeChildCount()
    {
        $db = Database::connection();

        $tree = $this->getTreeObject();
        $data = $tree ? $tree->getRequestData() : [];
        if (isset($data['displayOnly']) && $data['displayOnly']) {
            return (int) $db->fetchColumn('select count(tn.treeNodeID) from TreeNodes tn inner join TreeNodeTypes tnt on tn.treeNodeTypeID = tnt.treeNodeTypeID where treeNodeParentID = ? and treeNodeTypeHandle = ?', [$this->treeNodeID, $data['displayOnly']]);
        } else {
            return (int) $db->fetchColumn('select count(treeNodeID) from TreeNodes where treeNodeParentID = ?', [$this->treeNodeID]);
        }

    }

    public function getChildNodesLoaded() 
    {
        return $this->childNodesLoaded;
    }

    /**
     * Transforms a node to another node.
     */
    public function transformNode($treeNodeType)
    {
        $class = self::getClassByType($treeNodeType);
        $node = new $class();
        $node->setPropertiesFromArray($this);

        return $node;
    }

    /**
     * Returns an array of all parents of this tree node.
     */
    public function getTreeNodeParentArray()
    {
        $db = Database::connection();
        $nodeArray = [];
        $currentNodeParentID = $this->getTreeNodeParentID();
        if ($currentNodeParentID > 0) {
            while (is_numeric($currentNodeParentID) && $currentNodeParentID > 0 && $currentNodeParentID) {
                $row = $db->fetchAssoc('select treeNodeID, treeNodeParentID from TreeNodes where treeNodeID = ?', [$currentNodeParentID]);
                if ($row && $row['treeNodeID']) {
                    $nodeArray[] = self::getByID($row['treeNodeID']);
                }

                $currentNodeParentID = $row['treeNodeParentID']; // moving up the tree until we hit 1
            }
        }

        return $nodeArray;
    }

    /**
     * Recursively searches for a children node and marks it as selected.
     *
     * @param $nodeID - nodeID of the children to be selected
     * @param $loadMissingChildren - if set to true, it will fetch, as needed, the children 
     * of the current node, that have not been loaded yet
     *
     * @return JsonResponse
     */
    public function selectChildrenNodesByID($nodeID, $loadMissingChildren = false)
    {
        if ($this->getTreeNodeID() == $nodeID) {
            $this->treeNodeIsSelected = true;
        } else {
            foreach ($this->getChildNodes() as $childnode) {
                if( $loadMissingChildren && !$childnode->getChildNodesLoaded() ) {
                    $childnode->populateDirectChildrenOnly();
                }

                $childnode->selectChildrenNodesByID($nodeID, $loadMissingChildren);
            }
        }
    }

    public function getTreeNodeMenu()
    {
        return null;
    }

    public function getJSONObject()
    {
        return $this->getTreeNodeJSON();
    }

    public function getTreeNodeJSON()
    {
        $p = new Permissions($this);
        $tree = $this->getTreeObject();
        $data = $tree ? $tree->getRequestData() : [];
        if (isset($data['displayOnly'])) {
            // filter by node type handle
            if ($this->getTreeNodeTypeHandle() != $data['displayOnly']) {
                return false;
            }
        }

        if ($p->canViewTreeNode()) {
            $node = new stdClass();
            $node->title = $this->getTreeNodeDisplayName();
            $node->treeID = $this->getTreeID();
            $node->key = $this->getTreeNodeID();
            $node->treeNodeID = $this->getTreeNodeID();
            $node->folder = false;
            $node->selected = $this->treeNodeIsSelected;
            $node->canEditTreeNodePermissions = $p->canEditTreeNodePermissions();
            $node->canDuplicateTreeNode = $p->canDuplicateTreeNode();
            $node->canDeleteTreeNode = $p->canDeleteTreeNode();
            $node->canEditTreeNode = $p->canEditTreeNode();
            $node->treeNodeParentID = $this->getTreeNodeParentID();
            $node->treeNodeTypeID = $this->getTreeNodeTypeID();
            $node->treeNodeTypeHandle = $this->getTreeNodeTypeHandle();
            $node->treeNodeMenu = $this->getTreeNodeMenu();

            $node->children = [];
            foreach ($this->getChildNodes() as $childnode) {
                $childnodejson = $childnode->getTreeNodeJSON();
                if (is_object($childnodejson)) {
                    $node->children[] = $childnodejson;
                }
            }
            if (count($node->children)) {
                $node->lazy = false;
            } else {
                $node->lazy = ($this->getTreeNodeChildCount() > 0) ? true : false;
                if ($node->lazy) {
                    unset($node->children);
                }
            }

            if ($this->getTreeNodeParentID() == 0) {
                $node->expanded = true;
            }

            return $node;
        }
    }

    public function export(\SimpleXMLElement $x)
    {
        if (!$this->getTreeNodeParentID() == 0) {
            $tag = $x->addChild($this->getTreeNodeTypeHandle());
            $tag->addAttribute('name', $this->getTreeNodeName());
        } else {
            $tag = $x;
        }

        foreach ($this->getChildNodes() as $node) {
            $node->export($tag);
        }

        return $tag;
    }

    public function duplicate($parent = false)
    {
        $node = $this::add($parent);
        $this->duplicateChildren($node);

        return $node;
    }

    public function getTreeNodeDisplayPath()
    {
        $path = '/';
        $nodes = array_reverse($this->getTreeNodeParentArray());
        for ($i = 0; $i < count($nodes); ++$i) {
            if ($i == 0) {
                continue;
            }
            $n = $nodes[$i];
            $path .= $n->getTreeNodeName() . '/';
        }
        if (count($nodes) > 0) {
            $path .= $this->getTreeNodeName();
        }

        return $path;
    }

    protected function duplicateChildren(Node $node)
    {
        if ($this->overrideParentTreeNodePermissions()) {
            $node->setTreeNodePermissionsToOverride();
        }
        $this->populateDirectChildrenOnly();
        foreach ($this->getChildNodes() as $childnode) {
            $childnode->duplicate($node);
        }
    }

    public function setChildPermissionsToOverride()
    {
        $this->populateDirectChildrenOnly();
        foreach($this->getChildNodes() as $child) {
            $child->setTreeNodePermissionsToOverride();
        }
    }

    public function setPermissionsToOverride()
    {
        $this->setTreeNodePermissionsToOverride();
    }

    public function setTreeNodePermissionsToGlobal()
    {
        if ($this->treeNodeParentID > 0) {
            $db = Database::connection();
            $parentNode = $this->getTreeNodeParentObject();
            if (is_object($parentNode)) {
                $db->executeQuery('delete from TreeNodePermissionAssignments where treeNodeID = ?', [$this->treeNodeID]);
                $db->executeQuery('update TreeNodes set treeNodeOverridePermissions = 0, inheritPermissionsFromTreeNodeID = ? where treeNodeID = ?', [$parentNode->getTreeNodePermissionsNodeID(), $this->treeNodeID]);
                $this->treeNodeOverridePermissions = false;
                $this->inheritPermissionsFromTreeNodeID = $parentNode->getTreeNodePermissionsNodeID();
                $db->executeQuery(
                    'update TreeNodes set inheritPermissionsFromTreeNodeID = ? where inheritPermissionsFromTreeNodeID = ?',
                    [
                        $parentNode->getTreeNodePermissionsNodeID(),
                        $this->getTreeNodeID(),
                    ]
                );
            }
        }
    }

    public function setTreeNodePermissionsToOverride()
    {
        if (!$this->overrideParentTreeNodePermissions()) {
            $db = Database::connection();
            // grab all children
            $this->populateChildren();
            $childNodeIDs = $this->getAllChildNodeIDs();

            $db->executeQuery('delete from TreeNodePermissionAssignments where treeNodeID = ?', [$this->treeNodeID]);
            // copy permissions from the page to the area
            $permissions = PermissionKey::getList($this->getPermissionObjectKeyCategoryHandle());
            foreach ($permissions as $pk) {
                $pk->setPermissionObject($this);
                $pk->copyFromParentNodeToCurrentNode();
            }

            $db->executeQuery('update TreeNodes set treeNodeOverridePermissions = 1, inheritPermissionsFromTreeNodeID = ? where treeNodeID = ?', [$this->treeNodeID, $this->treeNodeID]);
            $this->treeNodeOverridePermissions = true;
            $this->inheritPermissionsFromTreeNodeID = $this->treeNodeID;

            if (count($childNodeIDs) > 0) {
                $db->executeQuery(
                    'update TreeNodes set inheritPermissionsFromTreeNodeID = ? where treeNodeID in (' . implode(',', $childNodeIDs) . ') and treeNodeOverridePermissions = 0',
                    [
                        $this->treeNodeID,
                    ]
                );
            }
        }
    }

    public function getAllChildNodeIDs()
    {
        $nodeIDs = [];
        $db = Database::connection();
        $walk = function ($treeNodeParentID) use (&$nodeIDs, &$db, &$walk) {
            $rows = $db->fetchAll('select treeNodeID from TreeNodes where treeNodeParentID = ?', [$treeNodeParentID]);
            foreach ($rows as $row) {
                $nodeIDs[] = $row['treeNodeID'];
                $walk($nodeID);
            }
        };
        $walk($this->treeNodeID);

        return $nodeIDs;
    }

    public function setTreeNodeTreeID($treeID)
    {
        $db = Database::connection();
        $db->executeQuery('update TreeNodes set treeID = ? where treeNodeID = ?', [$treeID, $this->treeNodeID]);
        $this->treeID = $treeID;
    }

    /**
     * Check if this node can be moved under another parent.
     *
     * @param Node $newParent The new parent node.
     *
     * @return MoveException|null Return a MoveException in case of problems, null in case of success.
     */
    public function checkMove(Node $newParent)
    {
        $result = null;
        if ($this->getTreeNodeParentID() != $newParent->getTreeNodeID()) {
            if ($this->getTreeNodeID() == $newParent->getTreeNodeID()) {
                $result = new MoveException(t("It's not possible to move a node under itself"));
            } else {
                foreach ($newParent->getTreeNodeParentArray() as $newParentAncestor) {
                    if ($newParentAncestor->getTreeNodeID() == $this->getTreeNodeID()) {
                        $result = MoveException(t("It's not possible to move a node under one of its descending nodes"));
                        break;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Move this node under another node.
     *
     * @param Node $newParent The new parent node
     *
     * @throws MoveException Throws a MoveException in case of errors.
     */
    public function move(Node $newParent)
    {
        $error = $this->checkMove($newParent);
        if ($error !== null) {
            throw $error;
        }
        if ($this->getTreeNodeParentID() != $newParent->getTreeNodeID()) {
            $db = Database::connection();
            $treeNodeDisplayOrder = (int) $db->fetchColumn('select count(treeNodeDisplayOrder) from TreeNodes where treeNodeParentID = ?', [$newParent->getTreeNodeID()]);
            $db->executeQuery('update TreeNodes set treeNodeParentID = ?, treeNodeDisplayOrder = ? where treeNodeID = ?', [$newParent->getTreeNodeID(), $treeNodeDisplayOrder, $this->treeNodeID]);
            if (!$this->overrideParentTreeNodePermissions()) {
                $db->executeQuery('update TreeNodes set inheritPermissionsFromTreeNodeID = ? where treeNodeID = ?', [$newParent->getTreeNodePermissionsNodeID(), $this->treeNodeID]);
            }
            $oldParent = $this->getTreeNodeParentObject();
            if (is_object($oldParent)) {
                $oldParent->rescanChildrenDisplayOrder();
            }
            $newParent->rescanChildrenDisplayOrder();
            $this->treeNodeParentID = $newParent->getTreeNodeID();
            $this->treeNodeDisplayOrder = $treeNodeDisplayOrder;
        }
    }

    protected function rescanChildrenDisplayOrder()
    {
        $db = Database::connection();
        $r = $db->executeQuery('select treeNodeID from TreeNodes WHERE treeNodeParentID = ? order by treeNodeDisplayOrder asc', [$this->getTreeNodeID()]);
        $displayOrder = 0;
        while ($row = $r->fetch()) {
            $db->executeQuery('update TreeNodes set treeNodeDisplayOrder = ? where treeNodeID = ?', [$displayOrder, $row['treeNodeID']]);
            ++$displayOrder;
        }
        $r->closeCursor();
    }

    public function saveChildOrder($orderedIDs)
    {
        $db = Database::connection();
        if (is_array($orderedIDs)) {
            $displayOrder = 0;
            foreach ($orderedIDs as $treeNodeID) {
                $db->executeQuery('update TreeNodes set treeNodeDisplayOrder = ? where treeNodeID = ?', [$displayOrder, $treeNodeID]);
                ++$displayOrder;
            }
        }
    }

    public static function add($parent = false)
    {
        $db = Database::connection();
        $treeNodeParentID = 0;
        $treeID = 0;
        $treeNodeDisplayOrder = 0;
        $inheritPermissionsFromTreeNodeID = 0;
        if (is_object($parent)) {
            $treeNodeParentID = $parent->getTreeNodeID();
            $treeID = $parent->getTreeID();
            $inheritPermissionsFromTreeNodeID = $parent->getTreeNodePermissionsNodeID();
            $treeNodeDisplayOrder = (int) $db->fetchColumn('select count(treeNodeDisplayOrder) from TreeNodes where treeNodeParentID = ?', [$treeNodeParentID]);
        }

        $treeNodeTypeHandle = uncamelcase(strrchr(get_called_class(), '\\'));

        $dateModified = Core::make('date')->toDB();
        $dateCreated = Core::make('date')->toDB();

        $type = TreeNodeType::getByHandle($treeNodeTypeHandle);
        $db->executeQuery(
            'insert into TreeNodes (treeNodeTypeID, treeNodeParentID, treeNodeDisplayOrder, inheritPermissionsFromTreeNodeID, dateModified, dateCreated, treeID) values (?, ?, ?, ?, ?, ?, ?)',
            [
                $type->getTreeNodeTypeID(),
                $treeNodeParentID,
                $treeNodeDisplayOrder,
                $inheritPermissionsFromTreeNodeID,
                $dateModified,
                $dateCreated,
                $treeID,
            ]
        );
        $id = $db->lastInsertId();
        $node = self::getByID($id);

        if (!$inheritPermissionsFromTreeNodeID) {
            $node->setTreeNodePermissionsToOverride();
        }

        return $node;
    }

    public static function importNode(\SimpleXMLElement $sx, $parent = false)
    {
        return static::add($parent);
    }

    public function importChildren(\SimpleXMLElement $sx)
    {
        $xnodes = $sx->children();
        foreach ($xnodes as $xn) {
            $type = NodeType::getByHandle($xn->getName());
            $class = $type->getTreeNodeTypeClass();
            $node = call_user_func_array([$class, 'importNode'], [$xn, $this]);
            call_user_func_array([$node, 'importChildren'], [$xn]);
        }
    }

    public function populateChildren()
    {
        $this->populateDirectChildrenOnly();
        foreach ($this->childNodes as $node) {
            $node->populateChildren();
        }
    }

    public function populateDirectChildrenOnly()
    {
        if (!$this->childNodesLoaded) {
            $db = Database::connection();
            $tree = $this->getTreeObject();
            $data = $tree ? $tree->getRequestData() : [];
            if (isset($data['displayOnly']) && $data['displayOnly']) {
                $rows = $db->fetchAll('select treeNodeID from TreeNodes tn inner join TreeNodeTypes tnt on tn.treeNodeTypeID = tnt.treeNodeTypeID where treeNodeParentID = ? and treeNodeTypeHandle = ? order by treeNodeDisplayOrder asc', [$this->treeNodeID, $data['displayOnly']]);
            } else {
                $rows = $db->fetchAll('select treeNodeID from TreeNodes where treeNodeParentID = ? order by treeNodeDisplayOrder asc', [$this->treeNodeID]);
            }

            foreach ($rows as $row) {
                $node = self::getByID($row['treeNodeID']);
                if ($tree) {
                    $node->setTree($tree);
                }
                if (is_object($node)) {
                    $this->childNodes[] = $node;
                }
            }
            $this->childNodesLoaded = true;
        }
    }

    public function delete()
    {

        // do other nodes that aren't child nodes somehow
        // inherit permissions from here? If so, we rescan those nodes
        $db = Database::connection();
        $r = $db->executeQuery(
            'select treeNodeID from TreeNodes where inheritPermissionsFromTreeNodeID = ? and treeNodeID <> ?',
            [
                $this->getTreeNodeID(),
                $this->getTreeNodeID(),
            ]
        );
        while ($row = $r->fetch()) {
            $node = self::getByID($row['treeNodeID']);
            $parentNode = $node->getTreeNodeParentObject();
            $db->executeQuery('update TreeNodes set inheritPermissionsFromTreeNodeID = ? where treeNodeID = ?', [$parentNode->getTreeNodePermissionsNodeID(), $node->getTreeNodeID()]);
        }
        $r->closeCursor();

        if (!$this->childNodesLoaded) {
            $this->populateChildren();
        }

        foreach ($this->childNodes as $node) {
            $node->delete();
        }

        $this->deleteDetails();
        $db->executeQuery('delete from TreeNodes where treeNodeID = ?', [$this->treeNodeID]);
        $db->executeQuery('delete from TreeNodePermissionAssignments where treeNodeID = ?', [$this->treeNodeID]);
    }

    public static function getByID($treeNodeID)
    {
        $app = Facade::getFacadeApplication();
        $db = Database::connection();
        $cache = $app->make('cache/request');
        $item = $cache->getItem(sprintf('tree/node/%s', $treeNodeID));
        if (!$item->isMiss()) {
            return $item->get();
        } else {
            $node = null;
            $row = $db->fetchAssoc('select * from TreeNodes where treeNodeID = ?', [$treeNodeID]);
            if ($row && $row['treeNodeID']) {
                $tt = TreeNodeType::getByID($row['treeNodeTypeID']);
                $node = Core::make($tt->getTreeNodeTypeClass());
                $row['treeNodeTypeHandle'] = $tt->getTreeNodeTypeHandle();
                $node->setPropertiesFromArray($row);
                $node->loadDetails();
            }
            $cache->save($item->set($node));
            return $node;
        }
    }
    
    /**
     * @param Translations $translations
     *
     * @internal
     */
    public function exportTranslations(Translations $translations)
    {
        $name = $this->getTreeNodeDisplayName('text');
        if (is_string($name) && ($name !== '')) {
            $context = method_exists($this, 'getTreeNodeTranslationContext') ? $this->getTreeNodeTranslationContext() : '';
            $translations->insert($context, $name);
        }
        $this->populateDirectChildrenOnly();
        foreach ($this->getChildNodes() as $childnode) {
            $childnode->exportTranslations($translations);
        }
    }

    public static function getNodeByName($name)
    {
        $db = Database::connection();
        $treeNodeTypeHandle = uncamelcase(strrchr(get_called_class(), '\\'));
        $type = TreeNodeType::getByHandle($treeNodeTypeHandle);
        $treeNodeID = $db->fetchColumn(
            'select treeNodeID from TreeNodes where treeNodeName = ? and treeNodeTypeID = ?',
            [$name, $type->getTreeNodeTypeID()]
        );
        if ($treeNodeID) {
            return static::getByID($treeNodeID);
        }
    }

    protected function populateRecursiveNodes($treeNodeTypeID, $nodes, $nodeRow, $level, $returnNodeObjects = false, $includeThisNode = true)
    {
        $db = Database::connection();
        $children = $db->fetchAll('select treeNodeID, treeNodeTypeID, treeNodeParentID, treeNodeDisplayOrder from TreeNodes where treeNodeTypeID = ? and treeNodeParentID = ? order by treeNodeDisplayOrder asc', [$treeNodeTypeID, $nodeRow['treeNodeID']]);
        
        if ($includeThisNode) {
            $data = [
                'treeNodeID' => $nodeRow['treeNodeID'],
                'treeNodeDisplayOrder' => $nodeRow['treeNodeDisplayOrder'],
                'treeNodeParentID' => $nodeRow['treeNodeParentID'],
                'level' => $level,
                'total' => count($children),
            ];
            if ($returnNodeObjects) {
                $node = self::getByID($nodeRow['treeNodeID']);
                if (is_object($node)) {
                        $data['treeNodeObject'] = $node;
                }
            }

            $nodes[] = $data;
        }
        ++$level;
        if (count($children) > 0) {
            foreach ($children as $nodeRow) {
                $nodes = $this->populateRecursiveNodes($treeNodeTypeID, $nodes, $nodeRow, $level, $returnNodeObjects);
            }
        }

        return $nodes;
    }

    public function getHierarchicalNodesOfType($treeNodeTypeHandle, $level = 1, $returnNodeObjects = false, $includeThisNode = true)
    {
        $treeNodeType = TreeNodeType::getByHandle($treeNodeTypeHandle);
        

        $nodesOfType = $this->populateRecursiveNodes($treeNodeType->getTreeNodeTypeID(), array(), array('treeNodeID' => $this->getTreeNodeID(), 'treeNodeParentID' => $this->getTreeNodeParentID(), 'treeNodeDisplayOrder' => 0), $level, $returnNodeObjects, $includeThisNode);
        
        return $nodesOfType;
    }

    public static function getNodesOfType($treeNodeTypeHandle)
    {
        $db = Database::connection();
        $type = TreeNodeType::getByHandle($treeNodeTypeHandle);
        $treeNodes = $db->fetchAll(
            'select treeNodeID from TreeNodes where treeNodeTypeID = ?',
            [$type->getTreeNodeTypeID()]
        );

        $nodeList = [];
        if (count($treeNodes)) {
            foreach ($treeNodes as $treeNode) {
                $node = self::getByID($treeNode['treeNodeID']);
                if (is_object($node)) {
                    $nodeList[] = $node;
                }
            }
        }

        return $nodeList;
    }
}
