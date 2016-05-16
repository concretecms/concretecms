<?php
namespace Concrete\Core\Entity\File\Image\Thumbnail\Type;

use Database;
use Concrete\Core\File\Image\Thumbnail\Type\Version;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="FileImageThumbnailTypes")
 */
class Type
{
    /**
     * @ORM\Column(type="string")
     */
    protected $ftTypeHandle;

    /**
     * @ORM\Column(type="string")
     */
    protected $ftTypeName;

    /**
     * @ORM\Column(type="integer")
     */
    protected $ftTypeWidth = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $ftTypeHeight = null;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $ftTypeIsRequired = false;

    /**
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $ftTypeID;

    /**
     * @param mixed $ftTypeHandle
     */
    public function setHandle($ftTypeHandle)
    {
        $this->ftTypeHandle = $ftTypeHandle;
    }

    /**
     * @return mixed
     */
    public function getHandle()
    {
        return $this->ftTypeHandle;
    }

    /**
     * @return mixed
     */
    public function getID()
    {
        return $this->ftTypeID;
    }

    /**
     * @param mixed $ftTypeIsRequired
     */
    public function requireType()
    {
        $this->ftTypeIsRequired = true;
    }

    /**
     * @return mixed
     */
    public function isRequired()
    {
        return $this->ftTypeIsRequired;
    }

    /**
     * @param mixed $ftTypeName
     */
    public function setName($ftTypeName)
    {
        $this->ftTypeName = $ftTypeName;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->ftTypeName;
    }

    /** Returns the display name for this thumbnail type (localized and escaped accordingly to $format)
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
     * @param mixed $ftTypeWidth
     */
    public function setWidth($ftTypeWidth)
    {
        $this->ftTypeWidth = $ftTypeWidth;
    }

    /**
     * @param mixed $ftTypeHeight
     */
    public function setHeight($ftTypeHeight)
    {
        $this->ftTypeHeight = is_numeric($ftTypeHeight) ? $ftTypeHeight : null;
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {
        return $this->ftTypeWidth;
    }

    /**
     * @return mixed
     */
    public function getHeight()
    {
        return $this->ftTypeHeight;
    }

    public function save()
    {
        $em = \ORM::entityManager();
        $em->persist($this);
        $em->flush();
    }

    public function delete()
    {
        $em = \ORM::entityManager();
        $em->remove($this);
        $em->flush();
    }

    public function getBaseVersion()
    {
        return new Version($this->getHandle(), $this->getHandle(), $this->getName(), $this->getWidth(), $this->getHeight());
    }

    public function getDoubledVersion()
    {
        $height = null;
        if ($this->getHeight()) {
            $height = $this->getHeight() * 2;
        }

        return new Version($this->getHandle() . '_2x', $this->getHandle() . '_2x', $this->getName(), $this->getWidth() * 2, $height, true);
    }
}
