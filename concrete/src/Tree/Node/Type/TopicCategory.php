<?php

namespace Concrete\Core\Tree\Node\Type;

use Permissions;
use Core;

class TopicCategory extends Category
{
    public function getTreeNodeTranslationContext()
    {
        return 'TreeNodeCategoryName';
    }
    public function getTreeNodeDisplayName($format = 'html')
    {
        if ($this->getTreeNodeName()) {
            $name = Core::make('helper/text')->unhandle($this->getTreeNodeName());
            $name = tc($this->getTreeNodeTranslationContext(), $name);
            switch ($format) {
                case 'html':
                    return h($name);
                case 'text':
                default:
                    return $name;
            }
        } elseif ($this->treeNodeParentID == 0) {
            return t('Categories');
        }
    }

    public function getPermissionResponseClassName()
    {
        return '\\Concrete\\Core\\Permission\\Response\\TopicCategoryTreeNodeResponse';
    }

    public function getPermissionAssignmentClassName()
    {
        return '\\Concrete\\Core\\Permission\\Assignment\\TopicCategoryTreeNodeAssignment';
    }
    public function getPermissionObjectKeyCategoryHandle()
    {
        return 'topic_category_tree_node';
    }

    public function getTreeNodeJSON()
    {
        $obj = parent::getTreeNodeJSON();
        if (is_object($obj)) {
            $p = new Permissions($this);
            $data = $this->getTreeObject()->getRequestData();
            if (is_array($data) && $data['allowFolderSelection']) {
                $obj->hideCheckbox = false;
            } else {
                $obj->hideCheckbox = true;
            }
            $obj->canAddTopicTreeNode = $p->canAddTopicTreeNode();
            $obj->canAddTopicCategoryTreeNode = $p->canAddTopicCategoryTreeNode();

            return $obj;
        }
    }
}
