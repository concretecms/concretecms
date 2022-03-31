<?php
namespace Concrete\Core\Entity\Board\Designer;

use Concrete\Core\Board\Item\ItemProviderInterface;
use Concrete\Core\Entity\Board\Item;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="BoardDesignerCustomElementItems")
 */
class ItemSelectorCustomElementItem implements \JsonSerializable, ItemProviderInterface
{

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $itemId;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Board\Item", cascade={"all"})
     * @ORM\JoinColumn(name="boardItemID", referencedColumnName="boardItemID")
     */
    protected $item;

    /**
     * @ORM\ManyToOne(targetEntity="ItemSelectorCustomElement", inversedBy="items")
     * @ORM\JoinColumn(name="customElementID", referencedColumnName="id")
     */
    protected $element;

    /**
     * @return mixed
     */
    public function getItemId()
    {
        return $this->itemId;
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

    /**
     * @return mixed
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * @param mixed $element
     */
    public function setElement($element): void
    {
        $this->element = $element;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [];
        /*
        $file = $this->getRelevantThumbnail();
        $thumbnail = null;
        if ($file) {
            $thumbnail = $file->getURL();
        }
        return [
            'id' => $this->getBoardInstanceItemID(),
            'name' => $this->getName(),
            'thumbnail' => $thumbnail,
            'relevantDate' => $this->getRelevantDate(),
        ];*/
    }


}
