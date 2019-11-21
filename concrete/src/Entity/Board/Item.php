<?php
namespace Concrete\Core\Entity\Board;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="BoardItems")
 */
class Item
{
    
    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $boardItemID;
    
    /**
     * @ORM\ManyToOne(targetEntity="Board", inversedBy="items")
     * @ORM\JoinColumn(name="boardID", referencedColumnName="boardID")
     */
    protected $board;

    /**
     * @ORM\ManyToOne(targetEntity="ItemBatch", inversedBy="items")
     * @ORM\JoinColumn(name="boardItemBatchID", referencedColumnName="boardItemBatchID")
     */
    protected $batch;

    /**
     * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource")
     * @ORM\JoinColumn(name="configuredDataSourceID", referencedColumnName="configuredDataSourceID")
     **/
    protected $data_source;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    protected $dateCreated;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    protected $relevantDate;
    
    /**
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    protected $bID = 0;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    protected $slot = 0;

    /**
     * @return mixed
     */
    public function getBoardItemID()
    {
        return $this->boardItemID;
    }
    
    /**
     * @return mixed
     */
    public function getBoard()
    {
        return $this->board;
    }

    /**
     * @param mixed $board
     */
    public function setBoard($board): void
    {
        $this->board = $board;
    }

    /**
     * @return mixed
     */
    public function getDataSource()
    {
        return $this->data_source;
    }

    /**
     * @param mixed $data_source
     */
    public function setDataSource($data_source): void
    {
        $this->data_source = $data_source;
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

    /**
     * @return mixed
     */
    public function getRelevantDate()
    {
        return $this->relevantDate;
    }

    /**
     * @param mixed $relevantDate
     */
    public function setRelevantDate($relevantDate): void
    {
        $this->relevantDate = $relevantDate;
    }

    /**
     * @return mixed
     */
    public function getBlockID()
    {
        return $this->bID;
    }

    /**
     * @param mixed $bID
     */
    public function setBlockID($bID): void
    {
        $this->bID = $bID;
    }

    /**
     * @return mixed
     */
    public function getSlot()
    {
        return $this->slot;
    }

    /**
     * @param mixed $slot
     */
    public function setSlot($slot): void
    {
        $this->slot = $slot;
    }

    /**
     * @return mixed
     */
    public function getBatch()
    {
        return $this->batch;
    }

    /**
     * @param mixed $batch
     */
    public function setBatch($batch): void
    {
        $this->batch = $batch;
    }

    
    



}
