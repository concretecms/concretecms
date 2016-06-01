<?php
namespace Concrete\Core\File\Search\Result;

use Concrete\Core\Entity\File\File;
use Concrete\Core\Search\Result\Item as SearchResultItem;
use Concrete\Core\Search\Result\Result as SearchResult;
use Concrete\Core\Search\Column\Set;
use Concrete\Core\Tree\Node\Node;

class Item extends SearchResultItem
{
    public function __construct(SearchResult $result, Set $columns, $item)
    {
        parent::__construct($result, $columns, $item);
        $this->populateDetails($item);
    }

    protected function populateDetails($item)
    {
        if ($item instanceof Node) {
            $obj = $item->getTreeNodeJSON();
        } else if ($item instanceof File) {
            $obj = $item->getJSONObject();
            $obj->treeNodeTypeHandle = 'file'; // We include this so our bulk menu works when searching.
        }
        foreach ($obj as $key => $value) {
            $this->{$key} = $value;
        }
        //$this->isStarred = $item->isStarred();
    }
}
