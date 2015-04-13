<?php

namespace Concrete\Core\Tree\Node;

use Concrete\Core\Foundation\Object;
use Concrete\Core\Tree\Tree;
use Loader;
use Concrete\Core\Tree\Node\NodeType as TreeNodeType;
use PermissionKey;
use Permissions;
use Core;
use stdClass;
use Gettext\Translations;

abstract class Node extends Object implements \Concrete\Core\Permission\ObjectInterface
{
    abstract public function loadDetails();

    /** Returns the standard name for this tree node
     * @return string
     */
    abstract public function getTreeNodeName();

    /** Returns the display name for this tree node (localized and escaped accordingly to $format)
     * @param  string $format = 'html' Escape the result in html format (if $format is 'html'). If $format is 'text' or any other value, the display name won't be escaped.
     *
     * @return string
     */
    abstract public function getTreeNodeDisplayName($format = 'html');
    abstract public function deleteDetails();

    protected $childNodes = array();
    protected $childNodesLoaded = false;
    protected $treeNodeIsSelected = false;

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
    public function getTreeObject()
    {
        return Tree::getByID($this->treeID);
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
        return TreeNodeType::getByID($this->treeNodeTypeID);
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
        $db = Loader::db();
        $count = $db->GetOne('select count(treeNodeID) from TreeNodes where treeNodeParentID = ?', array($this->treeNodeID));

        return $count;
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
        $db = Loader::db();
        $nodeArray = array();
        $currentNodeParentID = $this->getTreeNodeParentID();
        if ($currentNodeParentID > 0) {
            while (is_numeric($currentNodeParentID) && $currentNodeParentID > 0 && $currentNodeParentID) {
                $row = $db->GetRow('select treeNodeID, treeNodeParentID from TreeNodes where treeNodeID = ?', array($currentNodeParentID));
                if ($row['treeNodeID']) {
                    $nodeArray[] = self::getByID($row['treeNodeID']);
                }

                $currentNodeParentID = $row['treeNodeParentID']; // moving up the tree until we hit 1
            }
        }

        return $nodeArray;
    }

    public function selectChildrenNodesByID($nodeID)
    {
        if ($this->getTreeNodeID() == $nodeID) {
            $this->treeNodeIsSelected = true;
        } else {
            foreach ($this->getChildNodes() as $childnode) {
                $childnode->selectChildrenNodesByID($nodeID);
            }
        }
    }

