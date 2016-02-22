<?php
namespace Concrete\Core\User\Search;

use Concrete\Core\Attribute\Category\UserCategory;
use Concrete\Core\Search\ProviderInterface;
use Concrete\Core\User\Search\ColumnSet\Available;
use Concrete\Core\User\Search\ColumnSet\ColumnSet;

class SearchProvider implements ProviderInterface
{

    protected $userCategory;

    public function __construct(UserCategory $userCategory)
    {
        $this->userCategory = $userCategory;
    }

    public function getCustomAttributeKeys()
    {
        return $this->userCategory->getList();
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
