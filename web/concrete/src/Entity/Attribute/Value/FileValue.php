<?php
namespace Concrete\Core\Entity\Attribute\Value;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="FileAttributeValues"
 * )
 */
class FileValue extends Value
{
    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\File\Version")
     @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fID", referencedColumnName="fID"),
     *   @ORM\JoinColumn(name="fvID", referencedColumnName="fvID")
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
