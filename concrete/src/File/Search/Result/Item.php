<?php
namespace Concrete\Core\File\Search\Result;

use Concrete\Core\Entity\File\File;
use Concrete\Core\Search\Result\Item as SearchResultItem;
use Concrete\Core\Search\Result\Result as SearchResult;
use Concrete\Core\Search\Column\Set;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Concrete\Core\Tree\Node\Type\SearchPreset;
use Concrete\Core\Tree\Node\Type\File as FileNode;

class Item extends SearchResultItem
{
    public function __construct(SearchResult $result, Set $columns, $item)
    {
        parent::__construct($result, $columns, $item);
        $this->populateDetails($item);
    }

    public function getListingThumbnailImage()
    {
        $item = $this->getItem();
        if ($item instanceof SearchPreset || $item instanceof FileFolder) {
            $icon = $item->getListFormatter()->getIconElement();
        } else if ($item instanceof \Concrete\Core\Tree\Node\Type\File) {
            $icon = $item->getTreeNodeFileObject()->getListingThumbnailImage();
        }
        return $icon;
    }

    public function getDetailsURL()
    {
        if ($this->getItem() instanceof FileNode) {
            return app('url/resolver/path')->resolve(['/dashboard/files/details',
                $this->getItem()->getTreeNodeFileID()]
            );
        }
        if ($this->getItem() instanceof FileFolder) {
            return app('url/resolver/path')->resolve(['/dashboard/files/search', 'folder',
                    $this->getItem()->getTreeNodeID()]
            );
        }

        return '#';
    }

    /**
     * Returns an integer for a file ID if the result is a file, otherwise returns null.
     * @return int
     */
    public function getResultFileID()
    {
        if ($this->getItem() instanceof FileNode) {
            return $this->getItem()->getTreeNodeFileID();
        }
        return null;
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
