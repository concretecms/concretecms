<?php
namespace Concrete\Core\Entity\Attribute;

/**
 * @Entity
 * @Table(
 *     name="AttributeSetKeys"
 * )
 */
class SetKey
{
    /**
     * @Id @OneToOne(targetEntity="\Concrete\Core\Entity\Attribute\Key\Key")
     * @JoinColumn(name="akID", referencedColumnName="akID")
     */
    protected $attribute_key;

    /**
     * @Id @ManyToOne(targetEntity="\Concrete\Core\Entity\Attribute\Set")
     * @JoinColumn(name="asID", referencedColumnName="asID")
     */
    protected $set;

    /**
     * @Column(type="integer", options={"unsigned":true})
     */
    protected $asDisplayOrder = 0;

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

    /**
     * @return mixed
     */
    public function getAttributeSet()
    {
        return $this->set;
    }

    /**
     * @param mixed $set
     */
    public function setAttributeSet($set)
    {
        $this->set = $set;
    }

    /**
     * @return mixed
     */
    public function getDisplayOrder()
    {
        return $this->asDisplayOrder;
    }

    /**
     * @param mixed $asDisplayOrder
     */
    public function setDisplayOrder($asDisplayOrder)
    {
        $this->asDisplayOrder = $asDisplayOrder;
    }
}
