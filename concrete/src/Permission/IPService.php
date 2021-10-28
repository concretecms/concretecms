<?php

namespace Concrete\Core\Permission;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Http\Request;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;
use Concrete\Core\Utility\IPAddress;
use DateTime;
use IPLib\Address\AddressInterface;
use IPLib\Factory as IPFactory;
use IPLib\Range\RangeInterface;

/**
 * @deprecated check single methods to see the non-deprecated alternatives
 */
class IPService implements ApplicationAwareInterface, LoggerAwareInterface
{
    use ApplicationAwareTrait;
    use LoggerAwareTrait;

    /**
     * @deprecated Use \Concrete\Core\Permission\IpAccessControlService::IPRANGEFLAG_BLACKLIST
     *
     * @var int
     */
    const IPRANGEFLAG_BLACKLIST = IpAccessControlService::IPRANGEFLAG_BLACKLIST;

    /**
     * @deprecated Use \Concrete\Core\Permission\IpAccessControlService::IPRANGEFLAG_WHITELIST
     *
     * @var int
     */
    const IPRANGEFLAG_WHITELIST = IpAccessControlService::IPRANGEFLAG_WHITELIST;

    /**
     * @deprecated Use \Concrete\Core\Permission\IpAccessControlService::IPRANGEFLAG_MANUAL
     *
     * @var int
     */
    const IPRANGEFLAG_MANUAL = IpAccessControlService::IPRANGEFLAG_MANUAL;

    /**
     * @deprecated Use \Concrete\Core\Permission\IpAccessControlService::IPRANGEFLAG_AUTOMATIC
     *
     * @var int
     */
    const IPRANGEFLAG_AUTOMATIC = IpAccessControlService::IPRANGEFLAG_AUTOMATIC;

    /**
     * @deprecated Use \Concrete\Core\Permission\IpAccessControlService::IPRANGETYPE_BLACKLIST_MANUAL
     *
     * @var int
     */
    const IPRANGETYPE_BLACKLIST_MANUAL = IpAccessControlService::IPRANGETYPE_BLACKLIST_MANUAL;

    /**
     * @deprecated Use \Concrete\Core\Permission\IpAccessControlService::IPRANGETYPE_BLACKLIST_AUTOMATIC
     *
     * @var int
     */
    const IPRANGETYPE_BLACKLIST_AUTOMATIC = IpAccessControlService::IPRANGETYPE_BLACKLIST_AUTOMATIC;

    /**
     * @deprecated Use \Concrete\Core\Permission\IpAccessControlService::IPRANGETYPE_WHITELIST_MANUAL
     *
     * @var int
     */
    const IPRANGETYPE_WHITELIST_MANUAL = IpAccessControlService::IPRANGETYPE_WHITELIST_MANUAL;

    /**
     * @var \Concrete\Core\Config\Repository\Repository
     */
    protected $config;

    /**
     * @var \Concrete\Core\Database\Connection\Connection
     */
    protected $connection;

    /**
     * @var \Concrete\Core\Http\Request
     */
    protected $request;

