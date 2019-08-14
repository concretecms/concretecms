<?php
namespace Concrete\Core\Entity\Attribute\Key\Settings;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="atDateTimeSettings")
 * @since 8.0.0
 */
class DateTimeSettings extends Settings
{
    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     * @since 8.2.0
     */
    protected $akUseNowIfEmpty = false;

    /**
     * @ORM\Column(type="string")
     */
    protected $akDateDisplayMode = '';

    /**
     * @ORM\Column(type="text", nullable=false, options={"default": "", "comment": "Custom format for text inputs"})
     * @since 8.3.0
     */
    protected $akTextCustomFormat = '';

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default": 60, "unsigned": true, "comment": "Time resolution (in seconds)"})
     * @since 8.2.0
     */
    protected $akTimeResolution = 60;

    /**
     * @return bool
     * @since 8.2.0
     */
    public function getUseNowIfEmpty()
    {
        return $this->akUseNowIfEmpty;
    }

    /**
     * @param bool $value
     * @since 8.2.0
     */
    public function setUseNowIfEmpty($value)
    {
        $this->akUseNowIfEmpty = (bool) $value;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->akDateDisplayMode;
    }

    /**
     * @param mixed $mode
     */
    public function setMode($mode)
    {
        $this->akDateDisplayMode = (string) $mode;
    }

    /**
     * @return string
     * @since 8.3.0
     */
    public function getTextCustomFormat()
    {
        return $this->akTextCustomFormat;
    }

    /**
     * @param string $textCustomFormat
     * @since 8.3.0
     */
    public function setTextCustomFormat($textCustomFormat)
    {
        $this->akTextCustomFormat = (string) $textCustomFormat;
    }

    /**
     * Get the time resolution (in seconds).
     *
     * @return int
     * @since 8.2.0
     */
    public function getTimeResolution()
    {
        return $this->akTimeResolution;
    }

    /**
     * Set the time resolution (in seconds).
     *
     * @param int $value
     * @since 8.2.0
     */
    public function setTimeResolution($value)
    {
        $value = (int) $value;
        if ($value > 0) {
            $this->akTimeResolution = $value;
        }
    }
}
