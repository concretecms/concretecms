<?php
namespace Concrete\Core\User\Search;

use Concrete\Core\Attribute\Category\UserCategory;
use Concrete\Core\Search\AbstractSearchProvider;
use Concrete\Core\Search\ProviderInterface;
use Concrete\Core\User\Search\ColumnSet\Available;
use Concrete\Core\User\Search\ColumnSet\ColumnSet;
use Symfony\Component\HttpFoundation\Session\Session;

class SearchProvider extends AbstractSearchProvider
{

    protected $userCategory;

    public function __construct(UserCategory $userCategory, Session $session)
    {
        $this->userCategory = $userCategory;
        parent::__construct($session);
    }

    public function getSessionNamespace()
    {
        return 'user';
    }

    public function getCustomAttributeKeys()
    {
        return $this->userCategory->getList();
    }

    public function getBaseColumnSet()
    {
        return new ColumnSet();
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
