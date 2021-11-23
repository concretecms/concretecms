<?php

namespace Concrete\Core\User\Group\Search\Result;

use Concrete\Core\Search\Result\Item as SearchResultItem;
use Concrete\Core\Search\Result\Result as SearchResult;
use Concrete\Core\Search\Column\Set;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\User\Group\Group;

class Item extends SearchResultItem
{

    public function __construct(SearchResult $result, Set $columns, $item)
    {
        parent::__construct($result, $columns, $item);
        $this->populateDetails($item);
    }

    public function getResultGroupId()
    {
        $node = $this->getItem();
        if ($node instanceof \Concrete\Core\Tree\Node\Type\Group) {
            return $node->getTreeNodeGroupID();
        }
        return null;
    }

    protected function populateDetails($item)
    {
        if ($item instanceof Node) {
            $obj = $item->getTreeNodeJSON();
        } else if ($item instanceof Group) {
            $obj = $item->jsonSerialize();
            $obj->treeNodeTypeHandle = 'group';
        }
        foreach ($obj as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function getDetailsURL()
    {
        if ($this->getItem() instanceof \Concrete\Core\Tree\Node\Type\Group) {
            $group = $this->getItem()->getTreeNodeGroupObject();
            $countOfChildGroups = count($group->getChildGroups());

            if ($countOfChildGroups > 0) {
                return app('url/resolver/path')->resolve(['/dashboard/users/groups', 'folder',
                        $this->getItem()->getTreeNodeID()]
                );
            } else {
                return app('url/resolver/path')->resolve(['/dashboard/users/groups', 'edit',
                        $this->getItem()->getTreeNodeGroupID()]
                );
            }
        }

        if ($this->getItem() instanceof \Concrete\Core\Tree\Node\Type\GroupFolder) {
            return app('url/resolver/path')->resolve(['/dashboard/users/groups', 'folder',
                    $this->getItem()->getTreeNodeID()]
            );
        }

        return '#';
    }
}
