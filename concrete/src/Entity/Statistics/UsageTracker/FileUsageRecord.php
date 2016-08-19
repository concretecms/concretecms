<?php

namespace Concrete\Core\Entity\Statistics\UsageTracker;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class StackUsageRecord
 * @ORM\Entity(repositoryClass="\Concrete\Core\Entity\Statistics\UsageTracker\FileUsageRepository")
 * @ORM\Table(name="FileUsageRecord",
 *   indexes={
 *     @ORM\Index(name="block", columns={"block_id"}),
 *     @ORM\Index(name="collection_version", columns={"collection_id","collection_version_id"})
 *   }
 * )
 */
class FileUsageRecord
{

    /**
     * @ORM\Id @ORM\Column(type="integer")
     * @var int
     */
    protected $file_id;

    /**
     * @ORM\Id @ORM\Column(type="integer")
     * @var int
     */
    protected $block_id;

    /**
     * @ORM\Id @ORM\Column(type="integer")
     * @var int
     */
    protected $collection_id;

    /**
     * @ORM\Id @ORM\Column(type="integer")
     * @var int
     */
    protected $collection_version_id;

    /**
     * @return int
     */
    public function getFileId()
    {
        return $this->file_id;
    }

    /**
     * @param int $file_id
     */
    public function setFileId($file_id)
    {
        $this->file_id = $file_id;
    }

    /**
     * @return int
     */
    public function getBlockId()
    {
        return $this->block_id;
    }

    /**
     * @param int $block_id
     */
    public function setBlockId($block_id)
    {
        $this->block_id = $block_id;
    }

    /**
     * @return int
     */
    public function getCollectionId()
    {
        return $this->collection_id;
    }

    /**
     * @param int $collection_id
     */
    public function setCollectionId($collection_id)
    {
        $this->collection_id = $collection_id;
    }

    /**
     * @return int
     */
    public function getCollectionVersionId()
    {
        return $this->collection_version_id;
    }

    /**
     * @param int $collection_version_id
     */
    public function setCollectionVersionId($collection_version_id)
    {
        $this->collection_version_id = $collection_version_id;
    }

}
