<?php
namespace Concrete\Core\Localization\Service;

use Concrete\Core\Localization\Localization;
use Config;
use Core;
use Punic\Calendar;
use Punic\Comparer;
use Punic\Misc;
use Request;
use User;

class Date
{
    /**
     * The PHP date/time format string to be used when dealing with the database.
     *
     * @var string
     *
     * @see http://php.net/manual/function.date.php
     */
    const DB_FORMAT = 'Y-m-d H:i:s';

    /**
     * Convert any date/time representation to a string that can be used in DB queries.
     *
     * @param string|int|\DateTime $value It can be:<ul>
     *    <li>the special value 'now' (default) to return the current date/time</li>
     *    <li>a \DateTime instance</li>
     *    <li>a string parsable by strtotime (the $fromTimezone timezone is used)</li>
     *    <li>a timestamp</li>
     * </ul>
     * @param string $fromTimezone The timezone where $value is defined (useful only if $value is a date/time representation without a timezone, like for instance '2000-12-31 23:59:59').<br />
     * Special values are:<ul>
     *    <li>'system' (default) for the current system timezone</li>
     *    <li>'user' for the user's timezone</li>
     *    <li>'app' for the app's timezone</li>
     *    <li>Other values: one of the PHP supported time zones (see http://us1.php.net/manual/en/timezones.php )</li>
     * </ul>
     * @param string Returns the date/time representation (an empty string if $value is empty)
     */
    public function toDB($value = 'now', $fromTimezone = 'system')
    {
        $datetime = $this->toDateTime($value, 'system', $fromTimezone);

        return is_object($datetime) ? $datetime->format(static::DB_FORMAT) : '';
    }

    /**
     * Return the date/time representation for now, that can be overridden by a custom request when viewing pages in a moment specified by administrators (custom request date/time).
     *
     * @param bool $asTimestamp Set to true to retrieve the Unix timestamp, false to retrieve the string representation (eg '2000-12-31 23:59:59')
     *
     * @return string|int
     */
    public function getOverridableNow($asTimestamp = false)
    {
        $req = Request::getInstance();
        if ($req->hasCustomRequestUser() && $req->getCustomRequestDateTime()) {
            $timestamp = strtotime($req->getCustomRequestDateTime());
        } else {
            $timestamp = time();
        }

        return $asTimestamp ? $timestamp : date(static::DB_FORMAT, $timestamp);
    }

    /**
     * Subsitute for the native date() function that adds localized date support.
     * Use *ONLY* if really needed: you may want to use some of the formatDate/Time methods.
     * If you're not working with timestamps you may want to use the formatCustom method.
     *
     * @param string $mask The PHP format mask
     * @param bool|int $timestamp Use false for the current date/time, otherwise a valid Unix timestamp (we assume it's in the system timezone)
     * @param string $toTimezone The destination timezone.<br />
     * Special values are:<ul>
     *    <li>'system' (default) for the current system timezone</li>
     *    <li>'user' for the user's timezone</li>
     *    <li>'app' for the app's timezone</li>
     *    <li>Other values: one of the PHP supported time zones (see http://us1.php.net/manual/en/timezones.php )</li>
     * </ul>
     *
     * @return string
     *
     * @see http://php.net/manual/function.date.php
     */
    public function date($mask, $timestamp = false, $toTimezone = 'system')
    {
        if ($timestamp === false) {
            $timestamp = time();
        }
        $result = '';
        $datetime = $this->toDateTime($timestamp, $toTimezone);
        if (is_object($datetime)) {
            if (Localization::activeLocale() == Localization::BASE_LOCALE) {
                $result = $datetime->format($mask);
            } else {
                $result = Calendar::format(
                    $datetime,
                    Calendar::convertPhpToIsoFormat($mask)
                );
            }
        }

        return $result;
    }

    /**
     * Retrieve the display name (localized) of a time zone given its PHP identifier.
     *
     * @param string $timezoneID
     *
     * @return string
     */
    public function getTimezoneName($timezoneID)
    {
        $result = '';
        if (is_string($timezoneID) && $timezoneID !== '') {
            $names = $this->getTimezones();
            $result = isset($names[$timezoneID]) ? $names[$timezoneID] : $timezoneID;
        }

        return $result;
    }

