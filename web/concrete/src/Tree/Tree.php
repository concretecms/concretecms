<?php

namespace Concrete\Core\Tree;

use Concrete\Core\Foundation\Object;
use Core;
use Database;
use Localization;
use Gettext\Translations;

abstract class Tree extends Object
{
    protected $treeNodeSelectedIDs = array();

    abstract protected function loadDetails();

    abstract protected function deleteDetails();

    /** Returns the standard name for this tree
     * @return string
     */
    abstract public function getTreeName();

    /** Returns the display name for this tree (localized and escaped accordingly to $format)
     * @param  string $format = 'html' Escape the result in html format (if $format is 'html'). If $format is 'text' or any other value, the display name won't be escaped.
     *
     * @return string
     */
    abstract public function getTreeDisplayName($format = 'html');

    abstract public function exportDetails(\SimpleXMLElement $sx);

    /**
     * @param \SimpleXMLElement $sx
     *
     * @return static|null
     * @abstract
     */
    public static function importDetails(\SimpleXMLElement $sx)
    {
        return null;
    }

    public function setSelectedTreeNodeIDs($nodeIDs)
    {
        $this->treeNodeSelectedIDs = $nodeIDs;
    }

    public function getSelectedTreeNodeIDs()
    {
        return $this->treeNodeSelectedIDs;
    }

    public function getTreeTypeID()
    {
        return $this->treeTypeID;
    }
    public function getTreeTypeObject()
    {
        return TreeType::getByID($this->treeTypeID);
    }

    public function getTreeTypeHandle()
    {
        $type = $this->getTreeTypeObject();
        if (is_object($type)) {
            return $type->getTreeTypeHandle();
        }
    }

    public function export(\SimpleXMLElement $sx)
    {
        $treenode = $sx->addChild('tree');
        $treenode->addAttribute('type', $this->getTreeTypeHandle());
        $this->exportDetails($treenode);
        $root = $this->getRootTreeNodeObject();
        $root->populateChildren();
        $root->export($treenode);
    }

    public static function exportList(\SimpleXMLElement $sx)
    {
        $trees = $sx->addChild('trees');
        $db = Database::connection();
        $r = $db->Execute('select treeID from Trees order by treeID asc');
        while ($row = $r->Fetchrow()) {
            $tree = static::getByID($row['treeID']);
            $tree->export($trees);
        }
    }

    public static function import(\SimpleXMLElement $sx)
    {
        $type = TreeType::getByHandle((string) $sx['type']);
        $class = $type->getTreeTypeClass();
        $tree = call_user_func_array(array($class, 'importDetails'), array($sx));
        $parent = $tree->getRootTreeNodeObject();
        $parent->importChildren($sx);
    }

    public function getTreeID()
    {
        return $this->treeID;
    }

    public function getRootTreeNodeObject()
    {
        return \Concrete\Core\Tree\Node\Node::getByID($this->rootTreeNodeID);
    }

    public function getRootTreeNodeID()
    {
        return $this->rootTreeNodeID;
    }

    /**
     * Iterates through the segments in the path, to return the node at the proper display. Mostly used for export
     * and import.
     *
     * @param $path
     */
    public function getNodeByDisplayPath($path)
    {
        $root = $this->getRootTreeNodeObject();

        if ($path == '/' || !$path) {
            return $root;
        }

        $computedPath = '';
        $tree = $this;
        $walk = function ($node, $computedPath) use (&$walk, &$tree, &$path) {
            $node->populateDirectChildrenOnly();

            if ($node->getTreeNodeID() != $tree->getRootTreeNodeID()) {
                $name = $node->getTreeNodeName();
                $computedPath .= '/' . $name;
            }

            if ($computedPath == $path) {
                return $node;
            } else {
                $children = $node->getChildNodes();
                foreach ($children as $child) {
                    $node = $walk($child, $computedPath);
                    if ($node !== null) {
                        return $node;
                    }
                }
            }

            return;
        };
        $node = $walk($root, $computedPath);

        return $node;
    }

