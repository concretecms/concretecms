<?php
namespace Concrete\Core\Entity\Attribute\Key\Settings;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="atDateTimeSettings")
 */
class DateTimeSettings extends Settings
{
    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    protected $akUseNowIfEmpty = false;

    /**
     * @ORM\Column(type="string")
     */
    protected $akDateDisplayMode = '';

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default": 60, "unsigned": true, "comment": "Time resolution (in seconds)"})
     */
    protected $akTimeResolution = 60;

    /**
     * @return bool
     */
    public function getUseNowIfEmpty()
    {
        return $this->akUseNowIfEmpty;
    }

    /**
     * @param bool $value
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
     * Get the time resolution (in seconds).
     *
     * @return int
     */
    public function getTimeResolution()
    {
        return $this->akTimeResolution;
    }

    /**
     * Set the time resolution (in seconds).
     *
     * @param int $value
     */
    public function setTimeResolution($value)
    {
        $value = (int) $value;
        if ($value > 0) {
            $this->akTimeResolution = $value;
        }
    }
}
