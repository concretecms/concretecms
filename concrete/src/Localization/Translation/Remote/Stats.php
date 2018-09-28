<?php
namespace Concrete\Core\Localization\Translation\Remote;

use DateTime;

class Stats
{
    /**
     * The version handle.
     *
     * @var string
     */
    protected $version;

    /**
     * The total number of strings (translated and not translated).
     *
     * @var int
     */
    protected $total;

    /**
     * The number of translated strings.
     *
     * @var int
     */
    protected $translated;

    /**
     * The date/time of the last update of the translations (null if and only if $translated is null).
     *
     * @var DateTime|null
     */
    protected $updatedOn;

    /**
     * @param string $version the version handle
     * @param int $total the total number of strings (translated and not translated)
     * @param int $translated the number of translated strings
     * @param DateTime|null $updatedOn The date/time of the last update of the translations (null if and only if $translated is null)
     */
    public function __construct($version, $total, $translated, DateTime $updatedOn = null)
    {
        $this->version = (string) $version;
        $this->total = (int) $total;
        $this->translated = (int) $translated;
        $this->updatedOn = $updatedOn;
    }

    /**
     * Get the version handle.
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Get the total number of strings (translated and not translated).
     *
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
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
     * Get the date/time of the last update of the translations (null if and only if $translated is null).
     *
     * @return DateTime|null
     */
    public function getUpdatedOn()
    {
        return $this->updatedOn;
    }

    /**
     * Get the translation progress.
     *
     * @param int $decimals
     *
     * @return int|float
     */
    public function getProgress($decimals = 0)
    {
        if ($this->translated === 0) {
            $result = 0;
        } elseif ($this->translated === $this->total) {
            $result = 100;
        } else {
            $result = ($this->translated * 100.0) / $this->total;
            $decimals = (int) $decimals;
            if ($decimals <= 0) {
                $result = min(max((int) $result, 1), 99);
            } else {
                $min = 1 / pow(10, $decimals);
                $max = 100 - $min;
                $result = min(max(round($result, $decimals), $min), $max);
            }
        }

        return $result;
    }
}
