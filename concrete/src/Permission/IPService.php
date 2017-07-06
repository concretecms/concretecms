<?php
namespace Concrete\Core\Permission;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Http\Request;
use Concrete\Core\Utility\IPAddress;
use DateTime;
use IPLib\Address\AddressInterface;
use IPLib\Factory as IPFactory;
use IPLib\Range\RangeInterface;

class IPService
{
    /**
     * Bit mask for blacklist ranges.
     *
     * @var int
     */
    const IPRANGEFLAG_BLACKLIST = 0x0001;

    /**
     * Bit mask for whitelist ranges.
     *
     * @var int
     */
    const IPRANGEFLAG_WHITELIST = 0x0002;

    /**
     * Bit mask for manually generated ranges.
     *
     * @var int
     */
    const IPRANGEFLAG_MANUAL = 0x0010;

    /**
     * Bit mask for automatically generated ranges.
     *
     * @var int
     */
    const IPRANGEFLAG_AUTOMATIC = 0x0020;

    /**
     * IP range type: manually added to the blacklist.
     *
     * @var int
     */
    const IPRANGETYPE_BLACKLIST_MANUAL = 0x0011; // IPRANGEFLAG_BLACKLIST | IPRANGEFLAG_MANUAL

    /**
     * IP range type: automatically added to the blacklist.
     *
     * @var int
     */
    const IPRANGETYPE_BLACKLIST_AUTOMATIC = 0x0021; // IPRANGEFLAG_BLACKLIST | IPRANGEFLAG_AUTOMATIC

    /**
     * IP range type: manually added to the whitelist.
     *
     * @var int
     */
    const IPRANGETYPE_WHITELIST_MANUAL = 0x0012; // IPRANGEFLAG_WHITELIST | IPRANGEFLAG_MANUAL

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @param Repository $config
     * @param Connection $connection
     * @param Request $request
     */
    public function __construct(Repository $config, Connection $connection, Request $request)
    {
        $this->config = $config;
        $this->connection = $connection;
        $this->request = $request;
    }

    /**
     * Get the IP address of the current request.
     *
     * @return \IPLib\Address\AddressInterface
     */
    public function getRequestIPAddress()
    {
        return IPFactory::addressFromString($this->request->getClientIp());
    }

    /**
     * Check if an IP adress is blacklisted.
     *
     * @return bool
     */
    public function isBlacklisted(AddressInterface $ip = null)
    {
        if ($ip === null) {
            $ip = $this->getRequestIPAddress();
        }
        $rangeType = $this->getRangeType($ip);

        return $rangeType !== null && ($rangeType & static::IPRANGEFLAG_BLACKLIST) === static::IPRANGEFLAG_BLACKLIST;
    }

    /**
     * Check if an IP adress is blacklisted.
     *
     * @return bool
     */
    public function isWhitelisted(AddressInterface $ip = null)
    {
        if ($ip === null) {
            $ip = $this->getRequestIPAddress();
        }
        $rangeType = $this->getRangeType($ip);

        return $rangeType !== null && ($rangeType & static::IPRANGEFLAG_WHITELIST) === static::IPRANGEFLAG_WHITELIST;
    }

    /**
     * Get the (localized) message telling the users that their IP address has been banned.
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return t('Unable to complete action: your IP address has been banned. Please contact the administrator of this site for more information.');
    }

    /**
     * Add the current IP address to the list of IPs with failed login attempts.
     *
     * @param AddressInterface $ip the IP address to log (if null, we'll use the current IP address)
     * @param bool $ignoreConfig if set to true, we'll add the record even if the IP ban system is disabled in the configuration
     */
    public function logFailedLogin(AddressInterface $ip = null, $ignoreConfig = false)
    {
        if ($ignoreConfig || $this->config->get('concrete.security.ban.ip.enabled')) {
            if ($ip === null) {
                $ip = $this->getRequestIPAddress();
            }
            $comparableIP = $ip->getComparableString();
            $this->connection->executeQuery(
                '
                    INSERT INTO FailedLoginAttempts
                        (flaIp, flaTimestamp)
                    VALUES
                        (?, ' . $this->connection->getDatabasePlatform()->getNowExpression() . ')
                ',
                [$ip->getComparableString()]
            );
        }
    }

