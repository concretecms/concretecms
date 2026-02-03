<?php

namespace Concrete\Core\File\Tracker;

use Concrete\Core\Attribute\AttributeValueInterface;
use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\Statistics\UsageTracker\FileUsageRecord;
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
     * @var \Concrete\Core\Entity\Statistics\UsageTracker\FileUsageRepository
     */
    private $repository;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
        $this->repository = $manager->getRepository(FileUsageRecord::class);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Statistics\UsageTracker\TrackerInterface::track()
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
                $this->trackBlocks($collection, [$trackable]);
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Statistics\UsageTracker\TrackerInterface::forget()
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
     * Track a collection object.
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
     * Forget about a collection object.
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
     * @param \Concrete\Core\Attribute\AttributeValueInterface[] $attributes
     */
    private function trackAttributes(Collection $collection, array $attributes)
    {
        $this->trackTrackables(
            $collection,
            $this->getTrackables($attributes, static function (AttributeValueInterface $value) {
                return $value->getController();
            }),
            function (Collection $collection, \Concrete\Core\Attribute\Controller $attribute, $fileId) {
                $this->persist(
                    $fileId,
                    $collection->getCollectionID(),
                    $collection->getVersionID(),
                    0
                );

                return true;
            }
        );
    }

    /**
     * Track a list of blocks for a collection
     *
     * @param \Concrete\Core\Block\Block[]|\Concrete\Core\Block\BlockController[] $blocks
     */
    private function trackBlocks(Collection $collection, array $blocks)
    {
        $this->trackTrackables(
            $collection,
            $this->getTrackables($blocks, static function ($block) {
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
                    $controller->getBlockObject()->getBlockID()
                );

                return true;
            }
        );
    }

    /**
     * @return \Generator|\Concrete\Core\File\Tracker\FileTrackableInterface[]
     */
    private function getTrackables(array $list, ?callable $transformer = null)
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
     * @param \Iterator|\Concrete\Core\File\Tracker\FileTrackableInterface[] $trackables
     * @param callable $persist A callable that manages persisting the trackable
     */
    private function trackTrackables(Collection $collection, $trackables, callable $persist)
    {
        $buffer = 0;
        foreach ($trackables as $trackable) {
            foreach ($trackable->getUsedFiles() as $file) {
                $fileID = 0;
                if ($file instanceof File) {
                    $fileID = (int) $file->getFileID();
                } elseif (is_string($file) && uuid_is_valid($file)) {
                    $fo = \Concrete\Core\File\File::getByUUID($file);
                    if ($fo) {
                        $fileID = (int) $fo->getFileID();
                    }
                } elseif (is_numeric($file)) {
                    $fileID = (int) $file;
                }
                if ($fileID !== 0 && $persist($collection, $trackable, $fileID)) {
                    $buffer++;
                    if ($buffer > 2) {
                        $this->manager->flush();
                        $buffer = 0;
                    }
                }
            }
        }
        if ($buffer !== 0) {
            $this->manager->flush();
        }
    }

    /**
     * @param int $file_id
     * @param int $collection_id
     * @param int $collection_version_id
     * @param int $block_id
     *
     * @return bool returns true if we created a new entity, false if it already existed
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

        return true;
    }
}
