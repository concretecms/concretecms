<?php
namespace Concrete\Core\Entity\Attribute\Value\Value;

use Concrete\Core\File\FileProviderInterface;

/**
 * @Entity
 * @Table(name="ImageFileAttributeValues")
 */
class ImageFileValue extends Value implements FileProviderInterface
{
    /**
     * @ManyToOne(targetEntity="\Concrete\Core\File\File")
     * @JoinColumn(name="fID", referencedColumnName="fID")
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
        return array($this->getFileObject());
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
}
