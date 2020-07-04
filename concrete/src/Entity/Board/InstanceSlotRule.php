<?php
namespace Concrete\Core\Entity\Board;

use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="BoardInstanceSlotRules")
 */
class InstanceSlotRule implements \JsonSerializable
{

    /**
     * This is set when an editor or administrator uses the in-context controls to pin an auto generated slot
     */
    const RULE_TYPE_EDITOR_PINNED = 'EP';

    /**
     * This is set when an editor or administrator uses the in-context controls to generate a custom slot and place
     * it in the board.
     */
    const RULE_TYPE_EDITOR_CUSTOM_SLOT_CONTENT = 'ES';

    /**
     * This is set when an admin uses the Dashboard board designer interface to create custom content and push it out
     * to one or more boards. These slots supersede the editor rules above.
     */
    const RULE_TYPE_ADMIN_PUBLISHED_SLOT = 'AS';

    /**
     * Like the above, this is set when an admin uses the dashboard designer interface but chooses to LOCK the slot
     */
    const RULE_TYPE_ADMIN_PUBLISHED_SLOT_LOCKED = 'AL';

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
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    protected $bID;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\User\User")
     * @ORM\JoinColumn(name="uID", referencedColumnName="uID")
     */
    protected $user;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

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

    public function jsonSerialize()
    {
        return [
            'id' => $this->getBoardInstanceSlotRuleID(),
            'startDate' => $this->getStartDate(),
            'endDate' => $this->getEndDate() > 0 ? $this->getEndDate() : null,
            'dateCreated' => $this->getDateCreated(),
            'user' => $this->getUser(),
            'ruleType' => $this->getRuleType(),
            'slot' => $this->getSlot(),

        ];
    }


}
