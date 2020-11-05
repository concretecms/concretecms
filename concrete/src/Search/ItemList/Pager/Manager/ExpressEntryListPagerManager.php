<?php
namespace Concrete\Core\Search\ItemList\Pager\Manager;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\ObjectManager;
use Concrete\Core\Page\Search\ColumnSet\Available;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class ExpressEntryListPagerManager extends AbstractPagerManager
{

    /**
     * @var Entity
     */
    protected $entity;

    public function __construct(Entity $entity, ItemList $itemList)
    {
        $this->entity = $entity;
        parent::__construct($itemList);
    }

    /**
     * @param $mixed Entry
     * @return mixed
     */
    public function getCursorStartValue($mixed)
    {
        return $mixed->getID();
    }

    public function getCursorObject($cursor)
    {
        $entry = app(ObjectManager::class)->getEntry($cursor);
        if ($entry) {
            return $entry;
        }
    }

    public function getAvailableColumnSet()
    {
        return new \Concrete\Core\Express\Search\ColumnSet\Available($this->entity->getAttributeKeyCategory());
    }

    public function sortListByCursor(PagerProviderInterface $itemList, $direction)
    {
        $itemList->getQueryObject()->addOrderBy('e.exEntryID', $direction);
    }



}