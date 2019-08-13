<?php

namespace Concrete\Core\Tree\Menu\Item\Topic;

/**
 * @since 8.0.0
 */
class EditTopicItem extends TopicItem
{

    public function getDialogTitle()
    {
        return t('Edit Topic');
    }

    public function getAction()
    {
        return 'edit-node';
    }

    public function getActionURL()
    {
        return \URL::to('/ccm/system/dialogs/tree/node/edit/topic?treeNodeID=' . $this->topic->getTreeNodeID());
    }

    public function getItemName()
    {
        return t('Edit Topic');
    }


}