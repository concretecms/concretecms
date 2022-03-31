<?php
namespace Concrete\Core\Entity\Board;

use Concrete\Core\Board\Instance\Slot\Rule\BoardDesignerSharedSlotFormatter;
use Concrete\Core\Board\Instance\Slot\Rule\CustomSlotContentFormatter;
use Concrete\Core\Board\Instance\Slot\Rule\FormatterInterface;
use Concrete\Core\Board\Instance\Slot\Rule\SlotPinnedFormatter;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\ObjectInterface;
use Concrete\Core\Permission\Response\BoardInstanceSlotRuleResponse;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="InstanceSlotRuleRepository")
 * @ORM\Table(name="BoardInstanceSlotRules")
 */
class InstanceSlotRule implements \JsonSerializable, ObjectInterface
{

    /**
     * This is set when an editor or administrator uses the in-context controls to pin an auto generated slot
     */
    const RULE_TYPE_AUTOMATIC_SLOT_PINNED = 'EP';

    /**
     * This is set when an editor or administrator uses the in-context controls to generate a custom slot and place
     * it in the board.
     */
    const RULE_TYPE_CUSTOM_CONTENT = 'ES';

    /**
     * This is set when an admin uses the Dashboard board designer interface to create custom content and push it out
     * to one or more boards. These slots supersede the editor rules above.
     */
    const RULE_TYPE_DESIGNER_CUSTOM_CONTENT = 'AS';


    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $boardInstanceSlotRuleID;

    /**
     * @ORM\ManyToOne(targetEntity="Instance", inversedBy="rules")
     * @ORM\JoinColumn(name="boardInstanceID", referencedColumnName="boardInstanceID")
     */
    protected $instance;

    /**
     * Note: This CANNOT be an actual link to the slot object, even though you think it could/should be. This needs to be a dumb
     * rule with just the number of the slot, so that we can manipulate objects after we render.
     *
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    protected $slot;

    /**
     * @ORM\Column(type="guid")
     */
    protected $batchIdentifier;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    protected $bID;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\User\User")
     * @ORM\JoinColumn(name="uID", referencedColumnName="uID")
     */
    protected $user;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $notes;

    /**
     * @ORM\Column(type="string")
     */
    protected $timezone;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isLocked = false;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    protected $dateCreated;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    protected $startDate;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    protected $endDate;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $ruleType;

    /**
     * @return mixed
     */
    public function getBoardInstanceSlotRuleID()
    {
        return $this->boardInstanceSlotRuleID;
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
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
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
    public function getRuleType()
    {
        return $this->ruleType;
    }

    /**
     * @param mixed $ruleType
     */
    public function setRuleType($ruleType): void
    {
        $this->ruleType = $ruleType;
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
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param mixed $startDate
     */
    public function setStartDate($startDate): void
    {
        $this->startDate = $startDate;
    }

    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param mixed $endDate
     */
    public function setEndDate($endDate): void
    {
        $this->endDate = $endDate;
    }

    /**
     * @return mixed
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @param mixed $timezone
     */
    public function setTimezone($timezone): void
    {
        $this->timezone = $timezone;
    }

    /**
     * @return mixed
     */
    public function getNotes()
    {
        return $this->notes;
    }

    public function getRuleFormatter(): FormatterInterface
    {
        if ($this->getRuleType() === self::RULE_TYPE_AUTOMATIC_SLOT_PINNED) {
            return new SlotPinnedFormatter();
        }
        if ($this->getRuleType() === self::RULE_TYPE_CUSTOM_CONTENT) {
            return new CustomSlotContentFormatter();
        }
        if ($this->getRuleType() === self::RULE_TYPE_DESIGNER_CUSTOM_CONTENT) {
            return new BoardDesignerSharedSlotFormatter();
        }
    }

    /**
     * @param mixed $notes
     */
    public function setNotes($notes): void
    {
        $this->notes = $notes;
    }

    /**
     * @return bool
     */
    public function isLocked(): bool
    {
        return $this->isLocked;
    }

    /**
     * @param bool $isLocked
     */
    public function setIsLocked(bool $isLocked): void
    {
        $this->isLocked = $isLocked;
    }

    /**
     * @return mixed
     */
    public function getBatchIdentifier()
    {
        return $this->batchIdentifier;
    }

    /**
     * @param mixed $batchIdentifier
     */
    public function setBatchIdentifier($batchIdentifier): void
    {
        $this->batchIdentifier = $batchIdentifier;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $checker = new Checker($this);
        $canDeleteRule = $checker->canDeleteBoardInstanceSlotRule();
        return [
            'id' => $this->getBoardInstanceSlotRuleID(),
            'startDate' => $this->getStartDate(),
            'endDate' => $this->getEndDate() > 0 ? $this->getEndDate() : null,
            'dateCreated' => $this->getDateCreated(),
            'user' => $this->getUser(),
            'ruleType' => $this->getRuleType(),
            'slot' => $this->getSlot(),
            'timezone' => $this->getTimezone(),
            'name' =>  $this->getRuleFormatter()->getRuleName($this),
            'actionDescription' =>  $this->getRuleFormatter()->getRuleActionDescription($this),
            'canDeleteRule' => $canDeleteRule,
            'isLocked' => $this->isLocked(),
            'instance' => $this->getInstance(),
            'batchIdentifier' => $this->getBatchIdentifier(),
        ];
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
        return $this->getBoardInstanceSlotRuleID();
    }

    public function getPermissionResponseClassName()
    {
        return BoardInstanceSlotRuleResponse::class;
    }



}
