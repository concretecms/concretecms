<?php

namespace Concrete\Core\Entity\File;

/**
 * @Entity
 * @Table(
 *     name="FileAttributeValues",
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
    protected $fID;

    /**
     * @Id
     * @Column(type="integer", options={"unsigned": true})
     **/
    protected $fvID;

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
    public function getFileID()
    {
        return $this->fID;
    }

    /**
     * @param mixed $fID
     */
    public function setFileID($fID)
    {
        $this->fID = $fID;
    }

    /**
     * @return mixed
     */
    public function getVersionID()
    {
        return $this->fvID;
    }

    /**
     * @param mixed $fvID
     */
    public function setVersionID($fvID)
    {
        $this->fvID = $fvID;
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
