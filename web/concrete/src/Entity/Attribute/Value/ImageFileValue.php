<?php
namespace Concrete\Core\Entity\Attribute\Value;


/**
 * @Entity
 * @Table(name="ImageFileAttributeValues")
 */
class ImageFileValue extends Value
{
    /**
     * @Column(type="integer")
     */
    protected $fID;

    public function getFileID()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setFileID($value)
    {
        $this->value = $value;
    }


}
