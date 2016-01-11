<?php
namespace Concrete\Core\Tree\Type;

use Concrete\Core\Tree\Tree;
use Database;
use GroupTreeNode;

class Group extends Tree
{
    /** Returns the standard name for this tree
     * @return string
     */
    public function getTreeName()
    {
        return 'Groups Tree';
    }

    /** Returns the display name for this tree (localized and escaped accordingly to $format)
     * @param  string $format = 'html' Escape the result in html format (if $format is 'html'). If $format is 'text' or any other value, the display name won't be escaped.
     *
     * @return string
     */
    public function getTreeDisplayName($format = 'html')
    {
        $value = tc('TreeName', 'Groups Tree');
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }

    public static function get()
    {
        $db = Database::connection();
        $treeTypeID = $db->GetOne('select treeTypeID from TreeTypes where treeTypeHandle = ?', array('group'));
        $treeID = $db->GetOne('select treeID from Trees where treeTypeID = ?', array($treeTypeID));

        return Tree::getByID($treeID);
    }

    public function exportDetails(\SimpleXMLElement $sx)
    {
    }

    protected function deleteDetails()
    {
    }

    public static function add()
    {
        // copy permissions from the other node.
        $rootNode = GroupTreeNode::add();
        $treeID = parent::create($rootNode);
        $tree = self::getByID($treeID);

        return $tree;
    }

    protected function loadDetails()
    {
    }

    public static function ensureGroupNodes()
    {
        $db = Database::connection();
        $tree = GroupTree::get();
        $rootNode = $tree->getRootTreeNodeObject();
        $rows = $db->GetCol('select Groups.gID from Groups left join TreeGroupNodes on Groups.gID = TreeGroupNodes.gID where TreeGroupNodes.gID is null');
        foreach ($rows as $gID) {
            $g = static::getByID($gID);
            GroupTreeNode::add($g, $rootNode);
        }
    }
}