    /**
     * Check if the current IP address has reached the failed login attempts threshold.
     *
     * @param AddressInterface $ip the IP address to log (if null, we'll use the current IP address)
     * @param bool $ignoreConfig if set to true, we'll check the IP even if the IP ban system is disabled in the configuration
     *
     * @return bool
     */
    public function failedLoginsThresholdReached(AddressInterface $ip = null, $ignoreConfig = false)
    {
        $result = false;
        if ($ignoreConfig || $this->config->get('concrete.security.ban.ip.enabled')) {
            if ($ip === null) {
                $ip = $this->getRequestIPAddress();
            }
            if (!$this->isWhitelisted($ip)) {
                $thresholdSeconds = (int) $this->config->get('concrete.security.ban.ip.time');
                $thresholdTimestamp = new DateTime("-{$thresholdSeconds} seconds");
                $rs = $this->connection->executeQuery(
                    '
                        SELECT
                            ' . $this->connection->getDatabasePlatform()->getCountExpression('lcirID') . ' AS n
                        FROM
                            FailedLoginAttempts
                        WHERE
                            flaIp = ?
                            AND flaTimestamp > ?
                    ',
                    [$ip->getComparableString(), $thresholdTimestamp->format($this->connection->getDatabasePlatform()->getDateTimeFormatString())]
                );
                $count = $rs->fetchColumn();
                $rs->closeCursor();
                $thresholdAttempts = (int) $this->config->get('concrete.security.ban.ip.attempts');
                if ($count !== false && (int) $count >= $thresholdAttempts) {
                    $result = true;
                }
            }
        }

        return $result;
    }

    /**
     * Add an IP address to the list of IPs banned for too many failed login attempts.
     *
     * @param AddressInterface $ip the IP to add to the blacklist (if null, we'll use the current IP address)
     * @param bool $ignoreConfig if set to true, we'll add the IP address even if the IP ban system is disabled in the configuration
     */
    public function addToBlacklistForThresholdReached(AddressInterface $ip = null, $ignoreConfig = false)
    {
        if ($ignoreConfig || $this->config->get('concrete.security.ban.ip.enabled')) {
            if ($ip === null) {
                $ip = $this->getRequestIPAddress();
            }
            $banDurationMinutes = (int) $this->config->get('concrete.security.ban.ip.length');
            if ($banDurationMinutes > 0) {
                $expires = new DateTime("+{$banDurationMinutes} minutes");
            } else {
                $expires = null;
            }
            $this->createRange(IPFactory::rangeFromBoundaries($ip, $ip), static::IPRANGETYPE_BLACKLIST_AUTOMATIC, $expires);
        }
    }

    /**
     * Add persist an IP address range type.
     *
     * @param RangeInterface $range the IP address range to persist
     * @param int $type The range type (one of the IPService::IPRANGETYPE_... constants)
     * @param DateTime $expiration The optional expiration of the range type
     *
     * @return IPRange
     */
    public function createRange(RangeInterface $range, $type, DateTime $expiration = null)
    {
        $dateTimeFormat = $this->connection->getDatabasePlatform()->getDateTimeFormatString();
        $this->connection->executeQuery(
            '
                INSERT INTO LoginControlIpRanges
                    (lcirIpFrom, lcirIpTo, lcirType, lcirExpires)
                VALUES
                    (?, ?, ?, ?)
            ',
            [
                $range->getComparableStartString(),
                $range->getComparableEndString(),
                $type,
                ($expiration === null) ? null : $expiration->format($dateTimeFormat),
            ]
        );
        $id = $this->connection->lastInsertId();
        $rs = $this->connection->executeQuery('SELECT * FROM LoginControlIpRanges WHERE lcirID = ? LIMIT 1', [$id]);
        $row = $rs->fetch();
        $rs->closeCursor();

        return IPRange::createFromRow($row, $dateTimeFormat);
    }

    /**
     * Get the list of currently available ranges.
     *
     * @param int $type (one of the IPService::IPRANGETYPE_... constants)
     * @param bool $includeExpired Include expired records?
     *
     * @return IPRange[]|\Generator
     */
    public function getRanges($type, $includeExpired = false)
    {
        $sql = 'SELECT * FROM LoginControlIpRanges WHERE lcirType = ?';
        $params = [(int) $type];
        if (!$includeExpired) {
            $sql .= ' AND (lcirExpires IS NULL OR lcirExpires > ' . $this->connection->getDatabasePlatform()->getNowExpression() . ')';
        }
        $sql .= ' ORDER BY lcirID';
        $result = [];
        $dateTimeFormat = $this->connection->getDatabasePlatform()->getDateTimeFormatString();
        $rs = $this->connection->executeQuery($sql, $params);
        while (($row = $rs->fetch()) !== false) {
            yield IPRange::createFromRow($row, $dateTimeFormat);
        }
    }

