<?php
namespace Concrete\Core\Entity\Attribute\Value\Value;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="SelectAttributeValues")
 */
class SelectValue extends AbstractValue implements \Iterator
{
    /**
     * @ORM\ManyToMany(targetEntity="SelectValueOption", inversedBy="values", cascade={"persist"})
     * @ORM\JoinTable(name="SelectAttributeValueSelectedOptions",
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

    public function rewind()
    {
        $this->selectedOptions->getIterator()->rewind();
    }

    public function valid()
    {
        $this->selectedOptions->getIterator()->valid();
    }


    public function current()
    {
        return $this->selectedOptions->getIterator()->current();
    }

    public function key()
    {
        return $this->selectedOptions->getIterator()->key();
    }

    public function next()
    {
        $this->selectedOptions->getIterator()->next();
    }

    public function count()
    {
        return $this->selectedOptions->getIterator()->count();
    }

}
