<?php
namespace Concrete\Core\File\Search;

use Concrete\Core\Attribute\Category\FileCategory;
use Concrete\Core\Search\ProviderInterface;
use Concrete\Core\File\Search\ColumnSet\Available;
use Concrete\Core\File\Search\ColumnSet\ColumnSet;

class SearchProvider implements ProviderInterface
{

    protected $category;

    public function __construct(FileCategory $category)
    {
        $this->category = $category;
    }

    public function getCustomAttributeKeys()
    {
        return $this->category->getList();
    }

    public function getAvailableColumnSet()
    {
        return new Available();
    }

    public function getCurrentColumnSet()
    {
        return ColumnSet::getCurrent();
    }
}
