<?php

namespace Concrete\Core\Tree\Type;

use Concrete\Core\Tree\Node\Type\FileFolder;
use Concrete\Core\Tree\Tree;
use Database;

class FileManager extends Tree
{
    /**
     * Returns the standard name for this tree.
     *
     * @return string
     */
    public function getTreeName()
    {
        return 'File Manager';
    }

    /**
     * Returns the display name for this tree (localized and escaped accordingly to $format).
     *
     * @param  string $format = 'html' Escape the result in html format (if $format is 'html'). If $format is 'text' or any other value, the display name won't be escaped.
     *
     * @return string
     */
    public function getTreeDisplayName($format = 'html')
    {
        $value = tc('TreeName', 'File Manager');
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }

    /**
     * Get the FileManager instance.
     *
     * @return FileManager|null
     */
    public static function get()
    {
        $db = Database::connection();
        $treeTypeID = $db->GetOne('select treeTypeID from TreeTypes where treeTypeHandle = ?', array('file_manager'));
        $treeID = $db->GetOne('select treeID from Trees where treeTypeID = ?', array($treeTypeID));

        return Tree::getByID($treeID);
    }

    public function exportDetails(\SimpleXMLElement $sx)
    {
    }

    /**
     * @return FileManager
     */
    public static function add()
    {
        // copy permissions from the other node.
        $rootNode = FileFolder::add();
        $treeID = parent::create($rootNode);
        $tree = self::getByID($treeID);

        return $tree;
    }

    protected function deleteDetails()
    {
    }

    protected function loadDetails()
    {
    }
}
