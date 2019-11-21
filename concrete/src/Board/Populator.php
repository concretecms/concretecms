<?php
namespace Concrete\Core\Board;

use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\Item;
use Concrete\Core\Entity\Board\ItemBatch;
use Doctrine\ORM\EntityManager;

class Populator
{

    /**
     * @var EntityManager
     */
    protected $entityManager;
    
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    public function reset(Board $board)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->delete(Item::class, 'i')
            ->where('i.board = :board');
        $queryBuilder->setParameter('board', $board);
        $queryBuilder->getQuery()->execute();

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->delete(ItemBatch::class, 'ib')
            ->where('ib.board = :board');
        $queryBuilder->setParameter('board', $board);
        $queryBuilder->getQuery()->execute();
    }
    
    public function populate(Board $board)
    {
        $configuredDataSources = $board->getDataSources();
        $batch = new ItemBatch();
        $batch->setBoard($board);
        $this->entityManager->persist($batch);
        foreach($configuredDataSources as $configuredDataSource) {
            $configuration = $configuredDataSource->getConfiguration();
            $dataSource = $configuredDataSource->getDataSource();
            $dataSourceDriver = $dataSource->getDriver();
            $populator = $dataSourceDriver->getBoardPopulator();
            $objects = $populator->getDataSourceObjects($board, $configuration);
            foreach($objects as $object) {
                $item = new Item();
                $item->setBoard($board);
                $item->setDataSource($configuredDataSource);
                $item->setDateCreated($batch->getDateCreated());
                $item->setBatch($batch);
                $item->setRelevantDate($populator->getObjectRelevantDate($object));
                $block = $populator->createBoardItemBlock($object);
                if ($block) {
                    $item->setBlockID($block->getBlockID());
                }
                $this->entityManager->persist($item);
            }
        }
        $this->entityManager->flush();
    }
    
    public function rebuild(Board $board)
    {
        $this->reset($board);
        $this->populate($board);
    }

}
