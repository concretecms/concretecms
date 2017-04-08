<?php
namespace Concrete\Core\Entity\Attribute\Value\Value;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperClass
 */
abstract class AbstractValue
{

    public function getValue()
    {
        return $this;
    }

    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="\Concrete\Core\Entity\Attribute\Value\Value\Value")
     * @ORM\JoinColumn(name="avID", referencedColumnName="avID", onDelete="CASCADE")
     */
    protected $generic_value;

    /**
     * @return mixed
     */
    public function getGenericValue()
    {
        return $this->generic_value;
    }

    /**
     * @param mixed $attribute_value
     */
    public function setGenericValue($generic_value)
    {
        $this->generic_value = $generic_value;
    }



}