    /**
     * Returns a keyed array of timezone identifiers (keys are the standard PHP timezone names, values are the localized timezone names).
     *
     * @return array
     *
     * @see http://www.php.net/datetimezone.listidentifiers.php
     */
    public function getTimezones()
    {
        static $cache = [];
        $locale = Localization::activeLocale();
        if (array_key_exists($locale, $cache)) {
            $result = $cache[$locale];
        } else {
            $result = [];
            $continentNames = [
                'Africa' => \Punic\Territory::getName('002'),
                'Asia' => \Punic\Territory::getName('142'),
                'America' => \Punic\Territory::getName('019'),
                'Antarctica' => \Punic\Territory::getName('AQ'),
                'Arctic' => t('Arctic'),
                'Atlantic' => t('Atlantic Ocean'),
                'Australia' => \Punic\Territory::getName('AU'),
                'Europe' => \Punic\Territory::getName('150'),
                'Indian' => t('Indian Ocean'),
                'Pacific' => t('Pacific Ocean'),
            ];
            foreach (\DateTimeZone::listIdentifiers() as $timezoneID) {
                switch ($timezoneID) {
                    case 'UTC':
                    case 'GMT':
                        $timezoneName = t(/*i18n: %s is an acronym like UTC, GMT, ... */'Greenwich Mean Time (%s)', $timezoneID);
                        break;
                    default:
                        $chunks = explode('/', $timezoneID);
                        if (array_key_exists($chunks[0], $continentNames)) {
                            $chunks[0] = $continentNames[$chunks[0]];
                        }
                        if (count($chunks) > 0) {
                            $city = \Punic\Calendar::getTimezoneExemplarCity($timezoneID, false);
                            if (!strlen($city)) {
                                switch ($timezoneID) {
                                    case 'America/Fort_Nelson':
                                        $city = tc(/*i18n: Canadian territory */'Territory', 'Fort Nelson');
                                        break;
                                    case 'America/Montreal':
                                        $city = tc(/*i18n: Canadian city */'Territory', 'Montreal');
                                        break;
                                    case 'America/Shiprock':
                                        $city = tc(/*i18n: Territory in New Mexico (USA) */'Territory', 'Shiprock');
                                        break;
                                    case 'Antarctica/South_Pole':
                                        $city = tc(/*i18n: The South Pole */'Territory', 'South Pole');
                                        break;
                                    case 'Asia/Atyrau':
                                        $city = tc(/*i18n: Kazakh territory */'Territory', 'Atyrau');
                                        break;
                                    case 'Asia/Barnaul':
                                        $city = tc(/*i18n: Russian city */'Territory', 'Barnaul');
                                        break;
                                    case 'Asia/Famagusta':
                                        $city = tc(/*i18n: City in Cyprus Island */'Territory', 'Famagusta');
                                        break;
                                    case 'Asia/Tomsk':
                                        $city = tc(/*i18n: Russian city */'Territory', 'Tomsk');
                                        break;
                                    case 'Asia/Yangon':
                                        $city = tc(/*i18n: Burmese city */'Territory', 'Yangon');
                                        break;
                                    case 'Europe/Astrakhan':
                                        $city = tc(/*i18n: Russian city */'Territory', 'Astrakhan');
                                        break;
                                    case 'Europe/Kirov':
                                        $city = tc(/*i18n: Russian city */'Territory', 'Kirov');
                                        break;
                                    case 'Europe/Saratov':
                                        $city = tc(/*i18n: Russian city */'Territory', 'Saratov');
                                        break;
                                    case 'Europe/Ulyanovsk':
                                        $city = tc(/*i18n: Russian city */'Territory', 'Ulyanovsk');
                                        break;
                                }
                            }
                            if (strlen($city)) {
                                $chunks = [$chunks[0], $city];
                            }
                        }
                        $timezoneName = implode('/', $chunks);
                        break;
                }
                $result[$timezoneID] = $timezoneName;
            }
            $comparer = new Comparer();
            $comparer->sort($result, true);
            $cache[$locale] = $result;
        }

        return $result;
    }

