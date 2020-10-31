<?php

namespace Concrete\Core\Entity\Attribute\Value\Value;

use Concrete\Core\Entity\File\File;
use Concrete\Core\File\FileProviderInterface;
use Concrete\Core\Support\Facade\Url;
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
        if ($this->file instanceof File) {
            if ($this->file->hasFileUUID()) {
                return (string)Url::to('/download_file', $this->file->getFileUUID());
            } else {
                return (string)Url::to('/download_file', $this->file->getFileID());
            }
        }

        return '';
    }
}
