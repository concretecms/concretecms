<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Permissions;

use \Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Loader;
use \Concrete\Core\User\UserBannedIp;
use Exception;

class Blacklist extends DashboardPageController
{

    public function formatTimestampAsMinutesSeconds($seconds)
    {
        if ($seconds == 0) {
            return t('Never');
        } else {
            $seconds = $seconds - time();
            return floor($seconds / 60) . 'm' . $seconds % 60 . 's';
        }
    }

    // assumes ipv4
    private function parseIPBlacklistIntoRanges()
    {
        $ips = preg_split('{[\r\n]+}', $this->post('ip_ban_manual'), null, PREG_SPLIT_NO_EMPTY);
        $ip_ranges = Array();
        foreach ($ips as $ip) {
            if (strpos($ip, '*') === false) {
                $ip = long2ip(ip2long($ip)); // ensures a valid ip
                $ip_ranges[] = Array(
                    'ipFrom' => $ip,
                    'ipTo' => 0
                );
            } else {
                $aOctets = preg_split('{\.}', $ip);
                $ipFrom = '';
                $ipTo = '';
                for ($i = 0; $i < 4; $i ++) {
                    if (is_numeric($aOctets[$i])) {
                        $ipFrom .= $aOctets[$i] . '.';
                        $ipTo .= $aOctets[$i] . '.';
                    } else {
                        $ipFrom .= '0' . '.';
                        $ipTo .= '255' . '.';
                    }
                }
                $ipFrom = substr($ipFrom, 0, strlen($ipFrom) - 1);
                $ipTo = substr($ipTo, 0, strlen($ipTo) - 1);

                $ipFrom = long2ip(ip2long($ipFrom)); // ensures a valid ip
                $ipTo = long2ip(ip2long($ipTo)); // ensures a valid ip

                $ip_ranges[] = Array(
                    'ipFrom' => $ipFrom,
                    'ipTo' => $ipTo
                );
            }
        }

        return $ip_ranges;
    }

    const IP_BLACKLIST_CHANGE_MAKEPERM = 1;

    const IP_BLACKLIST_CHANGE_REMOVE = 2;

    const IP_BAN_LOCK_IP_HOW_LONG_TYPE_TIMED = 'timed';

    const IP_BAN_LOCK_IP_HOW_LONG_TYPE_FOREVER = 'forever';

    public function update_ipblacklist()
    {
        $db = Loader::db();
        if ($this->token->validate("update_ipblacklist")) {

            // configs from top part form
            $ip_ban_lock_ip_enable = (1 == $this->post('ip_ban_lock_ip_enable')) ? 1 : 0;
            Config::save('concrete.security.ban.ip.enabled', $ip_ban_lock_ip_enable);
            Config::save('concrete.security.ban.ip.attempts', $this->post('ip_ban_lock_ip_attempts'));
            Config::Save('concrete.security.ban.ip.time', $this->post('ip_ban_lock_ip_time'));

            if (self::IP_BAN_LOCK_IP_HOW_LONG_TYPE_FOREVER != $this->post('ip_ban_lock_ip_how_long_type')) {
                Config::Save('concrete.security.ban.ip.length', $this->post('ip_ban_lock_ip_how_long_min'));
            } else {
                Config::Save('concrete.security.ban.ip.length', 0);
            }

            // ip table actions
            // use a single sql query, more efficient than active record
            $ip_ban_changes = $this->post('ip_ban_changes');
            if (count($ip_ban_changes) > 0) {
                $ip_ban_change_to = $this->post('ip_ban_change_to');
                $q = 'UPDATE UserBannedIPs SET expires = ? WHERE ';
                $v = array();
                switch ($ip_ban_change_to) {
                    case self::IP_BLACKLIST_CHANGE_MAKEPERM:
                        $v[] = 0; // expires 0 is a perma-ban
                        break;
                    case self::IP_BLACKLIST_CHANGE_REMOVE:
                        $v[] = 1; // expires 1 is really far in past, defacto expire
                        break;
                }

                $utility = new UserBannedIp();
                foreach ($ip_ban_changes as $key) {
                    $q .= '(ipFrom = ? AND ipTo = ?) OR';
                    $ids = $utility->parseUniqueID($key);
                    $v[] = $ids['ipFrom'];
                    $v[] = $ids['ipTo'];
                }
                $q = substr($q, 0, strlen($q) - 3);
                $db->execute($q, $v);
            }

            // textarea actions
            $ip_ranges = $this->parseIPBlacklistIntoRanges();
            $db = Loader::db();
            $q = 'DELETE FROM UserBannedIPs WHERE isManual = 1';
            $db->execute($q);
            // no batch insert in adodb? :(

            foreach ($ip_ranges as $ip_range) {
                $ip = new UserBannedIp();

                $ip->ipFrom = ip2long($ip_range['ipFrom']);
                $ip->ipTo = $ip_range['ipTo'];
                if ($ip->ipTo != 0) {
                    $ip->ipTo = ip2long($ip_range['ipTo']);
                }
                $ip->banCode = UserBannedIp::IP_BAN_CODE_REGISTRATION_THROTTLE;
                $ip->expires = 0;
                $ip->isManual = 1;
                try {
                    $ip->save();
                } catch (Exception $e) {
                    // silently discard duplicates
                }
            }

            $this->redirect('/dashboard/system/permissions/blacklist', 'saved');
        } else {
            $this->set('error', array(
                $this->token->getErrorMessage()
            ));
        }
    }