    /**
     * Returns the list of timezones with translated names, grouped by region.
     *
     * @return array
     *
     * @example
     * <pre>[
     *     'Africa' => [
     *         'Africa/Abidjan' => 'Abidjan',
     *         'Africa/Addis_Ababa' => 'Addis Abeba',
     *     ],
     *     'Americas' => [
     *         'America/North_Dakota/Beulah' => 'Beulah, North Dakota',
     *     ],
     *     'Antarctica' => [
     *         'Antarctica/McMurdo' => 'McMurdo',
     *     ],
     *     'Arctic' => [
     *         ...
     *     ],
     *     'Asia' => [
     *         ....
     *     ],
     *     'Atlantic Ocean' => [
     *         ....
     *     ],
     *     'Australia' => [
     *         ....
     *     ],
     *     'Europe' => [
     *         ....
     *     ],
     *     'Indian Ocean' => [
     *         ....
     *     ],
     *     'Pacific Ocean' => [
     *         ....
     *     ],
     *     'Others' => [
     *         'UTC' => 'Greenwich Mean Time (UTC)',
     *     ],
     *  ]</pre>
     *
     * @see http://www.php.net/datetimezone.listidentifiers.php
     */
    public function getGroupedTimezones()
    {
        $groups = [];
        $generics = [];
        $genericGroupName = tc('GenericTimezonesGroupName', 'Others');
        foreach ($this->getTimezones() as $id => $fullName) {
            $chunks = explode('/', $fullName, 2);
            if (!isset($chunks[1])) {
                array_unshift($chunks, $genericGroupName);
            }
            list($groupName, $territoryName) = $chunks;
            if ($groupName === $genericGroupName) {
                $generics[$id] = $territoryName;
            } else {
                if (!isset($groups[$groupName])) {
                    $groups[$groupName] = [];
                }
                $groups[$groupName][$id] = $territoryName;
            }
        }
        if (!empty($generics)) {
            $groups[$genericGroupName] = $generics;
        }

        return $groups;
    }

    /**
     * Returns the display name of a timezone.
     *
     * @param string|\DateTimeZone|\DateTime $timezone The timezone for which you want the localized display name
     *
     * @return string
     */
    public function getTimezoneDisplayName($timezone)
    {
        $displayName = '';
        if (is_object($timezone)) {
            if ($timezone instanceof \DateTimeZone) {
                $displayName = $timezone->getName();
            } elseif ($timezone instanceof \DateTime) {
                $displayName = $timezone->getTimezone()->getName();
            }
        } elseif (is_string($timezone)) {
            $displayName = $timezone;
        }
        if (strlen($displayName) > 0) {
            $timezones = $this->getTimezones();
            if (array_key_exists($displayName, $timezones)) {
                $displayName = $timezones[$displayName];
            }
        }

        return $displayName;
    }

    /**
     * Describe the difference in time between now and a date/time in the past.
     * If the date/time is in the future or if it's more than one year old, you'll get the date representation of $posttime.
     *
     * @param int $posttime The timestamp to analyze
     * @param bool $precise = false Set to true to a more verbose and precise result, false for a more rounded result
     *
     * @return string
     */
    public function timeSince($posttime, $precise = false)
    {
        $diff = time() - $posttime;
        if (($diff < 0) || ($diff > 365 * 24 * 60 * 60)) {
            return $this->formatDate($posttime, false);
        } else {
            return $this->describeInterval($diff, $precise);
        }
    }

