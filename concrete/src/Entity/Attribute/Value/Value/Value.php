<?php
namespace Concrete\Core\Entity\Attribute\Value\Value;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\Table(name="AttributeValueValues")
 */
abstract class Value
{

    abstract public function __toString();

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $avID;

    /**
     * @ORM\OneToMany(targetEntity="\Concrete\Core\Entity\Attribute\Value\Value", mappedBy="value", cascade={"remove"})
     **/
    protected $attribute_values;

    /**
     * @return mixed
     */
    public function getAttributeKey()
    {
        $values = $this->getAttributeValues();
        if ($values->containsKey(0)) {
            return $values->get(0)->getAttributeKey();
        }
    }

    /**
     * @return mixed
     */
    public function getAttributeValues()
    {
        return $this->attribute_values;
    }

    public function getValue()
    {
        return $this;
    }

    public function getAttributeValueID()
    {
        return $this->avID;
    }


    public function __construct()
    {
        $this->attribute_values = new ArrayCollection();
    }

}
