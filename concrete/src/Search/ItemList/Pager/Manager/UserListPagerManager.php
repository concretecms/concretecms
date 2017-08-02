<?php
namespace Concrete\Core\Search\ItemList\Pager\Manager;

use Concrete\Core\Application\Application;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\PageList;
use Concrete\Core\User\Search\ColumnSet\Available;
use Concrete\Core\Search\Column\AttributeKeyColumn;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\User\UserInfo;
use Concrete\Core\User\UserInfoRepository;

class UserListPagerManager extends AbstractPagerManager
{

    public function getCursorStartValue($mixed)
    {
        return $mixed->getUserID();
    }

    public function getCursorObject($cursor)
    {
        $app = Facade::getFacadeApplication();
        return $app->make(UserInfoRepository::class)->getByID($cursor);
    }

    public function getAvailableColumnSet()
    {
        return new Available();
    }

    public function sortListByCursor(PagerProviderInterface $itemList, $direction)
    {
        $itemList->getQueryObject()->addOrderBy('u.uID', $direction);
    }



}