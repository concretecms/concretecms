<?php
namespace Concrete\Core\User;

/**
 * @deprecated
 */
class UserBannedIp
{
    protected $unique_keys;

    public function __construct($db_name = false, $keys = false)
    {
        if (!$keys) {
            $keys = ['ipFrom', 'ipTo'];
        }
        $this->unique_keys = $keys;
    }

    public function getUniqueID()
    {
        $id = '';
        foreach ($this->unique_keys as $key) {
            $id .= $this->$key . '-';
        }
        $id = substr($id, 0, strlen($id) - 1);

        return $id;
    }

    public function parseUniqueID($id)
    {
        $ids = preg_split('{-}', $id, null, PREG_SPLIT_NO_EMPTY);
        $info = [];
        for ($i = 0; $i < count($ids); ++$i) {
            $info[$this->unique_keys[$i]] = $ids[$i];
        }

        return $info;
    }

    public function getIPRangeForDisplay()
    {
        if ($this->ipTo == 0) {
            return long2ip($this->ipFrom);
        } else {
            $to = preg_split('{\.}', long2ip($this->ipTo));
            $from = preg_split('{\.}', long2ip($this->ipFrom));
            $ip = '';
            for ($i = 0; $i < 4; ++$i) {
                $ip = $ip . (($to[$i] == $from[$i]) ? $to[$i] : '*');
                $ip .= '.';
            }
            $ip = substr($ip, 0, strlen($ip) - 1);

            return $ip;
        }
    }

    public function Find($where)
    {
        return [];
    }

    public function save()
    {
    }

    const IP_BAN_CODE_REGISTRATION_THROTTLE = 1;

    public function getCodeText($code)
    {
        switch ($code) {
            case self::IP_BAN_CODE_REGISTRATION_THROTTLE:
                return t('Failed Registration');
            default:
                return 'Unknown Error Code';
        }
    }

    public function getReason()
    {
        return $this->getCodeText($this->banCode);
    }
}
