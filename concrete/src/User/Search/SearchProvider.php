<?php
namespace Concrete\Core\User\Search;

use Concrete\Core\Attribute\Category\UserCategory;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Search\AbstractSearchProvider;
use Concrete\Core\Search\ProviderInterface;
use Concrete\Core\User\Group\GroupList;
use Concrete\Core\User\Search\ColumnSet\Available;
use Concrete\Core\User\Search\ColumnSet\ColumnSet;
use Concrete\Core\User\Search\ColumnSet\DefaultSet;
use Concrete\Core\User\Search\Result\Result;
use Concrete\Core\User\UserList;
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
        return $this->userCategory->getSearchableList();
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

    public function getItemList()
    {
        return new UserList();
    }

    public function getDefaultColumnSet()
    {
        return new DefaultSet();
    }

    public function createSearchResultObject($columns, $list)
    {
        return new Result($columns, $list);
    }

    public function getSearchResultFromQuery(Query $query)
    {
        $result = parent::getSearchResultFromQuery($query);
        $u = new \User();
        if (!$u->isSuperUser()) {
            $gIDs = array(-1);
            $gs = new GroupList();
            $groups = $gs->getResults();
            foreach ($groups as $g) {
                $gp = new \Permissions($g);
                if ($gp->canSearchUsersInGroup()) {
                    $gIDs[] = $g->getGroupID();
                }
            }
            $result->getItemListObject()->getQueryObject()->leftJoin("u", "UserGroups", "ugRequired", "ugRequired.uID = u.uID");
            $groups = 'ugRequired.gID in (' . implode(',', $gIDs) . ')';
            $gg = \Group::getByID(REGISTERED_GROUP_ID);
            $ggp = new \Permissions($gg);
            if ($ggp->canSearchUsersInGroup()) {
                $null = 'ugRequired.gID is null';
            }
            $result->getItemListObject()->getQueryObject()->select('distinct (u.uID)');
            $expr = $result->getItemListObject()->getQueryObject()->expr()->orX($groups, $null);
            $result->getItemListObject()->getQueryObject()->andwhere($expr);
        }

        return $result;
    }


}