    /**
     * Returns the localized representation of a time interval specified as seconds.
     *
     * @param int $diff The time difference in seconds
     * @param bool $precise = false Set to true to a more verbose and precise result, false for a more rounded result
     *
     * @return string
     */
    public function describeInterval($diff, $precise = false)
    {
        $secondsPerMinute = 60;
        $secondsPerHour = 60 * $secondsPerMinute;
        $secondsPerDay = 24 * $secondsPerHour;
        $days = floor($diff / $secondsPerDay);
        $diff = $diff - $days * $secondsPerDay;
        $hours = floor($diff / $secondsPerHour);
        $diff = $diff - $hours * $secondsPerHour;
        $minutes = floor($diff / $secondsPerMinute);
        $seconds = $diff - $minutes * $secondsPerMinute;
        $chunks = [];
        if ($days > 0) {
            $chunks[] = t2('%d day', '%d days', $days, $days);
            if ($precise) {
                $chunks[] = t2('%d hour', '%d hours', $hours, $hours);
            }
        } elseif ($hours > 0) {
            $chunks[] = t2('%d hour', '%d hours', $hours, $hours);
            if ($precise) {
                $chunks[] = t2('%d minute', '%d minutes', $minutes, $minutes);
            }
        } elseif ($minutes > 0) {
            $chunks[] = t2('%d minute', '%d minutes', $minutes, $minutes);
            if ($precise) {
                $chunks[] = t2('%d second', '%d seconds', $seconds, $seconds);
            }
        } else {
            $chunks[] = t2('%d second', '%d seconds', $seconds, $seconds);
        }

        return Misc::join($chunks);
    }

    /**
     * Returns the normalized timezone identifier.
     *
     * @param string $timezone The timezone to retrieve. Special values are:<ul>
     *    <li>'system' (default) for the current system timezone</li>
     *    <li>'user' for the user's timezone</li>
     *    <li>'app' for the app's timezone</li>
     *    <li>Other values: one of the PHP supported time zones (see http://us1.php.net/manual/en/timezones.php )</li>
     * </ul>
     *
     * @return string
     */
    public function getTimezoneID($timezone)
    {
        switch ($timezone) {
            case 'system':
                $timezone = Config::get('app.server_timezone', date_default_timezone_get());
                break;
            case 'app':
                $site = \Core::make('site')->getSite();
                $timezone = $site->getConfigRepository()->get('timezone', date_default_timezone_get());
                break;
            case 'user':
                $tz = null;
                if (Config::get('concrete.misc.user_timezones')) {
                    $u = null;
                    $request = null;
                    if (!Core::make('app')->isRunThroughCommandLineInterface()) {
                        $request = Request::getInstance();
                    }
                    if ($request && $request->hasCustomRequestUser()) {
                        $u = $request->getCustomRequestUser();
                    } else {
                        $u = new User();
                    }
                    if (is_object($u) && $u->isRegistered()) {
                        $tz = $u->getUserTimezone();
                    }
                }
                if ($tz) {
                    $timezone = $tz;
                } else {
                    $timezone = $this->getTimezoneID('app');
                }
                break;
        }

        return $timezone;
    }

    /**
     * @return string
     */
    public function getUserTimeZoneID()
    {
        return $this->getTimezoneID('user');
    }

    /**
     * Returns a \DateTimeZone instance for a specified timezone identifier.
     *
     * @param string $timezone The timezone to retrieve. Special values are:<ul>
     *    <li>'system' (default) for the current system timezone</li>
     *    <li>'user' for the user's timezone</li>
     *    <li>'app' for the app's timezone</li>
     *    <li>Other values: one of the PHP supported time zones (see http://us1.php.net/manual/en/timezones.php )</li>
     * </ul>
     *
     * @return \DateTimeZone|null Returns null if $timezone is invalid or the \DateTimeZone corresponding to $timezone
     */
    public function getTimezone($timezone)
    {
        $tz = null;
        $phpTimezone = $this->getTimezoneID($timezone);
        if (is_string($phpTimezone) && strlen($phpTimezone)) {
            try {
                $tz = new \DateTimeZone($phpTimezone);
            } catch (\Exception $x) {
            }
        }

        return $tz;
    }

