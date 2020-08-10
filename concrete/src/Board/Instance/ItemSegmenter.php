<?php
namespace Concrete\Core\Board\Instance;

use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Entity\Board\InstanceItem;
use Concrete\Core\Entity\Board\Item;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;

class ItemSegmenter implements LoggerAwareInterface
{

    use LoggerAwareTrait;

    public function getLoggerChannel()
    {
        Channels::CHANNEL_CONTENT;
    }

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
        $qb->select('instanceItem, item')
            ->from(InstanceItem::class, 'instanceItem')
            ->innerJoin('instanceItem.item', 'item')
            ->where($qb->expr()->eq('instanceItem.instance', $instance))
            ->andWhere($qb->expr()->eq('instanceItem.dateAddedToBoard', 0));

        $board = $instance->getBoard();
        switch($board->getSortBy()) {
            case $board::ORDER_BY_RELEVANT_DATE_ASC:
                $qb->orderBy('item.relevantDate', 'asc');
                $qb->andWhere($qb->expr()->gte('item.relevantDate', time()));
                break;
            default:
                $qb->andWhere($qb->expr()->lte('item.relevantDate', time()));
                $qb->orderBy('item.relevantDate', 'desc');
                break;
        }

        $items = $qb->getQuery()->execute();
        $this->logger->debug(t('%s items returned from item segmenter', count($items)));
        return $items;
    }


}
