<?php
namespace Concrete\Core\Board\Instance;

use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Entity\Board\InstanceItem;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;

class ItemSegmenter
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Responsible for Getting board data items and returning them. This may involve taking a sub-set of all
     * data objects, for example, or it may involve complex weighting. Used by create board instance commands
     * and other commands that populate content into boards.
     *
     * @TODO - implement complex weighting and subset building ;-)
     *
     * @param $instance Instance
     * @return InstanceItem[]
     */
    public function getBoardItemsForInstance(Instance $instance)
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('i')
            ->from(InstanceItem::class, 'i')
            ->where($qb->expr()->eq('i.instance', $instance))
            ->andWhere($qb->expr()->eq('i.dateAddedToBoard', 0));

        $board = $instance->getBoard();
        switch($board->getSortBy()) {
            case $board::ORDER_BY_RELEVANT_DATE_ASC:
                $qb->orderBy('i.relevantDate', 'asc');
                break;
            default:
                $qb->orderBy('i.relevantDate', 'desc');
                break;
        }

        $items = $qb->getQuery()->execute();
        return $items;
    }


}
