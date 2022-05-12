<?php
namespace Concrete\Core\Entity\Board;

use Concrete\Core\Board\Item\ItemProviderInterface;
use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="InstanceItemRepository")
 * @ORM\Table(name="BoardInstanceItems")
 */
class InstanceItem implements \JsonSerializable, ItemProviderInterface
{

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $boardInstanceItemID;

    /**
     * @ORM\ManyToOne(targetEntity="Item", cascade={"remove"})
     * @ORM\JoinColumn(name="boardItemID", referencedColumnName="boardItemID")
     */
    protected $item;

    /**
     * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource", inversedBy="items")
     * @ORM\JoinColumn(name="configuredDataSourceID", referencedColumnName="configuredDataSourceID")
     **/
    protected $data_source;

    /**
     * @ORM\ManyToOne(targetEntity="Instance", inversedBy="items")
     * @ORM\JoinColumn(name="boardInstanceID", referencedColumnName="boardInstanceID")
     */
    protected $instance;

    /**
     * @ORM\ManyToOne(targetEntity="InstanceItemBatch", inversedBy="items")
     * @ORM\JoinColumn(name="boardInstanceItemBatchID", referencedColumnName="boardInstanceItemBatchID")
     */
    protected $batch;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    protected $dateAddedToBoard = 0;

    /**
     * @return mixed
     */
    public function getBoardInstanceItemID()
    {
        return $this->boardInstanceItemID;
    }

    /**
     * @return mixed
     */
    public function getDataSource() : ConfiguredDataSource
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
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * @param mixed $instance
     */
    public function setInstance($instance): void
    {
        $this->instance = $instance;
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

    /**
     * @return mixed
     */
    public function getDateAddedToBoard()
    {
        return $this->dateAddedToBoard;
    }

    /**
     * @param mixed $dateAddedToBoard
     */
    public function setDateAddedToBoard($dateAddedToBoard): void
    {
        $this->dateAddedToBoard = $dateAddedToBoard;
    }

    /**
     * @return mixed
     */
    public function getItem() :? Item
    {
        return $this->item;
    }

    /**
     * @param mixed $item
     */
    public function setItem($item): void
    {
        $this->item = $item;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $file = $this->item->getRelevantThumbnail();
        $thumbnail = null;
        if ($file) {
            $thumbnail = $file->getURL();
        }
        $timezone = $this->getInstance()->getSite()->getTimezone();
        $datetime = new \DateTime('@' . $this->item->getRelevantDate());
        $datetime->setTimezone(new \DateTimeZone($timezone));
        $relevantDateString = $datetime->format('F d, Y g:i a');
        return [
            'id' => $this->getBoardInstanceItemID(),
            'name' => $this->item->getName(),
            'thumbnail' => $thumbnail,
            'relevantDate' => $this->item->getRelevantDate(),
            'relevantDateString' => $relevantDateString,
        ];
    }


}
