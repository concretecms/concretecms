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
        return Channels::CHANNEL_CONTENT;
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
     * @param $instance Instance
     * @return InstanceItem[]
     */
    public function getBoardItemsForInstance(Instance $instance): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('instanceItem, item')
            ->from(InstanceItem::class, 'instanceItem')
            ->innerJoin('instanceItem.item', 'item')
            ->where($qb->expr()->eq('instanceItem.instance', $instance))
            ->andWhere($qb->expr()->eq('instanceItem.dateAddedToBoard', 0));

        $board = $instance->getBoard();
        switch ($board->getSortBy()) {
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
        $items = $this->filterItemsByDataSource($instance, $items);
        $this->logger->debug(t('%s items returned from data source filterer.', count($items)));
        if ($items) {
            return $items;
        }
        return [];
    }

    /**
     * Goes through each item returned for inclusion in the potential instance. Looks at all the configurations
     * present in the instance, and allows each configuration to optionally prune items out based on criteria
     * they don't like.
     *
     * Q: Why does this happen at this level instead of at the data pool population level?
     * A: In theory, we should be able to populate the board over time, with the data pool remaining mostly stable.
     * That means that if we're trying to keep X duplicate occurrences out of a board, we don't want the occurrences
     * to never be in the data pool, because then as the board exists over time new occurrences will never make it into
     * the board. That's why we need to move this population logic into this method, which uses the whole data pool
     * and then prunes before including in a particular front-end instance.
     *
     * @param Instance $instance
     * @param InstanceItem[] $items
     */
    protected function filterItemsByDataSource(Instance $instance, array $items)
    {
        $configuredDataSources = $instance->getBoard()->getDataSources();
        foreach($configuredDataSources as $configuredDataSource) {
            $configuration = $configuredDataSource->getConfiguration();
            $dataSource = $configuredDataSource->getDataSource();
            $dataSourceDriver = $dataSource->getDriver();
            $filterer = $dataSourceDriver->getItemFilterer();
            if ($filterer && $filterer->configurationSupportsFiltering($configuration)) {
                $items = $filterer->filterItems($configuration, $items);
            }
        }
        return $items;
    }


}
