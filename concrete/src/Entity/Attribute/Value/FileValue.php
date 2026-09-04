<?php
namespace Concrete\Core\Entity\Attribute\Value;

use Concrete\Core\Entity\File\Version;
use Concrete\Core\File\File;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="FileAttributeValues"
 * )
 */
class FileValue extends AbstractValue
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned": true})
     **/
    protected $fID;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned": true})
     **/
    protected $fvID;

    /**
     * @return \Concrete\Core\Entity\File\Version|null
     */
    public function getVersion()
    {
        $file = File::getByID($this->fID);

        return $file === null ? null : $file->getVersion($this->fvID);
    }

    /**
     * @param mixed $version
     */
    public function setVersion(Version $version)
    {
        $this->fID = $version->getFileID();
        $this->fvID = $version->getFileVersionID();
    }
}
