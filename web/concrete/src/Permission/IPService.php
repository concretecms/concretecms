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
     * @param bool|IPAddress $ip
     * @param bool $extraParamString
     * @param array $extraParamValues
     * @return bool
     */
    public function isBanned($ip = false, $extraParamString = false, $extraParamValues = array())
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

        $v = array($ip->getIp(), $ip->getIp(), $ip->getIp(), date('Y-m-d H:i:s'));
        $v = array_merge($v, $extraParamValues);

        $row = $db->fetchAssoc($q, $v);

        return ($row['count'] > 0) ? true : false;
    }

    /**
     * @param IPAddress $ip
     * @return bool
     */
    protected function existsManualPermBan(IPAddress $ip)
    {
        return $this->isBanned($ip, ' AND isManual = ? AND expires = ? ', Array(1, '1000-01-01 00:00:00'));
    }

    /** Returns an IPAddress object if one was found, or false if not
     * @return false|IPAddress
     */
    public function getRequestIP()
    {
        return new IPAddress(Request::getInstance()->getClientIp());
    }

    public function getErrorMessage()
    {
        return t(
            "Unable to complete action: your IP address has been banned. Please contact the administrator of this site for more information."
        );
    }

    public function logSignupRequest($ignoreConfig = false)
    {
        if ($ignoreConfig || Config::get('concrete.security.ban.ip.enabled') == 1) {
            $db = Database::connection();
            $ip = $this->getRequestIP();
            $db->insert('SignupRequests', array('date_access' => date('Y-m-d H:i:s'), 'ipFrom' => $ip->getIp()));
        }
    }

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
            $v = Array($ip->getIp(), date('Y-m-d H:i:s'), $threshhold_seconds);

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
     * @param bool|IPAddress $ip
     * @param bool $ignoreConfig
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
                $v = Array($ip->getIp(), 0, 0);
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
                $v = array($ip->getIp(), 0, UserBannedIp::IP_BAN_CODE_REGISTRATION_THROTTLE, $banUntil, 0);
                $db->executeQuery($q, $v);

                $db->commit();
            }
        }
    }

}
