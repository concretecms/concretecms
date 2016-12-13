<?php
namespace Concrete\Core\Tree\Type;

use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Access\Entity\GroupEntity;
use Concrete\Core\Permission\Key\CategoryTreeNodeKey;
use Concrete\Core\Permission\Key\ExpressTreeNodeKey;
use Concrete\Core\Tree\Node\Type\Category;
use Concrete\Core\Tree\Node\Type\ExpressEntryCategory;
use Concrete\Core\Tree\Tree;
use Concrete\Core\User\Group\Group as ConcreteGroup;
use Database;

class ExpressEntryResults extends Tree
{
    /** Returns the standard name for this tree
     * @return string
     */
    public function getTreeName()
    {
        return 'Express Entries';
    }

    /** Returns the display name for this tree (localized and escaped accordingly to $format)
     * @param  string $format = 'html' Escape the result in html format (if $format is 'html'). If $format is 'text' or any other value, the display name won't be escaped.
     *
     * @return string
     */
    public function getTreeDisplayName($format = 'html')
    {
        $value = tc('TreeName', 'Express Entries Tree');
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
        $treeTypeID = $db->GetOne('select treeTypeID from TreeTypes where treeTypeHandle = ?', array('express_entry_results'));
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
        $rootNode = ExpressEntryCategory::add();
        $treeID = parent::create($rootNode);
        $tree = self::getByID($treeID);

        $adminGroup = ConcreteGroup::getByID(ADMIN_GROUP_ID);
        if (is_object($adminGroup)) {
            $adminGroupEntity = GroupEntity::getOrCreate($adminGroup);
            $permissions = ['view_express_entries', 'add_express_entries', 'edit_express_entries', 'delete_express_entries'];
            foreach($permissions as $handle) {
                $pk = ExpressTreeNodeKey::getByHandle($handle);
                if (is_object($pk)) {
                    $pk->setPermissionObject($rootNode);
                    $pa = Access::create($pk);
                    $pa->addListItem($adminGroupEntity);
                    $pt = $pk->getPermissionAssignmentObject();
                    $pt->assignPermissionAccess($pa);
                }
            }
        }

        return $tree;
    }

    protected function loadDetails()
    {
    }

}