    /**
     * Convert a date to a \DateTime instance.
     *
     * @param string|\DateTime|int $value It can be:<ul>
     *    <li>the special value 'now' (default) to return the current date/time</li>
     *    <li>a \DateTime instance</li>
     *    <li>a string parsable by strtotime (the $fromTimezone timezone is used)</li>
     *    <li>a timestamp</li>
     * </ul>
     * @param string $toTimezone The timezone to set. Special values are:<ul>
     *    <li>'system' (default) for the current system timezone</li>
     *    <li>'user' for the user's timezone</li>
     *    <li>'app' for the app's timezone</li>
     *    <li>Other values: one of the PHP supported time zones (see http://us1.php.net/manual/en/timezones.php )</li>
     * </ul>
     * @param string $fromTimezone The original timezone of $value (useful only if $value is a string like '2000-12-31 23:59'); it accepts the same values as $toTimezone
     *
     * @return \DateTime|null Returns the \DateTime instance (or null if $value couldn't be parsed)
     */
    public function toDateTime($value = 'now', $toTimezone = 'system', $fromTimezone = 'system')
    {
        return Calendar::toDateTime($value, $this->getTimezone($toTimezone), $this->getTimezone($fromTimezone));
    }

    /**
     * Returns the difference in days between to dates.
     *
     * @param mixed $from The start date/time representation (one of the values accepted by toDateTime)
     * @param mixed $to The end date/time representation (one of the values accepted by toDateTime)
     * @param string $timezone The timezone to set. Special values are:<ul>
     *    <li>'system' for the current system timezone</li>
     *    <li>'user' (default) for the user's timezone</li>
     *    <li>'app' for the app's timezone</li>
     *    <li>Other values: one of the PHP supported time zones (see http://us1.php.net/manual/en/timezones.php )</li>
     * </ul>
     *
     * @return int|null Returns the difference in days (less than zero if $dateFrom if greater than $dateTo).
     * Returns null if one of both the dates can't be parsed
     */
    public function getDeltaDays($from, $to, $timezone = 'user')
    {
        $dtFrom = $this->toDateTime($from, $timezone);
        $dtTo = $this->toDateTime($to, $timezone);
        if (is_null($dtFrom) || is_null($dtTo)) {
            return null;
        }
        $utc = new \DateTimeZone('UTC');
        $dtFrom->setTimezone($utc);
        $dtFrom = new \DateTime($dtFrom->format('Y-m-d'), $utc);
        $dtTo->setTimezone($utc);
        $dtTo = new \DateTime($dtTo->format('Y-m-d'), $utc);

        $seconds = $dtTo->getTimestamp() - $dtFrom->getTimestamp();

        return round($seconds / 86400);
    }

    /**
     * Render the date part of a date/time as a localized string.
     *
     * @param mixed $value $The date/time representation (one of the values accepted by toDateTime)
     * @param bool $longDate $Set to true for the long date format (eg 'December 31, 2000'), false (default) for the short format (eg '12/31/2000')
     * @param string $toTimezone The timezone to set. Special values are:<ul>
     *     <li>'system' for the current system timezone</li>
     *     <li>'user' (default) for the user's timezone</li>
     *     <li>'app' for the app's timezone</li>
     *     <li>Other values: one of the PHP supported time zones (see http://us1.php.net/manual/en/timezones.php )</li>
     * </ul>
     *
     * @return string Returns an empty string if $value couldn't be parsed, the localized string otherwise
     */
    public function formatDate($value = 'now', $format = 'short', $toTimezone = 'user')
    {
        // legacy
        if ($format === true) {
            $format = 'medium';
        } elseif ($format === false) {
            $format = 'short';
        }

        return Calendar::formatDate(
            $this->toDateTime($value, $toTimezone),
            $format
        );
    }

    /**
     * Render the time part of a date/time as a localized string.
     *
     * @param mixed $value The date/time representation (one of the values accepted by toDateTime)
     * @param bool $withSeconds Set to true to include seconds (eg '11:59:59 PM'), false (default) otherwise (eg '11:59 PM');
     * @param string $toTimezone The timezone to set. Special values are:<ul>
     *     <li>'system' for the current system timezone</li>
     *     <li>'user' (default) for the user's timezone</li>
     *     <li>'app' for the app's timezone</li>
     *     <li>Other values: one of the PHP supported time zones (see http://us1.php.net/manual/en/timezones.php )</li>
     * </ul>
     *
     * @return string Returns an empty string if $value couldn't be parsed, the localized string otherwise
     */
    public function formatTime($value = 'now', $withSeconds = false, $toTimezone = 'user')
    {
        return Calendar::formatTime(
            $this->toDateTime($value, $toTimezone),
            $withSeconds ? 'medium' : 'short'
        );
    }

