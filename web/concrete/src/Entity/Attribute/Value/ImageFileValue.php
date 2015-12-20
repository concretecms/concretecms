<?php
namespace Concrete\Core\Entity\Attribute\Value;


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

    public function getFileObject()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setFileObject($file)
    {
        $this->file = $file;
    }


}
