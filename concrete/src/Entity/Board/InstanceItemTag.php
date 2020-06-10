<?php
namespace Concrete\Core\Entity\Board;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="BoardInstanceItemTags")
 */
class InstanceItemTag
{

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $boardInstanceItemTagID;

    /**
     * @ORM\ManyToOne(targetEntity="InstanceItem",  inversedBy="tags")
     * @ORM\JoinColumn(name="boardInstanceItemID", referencedColumnName="boardInstanceItemID")
     **/
    protected $item;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $tag;

    /**
     * ItemTag constructor.
     * @param $tag
     */
    public function __construct(InstanceItem $item, $tag)
    {
        $this->item = $item;
        $this->tag = $tag;
    }

    /**
     * @return mixed
     */
    public function getBoardInstanceItemTagID()
    {
        return $this->boardInstanceItemTagID;
    }

    /**
     * @return mixed
     */
    public function getItem()
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

    /**
     * @return mixed
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param mixed $tag
     */
    public function setTag($tag): void
    {
        $this->tag = $tag;
    }





}
