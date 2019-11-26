<?php
namespace Concrete\Core\Entity\Board;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="BoardInstances")
 */
class Instance
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
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    protected $dateCreated;

    /**
     * @ORM\OneToMany(targetEntity="InstanceSlot", cascade={"remove"}, mappedBy="instance")
     */
    protected $slots;

    public function __construct()
    {
        $this->slots = new ArrayCollection();
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


    



}
