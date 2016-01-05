<?php

namespace Concrete\Core\Entity\File;

use Concrete\Core\Entity\Attribute\AbstractAttributeValue;

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
class AttributeValue extends AbstractAttributeValue
{

    /**
     * @ManyToOne(targetEntity="\Concrete\Core\File\Version")
@JoinColumns({
     *   @JoinColumn(name="fID", referencedColumnName="fID"),
     *   @JoinColumn(name="fvID", referencedColumnName="fvID")
     * })
     */
    protected $version;

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param mixed $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }




}
