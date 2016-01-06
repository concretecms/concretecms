<?php

namespace Concrete\Core\Entity\Attribute\Value;

/**
 * @Entity
 * @Table(
 *     name="FileAttributeValues"
 * )
 */
class FileValue extends Value
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