    public function getTreeNodeJSON()
    {
        $p = new Permissions($this);
        if ($p->canViewTreeNode()) {
            $node = new stdClass();
            $node->title = $this->getTreeNodeDisplayName();
            $node->key = $this->getTreeNodeID();
            $node->treeNodeID = $this->getTreeNodeID();
            $node->isFolder = false;
            $node->select = $this->treeNodeIsSelected;
            $node->canEditTreeNodePermissions = $p->canEditTreeNodePermissions();
            $node->canDuplicateTreeNode = $p->canDuplicateTreeNode();
            $node->canDeleteTreeNode = $p->canDeleteTreeNode();
            $node->canEditTreeNode = $p->canEditTreeNode();
            $node->treeNodeParentID = $this->getTreeNodeParentID();
            $node->treeNodeTypeID = $this->getTreeNodeTypeID();
            $node->treeNodeTypeHandle = $this->getTreeNodeTypeHandle();

            foreach ($this->getChildNodes() as $childnode) {
                $childnodejson = $childnode->getTreeNodeJSON();
                if (is_object($childnodejson)) {
                    $node->children[] = $childnodejson;
                }
            }
            $node->isLazy = ($this->getTreeNodeChildCount() > 0) ? true : false;

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
        for ($i = 0; $i < count($nodes); $i++) {
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

    public function setTreeNodePermissionsToGlobal()
    {
        if ($this->treeNodeParentID > 0) {
            $db = Loader::db();
            $parentNode = $this->getTreeNodeParentObject();
            if (is_object($parentNode)) {
                $db->Execute('delete from TreeNodePermissionAssignments where treeNodeID = ?', array($this->treeNodeID));
                $db->Execute('update TreeNodes set treeNodeOverridePermissions = 0, inheritPermissionsFromTreeNodeID = ? where treeNodeID = ?', array($parentNode->getTreeNodePermissionsNodeID(), $this->treeNodeID));
                $this->treeNodeOverridePermissions = false;
                $this->inheritPermissionsFromTreeNodeID = $parentNode->getTreeNodePermissionsNodeID();
                $db->Execute('update TreeNodes set inheritPermissionsFromTreeNodeID = ? where inheritPermissionsFromTreeNodeID = ?', array(
                    $parentNode->getTreeNodePermissionsNodeID(), $this->getTreeNodeID(),
                ));
            }
        }
    }

    public function setTreeNodePermissionsToOverride()
    {
        $db = Loader::db();
        // grab all children
        $this->populateChildren();
        $childNodeIDs = $this->getAllChildNodeIDs();

        $db->Execute('delete from TreeNodePermissionAssignments where treeNodeID = ?', array($this->treeNodeID));
        // copy permissions from the page to the area
        $permissions = PermissionKey::getList($this->getPermissionObjectKeyCategoryHandle());
        foreach ($permissions as $pk) {
            $pk->setPermissionObject($this);
            $pk->copyFromParentNodeToCurrentNode();
        }

        $db->Execute('update TreeNodes set treeNodeOverridePermissions = 1, inheritPermissionsFromTreeNodeID = ? where treeNodeID = ?', array($this->treeNodeID, $this->treeNodeID));
        $this->treeNodeOverridePermissions = true;
        $this->inheritPermissionsFromTreeNodeID = $this->treeNodeID;

        if (count($childNodeIDs) > 0) {
            $db->Execute('update TreeNodes set inheritPermissionsFromTreeNodeID = ? where treeNodeID in (' . implode(',', $childNodeIDs) . ') and treeNodeOverridePermissions = 0', array(
                $this->treeNodeID,
            ));
        }
    }

    public function getAllChildNodeIDs()
    {
        $nodeIDs = array();
        $db = Loader::db();
        $walk = function ($treeNodeParentID) use (&$nodeIDs, &$db, &$walk) {
            $nodes = $db->GetCol('select treeNodeID from TreeNodes where treeNodeParentID = ?', array($treeNodeParentID));
            foreach ($nodes as $nodeID) {
                $nodeIDs[] = $nodeID;
                $walk($nodeID);
            }
        };
        $walk($this->treeNodeID);

        return $nodeIDs;
    }

    public function setTreeNodeTreeID($treeID)
    {
        $db = Loader::db();
        $db->Execute('update TreeNodes set treeID = ? where treeNodeID = ?', array($treeID, $this->treeNodeID));
        $this->treeID = $treeID;
    }

    public function move(Node $newParent)
    {
        $db = Loader::db();
        $treeNodeDisplayOrder = $db->GetOne('select count(treeNodeDisplayOrder) from TreeNodes where treeNodeParentID = ?', array($newParent->getTreeNodeID()));
        if (!$treeNodeDisplayOrder) {
            $treeNodeDisplayOrder = 0;
        }
        $db->Execute('update TreeNodes set treeNodeParentID = ?, treeNodeDisplayOrder = ? where treeNodeID = ?', array($newParent->getTreeNodeID(), $treeNodeDisplayOrder, $this->treeNodeID));
        if (!$this->overrideParentTreeNodePermissions()) {
            $db->Execute('update TreeNodes set inheritPermissionsFromTreeNodeID = ? where treeNodeID = ?', array($newParent->getTreeNodePermissionsNodeID(), $this->treeNodeID));
        }
        $oldParent = $this->getTreeNodeParentObject();
        if (is_object($oldParent)) {
            $oldParent->rescanChildrenDisplayOrder();
        }
        $newParent->rescanChildrenDisplayOrder();
        $this->treeNodeParentID = $newParent->getTreeNodeID();
        $this->treeNodeDisplayOrder = $treeNodeDisplayOrder;
    }

    protected function rescanChildrenDisplayOrder()
    {
        $db = Loader::db();
        $r = $db->Execute('select treeNodeID from TreeNodes WHERE treeNodeParentID = ? order by treeNodeDisplayOrder asc', array($this->getTreeNodeID()));
        $displayOrder = 0;
        while ($row = $r->FetchRow()) {
            $db->Execute('update TreeNodes set treeNodeDisplayOrder = ? where treeNodeID = ?', array($displayOrder, $row['treeNodeID']));
            $displayOrder++;
        }
    }

    public function saveChildOrder($orderedIDs)
    {
        $db = Loader::db();
        if (is_array($orderedIDs)) {
            $displayOrder = 0;
            foreach ($orderedIDs as $treeNodeID) {
                $db->Execute('update TreeNodes set treeNodeDisplayOrder = ? where treeNodeID = ?', array($displayOrder, $treeNodeID));
                $displayOrder++;
            }
        }
    }

    public static function add($parent = false)
    {
        $db = Loader::db();
        $treeNodeParentID = 0;
        $treeID = 0;
        $treeNodeDisplayOrder = false;
        $inheritPermissionsFromTreeNodeID = 0;
        if (is_object($parent)) {
            $treeNodeParentID = $parent->getTreeNodeID();
            $treeID = $parent->getTreeID();
            $inheritPermissionsFromTreeNodeID = $parent->getTreeNodePermissionsNodeID();
            $treeNodeDisplayOrder = $db->GetOne('select count(treeNodeDisplayOrder) from TreeNodes where treeNodeParentID = ?', array($treeNodeParentID));
        }

        if (!$treeNodeDisplayOrder) {
            $treeNodeDisplayOrder = 0;
        }
        $treeNodeTypeHandle = Loader::helper('text')->uncamelcase(strrchr(get_called_class(), '\\'));

        $type = TreeNodeType::getByHandle($treeNodeTypeHandle);
        $db->Execute('insert into TreeNodes (treeNodeTypeID, treeNodeParentID, treeNodeDisplayOrder, inheritPermissionsFromTreeNodeID, treeID) values (?, ?, ?, ?, ?)', array(
            $type->getTreeNodeTypeID(), $treeNodeParentID, $treeNodeDisplayOrder, $inheritPermissionsFromTreeNodeID, $treeID,
        ));
        $id = $db->Insert_ID();
        $node = self::getByID($id);

        if (!$inheritPermissionsFromTreeNodeID) {
            $node->setTreeNodePermissionsToOverride();
        }

        return $node;
    }

    public function importNode(\SimpleXMLElement $sx, $parent = false)
    {
        return static::add($parent);
    }

    public function importChildren(\SimpleXMLElement $sx)
    {
        $xnodes = $sx->children();
        foreach ($xnodes as $xn) {
            $type = NodeType::getByHandle($xn->getName());
            $class = $type->getTreeNodeTypeClass();
            $node = call_user_func_array(array($class, 'importNode'), array($xn, $this));
            call_user_func_array(array($node, 'importChildren'), array($xn));
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
            $db = Loader::db();
            $r = $db->GetCol('select treeNodeID from TreeNodes where treeNodeParentID = ? order by treeNodeDisplayOrder asc', array($this->treeNodeID));
            foreach ($r as $nodeID) {
                $node = self::getByID($nodeID);
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
        $db = Loader::db();
        $r = $db->Execute('select treeNodeID from TreeNodes where inheritPermissionsFromTreeNodeID = ? and treeNodeID <> ?', array(
            $this->getTreeNodeID(), $this->getTreeNodeID(),
        ));
        while ($row = $r->FetchRow()) {
            $node = self::getByID($row['treeNodeID']);
            $parentNode = $node->getTreeNodeParentObject();
            $db->Execute('update TreeNodes set inheritPermissionsFromTreeNodeID = ? where treeNodeID = ?', array($parentNode->getTreeNodePermissionsNodeID(), $node->getTreeNodeID()));
        }

        if (!$this->childNodesLoaded) {
            $this->populateChildren();
        }

        foreach ($this->childNodes as $node) {
            $node->delete();
        }

        $this->deleteDetails();
        $db->Execute('delete from TreeNodes where treeNodeID = ?', array($this->treeNodeID));
        $db->Execute('delete from TreeNodePermissionAssignments where treeNodeID = ?', array($this->treeNodeID));
    }

    public static function getByID($treeNodeID)
    {
        $db = Loader::db();
        $row = $db->GetRow('select * from TreeNodes where treeNodeID = ?', array($treeNodeID));
        if (is_array($row) && $row['treeNodeID']) {
            $tt = TreeNodeType::getByID($row['treeNodeTypeID']);
            $node = Core::make($tt->getTreeNodeTypeClass());
            $node->setPropertiesFromArray($row);
            $node->loadDetails();

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
}
