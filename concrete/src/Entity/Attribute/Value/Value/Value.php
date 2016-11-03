<?php
namespace Concrete\Core\Entity\Attribute\Value\Value;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\Table(name="AttributeValues")
 */
abstract class Value
{

    abstract public function __toString();

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $avID;

    public function getValue()
    {
        return $this;
    }

    public function getAttributeValueID()
    {
        return $this->avID;
    }

    public function __clone()
    {
        if ($this->avID) {
            $this->avID = null;
        }
    }


}
