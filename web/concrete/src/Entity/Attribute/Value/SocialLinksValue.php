<?php
namespace Concrete\Core\Entity\Attribute\Value;


/**
 * @Entity
 * @Table(name="SocialLinkAttributeValues")
 */
class SocialLinksValue extends Value
{
    /**
     * @Column(type="json_array")
     */
    protected $value;

    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

}
