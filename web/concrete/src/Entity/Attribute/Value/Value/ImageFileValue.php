<?php
namespace Concrete\Core\Entity\Attribute\Value\Value;


/**
 * @Entity
 * @Table(name="ImageFileAttributeValues")
 */
class ImageFileValue extends Value
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
