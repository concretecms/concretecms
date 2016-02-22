<?php
namespace Concrete\Core\Page\Search;

use Concrete\Core\Attribute\Category\PageCategory;
use Concrete\Core\Search\ProviderInterface;
use Concrete\Core\Page\Search\ColumnSet\Available;
use Concrete\Core\Page\Search\ColumnSet\ColumnSet;

class SearchProvider implements ProviderInterface
{

    protected $category;

    public function __construct(PageCategory $category)
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