    /**
     * Render both the date and time parts of a date/time as a localized string.
     *
     * @param mixed $value The date/time representation (one of the values accepted by toDateTime)
     * @param bool $longDate Set to true for the long date format (eg 'December 31, 2000 at ...'), false (default) for the short format (eg '12/31/2000 at ...')
     * @param bool $withSeconds Set to true to include seconds (eg '... at 11:59:59 PM'), false (default) otherwise (eg '... at 11:59 PM');
     * @param string $toTimezone The timezone to set. Special values are:<ul>
     *     <li>'system' for the current system timezone</li>
     *     <li>'user' (default) for the user's timezone</li>
     *     <li>'app' for the app's timezone</li>
     *     <li>Other values: one of the PHP supported time zones (see http://us1.php.net/manual/en/timezones.php )</li>
     * </ul>
     *
     * @return string Returns an empty string if $value couldn't be parsed, the localized string otherwise
     */
    public function formatDateTime($value = 'now', $longDate = false, $withSeconds = false, $toTimezone = 'user')
    {
        if ($longDate) {
            if ($withSeconds) {
                $format = 'medium|medium|medium';
            } else {
                $format = 'medium|medium|short';
            }
        } else {
            if ($withSeconds) {
                $format = 'short|short|medium';
            } else {
                $format = 'short|short|short';
            }
        }

        return Calendar::formatDateTime(
            $this->toDateTime($value, $toTimezone),
            $format
        );
    }

    /**
     * Render the date part of a date/time as a localized string. If the day is yesterday we'll print 'Yesterday' (the same for today, tomorrow).
     *
     * @param mixed $value The date/time representation (one of the values accepted by toDateTime)
     * @param bool $longDate Set to true for the long date format (eg 'December 31, 2000'), false (default) for the short format (eg '12/31/2000')
     * @param string $toTimezone The timezone to set. Special values are:<ul>
     *     <li>'system' for the current system timezone</li>
     *     <li>'user' (default) for the user's timezone</li>
     *     <li>'app' for the app's timezone</li>
     *     <li>Other values: one of the PHP supported time zones (see http://us1.php.net/manual/en/timezones.php )</li>
     * </ul>
     *
     * @return string Returns an empty string if $value couldn't be parsed, the localized string otherwise
     */
    public function formatPrettyDate($value, $longDate = false, $toTimezone = 'user')
    {
        $dtDate = $this->toDateTime($value, $toTimezone);
        if (is_null($dtDate)) {
            return '';
        }
        $days = $this->getDeltaDays('now', $dtDate, $toTimezone);
        switch ($days) {
            case 0:
                return t('Today');
            case 1:
                return t('Tomorrow');
            case -1:
                return t('Yesterday');
            default:
                return $this->formatDate($dtDate, $longDate);
        }
    }

    /**
     * Render both the date and time parts of a date/time as a localized string. If the day is yesterday we'll print 'Yesterday' (the same for today, tomorrow).
     *
     * @param mixed $value The date/time representation (one of the values accepted by toDateTime)
     * @param bool $longDate Set to true for the long date format (eg 'December 31, 2000 at ...'), false (default) for the short format (eg '12/31/2000 at ...')
     * @param bool $withSeconds Set to true to include seconds (eg '... at 11:59:59 PM'), false (default) otherwise (eg '... at 11:59 PM');
     * @param string $timezone The timezone to set. Special values are:<ul>
     *     <li>'system' for the current system timezone</li>
     *     <li>'user' (default) for the user's timezone</li>
     *     <li>'app' for the app's timezone</li>
     *     <li>Other values: one of the PHP supported time zones (see http://us1.php.net/manual/en/timezones.php )</li>
     * </ul>
     *
     * @return string Returns an empty string if $value couldn't be parsed, the localized string otherwise
     */
    public function formatPrettyDateTime($value, $longDate = false, $withSeconds = false, $timezone = 'user')
    {
        $dtDate = $this->toDateTime($value, $timezone);
        if (is_null($dtDate)) {
            return '';
        }
        $days = $this->getDeltaDays('now', $dtDate, $timezone);
        switch ($days) {
            case 0:
                return t(/*i18n: %s is a time */
                    'Today at %s',
                    $this->formatTime($dtDate, $withSeconds, $timezone)
                );
            case 1:
                return t(/*i18n: %s is a time */
                    'Tomorrow at %s',
                    $this->formatTime($dtDate, $withSeconds, $timezone)
                );
            case -1:
                return t(/*i18n: %s is a time */
                    'Yesterday at %s',
                    $this->formatTime($dtDate, $withSeconds, $timezone)
                );
            default:
                return $this->formatDateTime($dtDate, $longDate, $withSeconds);
        }
    }

