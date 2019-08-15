<?php
namespace Concrete\Core\Permission\Response;

use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker as Permissions;

class TopicTreeNodeResponse extends Response
{
    protected function canAccessTopics()
    {
        $c = Page::getByPath('/dashboard/system/attributes/topics');
        $cp = new Permissions($c);

        return $cp->canViewPage();
    }

    public function canEditTreeNodePermissions()
    {
        return $this->canAccessTopics();
    }

    public function canViewTreeNode()
    {
        return $this->validate('view_topic_tree_node');
    }

    public function canDuplicateTreeNode()
    {
        return $this->canAccessTopics();
    }

    public function canEditTreeNode()
    {
        return $this->canAccessTopics();
    }

    public function canDeleteTreeNode()
    {
        return $this->canAccessTopics();
    }

    public function canAddTreeSubNode()
    {
        return $this->canAccessTopics();
    }

    public function canAddTopicTreeNode()
    {
        return $this->canAccessTopics();
    }
}
