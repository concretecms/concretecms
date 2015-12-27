<?php

namespace Concrete\Core\Entity\File;

use Concrete\Core\Attribute\AttributeInterface;

/**
 * @Entity
 * @Table(name="FileAttributeKeys")
 */
class Attribute implements AttributeInterface
{

    /**
     * @Id
     * @OneToOne(targetEntity="\Concrete\Core\Entity\Attribute\Key\Key")
     * @JoinColumn(name="akID", referencedColumnName="akID")
     **/
    protected $attribute_key;

    /**
     * @ManyToOne(targetEntity="\Concrete\Core\File\Version",  inversedBy="attributes")
     * @JoinColumns({
     *   @JoinColumn(name="fID", referencedColumnName="fID"),
     *   @JoinColumn(name="fvID", referencedColumnName="fvID")
     * })
     */
    protected $version;

    /**
     * @return mixed
     */
    public function getFileVersion()
    {
        return $this->version;
    }

    /**
     * @param mixed $version
     */
    public function setFileVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return mixed
     */
    public function getAttributeKey()
    {
        return $this->attribute_key;
    }

    /**
     * @param mixed $attribute
     */
    public function setAttributeKey($attribute)
    {
        $this->attribute_key = $attribute;
    }





}
