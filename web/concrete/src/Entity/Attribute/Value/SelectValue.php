<?php
namespace Concrete\Core\Entity\Attribute\Value;

/**
 * @Entity
 * @Table(name="SelectAttributeValues")
 */
class SelectValue extends Value
{

    /**
     * @ManyToMany(targetEntity="SelectValueOption", inversedBy="categories")
     * @JoinTable(name="SelectAttributeValueSelectedOptions",
     * joinColumns={@JoinColumn(name="avID", referencedColumnName="avID")},
     * inverseJoinColumns={@JoinColumn(name="avSelectOptionID", referencedColumnName="avSelectOptionID")}
     * )
     */
    protected $selectedOptions;

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