    /**
     * Render a date/time as a localized string, by specifying a custom format.
     *
     * @param string $format The custom format (see http://www.php.net/manual/en/function.date.php for applicable formats)
     * @param mixed $value The date/time representation (one of the values accepted by toDateTime)
     * @param string $toTimezone The timezone to set. Special values are:<ul>
     *     <li>'system' for the current system timezone</li>
     *     <li>'user' (default) for the user's timezone</li>
     *     <li>'app' for the app's timezone</li>
     *     <li>Other values: one of the PHP supported time zones (see http://us1.php.net/manual/en/timezones.php )</li>
     * </ul>
     * @param string $fromTimezone The original timezone of $value (useful only if $value is a string like '2000-12-31 23:59'); it accepts the same values as $toTimezone
     *
     * @return string Returns an empty string if $value couldn't be parsed, the localized string otherwise
     */
    public function formatCustom($format, $value = 'now', $toTimezone = 'user', $fromTimezone = 'system')
    {
        return Calendar::format(
            $this->toDateTime($value, $toTimezone, $fromTimezone),
            Calendar::convertPhpToIsoFormat($format)
        );
    }

    /** Returns the format string for the jQueryUI DatePicker widget
     * @param string $relatedPHPFormat = '' Related PHP date format that will be used to parse the format handled by the DatePicker.
     *     If not specified we'll use the same format used by formatDate(..., false)
     *
     * @return string
     */
    public function getJQueryUIDatePickerFormat($relatedPHPFormat = '')
    {
        if (is_string($relatedPHPFormat) && $relatedPHPFormat !== '') {
            $phpFormat = $relatedPHPFormat;
        } else {
            $phpFormat = $this->getPHPDatePattern();
        }
        // Special chars that need to be escaped in the DatePicker format string
        $datepickerSpecials = ['d', 'o', 'D', 'm', 'M', 'y', '@', '!', '\''];
        // Map from php to DatePicker format
        $map = [
            'j' => 'd',
            'd' => 'dd',
            'z' => 'o',
            'D' => 'D',
            'l' => 'DD',
            'n' => 'm',
            'm' => 'mm',
            'M' => 'M',
            'F' => 'MM',
            'y' => 'y',
            'Y' => 'yy',
        ];
        $datepickerFormat = '';
        $escaped = false;
        for ($i = 0; $i < strlen($phpFormat); ++$i) {
            $c = substr($phpFormat, $i, 1);
            if ($escaped) {
                if (in_array($c, $datepickerSpecials)) {
                    $datepickerFormat .= '\'' . $c;
                } else {
                    $datepickerFormat .= $c;
                }
                $escaped = false;
            } elseif ($c === '\\') {
                $escaped = true;
            } elseif (array_key_exists($c, $map)) {
                $datepickerFormat .= $map[$c];
            } elseif (in_array($c, $datepickerSpecials)) {
                $datepickerFormat .= '\'' . $c;
            } else {
                $datepickerFormat .= $c;
            }
        }
        if ($escaped) {
            $datepickerFormat .= '\\';
        }

        return $datepickerFormat;
    }

    /**
     * Returns the time format (12 or 24).
     *
     * @return int
     */
    public function getTimeFormat()
    {
        return \Punic\Calendar::has12HoursClock() ? 12 : 24;
    }

