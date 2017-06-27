<?php
namespace Concrete\Core\Database\Connection;

use Concrete\Core\Localization\Service\Date;
use DateTime;
use DateTimeZone;

/**
 * Helper class to work with database time zones.
 */
class Timezone
{
    /**
     * The database connection.
     *
     * @var Connection
     */
    protected $connection;
    /**
     * The date date.
     *
     * @var Date
     */
    protected $dateHelper;

    /**
     * Inizialize the instance.
     *
     * @param Connection $connection the database connection to check the time zones against
     * @param Date $dateHelper the date helper
     */
    public function __construct(Connection $connection, Date $dateHelper)
    {
        $this->connection = $connection;
        $this->dateHelper = $dateHelper;
    }

    /**
     * Get a list of time zones that are compatible with the database.
     *
     * @return array array keys are the time zone IDs (eg: 'Pacific/Pago_Pago'), array values are the localized time zone names
     */
    public function getCompatibleTimezones()
    {
        $validTimezones = [];
        foreach ($this->dateHelper->getTimezones() as $timezoneID => $timezoneName) {
            if ($this->getDeltaTimezone($timezoneID) === null) {
                $validTimezones[$timezoneID] = $timezoneName;
            }
        }

        return $validTimezones;
    }

    /**
     * Check if a PHP time zone is compatible with the database timezone.
     *
     * @param DateTimeZone|string $phpTimezone
     *
     * @throws \Exception throws an Exception if $phpTimezone is not recognised as a valid timezone
     *
     * @return null|array If the time zone matches, we'll return null, otherwise an array with the keys 'dstProblems' (boolean) and 'maxDeltaMinutes' (int)
     */
    public function getDeltaTimezone($phpTimezone)
    {
        if (!($phpTimezone instanceof DateTimeZone)) {
            $phpTimezone = new DateTimeZone($phpTimezone);
        }
        $data = $this->getDatabaseTimestamps();
        extract($data);
        $sometimesSame = false;
        $maxDeltaMinutes = 0;
        foreach ($timestamps as $index => $timestamp) {
            $databaseValue = new DateTime($databaseDatetimes[$index], $phpTimezone);
            $phpValue = DateTime::createFromFormat('U', $timestamp, $phpTimezone);
            $deltaMinutes = (int) floor(($phpValue->getTimestamp() - $databaseValue->getTimestamp()) / 60);
            if ($deltaMinutes === 0) {
                $sometimesSame = true;
            } else {
                if (abs($deltaMinutes) > abs($maxDeltaMinutes)) {
                    $maxDeltaMinutes = $deltaMinutes;
                }
            }
        }

        if ($maxDeltaMinutes === 0) {
            return null;
        } else {
            return [
                'dstProblems' => $sometimesSame,
                'maxDeltaMinutes' => $maxDeltaMinutes,
            ];
        }
    }

    /**
     * Get a textual representation of the result of getDeltaTimezone().
     *
     * @param array $deltaTimezone The result of getDeltaTimezone (when it's not null)
     *
     * @return string
     */
    public function describeDeltaTimezone(array $deltaTimezone)
    {
        $interval = $this->dateHelper->describeInterval(60 * abs($deltaTimezone['maxDeltaMinutes']), true);
        if ($deltaTimezone['dstProblems']) {
            $result = t(/*i18n: %s is an interval, like "3 hours and 30 minutes"*/'The way PHP and database handle daylight saving times differs by %s.', $interval);
        } elseif ($deltaTimezone['maxDeltaMinutes'] > 0) {
            $result = t(/*i18n: %s is an interval, like "3 hours and 30 minutes"*/'The database timezone has times greater by %s compared to the PHP timezone.', $interval);
        } else {
            $result = t(/*i18n: %s is an interval, like "3 hours and 30 minutes"*/'The database timezone has times smaller by %s compared to the PHP timezone.', $interval);
        }

        return $result;
    }

    /**
     * Cache the result of getDatabaseTimestamps.
     *
     * @var array|null
     */
    private $databaseTimestamps = null;

    /**
     * Get a list of date/times checked against the database.
     *
     * @return array {
     *
     *     @var int[] $timestamps the UNIX timestamps of the date/times checked
     *     @var string[] $databaseDatetimes The corresponding date/time representations of the timestamps as formatted by the database.
     * }
     */
    protected function getDatabaseTimestamps()
    {
        if ($this->databaseTimestamps === null) {
            // Let's check the timestamp at solstices,
            // to be sure we also check potential daylight saving time changes.
            $timestamps = [
                mktime(12, 0, 0, 6, 21, date('Y')),
                mktime(12, 0, 0, 12, 21, date('Y')),
            ];
            $sql = 'SELECT ';
            foreach ($timestamps as $index => $timestamp) {
                if ($index > 0) {
                    $sql .= ', ';
                }
                $sql .= "FROM_UNIXTIME($timestamp) as datetime_$index";
            }
            $row = $this->connection->fetchAssoc($sql);
            $databaseDatetimes = [];
            foreach (array_keys($timestamps) as $index) {
                $databaseDatetimes[$index] = $row["datetime_$index"];
            }
            $this->databaseTimestamps = [
                'timestamps' => $timestamps,
                'databaseDatetimes' => $databaseDatetimes,
            ];
        }

        return $this->databaseTimestamps;
    }

    /**
     * Cache the result of getDatabaseTimezoneName().
     *
     * @var string|null
     */
    private $databaseTimezoneName = null;

    /**
     * Get the database time zone name (example: 'SYSTEM').
     *
     * @return string|null
     */
    public function getDatabaseTimezoneName()
    {
        if ($this->databaseTimezoneName === null) {
            $this->databaseTimezoneName = $this->connection->fetchColumn('select @@time_zone') ?: '';
        }

        return $this->databaseTimezoneName;
    }
}
