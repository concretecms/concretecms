<?php

namespace Concrete\Core\Entity\Page;

/**
 * @Entity
 * @Table(
 *     name="CollectionAttributeValues",
 *     indexes={
 *      @Index(name="akID", columns={"akID"}),
 *      @Index(name="avID", columns={"avID"})
 *     }
 * )
 */
class AttributeValue
{

    /**
     * @Id
     * @ManyToOne(targetEntity="\Concrete\Core\Entity\Attribute\Key\Key")
     * @JoinColumn(name="akID", referencedColumnName="akID")
     **/
    protected $attribute_key;

    /**
     * @Id
     * @Column(type="integer", options={"unsigned": true})
     **/
    protected $cID;

    /**
     * @Id
     * @Column(type="integer", options={"unsigned": true})
     **/
    protected $cvID;

    /**
     * @Id
     * @OneToOne(targetEntity="\Concrete\Core\Entity\Attribute\Value\Value")
     * @JoinColumn(name="avID", referencedColumnName="avID")
     **/
    protected $attribute_value;

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
    public function getPageID()
    {
        return $this->cID;
    }

    /**
     * @param mixed $cID
     */
    public function setPageID($cID)
    {
        $this->cID = $cID;
    }

    /**
     * @return mixed
     */
    public function getVersionID()
    {
        return $this->cvID;
    }

    /**
     * @param mixed $cvID
     */
    public function setVersionID($cvID)
    {
        $this->cvID = $cvID;
    }

    /**
     * @return mixed
     */
    public function getAttributeValue()
    {
        return $this->attribute_value;
    }

    /**
     * @param mixed $attribute_value
     */
    public function setAttributeValue($attribute_value)
    {
        $this->attribute_value = $attribute_value;
    }









}
