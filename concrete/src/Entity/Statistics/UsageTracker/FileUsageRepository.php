<?php

namespace Concrete\Core\Entity\Statistics\UsageTracker;

use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Page\Collection\Collection;
use Concrete\Core\Page\Collection\Version\Version;

class FileUsageRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * Find usage records related to a file
     * @param File|int $file The file object or the file id
     * @return FileUsageRecord[]
     */
    public function findByFile($file)
    {
        if ($file instanceof File) {
            $file = $file->getFileID();
        }

        return $this->findBy([
            'file_id' => $file
        ]);
    }

    /**
     * Find FileUsageRecords given a collection and a version
     * @param Collection|int $collection A collection or a collection ID
     * @param Version|int|null $version The version, a version ID, or null
     * @return \Concrete\Core\Entity\Statistics\UsageTracker\FileUsageRecord[]
     */
    public function findByCollection($collection, $version = null)
    {
        if ($collection instanceof Collection) {
            $collection = $collection->getCollectionID();
        }

        $criteria = [
            'collection_id' => $collection
        ];

        if ($version && $version instanceof Version) {
            $version = $version->getVersionID();
        }

        if ($version) {
            $criteria['collection_version_id'] = $version;
        }

        return $this->findBy($criteria);
    }

    /**
     * Find FileUsageRecords given a block
     * @param $block
     * @return \Concrete\Core\Entity\Statistics\UsageTracker\FileUsageRecord[]
     */
    public function findByBlock($block)
    {
        if ($block instanceof Block) {
            $block = $block->getBlockID();
        } elseif ($block instanceof BlockController) {
            $block = $block->getBlockObject()->getBlockID();
        }

        return $this->findBy([
            'block_id' => $block
        ]);
    }

    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     * @return FileUsageRecord[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }

}
