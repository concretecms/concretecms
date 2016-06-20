<?php

namespace Concrete\Core\Entity\Statistics\UsageTracker;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class StackUsageRecord
 * @ORM\Entity
 * @ORM\Table(name="StackUsageRecord",
 *   indexes={
 *     @ORM\Index(name="block", columns={"block_id"}),
 *     @ORM\Index(name="collection_version", columns={"collection_id","collection_version_id"})
 *   }
 * )
 */
class StackUsageRecord
{

    /**
     * @ORM\Id @ORM\Column(type="integer")
     * @var int
     */
    protected $stack_id;

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
    public function getStackId()
    {
        return $this->stack_id;
    }

    /**
     * @param int $stack_id
     */
    public function setStackId($stack_id)
    {
        $this->stack_id = $stack_id;
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