    /**
     * Find already defined range given its record ID.
     *
     * @param int $id
     *
     * @return IPRange|null
     */
    public function getRangeByID($id)
    {
        $result = null;
        if ($id) {
            $rs = $this->connection->executeQuery(
                'SELECT * FROM LoginControlIpRanges WHERE lcirID = ? LIMIT 1',
                [$id]
            );
            $row = $rs->fetch();
            $rs->closeCursor();
            if ($row !== false) {
                $result = IPRange::createFromRow($row, $this->connection->getDatabasePlatform()->getDateTimeFormatString());
            }
        }

        return $result;
    }

    /**
     * Delete a range record.
     *
     * @param IPRange|int $range
     */
    public function deleteRange($range)
    {
        if ($range instanceof IPRange) {
            $id = $range->getID();
        } else {
            $id = (int) $range;
        }
        if ($id) {
            $this->connection->executeQuery('DELETE FROM LoginControlIpRanges WHERE lcirID = ? LIMIT 1', [$id]);
        }
    }

    /**
     * Get the range type (if defined) of an IP adress.
     *
     * @return int|null One of the IPRANGETYPE_... constants (or null if range is not defined).
     */
    protected function getRangeType(AddressInterface $ip)
    {
        $comparableIP = $ip->getComparableString();
        $rs = $this->connection->executeQuery(
            '
                SELECT
                    lcirType
                FROM
                    LoginControlIpRanges
                WHERE
                    lcirIpFrom <= ? AND ? <= lcirIpTo
                    AND (lcirExpires IS NULL OR lcirExpires > ' . $this->connection->getDatabasePlatform()->getNowExpression() . ')
            ',
            [$comparableIP, $comparableIP]
        );
        $type = null;
        while (($col = $rs->fetchColumn()) !== false) {
            $type = (int) $col;
            if (($type & static::IPRANGEFLAG_WHITELIST) === static::IPRANGEFLAG_WHITELIST) {
                break;
            }
        }
        $rs->closeCursor();

        return $type;
    }

    /**
     * @deprecated Use \Core::make('ip')->getRequestIPAddress()
     */
    public function getRequestIP()
    {
        $ip = $this->getRequestIPAddress();
        return new IPAddress($ip === null ? null : (string) $ip);
    }

    /**
     * @deprecated Use \Core::make('ip')->isBlacklisted()
     */
    public function isBanned($ip = false)
    {
        $ipAddress = null;
        if ($ip instanceof IPAddress) {
            $ipAddress = IPFactory::addressFromString($ip->getIp(IPAddress::FORMAT_IP_STRING));
        }

        return $this->isBlacklisted($ipAddress);
    }

    /**
     * @deprecated Use \Core::make('ip')->addToBlacklist()
     */
    public function createIPBan($ip = false, $ignoreConfig = false)
    {
        $ipAddress = null;
        if ($ip instanceof IPAddress) {
            $ipAddress = IPFactory::addressFromString($ip->getIp(IPAddress::FORMAT_IP_STRING));
        }
        $this->addToBlacklistForThresholdReached($ipAddress, $ignoreConfig);
    }

    /**
     * @deprecated Use \Core::make('ip')->logFailedLogin()
     */
    public function logSignupRequest($ignoreConfig = false)
    {
        $this->logFailedLogin(null, $ignoreConfig);
    }

    /**
     * @deprecated use signupRequestThresholdReached (same syntax, just fixed the typo in the name)
     */
    public function signupRequestThreshholdReached($ignoreConfig = false)
    {
        return $this->failedLoginsThresholdReached(null, $ignoreConfig);
    }

    /**
     * @deprecated Use \Core::make('ip')->failedLoginsThresholdReached()
     */
    public function signupRequestThresholdReached($ignoreConfig = false)
    {
        return $this->failedLoginsThresholdReached(null, $ignoreConfig);
    }
}
