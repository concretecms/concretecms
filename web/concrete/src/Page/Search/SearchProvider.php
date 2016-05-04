<?php
namespace Concrete\Core\Page\Search;

use Concrete\Core\Attribute\Category\PageCategory;
use Concrete\Core\Search\AbstractSearchProvider;
use Concrete\Core\Search\ProviderInterface;
use Concrete\Core\Page\Search\ColumnSet\Available;
use Concrete\Core\Page\Search\ColumnSet\ColumnSet;
use Symfony\Component\HttpFoundation\Session\Session;

class SearchProvider extends AbstractSearchProvider
{

    protected $category;

    public function getSessionNamespace()
    {
        return 'page';
    }


    public function __construct(PageCategory $category, Session $session)
    {
        $this->category = $category;
        parent::__construct($session);
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
