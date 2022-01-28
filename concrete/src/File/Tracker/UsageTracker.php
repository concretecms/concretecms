<?php

namespace Concrete\Core\File\Tracker;

use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Attribute\AttributeValueInterface;
use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\Statistics\UsageTracker\FileUsageRecord;
use Concrete\Core\Entity\Statistics\UsageTracker\FileUsageRepository;
use Concrete\Core\Page\Collection\Collection;
use Concrete\Core\Page\Controller\PageController;
use Concrete\Core\Statistics\UsageTracker\TrackableInterface;
use Concrete\Core\Statistics\UsageTracker\TrackerInterface;
use Doctrine\ORM\EntityManagerInterface;

class UsageTracker implements TrackerInterface
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $manager;

    /**
     * @var FileUsageRepository
     */
    private $repository;

    public function __construct(EntityManagerInterface $manager)
    {

        $this->manager = $manager;
        $this->repository = $manager->getRepository(FileUsageRecord::class);
    }

    /**
     * Track a trackable object
     * Any object could be passed to this method so long as it implements TrackableInterface
     * @param \Concrete\Core\Statistics\UsageTracker\TrackableInterface $trackable
     * @return void
     */
    public function track(TrackableInterface $trackable)
    {
        if ($trackable instanceof Collection) {
            $this->trackCollection($trackable);
        }

        if ($trackable instanceof PageController) {
            $this->trackCollection($trackable->getPageObject());
        }

        if ($trackable instanceof BlockController) {
            if ($collection = $trackable->getCollectionObject()) {
                $this->trackBlocks($trackable->getCollectionObject(), [$trackable]);
            }
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

        if ($trackable instanceof BlockController) {
            // Delete all blocks with this id
            $this->manager->createQueryBuilder()
                ->delete(FileUsageRecord::class, 'r')
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

        $version = $collection->getVersionObject();
        $category = $version->getObjectAttributeCategory();
        $values = $category->getAttributeValues($version);
        $this->trackAttributes($collection, $values);
    }

    /**
     * Forget about a collection object
     * @param \Concrete\Core\Page\Collection\Collection $collection
     */
    private function forgetCollection(Collection $collection)
    {
        $query_builder = $this->manager->createQueryBuilder();
        $query_builder
            ->delete(FileUsageRecord::class, 'r')
            ->where('r.collection_id = :collection_id')
            ->setParameter('collection_id', $collection->getCollectionID())
            ->getQuery()->execute();
    }

    /**
     * @param \Concrete\Core\Page\Collection\Collection $collection
     * @param AttributeValueInterface[] $attributes
     */
    private function trackAttributes(Collection $collection, array $attributes)
    {
        $this->trackTrackables(
            $collection,
            $this->getTrackables($attributes, function (AttributeValueInterface $value) {
                return $value->getController();
            }),
            function (Collection $collection, \Concrete\Core\Attribute\Controller $attribute, $fileId) {
                $this->persist(
                    $fileId,
                    $collection->getCollectionID(),
                    $collection->getVersionID(),
                    0);
                return true;
            });
    }

    /**
     * Track a list of blocks for a collection
     * @param \Concrete\Core\Page\Collection\Collection $collection
     * @param Block[]|BlockController[] $blocks
     */
    private function trackBlocks(Collection $collection, array $blocks)
    {
        $this->trackTrackables(
            $collection,
            $this->getTrackables($blocks, function ($block) {
                if ($block instanceof Block) {
                    return $block->getController();
                }

                return $block;
            }),
            function (Collection $collection, BlockController $controller, $fileId) {
                $this->persist(
                    $fileId,
                    $collection->getCollectionID(),
                    $collection->getVersionID(),
                    $controller->getBlockObject()->getBlockID());
                return true;
            });
    }

    /**
     * @param array $list
     * @param callable $transformer
     * @return \Generator
     * @internal param bool $getController
     */
    private function getTrackables(array $list, callable $transformer = null)
    {
        foreach ($list as $item) {
            if ($transformer) {
                $item = $transformer($item);
            }

            if ($item instanceof FileTrackableInterface) {
                yield $item;
            }
        }
    }

    /**
     * @param \Concrete\Core\Page\Collection\Collection $collection
     * @param \Iterator|FileTrackableInterface[] $trackables
     * @param callable $persist A callable that manages persisting the trackable
     */
    private function trackTrackables(Collection $collection, $trackables, callable $persist)
    {
        $buffer = 0;

        foreach ($trackables as $trackable) {
            foreach ($trackable->getUsedFiles() as $file) {
                if ($file instanceof File) {
                    $file = $file->getFileID();
                } elseif (uuid_is_valid($file)) {
                    $fo = \Concrete\Core\File\File::getByUUID($file);
                    $file = $fo->getFileID();
                }

                if ($file && $persist($collection, $trackable, (int) $file)) {
                    $buffer++;
                }

                if ($buffer > 2) {
                    $this->manager->flush();
                    $buffer = 0;
                }
            }
        }

        if ($buffer) {
            $this->manager->flush();
        }
    }

    /**
     * @param $file_id
     * @param $collection_id
     * @param $collection_version_id
     * @param $block_id
     * @return bool
     */
    private function persist($file_id, $collection_id, $collection_version_id, $block_id)
    {
        $search = [
            'collection_id' => $collection_id,
            'collection_version_id' => $collection_version_id,
            'block_id' => $block_id,
            'file_id' => $file_id
        ];

        if ($this->repository->findOneBy($search)) {
            return false;
        }

        $record = new FileUsageRecord();

        $record->setCollectionId($collection_id);
        $record->setCollectionVersionId($collection_version_id);
        $record->setBlockId($block_id);
        $record->setFileId($file_id);

        $this->manager->merge($record);
    }

}