    public function saved()
    {
        $this->set("message", t("IP Blacklist settings saved."));
        $this->view();
    }

    public function view()
    {
        // IP Address Blacklist
        $ip_ban_enable_lock_ip_after = Config::get('concrete.security.ban.ip.enabled');
        $ip_ban_enable_lock_ip_after = ($ip_ban_enable_lock_ip_after == 1) ? 1 : 0;
        $ip_ban_lock_ip_after_attempts = Config::get('concrete.security.ban.ip.attempts');
        $ip_ban_lock_ip_after_time = Config::get('concrete.security.ban.ip.time');
        $ip_ban_lock_ip_how_long_min = Config::get('concrete.security.ban.ip.length', '');
        if (! $ip_ban_lock_ip_how_long_min) {
            $ip_ban_lock_ip_how_long_type = self::IP_BAN_LOCK_IP_HOW_LONG_TYPE_FOREVER;
        } else {
            $ip_ban_lock_ip_how_long_type = self::IP_BAN_LOCK_IP_HOW_LONG_TYPE_TIMED;
        }

        $user_banned_ip = new UserBannedIp();
        // pull all once filter various lists using code
        $user_banned_ips = $user_banned_ip->Find('1=1');
        $user_banned_manual_ips = Array();
        $user_banned_limited_ips = Array();

        foreach ($user_banned_ips as $user_banned_ip) {
            if ($user_banned_ip->isManual == 1) {
                $user_banned_manual_ips[] = $user_banned_ip->getIPRangeForDisplay();
            } else
                if ($user_banned_ip->expires - time() > 0 || $user_banned_ip->expires == 0) {
                    $user_banned_limited_ips[] = $user_banned_ip;
                }
        }
        $user_banned_manual_ips = join($user_banned_manual_ips, "\n");
        $this->set('user_banned_manual_ips', $user_banned_manual_ips);
        $this->set('user_banned_limited_ips', $user_banned_limited_ips);
        $this->set('ip_ban_enable_lock_ip_after', $ip_ban_enable_lock_ip_after);
        $this->set('ip_ban_lock_ip_after_attempts', $ip_ban_lock_ip_after_attempts);
        $this->set('ip_ban_lock_ip_after_time', $ip_ban_lock_ip_after_time);
        $this->set('ip_ban_change_makeperm', self::IP_BLACKLIST_CHANGE_MAKEPERM);
        $this->set('ip_ban_change_remove', self::IP_BLACKLIST_CHANGE_REMOVE);

        $this->set('ip_ban_lock_ip_how_long_type', $ip_ban_lock_ip_how_long_type);
        $this->set('ip_ban_lock_ip_how_long_type', $ip_ban_lock_ip_how_long_type);
        $this->set('ip_ban_lock_ip_how_long_type_forever', self::IP_BAN_LOCK_IP_HOW_LONG_TYPE_FOREVER);
        $this->set('ip_ban_lock_ip_how_long_type_timed', self::IP_BAN_LOCK_IP_HOW_LONG_TYPE_TIMED);
        $this->set('ip_ban_lock_ip_how_long_min', $ip_ban_lock_ip_how_long_min);

        $this->set('user_banned_ips', $user_banned_ips);
    }
}
