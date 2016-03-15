<?php
namespace Concrete\Core\Entity\Attribute\Key\Type;

use Concrete\Core\Entity\Attribute\Value\Value\ImageFileValue;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ImageFileAttributeKeyTypes")
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
     * @ORM\Column(type="integer")
     */
    protected $mode;

    public function __construct()
    {
        $this->mode = self::TYPE_FILE_MANAGER;
    }

    public function getMode()
    {
        return $this->mode;
    }

    public function isModeFileManager()
    {
        return $this->mode == self::TYPE_FILE_MANAGER;
    }

    public function isModeHtmlInput()
    {
        return $this->mode == self::TYPE_HTML_INPUT;
    }

    public function setModeToFileManager()
    {
        $this->mode = self::TYPE_FILE_MANAGER;
    }

    public function setModeToHtmlInput()
    {
        $this->mode = self::TYPE_HTML_INPUT;
    }

}