    public function getRequestData()
    {
        return $this->requestData;
    }

    public function setRequest($data)
    {
        $this->requestData = $data;
    }

    public function delete()
    {
        $db = Database::connection();
        // delete top level node
        $node = $this->getRootTreeNodeObject();
        if (is_object($node)) {
            $node->delete();
        }
        $this->deleteDetails();
        $db->Execute('delete from Trees where treeID = ?', array($this->treeID));
    }

    public function duplicate()
    {
        $root = $this->getRootTreeNodeObject();
        $newRoot = $root->duplicate();
        $type = $this->getTreeTypeObject();
        $tree = $type->addTree($newRoot);
        $db = Database::connection();
        $nodes = $newRoot->getAllChildNodeIDs();
        foreach ($nodes as $nodeID) {
            $db->Execute('update TreeNodes set treeID = ? where treeNodeID = ?', array($tree->getTreeID(), $nodeID));
        }

        return $tree;
    }

    public function getJSON()
    {
        $root = $this->getRootTreeNodeObject();
        $root->setTree($this);
        $root->populateDirectChildrenOnly();
        if (is_array($this->getSelectedTreeNodeIDs())) {
            foreach ($this->getSelectedTreeNodeIDs() as $magicNodes) {
                $root->selectChildrenNodesByID($magicNodes);
            }
        }
        if ($this->getSelectedTreeNodeIDs() > 0) {
            $root->selectChildrenNodesByID($this->getSelectedTreeNodeIDs());
        }

        return $root->getTreeNodeJSON();
    }

    protected static function create(\Concrete\Core\Tree\Node\Node $rootNode)
    {
        $db = Database::connection();
        $date = Core::make('helper/date')->getOverridableNow();
        $treeTypeHandle = Core::make('helper/text')->uncamelcase(strrchr(get_called_class(), '\\'));
        $type = TreeType::getByHandle($treeTypeHandle);
        $db->Execute('insert into Trees (treeDateAdded, rootTreeNodeID, treeTypeID) values (?, ?, ?)', array(
            $date, $rootNode->getTreeNodeID(), $type->getTreeTypeID(),
        ));
        $treeID = $db->Insert_ID();
        $rootNode->setTreeNodeTreeID($treeID);

        return $treeID;
    }

    final public static function getByID($treeID)
    {
        $db = Database::connection();
        $row = $db->GetRow('select * from Trees where treeID = ?', array($treeID));
        if (!empty($row)) {
            $tt = TreeType::getByID($row['treeTypeID']);
            $class = $tt->getTreeTypeClass();
            $tree = Core::make($class);
            $tree->setPropertiesFromArray($row);
            $tree->loadDetails();

            return $tree;
        }
    }

    /**
     * Export all the translations associates to every trees.
     *
     * @return Translations
     */
    public static function exportTranslations()
    {
        $translations = new Translations();
        $currentLocale = Localization::activeLocale();
        if ($currentLocale !== 'en_US') {
            Localization::changeLocale('en_US');
        }
        try {
            $db = \Database::get();
            $r = $db->Execute('select treeID from Trees order by treeID asc');
            while ($row = $r->FetchRow()) {
                try {
                    $tree = static::getByID($row['treeID']);
                } catch (\Exception $x) {
                    $tree = null;
                }
                if (isset($tree)) {
                    /* @var $tree Tree */
                    $treeName = $tree->getTreeName();
                    if (is_string($treeName) && ($treeName !== '')) {
                        $translations->insert('TreeName', $treeName);
                    }
                    $rootNode = $tree->getRootTreeNodeObject();
                    /* @var $rootNode \Concrete\Core\Tree\Node\Node */
                    if (isset($rootNode)) {
                        $rootNode->exportTranslations($translations);
                    }
                }
            }
        } catch (\Exception $x) {
            if ($currentLocale !== 'en_US') {
                Localization::changeLocale($currentLocale);
            }
            throw $x;
        }
        if ($currentLocale !== 'en_US') {
            Localization::changeLocale($currentLocale);
        }

        return $translations;
    }
}
