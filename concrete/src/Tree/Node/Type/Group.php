<?php

namespace Concrete\Core\Tree\Node\Type;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Tree\Node\Node as TreeNode;
use Concrete\Core\Tree\Node\Type\Formatter\GroupFormatter;
use Concrete\Core\Tree\Node\Type\Formatter\LegacyGroupFormatter;
use Concrete\Core\Tree\Node\Type\Menu\GroupMenu;
use Concrete\Core\User\Group\Group as UserGroup;
use Concrete\Core\User\Group\GroupRepository;

class Group extends TreeNode
{
    /**
     * @var int|null
     */
    protected $gID;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\ObjectInterface::getPermissionResponseClassName()
     */
    public function getPermissionResponseClassName()
    {
        return '\\Concrete\\Core\\Permission\\Response\\GroupTreeNodeResponse';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\ObjectInterface::getPermissionAssignmentClassName()
     */
    public function getPermissionAssignmentClassName()
    {
        return '\\Concrete\\Core\\Permission\\Assignment\\GroupTreeNodeAssignment';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\ObjectInterface::getPermissionObjectKeyCategoryHandle()
     */
    public function getPermissionObjectKeyCategoryHandle()
    {
        return 'group_tree_node';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Tree\Node\Node::getTreeNodeTypeName()
     */
    public function getTreeNodeTypeName()
    {
        return 'Group';
    }

    /**
     * @return int|null
     */
    public function getTreeNodeGroupID()
    {
        return $this->gID;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Tree\Node\Node::getTreeNodeMenu()
     */
    public function getTreeNodeMenu()
    {
        return new GroupMenu($this);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Tree\Node\Node::getListFormatter()
     */
    public function getListFormatter()
    {
        if (count($this->getTreeNodeGroupObject()->getChildGroups()) > 0) {
            return new LegacyGroupFormatter();
        }

        return new GroupFormatter();
    }

    /**
     * @return \Concrete\Core\User\Group\Group|null
     */
    public function getTreeNodeGroupObject()
    {
        if (!$this->gID) {
            return null;
        }
        $repository = app(GroupRepository::class);

        return $repository->getGroupByID($this->gID);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Tree\Node\Node::getTreeNodeName()
     */
    public function getTreeNodeName()
    {
        $group = $this->getTreeNodeGroupObject();

        return $group === null ? null : $group->getGroupName();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Tree\Node\Node::getTreeNodeDisplayName()
     */
    public function getTreeNodeDisplayName($format = 'html')
    {
        if ($this->treeNodeParentID == 0) {
            return t('All Groups');
        }

        $group = $this->getTreeNodeGroupObject();
        if ($group === null) {
            return null;
        }
        $gName = $group->getGroupDisplayName(false, false);
        switch ($format) {
            case 'html':
                return h($gName);
            case 'text':
            default:
                return $gName;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Tree\Node\Node::loadDetails()
     */
    public function loadDetails()
    {
        $db = app(Connection::class);
        $row = $db->fetchAssociative(
            'SELECT * FROM TreeGroupNodes WHERE treeNodeID = ? LIMIT 1',
            [$this->treeNodeID]
        );
        if ($row !== false) {
            $this->setPropertiesFromArray($row);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Tree\Node\Node::move()
     */
    public function move(TreeNode $newParent)
    {
        switch ($this->gID) {
            case GUEST_GROUP_ID:
                throw new UserMessageException(t("The guest users group can't be moved."));
            case REGISTERED_GROUP_ID:
                throw new UserMessageException(t("The registered users group can't be moved."));
            case ADMIN_GROUP_ID:
                throw new UserMessageException(t("The administrators group can't be moved."));
        }
        parent::move($newParent);
        $g = $this->getTreeNodeGroupObject();
        if (is_object($g)) {
            $g->rescanGroupPathRecursive();
        }
    }

    /**
     * @param int $gID
     *
     * @return \Concrete\Core\Tree\Node\Type\Group|null
     */
    public static function getTreeNodeByGroupID($gID)
    {
        $db = app(Connection::class);
        $treeNodeID = $db->fetchOne('select treeNodeID from TreeGroupNodes where gID = ?', [$gID]);

        return TreeNode::getByID($treeNodeID);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Tree\Node\Node::deleteDetails()
     */
    public function deleteDetails()
    {
        $db = app(Connection::class);
        $db->delete('TreeGroupNodes', ['treeNodeID' => $this->treeNodeID]);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Tree\Node\Node::getTreeNodeJSON()
     */
    public function getTreeNodeJSON()
    {
        $obj = parent::getTreeNodeJSON();
        if (!$obj) {
            return null;
        }
        $obj->gID = $this->gID;
        if (count($this->getTreeNodeGroupObject()->getChildGroups()) > 0) {
            $obj->icon = 'fas fa-folder';
        } else {
            $obj->icon = 'fas fa-users';
        }
        $group = $this->getTreeNodeGroupObject();
        if ($group !== null) {
            foreach ($group->jsonSerialize() as $field => $value) {
                $obj->{$field} = $value;
            }
            $obj->title = $group->getGroupDisplayName(false, false);
        }
        if ($this->treeNodeParentID == 0) {
            $obj->title = t('All Groups');
        }

        return $obj;
    }

    public function setTreeNodeGroup(UserGroup $g)
    {
        $db = app(Connection::class);
        $db->replace('TreeGroupNodes', ['treeNodeID' => $this->getTreeNodeID(), 'gID' => $g->getGroupID()], ['treeNodeID'], true);
        $this->gID = $g->getGroupID();
    }

    /**
     * @param \Concrete\Core\User\Group\Group|false|null $group
     * @param \Concrete\Core\Tree\Node\Node|false|null $parent
     *
     * @return \Concrete\Core\Tree\Node\Type\Group
     */
    public static function add($group = false, $parent = false)
    {
        $node = parent::add($parent);
        if (is_object($group)) {
            $node->setTreeNodeGroup($group);
        }

        return $node;
    }
}
