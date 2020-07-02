<?php
namespace Concrete\Core\Entity\Board\Designer;

use Concrete\Core\Localization\Service\Date;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class ItemSelectorCustomElement extends CustomElement
{

    /**
     * @ORM\OneToMany(targetEntity="ItemSelectorCustomElementItem", cascade={"remove"}, mappedBy="element", fetch="EXTRA_LAZY")
     */
    protected $items;

    /**
     * @ORM\Column(type="json", length=255, nullable=true)
     */
    protected $contentObjectCollection;

    /**
     * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\Board\SlotTemplate")
     */
    protected $slotTemplate;

    /**
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return mixed
     */
    public function getContentObjectCollection()
    {
        return $this->contentObjectCollection;
    }

    /**
     * @param mixed $contentObjectCollection
     */
    public function setContentObjectCollection($contentObjectCollection): void
    {
        $this->contentObjectCollection = $contentObjectCollection;
    }

    /**
     * @return mixed
     */
    public function getSlotTemplate()
    {
        return $this->slotTemplate;
    }

    /**
     * @param mixed $slotTemplate
     */
    public function setSlotTemplate($slotTemplate): void
    {
        $this->slotTemplate = $slotTemplate;
    }


}
