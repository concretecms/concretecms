<?php
namespace Concrete\Core\Entity\Attribute\Key\Type;

use Concrete\Core\Entity\Attribute\Value\Value\ImageFileValue;

/**
 * @Entity
 * @Table(name="ImageFileAttributeKeyTypes")
 */
class ImageFileType extends Type
{

    const TYPE_FILE_MANAGER = 0;
    const TYPE_HTML_INPUT = 5;

    public function getAttributeValue()
    {
        return new ImageFileValue();
    }

    /**
     * @Column(type="integer")
     */
    protected $akFileManagerMode;

    public function __construct()
    {
        $this->mode = self::TYPE_FILE_MANAGER;
    }

    public function getMode()
    {
        return $this->akFileManagerMode;
    }

    public function isModeFileManager()
    {
        return $this->akFileManagerMode == self::TYPE_FILE_MANAGER;
    }

    public function isModeHtmlInput()
    {
        return $this->akFileManagerMode == self::TYPE_HTML_INPUT;
    }

    public function setModeToFileManager()
    {
        $this->akFileManagerMode = self::TYPE_FILE_MANAGER;
    }

    public function setModeToHtmlInput()
    {
        $this->akFileManagerMode = self::TYPE_HTML_INPUT;
    }

}
