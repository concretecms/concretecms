<?php

namespace Concrete\Core\Utility;

class IPAddress {

    const FORMAT_HEX = 0;
    const FORMAT_IP_STRING = 1;

    protected $ipHex = null;

    /**
     * Builds the IPAddress object from the ip address string provided, or from a hexadecimal string
     * If no ip address is provided, it can be set later by running the setIp function
     * @param string|null $ipAddress
     * @param bool $isHex
     */
    public function __construct($ipAddress = null, $isHex = false)
    {
        if($ipAddress !== null && $ipAddress !== '') {
            $this->setIp($ipAddress, $isHex);
        }
    }

    /**
     * Sets the current IP Address
     * @param string $ipAddress
     * @param bool $isHex
     * @return $this
     */
    public function setIp($ipAddress, $isHex = false)
    {
        if($isHex) {
            $this->ipHex = $ipAddress;
        } else {
            //discard any IPv6 port
            $ipAddress = preg_replace('/\[(.*?)\].*/', '$1', $ipAddress);
            //discard any IPv4 port
            if(strpos($ipAddress, '.') !== false) {
                $ipAddress = preg_replace('/(.*?(?:\d{1,3}\.?){4}).*/', "$1", $ipAddress);
            }
            $this->ipHex = bin2hex(inet_pton($ipAddress));
        }
        return $this;
    }

    /**
     * Returns the IPAddress string, null if no ip address has been set
     * @param int $format Uses the IPAddress::FORMAT_* constants
     * @throws \Exception Throws an exception if the value is not null and no valid format constant is given
     * @return string|null
     */
    public function getIp($format = self::FORMAT_HEX)
    {
        if($this->ipHex === null) {
            return null;
        }elseif($format === self::FORMAT_HEX) {
            return $this->ipHex;
        } elseif($format === self::FORMAT_IP_STRING) {
            return inet_ntop($this->hex2bin($this->ipHex));
        }
        throw new \Exception('Invalid IP format');
    }

    /**
     * @return bool Returns true if the IP address is set
     */
    protected function isIpSet()
    {
        return ($this->ipHex !== null);
    }

    /**
     * Fallback function for those using < PHP 5.4
     * Decodes a hexadecimally encoded binary string
     * @param string $hex
     * @return string Returns the binary representation of the given data
     */
    public function hex2bin($hex)
    {
        if(!function_exists('hex2bin')){
            return pack("H*" , $hex);
        } else {
            return hex2bin($hex);
        }
    }

    /**
     * Used to check of the current IP is a loopback IP address
     * @throws \Exception if no IP is set
     * @return bool returns true for loopback IP's, returns false if it is not a loopback IP
     */
    public function isLoopBack()
    {
        if(!$this->isIpSet()) {
            throw new \Exception('No IP Set');
        }
        if($this->isIPv4() && strpos($this->ipHex, '7f') === 0) {
            return true; //IPv4 loopback 127.0.0.0/8
        } elseif ($this->ipHex === '00000000000000000000000000000001'
            || $this->ipHex === '00000000000000000000ffff7f000001'
        ) {
            return true; //IPv6 loopback ::1 or ::ffff:127.0.0.1
        }
        return false;
    }

    /**
     * Returns true if the IP address belongs to a private network, false if it is not
     * @return bool
     * @throws \Exception
     */
    public function isPrivate()
    {
        if(!$this->isIpSet()) {
            throw new \Exception('No IP Set');
        }
        if(
            ($this->isIPv4() &&
                (  strpos($this->ipHex, '0a') === 0 //10.0.0.0/8
                || strpos($this->ipHex, 'ac1') === 0 //172.16.0.0/12
                || strpos($this->ipHex, 'c0a8') === 0 //192.168.0.0/16
                )
            )
            ||
            ($this->isIPv6() &&
                (  strpos($this->ipHex, 'fc') === 0 //fc00::/7
                || strpos($this->ipHex, 'fd') === 0 //fd00::/7
                || strpos($this->ipHex, 'ffff0a') === 20 //::ffff:10.0.0.0/8
                || strpos($this->ipHex, 'ffffac1') === 20 //::ffff:172.16.0.0/12
                || strpos($this->ipHex, 'ffffc0a8') === 20 //::ffff:192.168.0.0/16
                )
            )
        ) {
            return true;
        }
        return false;
    }

    /**
     * Returns true if the IP is a Link-local address, false if it is not
     * @return bool
     * @throws \Exception
     */
    public function isLinkLocal()
    {
        if(!$this->isIpSet()) {
            throw new \Exception('No IP Set');
        }
        if(
            ($this->isIPv4() &&
                strpos($this->ipHex, 'a9fe') === 0 //169.254.0.0/16
            )
            ||
            ($this->isIPv6() &&
                (  strpos($this->ipHex, 'fe8') === 0 //fe80::/10 Link-Scope Unicast
                    || strpos($this->ipHex, 'fe9') === 0 //fe80::/10 Link-Scope Unicast
                    || strpos($this->ipHex, 'fea') === 0 //fe80::/10 Link-Scope Unicast
                    || strpos($this->ipHex, 'feb') === 0 //fe80::/10 Link-Scope Unicast
                    || strpos($this->ipHex, 'ffffa9fe') === 20 //::ffff:169.254.0.0/16
                )
            )
        ) {
            return true;
        }
        return false;
    }

    public function isIPv4()
    {
        return (strlen($this->ipHex) === 8);
    }

    public function isIPv6()
    {
        return (strlen($this->ipHex) == 32);
    }
} 