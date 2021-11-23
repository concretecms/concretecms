<?php

/** @noinspection PhpUnused */

/** @noinspection PhpDocSignatureInspection */

namespace Concrete\Core\Search\ItemList\Pager\Manager;

use Concrete\Core\User\Group\Group;
use Concrete\Core\User\Group\GroupList;
use Concrete\Core\User\Group\GroupRepository;
use Concrete\Core\User\Search\ColumnSet\Available;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Support\Facade\Facade;

class UserGroupPagerManager extends AbstractPagerManager
{
    /**
     * @param Group $mixed
     * @return mixed
     */
    public function getCursorStartValue($mixed)
    {
        return $mixed->getGroupID();
    }

    public function getCursorObject($cursor)
    {
        $app = Facade::getFacadeApplication();
        /** @var GroupRepository $groupRepository */
        $groupRepository = $app->make(GroupRepository::class);
        return $groupRepository->getGroupById($cursor);
    }

    public function getAvailableColumnSet()
    {
        return new Available();
    }

    /**
     * @param GroupList $itemList
     * @param string $direction
     */
    public function sortListByCursor(PagerProviderInterface $itemList, $direction)
    {
        $itemList->getQueryObject()->addOrderBy('g.gID', $direction);
    }

}