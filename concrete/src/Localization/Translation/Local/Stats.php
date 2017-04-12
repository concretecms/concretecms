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
     * The version of the translations.
     *
     * @var string
     */
    protected $version;

    /**
     * The date/time of the last update of the translations (null if and only if no translated string is present).
     *
     * @var DateTime|null
     */
    protected $updatedOn;

    /**
     * @param string $formatHandle the translations file format handle
     * @param string $filename the translations file name
     * @param string $version the version of the file
     * @param DateTime $updatedOn the date/time of the last update of the translations (null if and only if no translated string is present)
     */
    public function __construct($formatHandle, $filename, $version, DateTime $updatedOn = null)
    {
        $this->formatHandle = $formatHandle;
        $this->filename = $filename;
        $this->version = (string) $version;
        $this->updatedOn = $updatedOn;
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
     * Get the version of the translations.
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Get the date/time of the last update of the translations (null if and only if no translated string is present).
     *
     * @return DateTime|null
     */
    public function getUpdatedOn()
    {
        return $this->updatedOn;
    }
}
