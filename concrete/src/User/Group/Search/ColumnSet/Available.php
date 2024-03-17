<?php

namespace Concrete\Core\User\Group\Search\ColumnSet;

use Concrete\Core\Tree\Node\Node;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\Group\Search\ColumnSet\Column\GroupIdColumn;

class Available extends DefaultSet
{

    public function __construct()
    {
        parent::__construct();
        $this->addColumn(new GroupIdColumn());
    }

    /**
     * @param Node $node
     * @return mixed
     */
    public static function getType($node)
    {
        switch ($node->getTreeNodeTypeHandle()) {
            case 'group_folder':
                return t('Folder');
            default:
                if ($node->getTreeNodeTypeHandle() == 'group') {
                    /** @var Group $group */
                    $group = $node->getTreeNodeGroupObject();
                    $countOfChildGroups = count($group->getChildGroups());

                    if ($countOfChildGroups > 0) {
                        return t('Group with child groups');
                    } else {
                        $groupType = $group->getGroupType();

                        if (is_object($groupType)) {
                            return $groupType->getDisplayName();
                        }
                    }
                }
                return t('Group');
        }
    }

    /**
     * @param Node $node
     * @return mixed
     */
    public static function getMemberCount($node)
    {
        if ($node->getTreeNodeTypeHandle() == 'group_folder') {
            return '';
        } else if ($node->getTreeNodeTypeHandle() == 'group') {
            /** @var Group $group */
            $group = $node->getTreeNodeGroupObject();
            return (int)$group->getGroupMembersNum();
        }
    }

    /**
     * @param Node $node
     * @return mixed
     */
    public function getGroupID($node)
    {
        if ($node->getTreeNodeTypeHandle() == 'group_folder') {
            return '';
        } else if ($node->getTreeNodeTypeHandle() == 'group') {
            $group = $node->getTreeNodeGroupObject();
            if (is_object($group)) {
                return $group->getGroupID();
            }
        }
    }

    /**
     * @param Node $node
     * @return mixed
     */
    public static function getGroupName($node)
    {
        if ($node->getTreeNodeTypeHandle() == 'group_folder') {
            return $node->getTreeNodeName();
        } else if ($node->getTreeNodeTypeHandle() == 'group') {
            $group = $node->getTreeNodeGroupObject();
            if (is_object($group)) {
                return $group->getGroupName();
            }
        }
    }
}