    public function getPHPDatePattern()
    {
        $isoFormat = \Punic\Calendar::getDateFormat('short');
        $result = \Punic\Calendar::tryConvertIsoToPhpFormat($isoFormat);
        if ($result === null) {
            $result = t(/*i18n: Short date format: see http://www.php.net/manual/en/function.date.php */ 'n/j/Y');
        }

        return $result;
    }

    /**
     * Get the PHP date format string for times.
     *
     * @return string
     */
    public function getPHPTimePattern()
    {
        $isoFormat = \Punic\Calendar::getTimeFormat('short');
        $result = \Punic\Calendar::tryConvertIsoToPhpFormat($isoFormat);
        if ($result === null) {
            $result = t(/*i18n: Short time format: see http://www.php.net/manual/en/function.date.php */ 'g.i A');
        }

        return $result;
    }

    /**
     * Get the PHP date format string for dates/times.
     *
     * @return string
     */
    public function getPHPDateTimePattern()
    {
        $isoFormat = \Punic\Calendar::getDateTimeFormat('short');
        $result = \Punic\Calendar::tryConvertIsoToPhpFormat($isoFormat);
        if ($result === null) {
            $result = t(/*i18n: Short date/time format: see http://www.php.net/manual/en/function.date.php */ 'n/j/Y g.i A');
        }

        return $result;
    }

    /**
     * @deprecated
     */
    public function getLocalDateTime($systemDateTime = 'now', $mask = null)
    {
        if (!isset($mask) || !strlen($mask)) {
            $mask = static::DB_FORMAT;
        }

        if (!isset($systemDateTime) || !strlen($systemDateTime)) {
            return null; // if passed a null value, pass it back
        } elseif (strlen($systemDateTime)) {
            $datetime = new \DateTime($systemDateTime);
        } else {
            $datetime = new \DateTime();
        }

        if (Config::get('concrete.misc.user_timezones')) {
            $u = new User();
            if ($u && $u->isRegistered()) {
                $utz = $u->getUserTimezone();
                if ($utz) {
                    $tz = new \DateTimeZone($utz);
                    $datetime->setTimezone($tz);
                }
            }
        }
        if (Localization::activeLocale() != Localization::BASE_LOCALE) {
            return $this->dateTimeFormatLocal($datetime, $mask);
        } else {
            return $datetime->format($mask);
        }
    }

    /**
     * @deprecated
     */
    public function getSystemDateTime($userDateTime = 'now', $mask = null)
    {
        if (!isset($mask) || !strlen($mask)) {
            $mask = static::DB_FORMAT;
        }

        if (!isset($userDateTime) || !strlen($userDateTime)) {
            return null; // if passed a null value, pass it back
        }
        $datetime = new \DateTime($userDateTime);

        $timezone = \Core::make('site')->getSite()->getConfigRepository()->get('timezone');
        if ($timezone) {
            $tz = new \DateTimeZone($timezone);

            // create the in the user's timezone
            $datetime = new \DateTime($userDateTime, $tz);

            // grab the default timezone
            $stz = new \DateTimeZone(Config::get('app.server_timezone', date_default_timezone_get()));

            // convert the datetime object to the current timezone
            $datetime->setTimeZone($stz);
        }

        if (Config::get('concrete.misc.user_timezones')) {
            $u = new User();
            if ($u && $u->isRegistered()) {
                $utz = $u->getUserTimezone();
                if ($utz) {
                    $tz = new \DateTimeZone($utz);

                    // create the `DateTime` in the user's timezone
                    $datetime = new \DateTime($userDateTime, $tz);

                    // grab the default timezone
                    $stz = new \DateTimeZone(Config::get('app.server_timezone', date_default_timezone_get()));

                    // convert the datetime object to the server timezone
                    $datetime->setTimeZone($stz);
                }
            }
        }
        if (Localization::activeLocale() != Localization::BASE_LOCALE) {
            return $this->dateTimeFormatLocal($datetime, $mask);
        } else {
            return $datetime->format($mask);
        }
    }

    /**
     * @deprecated
     */
    public function dateTimeFormatLocal($datetime, $mask)
    {
        return Calendar::format(
            $datetime,
            Calendar::convertPhpToIsoFormat($mask)
        );
    }
}
