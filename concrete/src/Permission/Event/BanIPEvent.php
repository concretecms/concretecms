<?php
namespace Concrete\Core\Permission\Event;

use Symfony\Component\EventDispatcher\Event as AbstractEvent;
use Concrete\Core\Utility\IPAddress;
use DateTime;

class BanIPEvent extends AbstractEvent
{
    /**
     * The IP address that is going to be banned.
     *
     * @var IPAddress
     */
    protected $ipAddress;

    /**
     * The ban expiration date/time (or null if not expiration).
     *
     * @var DateTime
     */
    protected $banExpiration;

    /**
     * Should we proceed or the ban has been canceled?
     *
     * @var bool
     */
    protected $proceed = true;

    /**
     * @param IPAddress $ipAddress the IP address that is going to be banned
     * @param DateTime $banExpiration the ban expiration date/time (or null if not expiration)
     */
    public function __construct(IPAddress $ipAddress, DateTime $banExpiration = null)
    {
        $this->ipAddress = $ipAddress;
        $this->banExpiration = $banExpiration;
    }

    /**
     * Get the IP address that is going to be banned.
     *
     * @return IPAddress
     */
    public function getIPAddress()
    {
        return $this->ipAddress;
    }

    /**
     * Get the ban expiration date/time (or null if not expiration).
     *
     * @return DateTime
     */
    public function getBanExpiration()
    {
        return $this->banExpiration;
    }

    /**
     * Set the ban expiration date/time (or null if not expiration).
     *
     * @param DateTime $banExpiration
     *
     * @return static
     */
    public function setBanExpiration(DateTime $banExpiration = null)
    {
        $this->banExpiration = $banExpiration;

        return $this;
    }

    /**
     * Cancel the ban.
     */
    public function cancelBan()
    {
        $this->proceed = false;
    }

    /**
     * Should we proceed or the ban has been canceled?
     *
     * @return bool
     */
    public function proceed()
    {
        return $this->proceed;
    }
}
