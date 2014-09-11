<?php
namespace Concrete\Core\Permission;

use Loader;
use Concrete\Core\Utility\IPAddress;
use Config;
use Concrete\Core\User\UserBannedIp;
use Zend\Stdlib\DateTime;

class IPService {

    /**
     * @param bool|IPAddress $ip
     * @param bool $extraParamString
     * @param array $extraParamValues
     * @return bool
     */
    public function isBanned($ip=false, $extraParamString=false, $extraParamValues=array()) {
		$ip = ($ip instanceof IPAddress) ? $ip : $this->getRequestIP();
		$db = Loader::db();
		//do ip check
		$q = "SELECT count(expires) as count
		FROM UserBannedIPs
		WHERE
		(
			(ipFrom = ? AND ipTo IS NULL)
			OR
			(ipFrom <= ? AND ipTo >= ?)
		)
		AND (expires = '0000-00-00 00:00:00' OR expires > now())
		";

		if($extraParamString !== false){
			$q .= $extraParamString;
		}

		$v = array($ip->getIp(), $ip->getIp(), $ip->getIp());
		$v = array_merge($v,$extraParamValues);

		$rs 	= $db->Execute($q,$v);
		$row 	= $rs->fetchRow();

		return ($row['count'] > 0) ? true : false;
	}

    /**
     * @param IPAddress $ip
     * @return bool
     */
    protected function checkForManualPermBan(IPAddress $ip){
		return $this->isBanned($ip, ' AND isManual = ? AND expires = ? ',Array(1,'0000-00-00 00:00:00'));
	}

	/** Returns an IPAddress object if one was found, or false if not
	* @return false|IPAddress
	*/
	public function getRequestIP() {
		$result = false;
		foreach(array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $index) {
			if(array_key_exists($index, $_SERVER) && is_string($_SERVER[$index])) {
				foreach(explode(',', $_SERVER[$index]) as $ip) {
					$ip = trim($ip);
					if(strlen($ip)) {
                        $ip = new IPAddress($ip);
						if($ip->isPrivate()) {
							$result = $ip;
						} else {
							return $ip;
						}
					}
				}
			}
		}
		return $result;
	}

	public function getErrorMessage() {
		return t("Unable to complete action: your IP address has been banned. Please contact the administrator of this site for more information.");
	}

	public function logSignupRequest($ignoreConfig=false) {

		if (Config::get('IP_BAN_LOCK_IP_ENABLE') == 1) {
			$db = Loader::db();
            $ip = $this->getRequestIP();
			$db->insert('SignupRequests', array('date_access' => date('Y-m-d H:i:s'), 'ipFrom' => $ip->getIp()));
		}
	}

	public function signupRequestThreshholdReached($ignoreConfig=false) {
		if ($ignoreConfig || Config::get('IP_BAN_LOCK_IP_ENABLE') == 1) {
			$db = Loader::db();
			$threshold_attempts  = Config::get('IP_BAN_LOCK_IP_ATTEMPTS');
			$threshhold_seconds = Config::get('IP_BAN_LOCK_IP_TIME');
			$ip = $this->getRequestIP();
			$q = 'SELECT count(ipFrom) as count
			FROM SignupRequests
			WHERE ipFrom = ?
			AND date_access > DATE_SUB(now(), INTERVAL ? SECOND)';
			$v = Array($ip->getIp(), $threshhold_seconds);

			$rs = $db->execute($q,$v);
			$row = $rs->fetchRow();
			if ($row['count'] >= $threshold_attempts) {
				return true;
			}
			else{
				return false;
			}
		}
	}

    /**
     * @param bool|IPAddress $ip
     * @param bool $ignoreConfig
     */
    public function createIPBan($ip=false,$ignoreConfig=false) {
		if ($ignoreConfig || Config::get('IP_BAN_LOCK_IP_ENABLE') == 1) {
			$ip = ($ip instanceof IPAddress) ? $ip : $this->getRequestIP();

			$db	= Loader::db();

			//If there's a permanent ban, obey its setting otherwise set up a temporary ban
			if ($this->checkForManualPermBan($ip)) {
				$db->StartTrans();
				$q 	= 'DELETE FROM UserBannedIPs WHERE ipFrom = ? AND ipTo = ? AND isManual = ?';
				$v  = Array($ip->getIp(),0, 0);
				$db->execute($q,$v);


                //IP_BAN_LOCK_IP_HOW_LONG_MIN of 0 or undefined  means forever
                $timeOffset = Config::get('IP_BAN_LOCK_IP_HOW_LONG_MIN');
                $timeOffset = $timeOffset ? ($timeOffset)  : 0;
                if($timeOffset !== 0) {
                    $banUntil = new \DateTime();
                    $banUntil->modify('+'.$timeOffset.' minutes');
                    $banUntil = $banUntil->format('Y-m-d H:i:s');
                } else {
                    $banUntil = '0000-00-00 00:00:00';
                }

				$q	=  'INSERT INTO UserBannedIPs (ipFrom,ipTo,banCode,expires,isManual) ';
				$q  .= 'VALUES (?,?,?,?,?)';
				$v  = array($ip->getIp(),0,UserBannedIp::IP_BAN_CODE_REGISTRATION_THROTTLE,$banUntil,0);
				$db->execute($q,$v);

				$db->CompleteTrans();
			}
		}
	}

}
