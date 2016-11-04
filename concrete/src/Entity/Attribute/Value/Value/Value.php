<?php
namespace Concrete\Core\Entity\Attribute\Value\Value;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="AttributeValues")
 */
class Value
{

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $avID;

    public function getAttributeValueID()
    {
        return $this->avID;
    }

    public function __toString()
    {
        return (string) $this->avID;
    }

    public function __clone()
    {
        if ($this->avID) {
            $this->avID = null;
        }
    }


}
