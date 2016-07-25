<?php
namespace Concrete\Core\Tree\Node\Type;

use Concrete\Core\Entity\Search\SavedFileSearch;
use Concrete\Core\Search\Menu\SavedSearchMenu;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\Formatter\SavedSearchListFormatter;
use Loader;

class SearchPreset extends Node
{
    protected $savedSearchID = null;

    public function getPermissionResponseClassName()
    {
        return '\\Concrete\\Core\\Permission\\Response\\TopicTreeNodeResponse';
    }

    public function getPermissionAssignmentClassName()
    {
        return '\\Concrete\\Core\\Permission\\Assignment\\TopicTreeNodeAssignment';
    }
    public function getPermissionObjectKeyCategoryHandle()
    {
        return 'topic_tree_node';
    }

    public function getTreeNodeTypeName()
    {
        return 'Search Preset';
    }

    public function getSavedSearchID()
    {
        return $this->savedSearchID;
    }

    public function getTreeNodeDisplayName($format = 'html')
    {
        return $this->getTreeNodeName();
    }

    public function getTreeNodeMenu()
    {
        return new SavedSearchMenu($this);
    }

    public function getSavedSearchObject()
    {
        $em = \Database::connection()->getEntityManager();
        $search = $em->find('\Concrete\Core\Entity\Search\SavedFileSearch', $this->getSavedSearchID());
        return $search;
    }

    public function getTreeNodeName()
    {
        $search = $this->getSavedSearchObject();
        if (is_object($search)) {
            return $search->getPresetName();
        }
    }

    public function loadDetails()
    {
        $db = Loader::db();
        $row = $db->GetRow('select * from TreeSearchQueryNodes where treeNodeID = ?', array($this->treeNodeID));
        $this->setPropertiesFromArray($row);
    }

    public function deleteDetails()
    {
        $db = Loader::db();
        $db->Execute('delete from TreeSearchQueryNodes where treeNodeID = ?', array($this->treeNodeID));
        $search = $this->getSavedSearchObject();
        if (is_object($search)) {
            $em = \Database::connection()->getEntityManager();
            $em->remove($search);
            $em->flush();
        }
    }

    public function getTreeNodeJSON()
    {
        $node = parent::getTreeNodeJSON();
        if ($node) {
            $node->isFolder = true;
            $node->resultsThumbnailImg = $this->getListFormatter()->getIconElement();
        }
        return $node;
    }

    public static function getNodeBySavedSearchID($presetID)
    {
        $db = \Database::connection();
        $nodeID = $db->GetOne('select treeNodeID from TreeSearchQueryNodes where savedSearchID = ?', array($presetID));
        if ($nodeID) {
            return static::getByID($nodeID);
        }
    }

    public function setTreeNodeSavedSearch(SavedFileSearch $search)
    {
        $db = Loader::db();
        $db->Replace('TreeSearchQueryNodes', array('treeNodeID' => $this->getTreeNodeID(), 'savedSearchID' => $search->getID()), array('treeNodeID'), true);
        $this->savedSearchID = $search->getID();
    }

    public function getListFormatter()
    {
        return new SavedSearchListFormatter();
    }

    public static function addSearchPreset(SavedFileSearch $search, $parent = false)
    {
        $node = parent::add($parent);
        if (is_object($search)) {
            $node->setTreeNodeSavedSearch($search);
        }

        return $node;
    }
}
