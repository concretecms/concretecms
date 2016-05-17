<?php

namespace Concrete\Core\Page\Stack;

use Concrete\Block\CoreStackDisplay\Controller;
use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Entity\Statistics\UsageTracker\StackUsageRecord;
use Concrete\Core\Page\Collection\Collection;
use Concrete\Core\Page\Controller\PageController;
use Concrete\Core\Statistics\UsageTracker\TrackableInterface;
use Concrete\Core\Statistics\UsageTracker\TrackerInterface;
use Doctrine\ORM\EntityManager;

class UsageTracker implements TrackerInterface
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $manager;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $repository;

    public function __construct(EntityManager $manager)
    {

        $this->manager = $manager;
        $this->repository = $manager->getRepository(StackUsageRecord::class);
    }

    /**
     * Track a trackable object
     * Any object could be passed to this method so long as it implements TrackableInterface
     * @param \Concrete\Core\Statistics\UsageTracker\TrackableInterface $trackable
     * @return static|TrackerInterface
     */
    public function track(TrackableInterface $trackable)
    {
        if ($trackable instanceof Collection) {
            $this->trackCollection($trackable);
        }

        if ($trackable instanceof PageController) {
            $this->trackCollection($trackable->getPageObject());
        }

        if ($trackable instanceof Controller) {
            $this->trackBlocks($trackable->getCollectionObject(), [$trackable]);
        }
    }

    /**
     * Forget a trackable object
     * Any object could be passed to this method so long as it implements TrackableInterface
     * @param \Concrete\Core\Statistics\UsageTracker\TrackableInterface $trackable
     * @return static|TrackerInterface
     */
    public function forget(TrackableInterface $trackable)
    {
        if ($trackable instanceof Collection) {
            $this->forgetCollection($trackable);
        }

        if ($trackable instanceof PageController) {
            $this->forgetCollection($trackable->getPageObject());
        }

        if ($trackable instanceof Controller) {
            // Delete all blocks with this id
            $this->manager->createQueryBuilder()
                ->delete(StackUsageRecord::class, 'r')
                ->where('r.block_id = :block_id')
                ->setParameter('block_id', $trackable->getBlockObject()->getBlockID())
                ->getQuery()->execute();
        }
    }

    /**
     * Track a collection object
     * @param \Concrete\Core\Page\Collection\Collection $collection
     */
    private function trackCollection(Collection $collection)
    {
        $blocks = $collection->getBlocks();
        $this->trackBlocks($collection, $blocks);
    }

    /**
     * Forget about a collection object
     * @param \Concrete\Core\Page\Collection\Collection $collection
     */
    private function forgetCollection(Collection $collection)
    {
        $query_builder = $this->manager->createQueryBuilder();
        $query_builder
            ->delete(StackUsageRecord::class, 'r')
            ->where('r.collection_id = :collection_id')
            ->setParameter('collection_id', $collection->getCollectionID())
            ->getQuery()->execute();
    }

    /**
     * Track a list of blocks for a collection
     * @param \Concrete\Core\Page\Collection\Collection $collection
     * @param Block[]|BlockController[] $blocks
     */
    private function trackBlocks(Collection $collection, array $blocks)
    {
        $version = $collection->getVersionID();
        $buffer = 0;

        foreach ($blocks as $block) {

            if ($block instanceof Controller) {
                $controller = $block;
                $block = $controller->getBlockObject();
            }

            if ($block->getBlockTypeHandle() == BLOCK_HANDLE_STACK_PROXY) {
                if (!$controller) {
                    $controller = $block->getController();
                }

                $this->persist(
                    $controller->getStackID(),
                    $collection->getCollectionID(),
                    $version,
                    $block->getBlockID());
                $buffer++;
            }

            if ($buffer > 2) {
                $this->manager->flush();
                $buffer = 0;
            }
        }

        if ($buffer) {
            // Sometimes you just need an extra flush...
            $this->manager->flush();
        }
    }

    /**
     * @param $stack_id
     * @param $collection_id
     * @param $collection_version_id
     * @param $block_id
     */
    private function persist($stack_id, $collection_id, $collection_version_id, $block_id)
    {
        $search = [
            'block_id' => $block_id
        ];

        if (!$record = $this->repository->findOneBy($search)) {
            $record = new StackUsageRecord();
        }

        $record->setCollectionId($collection_id);
        $record->setCollectionVersionId($collection_version_id);
        $record->setBlockId($block_id);
        $record->setStackId($stack_id);
        $this->manager->merge($record);
    }

}
