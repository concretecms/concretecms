<?php
namespace Concrete\Core\Entity\Board;

use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Permission\Assignment\BoardAssignment;
use Concrete\Core\Permission\ObjectInterface;
use Concrete\Core\Permission\Response\BoardInstanceResponse;
use Concrete\Core\Permission\Response\BoardResponse;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="InstanceRepository")
 * @ORM\Table(name="BoardInstances")
 */
class Instance implements \JsonSerializable, ObjectInterface
{

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $boardInstanceID;

    /**
     * @ORM\ManyToOne(targetEntity="Board", inversedBy="instances")
     * @ORM\JoinColumn(name="boardID", referencedColumnName="boardID")
     */
    protected $board;

    /**
     * @ORM\OneToMany(targetEntity="InstanceItem", cascade={"remove"}, mappedBy="instance", fetch="EXTRA_LAZY")
     */
    protected $items;

    /**
     * @ORM\OneToMany(targetEntity="InstanceSlotRule", cascade={"remove"}, mappedBy="instance", fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"ruleType" = "DESC", "slot" = "ASC"})
     *
     * What's the story with ruleType here? We have three rule types as of this comment,
     * RULE_TYPE_AUTOMATIC_SLOT_PINNED = EP
     * RULE_TYPE_DESIGNER_CUSTOM_CONTENT = ES
     * and RULE_TYPE_DESIGNER_CUSTOM_CONTENT = AS
     *
     * A requirement is that the admin created slots (those that match RULE_TYPE_DESIGNER_CUSTOM_CONTENT) come at the
     * END of any lists. So in order to achieve that, we have to order rules by ruleType descending first, so that
     * items with AS come at the end.
     */
    protected $rules;

    /**
     * @ORM\OneToMany(targetEntity="InstanceItemBatch", cascade={"remove"}, mappedBy="instance", fetch="EXTRA_LAZY")
     */
    protected $batches;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $boardInstanceName;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    protected $dateCreated;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true}, nullable=true)
     */
    protected $dateDataPoolLastUpdated;

    /**
     * Not to be confused with the site that is set at the board level, a shared board (e.g. a board with a site
     *  = null, can have instances across multiple sites)
     *
     * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\Site\Site")
     * @ORM\JoinColumn(name="siteID", referencedColumnName="siteID")
     */
    protected $site;


    /**
     * @ORM\OneToMany(targetEntity="InstanceSlot", cascade={"persist", "remove"}, mappedBy="instance")
     * @ORM\OrderBy({"slot" = "ASC"})
     */
    protected $slots;

    public function __construct()
    {
        $this->slots = new ArrayCollection();
        $this->items = new ArrayCollection();
        $this->batches = new ArrayCollection();
        $this->rules = new ArrayCollection();
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
    public function getBatches()
    {
        return $this->batches;
    }

    /**
     * @param mixed $batches
     */
    public function setBatches($batches): void
    {
        $this->batches = $batches;
    }

    /**
     * @return mixed
     */
    public function getBoardInstanceID()
    {
        return $this->boardInstanceID;
    }

    /**
     * @return mixed
     */
    public function getBoardInstanceName()
    {
        return $this->boardInstanceName;
    }

    /**
     * @param mixed $boardInstanceName
     */
    public function setBoardInstanceName($boardInstanceName): void
    {
        $this->boardInstanceName = $boardInstanceName;
    }

    /**
     * @return mixed
     */
    public function getSite(): ?Site
    {
        return $this->site;
    }

    /**
     * @param mixed $site
     */
    public function setSite(Site $site = null): void
    {
        $this->site = $site;
    }


    /**
     * @return mixed
     */
    public function getSlots()
    {
        return $this->slots;
    }

    /**
     * @param mixed $slots
     */
    public function setSlots($slots): void
    {
        $this->slots = $slots;
    }


    /**
     * @return mixed
     */
    public function getBoard() : Board
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

    public function getDateCreatedObject() : \DateTime
    {
        $dateTime = new \DateTime();
        $dateTime->setTimestamp($this->getDateCreated());
        $site = $this->getBoard()->getSite();
        if ($site) {
            $dateTime->setTimezone(new \DateTimeZone($site->getTimezone()));
        }
        return $dateTime;
    }

    /**
     * @return mixed
     */
    public function getDateDataPoolLastUpdated()
    {
        return $this->dateDataPoolLastUpdated;
    }

    /**
     * @param mixed $dateDataPoolLastUpdated
     */
    public function setDateDataPoolLastUpdated($dateDataPoolLastUpdated): void
    {
        $this->dateDataPoolLastUpdated = $dateDataPoolLastUpdated;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $site = $this->getSite();
        return [
            'boardInstanceID' => $this->getBoardInstanceID(),
            'name' => $this->getBoardInstanceName(),
            'dateCreated' => $this->getDateCreated(),
            'site' => $site,
            'board' => $this->getBoard(),
        ];
    }

    /**
     * @return mixed
     */
    public function getRules()
    {
        return $this->rules;
    }


    public function __toString()
    {
        return (string) $this->getBoardInstanceID();
    }

    public function getPermissionAssignmentClassName()
    {
        return false;
    }

    public function getPermissionObjectKeyCategoryHandle()
    {
        return false;
    }

    public function getPermissionObjectIdentifier()
    {
        return $this->getBoardInstanceID();
    }

    public function getPermissionResponseClassName()
    {
        return BoardInstanceResponse::class;
    }
}
