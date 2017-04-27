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

    /**
     * This is needed for backward compatibility –but it also might be handy if you need to figure out what kind of
     * attribute something is but we don't want a direct association due to performance concerns
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Attribute\Key\Key")
     * @ORM\JoinColumn(name="akID", referencedColumnName="akID", onDelete="CASCADE")
     **/
    protected $attribute_key;

    public function getAttributeValueID()
    {
        return $this->avID;
    }

    /**
     * @return mixed
     */
    public function getAttributeKey()
    {
        return $this->attribute_key;
    }

    /**
     * @param mixed $attribute_key
     */
    public function setAttributeKey($attribute_key)
    {
        $this->attribute_key = $attribute_key;
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
