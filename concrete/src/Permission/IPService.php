<?php
namespace Concrete\Core\Permission;

use Concrete\Core\Utility\IPAddress;
use Config;
use Concrete\Core\User\UserBannedIp;
use Database;
use Request;

class IPService
{
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
        $db = Database::connection();
        //do ip check
        $q = "SELECT count(expires) as count
		FROM UserBannedIPs
		WHERE
		(
			(ipFrom = ? AND ipTo = 0)
			OR
			(ipFrom <= ? AND ipTo >= ?)
		)
		AND (expires = '1000-01-01 00:00:00' OR expires > ?)
		";

        if ($extraParamString !== false) {
            $q .= $extraParamString;
        }

        $v = [$ip->getIp(), $ip->getIp(), $ip->getIp(), date('Y-m-d H:i:s')];
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
        return $this->isBanned($ip, ' AND isManual = ? AND expires = ? ', [1, '1000-01-01 00:00:00']);
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
        if ($ignoreConfig || Config::get('concrete.security.ban.ip.enabled') == 1) {
            $db = Database::connection();
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
    public function signupRequestThreshholdReached($ignoreConfig = false)
    {
        if ($ignoreConfig || Config::get('concrete.security.ban.ip.enabled') == 1) {
            $db = Database::connection();
            $threshold_attempts = Config::get('concrete.security.ban.ip.attempts');
            $threshhold_seconds = Config::get('concrete.security.ban.ip.time');
            $ip = $this->getRequestIP();
            $q = 'SELECT count(*) as count
			FROM SignupRequests
			WHERE ipFrom = ?
			AND date_access > DATE_SUB(?, INTERVAL ? SECOND)';
            $v = [$ip->getIp(), date('Y-m-d H:i:s'), $threshhold_seconds];

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
        if ($ignoreConfig || Config::get('concrete.security.ban.ip.enabled') == 1) {
            $ip = ($ip instanceof IPAddress) ? $ip : $this->getRequestIP();

            $db = Database::connection();

            //If there's a permanent ban, obey its setting otherwise set up a temporary ban
            if (!$this->existsManualPermBan($ip)) {
                $db->beginTransaction();
                $q = 'DELETE FROM UserBannedIPs WHERE ipFrom = ? AND ipTo = ? AND isManual = ?';
                $v = [$ip->getIp(), 0, 0];
                $db->executeQuery($q, $v);

                //IP_BAN_LOCK_IP_HOW_LONG_MIN of 0 or undefined  means forever
                $timeOffset = Config::get('concrete.security.ban.ip.length');
                $timeOffset = $timeOffset ? ($timeOffset) : 0;
                if ($timeOffset !== 0) {
                    $banUntil = new \DateTime();
                    $banUntil->modify('+' . $timeOffset . ' minutes');
                    $banUntil = $banUntil->format('Y-m-d H:i:s');
                } else {
                    $banUntil = '1000-01-01 00:00:00';
                }

                $q = 'INSERT INTO UserBannedIPs (ipFrom,ipTo,banCode,expires,isManual) VALUES (?,?,?,?,?)';
                $v = [$ip->getIp(), 0, UserBannedIp::IP_BAN_CODE_REGISTRATION_THROTTLE, $banUntil, 0];
                $db->executeQuery($q, $v);

                $db->commit();
            }
        }
    }
}
