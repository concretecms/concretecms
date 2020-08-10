<?php
namespace Concrete\Core\Entity\Board;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="BoardInstanceItemBatches")
 */
class InstanceItemBatch
{

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $boardInstanceItemBatchID;

    /**
     * @ORM\OneToMany(targetEntity="InstanceItem", cascade={"remove"}, mappedBy="batch", fetch="EXTRA_LAZY")
     */
    protected $items;

    /**
     * @ORM\ManyToOne(targetEntity="Instance", inversedBy="batches")
     * @ORM\JoinColumn(name="boardInstanceID", referencedColumnName="boardInstanceID")
     */
    protected $instance;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    protected $dateCreated;

    public function __construct()
    {
        $this->dateCreated = time();
    }

    /**
     * @return mixed
     */
    public function getBoardItemBatchID()
    {
        return $this->boardItemBatchID;
    }

    /**
     * @param mixed $boardItemBatchID
     */
    public function setBoardItemBatchID($boardItemBatchID): void
    {
        $this->boardItemBatchID = $boardItemBatchID;
    }

    /**
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param mixed $items
     */
    public function setItems($items): void
    {
        $this->items = $items;
    }

    /**
     * @return mixed
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * @param mixed $instance
     */
    public function setInstance($instance)
    {
        $this->instance = $instance;
    }


    /**
     * @return mixed
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param mixed $dateCreated
     */
    public function setDateCreated($dateCreated): void
    {
        $this->dateCreated = $dateCreated;
    }



}