    /**
     * @param \Concrete\Core\Config\Repository\Repository $config
     * @param \Concrete\Core\Database\Connection\Connection $connection
     * @param \Concrete\Core\Http\Request $request
     */
    public function __construct(Repository $config, Connection $connection, Request $request)
    {
        $this->config = $config;
        $this->connection = $connection;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Logging\LoggerAwareInterface::getLoggerChannel()
     */
    public function getLoggerChannel()
    {
        return Channels::CHANNEL_SECURITY;
    }

    /**
     * @deprecated Use $app->make(\IPLib\Address\AddressInterface::class)
     *
     * @return \IPLib\Address\AddressInterface
     */
    public function getRequestIPAddress()
    {
        return $this->app->make(AddressInterface::class);
    }

    /**
     * @deprecated use $app->make('failed_login')->isDenylisted()
     *
     * @param \IPLib\Address\AddressInterface|null $ip
     *
     * @return bool
     */
    public function isDenylisted(AddressInterface $ip = null)
    {
        return $this->getFailedLoginService()->isDenylisted($ip);
    }

    /**
     * @deprecated use $app->make('failed_login')->isBlacklisted()
     *
     * @param \IPLib\Address\AddressInterface|null $ip
     *
     * @return bool
     */
    public function isBlacklisted(AddressInterface $ip = null)
    {
        return $this->getFailedLoginService()->isBlacklisted($ip);
    }

    /**
     * @deprecated use $app->make('failed_login')->isAllowlisted()
     *
     * @param \IPLib\Address\AddressInterface|null $ip
     *
     * @return bool
     */
    public function isAllowlisted(AddressInterface $ip = null)
    {
        return $this->getFailedLoginService()->isAllowlisted($ip);
    }

    /**
     * @deprecated use $app->make('failed_login')->isWhitelisted()
     *
     * @param \IPLib\Address\AddressInterface|null $ip
     *
     * @return bool
     */
    public function isWhitelisted(AddressInterface $ip = null)
    {
        return $this->getFailedLoginService()->isWhitelisted($ip);
    }

    /**
     * @deprecated use $app->make('failed_login')->getErrorMessage()
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->getFailedLoginService()->getErrorMessage();
    }

    /**
     * @deprecated use $app->make('failed_login')->registerEvent()
     *
     * @param \IPLib\Address\AddressInterface|null $ip
     * @param bool $ignoreConfig
     */
    public function logFailedLogin(AddressInterface $ip = null, $ignoreConfig = false)
    {
        return $this->getFailedLoginService()->registerEvent($ip, $ignoreConfig);
    }

    /**
     * @deprecated use $app->make('failed_login')->isThresholdReached()
     *
     * @param \IPLib\Address\AddressInterface|null $ip
     * @param bool $ignoreConfig
     *
     * @return bool
     */
    public function failedLoginsThresholdReached(AddressInterface $ip = null, $ignoreConfig = false)
    {
        return $this->getFailedLoginService()->isThresholdReached($ip, $ignoreConfig);
    }

    /**
     * @deprecated use $app->make('failed_login')->addToDenylistForThresholdReached()
     *
     * @param \IPLib\Address\AddressInterface $ip
     * @param bool $ignoreConfig
     */
    public function addToDenylistForThresholdReached(AddressInterface $ip = null, $ignoreConfig = false)
    {
        $this->getFailedLoginService()->addToDenylistForThresholdReached($ip, $ignoreConfig);
    }

    /**
     * @deprecated use $app->make('failed_login')->createRange()
     *
     * @param \IPLib\Range\RangeInterface $range
     * @param int $type
     * @param \DateTime|null $expiration
     *
     * @return \Concrete\Core\Permission\IPRange
     */
    public function createRange(RangeInterface $range, $type, DateTime $expiration = null)
    {
        $rangeEntity = $this->getFailedLoginService()->createRange($range, $type, $expiration);

        return IPRange::createFromEntity($rangeEntity);
    }

    /**
     * @deprecated use $app->make('failed_login')->getRanges()
     *
     * @param int $type (one of the IPService::IPRANGETYPE_... constants)
     * @param bool $includeExpired Include expired records?
     *
     * @return \Concrete\Core\Permission\IPRange[]|\Generator
     */
    public function getRanges($type, $includeExpired = false)
    {
        $rangeEntities = $this->getFailedLoginService()->getRanges($type, $includeExpired);
        foreach ($rangeEntities as $rangeEntity) {
            yield IPRange::createFromEntity($rangeEntity);
        }
    }

    /**
     * @deprecated use $app->make('failed_login')->getRangeByID()
     *
     * @param int $id
     *
     * @return \Concrete\Core\Permission\IPRange|null
     */
    public function getRangeByID($id)
    {
        $rangeEntity = $this->getFailedLoginService()->getRangeByID($id);

        return $rangeEntity === null ? null : IPRange::createFromEntity($rangeEntity);
    }

    /**
     * @deprecated use $app->make('failed_login')->deleteRange()
     *
     * @param \Concrete\Core\Permission\IPRange|int $range
     */
    public function deleteRange($range)
    {
        if (!$range) {
            return;
        }
        $id = $range instanceof IPRange ? $range->getID() : $range;
        $this->getFailedLoginService()->deleteRange($id);
    }

    /**
     * @deprecated use $app->make('failed_login')->deleteEvents()
     *
     * @param int|null $maxAge
     *
     * @return int
     */
    public function deleteFailedLoginAttempts($maxAge = null)
    {
        return $this->getFailedLoginService()->deleteEvents($maxAge);
    }

    /**
     * @deprecated use $app->make('failed_login')->deleteAutomaticDenylist()
     *
     * @param bool $onlyExpired
     *
     * @return int
     */
    public function deleteAutomaticDenylist($onlyExpired = true)
    {
        return $this->getFailedLoginService()->deleteAutomaticDenylist($onlyExpired);
    }

    /**
     * @deprecated Use $app->make(\IPLib\Address\AddressInterface::class)
     *
     * @return \Concrete\Core\Utility\IPAddress
     */
    public function getRequestIP()
    {
        $ip = $this->app->make(AddressInterface::class);

        return new IPAddress((string) $ip);
    }

    /**
     * @deprecated use $app->make('failed_login')->isDenylisted()
     *
     * @param mixed $ip
     */
    public function isBanned($ip = false)
    {
        $ipAddress = null;
        if ($ip instanceof IPAddress) {
            $ipAddress = IPFactory::parseAddressString($ip->getIp(IPAddress::FORMAT_IP_STRING));
        }

        return $this->getFailedLoginService()->isDenylisted($ipAddress);
    }

    /**
     * @deprecated use $app->make('failed_login')->addToDenylistForThresholdReached()
     *
     * @param mixed $ip
     * @param mixed $ignoreConfig
     */
    public function createIPBan($ip = false, $ignoreConfig = false)
    {
        $ipAddress = null;
        if ($ip instanceof IPAddress) {
            $ipAddress = IPFactory::parseAddressString($ip->getIp(IPAddress::FORMAT_IP_STRING));
        }
        $this->getFailedLoginService()->addToDenylistForThresholdReached($ipAddress, $ignoreConfig);
    }

    /**
     * @deprecated use $app->make('failed_login')->registerEvent()
     *
     * @param bool $ignoreConfig
     */
    public function logSignupRequest($ignoreConfig = false)
    {
        return $this->getFailedLoginService()->registerEvent(null, $ignoreConfig);
    }

    /**
     * @deprecated use $app->make('failed_login')->isThresholdReached()
     *
     * @param bool $ignoreConfig
     */
    public function signupRequestThreshholdReached($ignoreConfig = false)
    {
        return $this->getFailedLoginService()->isThresholdReached(null, $ignoreConfig);
    }

    /**
     * @deprecated use $app->make('failed_login')->isThresholdReached()
     *
     * @param bool $ignoreConfig
     */
    public function signupRequestThresholdReached($ignoreConfig = false)
    {
        return $this->getFailedLoginService()->isThresholdReached(null, $ignoreConfig);
    }

    /**
     * @deprecated use $app->make('failed_login')->getRangeType()
     *
     * @param \IPLib\Address\AddressInterface $ip
     *
     * @return int|null
     */
    protected function getRangeType(AddressInterface $ip)
    {
        $range = $this->getFailedLoginService()->getRange($ip);

        return $range === null ? null : $range->getType();
    }

    /**
     * @return \Concrete\Core\Permission\IpAccessControlService
     */
    private function getFailedLoginService()
    {
        return $this->app->make('failed_login');
    }
}
