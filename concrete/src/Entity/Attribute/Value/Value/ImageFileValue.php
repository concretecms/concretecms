<?php
namespace Concrete\Core\Entity\Attribute\Value\Value;

use Concrete\Core\File\FileProviderInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="atFile")
 */
class ImageFileValue extends AbstractValue implements FileProviderInterface
{
    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\File\File")
     * @ORM\JoinColumn(name="fID", referencedColumnName="fID", onDelete="CASCADE")
     */
    protected $file;

    public function getFileID()
    {
        if (is_object($this->file)) {
            return $this->file->getFileID();
        }

        return 0;
    }

    public function getFileObjects()
    {
        if (is_object($this->file)) {
            return array($this->file);
        }
        return array();
    }

    public function getValue()
    {
        return $this->file;
    }

    public function getFileObject()
    {
        return $this->file;
    }

    /**
     * @param mixed $value
     */
    public function setFileObject($file)
    {
        $this->file = $file;
    }

    public function __toString()
    {
        if (is_object($this->file)) {
            return (string) \URL::to('/download_file', $this->file->getFileID());
        }
        return '';
    }
}
