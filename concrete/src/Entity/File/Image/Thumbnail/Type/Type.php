<?php

namespace Concrete\Core\Entity\File\Image\Thumbnail\Type;

use Concrete\Core\File\Image\Thumbnail\Type\Version;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entity representing a thumbnail type.
 *
 * @ORM\Entity
 * @ORM\Table(name="FileImageThumbnailTypes")
 */
class Type
{
    /**
     * Thumbnail sizing mode: proportional.
     *
     * @var string
     */
    const RESIZE_PROPORTIONAL = 'proportional';

    /**
     * Thumbnail sizing mode: exact dimensions.
     *
     * @var string
     */
    const RESIZE_EXACT = 'exact';

    /**
     * Default thumbnail sizing mode.
     *
     * @var string
     */
    const RESIZE_DEFAULT = self::RESIZE_PROPORTIONAL;

    /**
     * The thumbnail unique identifier.
     *
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     *
     * @var int|null
     */
    protected $ftTypeID = null;

    /**
     * The handle that identifies the thumbnail type.
     *
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $ftTypeHandle = '';

    /**
     * The name of the thumbnail type.
     *
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $ftTypeName = '';

    /**
     * The width of the thumbnails (or the maximum width in case of proportional sizing).
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int|null
     */
    protected $ftTypeWidth = null;

    /**
     * The height of the thumbnails (or the maximum height in case of proportional sizing).
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int|null
     */
    protected $ftTypeHeight = null;

    /**
     * Is this thumbnail type required? If yes, it can't be deleted.
     *
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    protected $ftTypeIsRequired = false;

    /**
     * The thumbnail sizing mode (one of the Type::RESIZE_... constants).
     *
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $ftTypeSizingMode = self::RESIZE_DEFAULT;

    /**
     * Get the thumbnail unique identifier.
     *
     * @return int|null
     */
    public function getID()
    {
        return $this->ftTypeID;
    }

    /**
     * Set the handle that identifies the thumbnail type.
     *
     * @param string $ftTypeHandle
     */
    public function setHandle($ftTypeHandle)
    {
        $this->ftTypeHandle = (string) $ftTypeHandle;
    }

    /**
     * Get the handle that identifies the thumbnail type.
     *
     * @return string
     */
    public function getHandle()
    {
        return $this->ftTypeHandle;
    }

    /**
     * Set the name of the thumbnail type.
     *
     * @param string $ftTypeName
     */
    public function setName($ftTypeName)
    {
        $this->ftTypeName = (string) $ftTypeName;
    }

    /**
     * Get the name of the thumbnail type.
     *
     * @return string
     */
    public function getName()
    {
        return $this->ftTypeName;
    }

    /**
     * Get the display name for this thumbnail type (localized and escaped accordingly to $format).
     *
     * @param string $format = 'html'
     *    Escape the result in html format (if $format is 'html').
     *    If $format is 'text' or any other value, the display name won't be escaped.
     *
     * @return string
     */
    public function getDisplayName($format = 'html')
    {
        $value = tc('ThumbnailTypeName', $this->getName());
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }

    /**
     * Set the width of the thumbnails (or the maximum width in case of proportional sizing).
     *
     * @param int|null $ftTypeWidth
     */
    public function setWidth($ftTypeWidth)
    {
        $this->ftTypeWidth = null;
        if (is_numeric($ftTypeWidth)) {
            $v = (int) $ftTypeWidth;
            if ($v > 0) {
                $this->ftTypeWidth = $v;
            }
        }
    }

    /**
     * Get the width of the thumbnails (or the maximum width in case of proportional sizing).
     *
     * @return int|null
     */
    public function getWidth()
    {
        return $this->ftTypeWidth;
    }

    /**
     * Set the height of the thumbnails (or the maximum height in case of proportional sizing).
     *
     * @param int|null $ftTypeHeight
     */
    public function setHeight($ftTypeHeight)
    {
        $this->ftTypeHeight = null;
        if (is_numeric($ftTypeHeight)) {
            $v = (int) $ftTypeHeight;
            if ($v > 0) {
                $this->ftTypeHeight = $v;
            }
        }
    }

    /**
     * Get the height of the thumbnails (or the maximum height in case of proportional sizing).
     *
     * @return int|null
     */
    public function getHeight()
    {
        return $this->ftTypeHeight;
    }

    /**
     * Mark this this thumbnail type as required (that is, it can't be deleted).
     */
    public function requireType()
    {
        $this->ftTypeIsRequired = true;
    }

    /**
     * Is this thumbnail type required? If yes, it can't be deleted.
     *
     * @return bool
     */
    public function isRequired()
    {
        return $this->ftTypeIsRequired;
    }

    /**
     * Set the thumbnail sizing mode.
     *
     * @param string $ftTypeSizingMode one of the Type::RESIZE_... constants
     */
    public function setSizingMode($ftTypeSizingMode = self::RESIZE_DEFAULT)
    {
        $this->ftTypeSizingMode = (string) $ftTypeSizingMode;
    }

    /**
     * Get the thumbnail sizing mode.
     *
     * @return string One of the Type::RESIZE_... constants.
     */
    public function getSizingMode()
    {
        return $this->ftTypeSizingMode ? $this->ftTypeSizingMode : self::RESIZE_DEFAULT;
    }

    /**
     * Get the display name of the thumbnail sizing mode.
     *
     * @return string
     */
    public function getSizingModeDisplayName()
    {
        $sizingModeDisplayNames = [
            self::RESIZE_PROPORTIONAL => t('Proportional'),
            self::RESIZE_EXACT => t('Exact'),
        ];

        return $sizingModeDisplayNames[$this->getSizingMode()];
    }

    /**
     * Save this instance to the database.
     */
    public function save()
    {
        $em = Application::getFacadeApplication()->make(EntityManagerInterface::class);
        $em->persist($this);
        $em->flush();
    }

    /**
     * Delete this instance from the database.
     */
    public function delete()
    {
        $em = Application::getFacadeApplication()->make(EntityManagerInterface::class);
        $em->remove($this);
        $em->flush();
    }

    /**
     * Get a thumbnail type version instance representing this thumbnail type (normal-DPI).
     *
     * @return \Concrete\Core\File\Image\Thumbnail\Type\Version
     */
    public function getBaseVersion()
    {
        return new Version($this->getHandle(), $this->getHandle(), $this->getName(), $this->getWidth(), $this->getHeight(), false, $this->getSizingMode());
    }

    /**
     * Get a thumbnail type version instance representing this thumbnail type (high-DPI).
     *
     * @return \Concrete\Core\File\Image\Thumbnail\Type\Version
     */
    public function getDoubledVersion()
    {
        $width = $this->getWidth();
        if ($width !== null) {
            $width *= 2;
        }
        $height = $this->getHeight();
        if ($height !== null) {
            $height *= 2;
        }

        return new Version($this->getHandle() . '_2x', $this->getHandle() . '_2x', $this->getName(), $width, $height, true, $this->getSizingMode());
    }
}
