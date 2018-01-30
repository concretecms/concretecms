<?php
namespace Concrete\Core\Tree;

use Concrete\Core\Foundation\ConcreteObject;
use Concrete\Core\Localization\Localization;
use Gettext\Translations;
use SimpleXMLElement;
use Concrete\Core\Tree\Node\Node as TreeNode;
use Exception;
use Concrete\Core\Support\Facade\Application;

abstract class Tree extends ConcreteObject
{
    protected $treeNodeSelectedIDs = [];

    abstract protected function loadDetails();

    abstract protected function deleteDetails();

    /** Returns the standard name for this tree
     * @return string
     */
    abstract public function getTreeName();

    /** Returns the display name for this tree (localized and escaped accordingly to $format)
     * @param  string $format = 'html' Escape the result in html format (if $format is 'html'). If $format is 'text' or any other value, the display name won't be escaped
     *
     * @return string
     */
    abstract public function getTreeDisplayName($format = 'html');

    abstract public function exportDetails(SimpleXMLElement $sx);

    /**
     * @param SimpleXMLElement $sx
     *
     * @return static|null
     * @abstract
     */
    public static function importDetails(SimpleXMLElement $sx)
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

    public function export(SimpleXMLElement $sx)
    {
        $treenode = $sx->addChild('tree');
        $treenode->addAttribute('type', $this->getTreeTypeHandle());
        $this->exportDetails($treenode);
        $root = $this->getRootTreeNodeObject();
        $root->populateChildren();
        $root->export($treenode);
    }

    public static function exportList(SimpleXMLElement $sx)
    {
        $trees = $sx->addChild('trees');
        $app = Application::getFacadeApplication();
        $db = $app->make('database')->connection();
        $r = $db->executeQuery('select treeID from Trees order by treeID asc');
        while ($row = $r->fetch()) {
            $tree = static::getByID($row['treeID']);
            $tree->export($trees);
        }
    }

    public static function import(SimpleXMLElement $sx)
    {
        $type = TreeType::getByHandle((string) $sx['type']);
        $class = $type->getTreeTypeClass();
        $tree = call_user_func_array([$class, 'importDetails'], [$sx]);
        $parent = $tree->getRootTreeNodeObject();
        $parent->importChildren($sx);
    }

    public function getTreeID()
    {
        return $this->treeID;
    }

    public function getRootTreeNodeObject()
    {
        return TreeNode::getByID($this->rootTreeNodeID);
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

            if (strcasecmp($computedPath, $path) == 0) {
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
        return isset($this->requestData) ? $this->requestData : null;
    }

    public function setRequest($data)
    {
        $this->requestData = $data;
    }

    public function delete()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make('database')->connection();
        // delete top level node
        $node = $this->getRootTreeNodeObject();
        if (is_object($node)) {
            $node->delete();
        }
        $this->deleteDetails();
        $db->executeQuery('delete from Trees where treeID = ?', [$this->treeID]);
    }

    public function duplicate()
    {
        $root = $this->getRootTreeNodeObject();
        $newRoot = $root->duplicate();
        $type = $this->getTreeTypeObject();
        $tree = $type->addTree($newRoot);
        $app = Application::getFacadeApplication();
        $db = $app->make('database')->connection();
        $nodes = $newRoot->getAllChildNodeIDs();
        foreach ($nodes as $nodeID) {
            $db->executeQuery('update TreeNodes set treeID = ? where treeNodeID = ?', [$tree->getTreeID(), $nodeID]);
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

        return [$root->getTreeNodeJSON()];
    }

    protected static function create(TreeNode $rootNode)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make('database')->connection();
        $date = $app->make('date')->getOverridableNow();
        $treeTypeHandle = uncamelcase(strrchr(get_called_class(), '\\'));
        $type = TreeType::getByHandle($treeTypeHandle);
        $db->executeQuery(
            'insert into Trees (treeDateAdded, rootTreeNodeID, treeTypeID) values (?, ?, ?)',
            [$date, $rootNode->getTreeNodeID(), $type->getTreeTypeID()]
        );
        $treeID = $db->lastInsertId();
        $rootNode->setTreeNodeTreeID($treeID);

        return $treeID;
    }

    final public static function getByID($treeID)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make('database')->connection();
        $row = $db->fetchAssoc('select * from Trees where treeID = ?', [$treeID]);
        if ($row) {
            $tt = TreeType::getByID($row['treeTypeID']);
            $class = $tt->getTreeTypeClass();
            $tree = $app->make($class);
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
        $loc = Localization::getInstance();
        $loc->pushActiveContext(Localization::CONTEXT_SYSTEM);
        try {
            $app = Application::getFacadeApplication();
            $db = $app->make('database')->connection();
            $r = $db->executeQuery('select treeID from Trees order by treeID asc');
            while ($row = $r->fetch()) {
                try {
                    $tree = static::getByID($row['treeID']);
                } catch (Exception $x) {
                    $tree = null;
                }
                if (isset($tree)) {
                    /* @var $tree Tree */
                    $treeName = $tree->getTreeName();
                    if (is_string($treeName) && ($treeName !== '')) {
                        $translations->insert('TreeName', $treeName);
                    }
                    $rootNode = $tree->getRootTreeNodeObject();
                    /* @var $rootNode TreeNode */
                    if (isset($rootNode)) {
                        $rootNode->exportTranslations($translations);
                    }
                }
            }
        } catch (Exception $x) {
            $loc->popActiveContext();
            throw $x;
        }
        $loc->popActiveContext();

        return $translations;
    }
}
