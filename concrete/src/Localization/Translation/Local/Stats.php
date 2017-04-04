<?php
namespace Concrete\Core\Localization\Translation\Local;

use DateTime;

class Stats
{
    /**
     * The translations file format handle.
     *
     * @var string
     */
    protected $formatHandle;

    /**
     * The translations file name.
     *
     * @var string
     */
    protected $filename;

    /**
     * The number of translated strings.
     *
     * @var int
     */
    protected $translated;

    /**
     * The date/time of the last update of the translations (null if and only if no translated string is present).
     *
     * @var DateTime|null
     */
    protected $lastUpdated;

    /**
     * @param string $formatHandle the translations file format handle
     * @param string $filename the translations file name
     * @param int $translated the number of translated strings
     * @param DateTime $lastUpdated the date/time of the last update of the translations (null if and only if no translated string is present)
     */
    public function __construct($formatHandle, $filename, $translated, DateTime $lastUpdated = null)
    {
        $this->formatHandle = $formatHandle;
        $this->filename = $filename;
        $this->translated = $numTranslated;
        $this->lastUpdated = $lastUpdated;
    }

    /**
     * Get the translations file format handle.
     *
     * @return string
     */
    public function getFormatHandle()
    {
        return $this->formatHandle;
    }

    /**
     * Get the translations file name.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Get the number of translated strings.
     *
     * @return int
     */
    public function getTranslated()
    {
        return $this->translated;
    }

    /**
     * Get the date/time of the last update of the translations (null if and only if no translated string is present).
     *
     * @return DateTime|null
     */
    public function getLastUpdated()
    {
        return $this->lastUpdated;
    }
}
