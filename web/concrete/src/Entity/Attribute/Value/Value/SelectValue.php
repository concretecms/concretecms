<?php
namespace Concrete\Core\Entity\Attribute\Value\Value;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="SelectAttributeValues")
 */
class SelectValue extends Value
{
    /**
     * @ManyToMany(targetEntity="SelectValueOption", inversedBy="values", cascade={"persist"})
     * @JoinTable(name="SelectAttributeValueSelectedOptions",
     * joinColumns={@JoinColumn(name="avID", referencedColumnName="avID")},
     * inverseJoinColumns={@JoinColumn(name="avSelectOptionID", referencedColumnName="avSelectOptionID")}
     * )
     */
    protected $selectedOptions;

    public function __construct()
    {
        parent::__construct();
        $this->selectedOptions = new ArrayCollection();
    }

    /**
     * @deprecated
     */
    public function getOptions()
    {
        return $this->getSelectedOptions();
    }

    /**
     * @return mixed
     */
    public function getSelectedOptions()
    {
        return $this->selectedOptions;
    }

    /**
     * @param mixed $selectedOptions
     */
    public function setSelectedOptions($selectedOptions)
    {
        $this->selectedOptions = $selectedOptions;
    }
}
