<?php
namespace Concrete\Core\Entity\Attribute\Value\Value;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="atSelect")
 */
class SelectValue extends AbstractValue implements \IteratorAggregate
{
    /**
     * @ORM\ManyToMany(targetEntity="SelectValueOption", inversedBy="values", cascade={"persist"})
     * @ORM\JoinTable(name="atSelectOptionsSelected",
     * joinColumns={@ORM\JoinColumn(name="avID", referencedColumnName="avID")},
     * inverseJoinColumns={@ORM\JoinColumn(name="avSelectOptionID", referencedColumnName="avSelectOptionID")}
     * )
     */
    protected $selectedOptions;

    public function __construct()
    {
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

    public function __toString()
    {
        $str = '';
        $i = 0;
        /**
         * @var $option SelectValueOption
         */
        foreach ($this->selectedOptions as $option) {
            $str .= $option->getSelectAttributeOptionValue();
            $i++;
            if ($i < count($this->selectedOptions)) {
                $str .= "\n";
            }
        }
        return $str;
    }

    public function getIterator()
    {
        return $this->selectedOptions->getIterator();
    }
}
