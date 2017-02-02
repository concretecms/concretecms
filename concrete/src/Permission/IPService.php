<?php
namespace Concrete\Core\Permission;

use Concrete\Core\Utility\IPAddress;
use Concrete\Core\User\UserBannedIp;
use Request;
use Concrete\Core\Application\Application;
use Concrete\Core\Database\Connection\Connection;
use DateTime;
use Concrete\Core\Permission\Event\BanIPEvent;

class IPService
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * DateTime value representing 'ban forever' (ie manual bans).
     *
     * @var string
     */
    const FOREVER_BAN_DATETIME = '1000-01-01 00:00:00';

    /**
     * Check if an IP adress is banned.
     *
     * @param IPAddress|mixed $ip The IPAddress instance containing the IP to check (if it's not an IPAddress instance we'll use the current IP address)
     * @param bool $extraParamString An extra SQL chunk to be added to the final query
     * @param array $extraParamValues Extra parameters to be passed to the final query
     *
     * @return bool
     */
    public function isBanned($ip = false, $extraParamString = false, $extraParamValues = [])
    {
        $ip = ($ip instanceof IPAddress) ? $ip : $this->getRequestIP();
        $db = $this->app->make(Connection::class);
        //do ip check
        $q = "SELECT count(expires) as count
		FROM UserBannedIPs
		WHERE
		(
			(ipFrom = ? AND ipTo = 0)
			OR
			(ipFrom <= ? AND ipTo >= ?)
		)
		AND (expires = ? OR expires > ?)
		";

        if ($extraParamString !== false) {
            $q .= $extraParamString;
        }

        $v = [$ip->getIp(), $ip->getIp(), $ip->getIp(), static::FOREVER_BAN_DATETIME, date('Y-m-d H:i:s')];
        $v = array_merge($v, $extraParamValues);

        $row = $db->fetchAssoc($q, $v);

        return ($row['count'] > 0) ? true : false;
    }

    /**
     * CHeck if an IP address has been manually banned.
     *
     * @param IPAddress $ip the IP address to ban
     *
     * @return bool
     */
    protected function existsManualPermBan(IPAddress $ip)
    {
        return $this->isBanned($ip, ' AND isManual = ? AND expires = ? ', [1, static::FOREVER_BAN_DATETIME]);
    }

    /**
     * Get the IPAddress instance containing the IP address of the current request.
     *
     * @return IPAddress
     */
    public function getRequestIP()
    {
        return new IPAddress(Request::getInstance()->getClientIp());
    }

    /**
     * Get the (localized) message telling the users that their IP address has been banned.
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return t(
            "Unable to complete action: your IP address has been banned. Please contact the administrator of this site for more information."
        );
    }

    /**
     * Add the current IP address to the list of IPs with failed login attempts.
     *
     * @param bool $ignoreConfig if set to true, we'll add the record even if the IP ban system is disabled in the configuration
     */
    public function logSignupRequest($ignoreConfig = false)
    {
        if ($ignoreConfig || $this->app->make('config')->get('concrete.security.ban.ip.enabled')) {
            $db = $this->app->make(Connection::class);
            $ip = $this->getRequestIP();
            $db->insert('SignupRequests', ['date_access' => date('Y-m-d H:i:s'), 'ipFrom' => $ip->getIp()]);
        }
    }

    /**
     * Check if the current UP address has reached the failed logi attempts threshold.
     *
     * @param bool $ignoreConfig if set to true, we'll check the IP even if the IP ban system is disabled in the configuration
     *
     * @return bool
     */
    public function signupRequestThresholdReached($ignoreConfig = false)
    {
        $config = $this->app->make('config');
        if ($ignoreConfig || $config->get('concrete.security.ban.ip.enabled') == 1) {
            $db = $this->app->make(Connection::class);
            $threshold_attempts = $config->get('concrete.security.ban.ip.attempts');
            $threshold_seconds = $config->get('concrete.security.ban.ip.time');
            $ip = $this->getRequestIP();
            $q = 'SELECT count(*) as count
			FROM SignupRequests
			WHERE ipFrom = ?
			AND date_access > DATE_SUB(?, INTERVAL ? SECOND)';
            $v = [$ip->getIp(), date('Y-m-d H:i:s'), $threshold_seconds];

            $row = $db->fetchAssoc($q, $v);
            if ($row['count'] >= $threshold_attempts) {
                return true;
            } else {
                return false;
            }
        }

        return false;
    }

    /**
     * Add an IP address to the list of IPs banned for too many failed login attempts.
     *
     * @param IPAddress|mixed $ip the IPAddress instance containing the IP to check (if it's not an IPAddress instance we'll use the current IP address)
     * @param bool $ignoreConfig if set to true, we'll add the IP address even if the IP ban system is disabled in the configuration
     */
    public function createIPBan($ip = false, $ignoreConfig = false)
    {
        $config = $this->app->make('config');
        if ($ignoreConfig || $config->get('concrete.security.ban.ip.enabled') == 1) {
            $ip = ($ip instanceof IPAddress) ? $ip : $this->getRequestIP();

            $db = $this->app->make(Connection::class);

            //If there's a permanent ban, obey its setting otherwise set up a temporary ban
            if (!$this->existsManualPermBan($ip)) {
                //IP_BAN_LOCK_IP_HOW_LONG_MIN of 0 or undefined  means forever
                $timeOffset = $config->get('concrete.security.ban.ip.length');
                $timeOffset = $timeOffset ? (int) $timeOffset : 0;
                if ($timeOffset !== 0) {
                    $banExpiration = new DateTime();
                    $banExpiration->modify('+' . $timeOffset . ' minutes');
                    //$banExpiration = $banExpiration->format('Y-m-d H:i:s');
                } else {
                    $banExpiration = null;
                }
                $event = new BanIPEvent($ip, $banExpiration);
                $this->app->make('director')->dispatch('on_ip_ban', $event);
                if ($event->proceed()) {
                    $banExpiration = $event->getBanExpiration();

                    $db->beginTransaction();
                    $q = 'DELETE FROM UserBannedIPs WHERE ipFrom = ? AND ipTo = ? AND isManual = ?';
                    $v = [$ip->getIp(), 0, 0];
                    $db->executeQuery($q, $v);

                    if ($banExpiration === null) {
                        $banUntil = static::FOREVER_BAN_DATETIME;
                    } else {
                        $banUntil = $banExpiration->format('Y-m-d H:i:s');
                    }

                    $q = 'INSERT INTO UserBannedIPs (ipFrom,ipTo,banCode,expires,isManual) VALUES (?,?,?,?,?)';
                    $v = [$ip->getIp(), 0, UserBannedIp::IP_BAN_CODE_REGISTRATION_THROTTLE, $banUntil, 0];
                    $db->executeQuery($q, $v);

                    $db->commit();
                }
            }
        }
    }

    /**
     * @deprecated use signupRequestThresholdReached (same syntax, just fixed the typo in the name)
     */
    public function signupRequestThreshholdReached($ignoreConfig = false)
    {
        return $this->signupRequestThresholdReached($ignoreConfig);
    }
}
