<?php

namespace Concrete\Core\Entity\Permission;

use Concrete\Core\Entity\Package;
use Concrete\Core\Logging\Channels;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Punic\Unit;

/**
 * Represent an IP Access Control Category.
 *
 * @ORM\Entity()
 * @ORM\Table(
 *     name="IpAccessControlCategories",
 *     options={
 *         "comment": "List of IP Access Control Categories"
 *     }
 * )
 */
class IpAccessControlCategory
{
    /**
     * The units of the time limit.
     * Array keys are the number of seconds, array values are the Unicode CLDR names of these numbers of seconds.
     */
    public const TIMEWINDOW_UNITS = [
        1 => 'duration/second',
        60 => 'duration/minute',
        3600 => 'duration/hour',
        86400 => 'duration/day',
        604800 => 'duration/week',
    ];

    /**
     * The IP Access Control Category identifier.
     *
     * @ORM\Column(name="iaccID", type="integer", nullable=false, options={"unsigned":true , "comment": "The IP Access Control Category identifier"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int|null NULL when the record has not been saved yet
     */
    protected $ipAccessControlCategoryID;

    /**
     * The IP Access Control Category handle.
     *
     * @ORM\Column(name="iaccHandle", type="string", length=255, nullable=false, unique=true, options={"comment": "The IP Access Control handle"})
     *
     * @var string
     */
    protected $handle;

    /**
     * The IP Access Control Category name.
     *
     * @ORM\Column(name="iaccName", type="string", length=255, nullable=false, options={"comment": "The IP Access Control name"})
     *
     * @var string
     */
    protected $name;

    /**
     * The package that defines this IP Access Control Category.
     *
     * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\Package")
     * @ORM\JoinColumn(name="iaccPackage", referencedColumnName="pkgID", nullable=true, onDelete="CASCADE")
     *
     * @var \Concrete\Core\Entity\Package|null
     */
    protected $package;

    /**
     * Is this IP Access Control Category enabled?
     *
     * @ORM\Column(name="iaccEnabled", type="boolean", nullable=false, options={"comment": "Is this IP Access Control enabled?"})
     *
     * @var bool
     */
    protected $enabled;

    /**
     * The maximum allowed events in the time window.
     *
     * @ORM\Column(name="iaccMaxEvents", type="integer", nullable=false, options={"unsigned": true, "comment": "The maximum allowed events in the time window"})
     *
     * @var int
     */
    protected $maxEvents;

    /**
     * The time window (in seconds) where the events should be counted (NULL means no limits).
     *
     * @ORM\Column(name="iaccTimeWindow", type="integer", nullable=true, options={"unsigned": true, "comment": "The time window (in seconds) where the events should be counted (NULL means no limits)"})
     *
     * @var int|null
     */
    protected $timeWindow;

    /**
     * The duration (in seconds) of the ban when the maximum number of events occur in the time window (NULL means forever).
     *
     * @ORM\Column(name="iaccBanDuration", type="integer", nullable=true, options={"unsigned": true, "comment": "The duration (in seconds) of the ban when the maximum number of events occur in the time window (NULL means forever)"})
     *
     * @var int|null
     */
    protected $banDuration;

    /**
     * Is this IP Access Control Category site-specific?
     *
     * @ORM\Column(name="iaccSiteSpecific", type="boolean", nullable=false, options={"comment": "Is this IP Access Control Category site-specific?"})
     *
     * @var bool
     */
    protected $siteSpecific;

    /**
     * The log channel handle.
     *
     * @ORM\Column(name="iaccLogChannel", type="string", length=255, nullable=false, options={"comment": "The log channel handle"})
     *
     * @var string
     */
    protected $logChannelHandle = Channels::CHANNEL_SECURITY;

    /**
     * The list of recorded events associated to this category.
     *
     * @ORM\OneToMany(targetEntity="Concrete\Core\Entity\Permission\IpAccessControlEvent", mappedBy="category")
     *
     * @var \Doctrine\Common\Collections\ArrayCollection|\Concrete\Core\Entity\Permission\IpAccessControlEvent[]
     */
    protected $events;

    /**
     * The list of defined ranges associated to this category.
     *
     * @ORM\OneToMany(targetEntity="Concrete\Core\Entity\Permission\IpAccessControlRange", mappedBy="category")
     *
     * @var \Doctrine\Common\Collections\ArrayCollection|\Concrete\Core\Entity\Permission\IpAccessControlRange[]
     */
    protected $ranges;

    /**
     * Initialize the instance.
     */
    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->ranges = new ArrayCollection();
    }

    /**
     * Get the IP Access Control Category identifier.
     *
     * @return int|null returns NULL when the record has not been saved yet
     */
    public function getIpAccessControlCategoryID()
    {
        return $this->ipAccessControlCategoryID;
    }

    /**
     * Get the IP Access Control handle.
     *
     * @return string
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * Set the IP Access Control handle.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setHandle($value)
    {
        $this->handle = (string) $value;

        return $this;
    }

    /**
     * Get the IP Access Control name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the IP Access Control display name.
     *
     * @return string
     */
    public function getDisplayName()
    {
        return tc('IpAccessControlCategory', $this->name);
    }

    /**
     * Set the IP Access Control name.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setName($value)
    {
        $this->name = (string) $value;

        return $this;
    }

    /**
     * Get the package that defines this IP Access Control Category.
     *
     * @return \Concrete\Core\Entity\Package|null
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * Set the package that defines this IP Access Control Category.
     *
     * @param \Concrete\Core\Entity\Package|null $value
     *
     * @return $this
     */
    public function setPackage(Package $value = null)
    {
        $this->package = $value;

        return $this;
    }

    /**
     * Is this IP Access Control Category enabled?
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Is this IP Access Control Category enabled?
     *
     * @param bool $value
     *
     * @return $this
     */
    public function setEnabled($value)
    {
        $this->enabled = (bool) $value;

        return $this;
    }

    /**
     * Get the maximum allowed events in the time window.
     *
     * @return int
     */
    public function getMaxEvents()
    {
        return $this->maxEvents;
    }

    /**
     * Set the maximum allowed events in the time window.
     *
     * @param int $value
     *
     * @return $this
     */
    public function setMaxEvents($value)
    {
        $this->maxEvents = (int) $value;

        return $this;
    }

    /**
     * Get the time window (in seconds) where the events should be counted (NULL means no limits).
     *
     * @return int|null
     */
    public function getTimeWindow()
    {
        return $this->timeWindow;
    }

    /**
     * Get the time window (in seconds) where the events should be counted (NULL means no limits).
     *
     * @param int|null $value
     *
     * @return $this
     */
    public function setTimeWindow($value)
    {
        $this->timeWindow = ((string) $value === '') ? null : (int) $value;

        return $this;
    }

    /**
     * Get the duration (in seconds) of the ban when the maximum number of events occur in the time window (NULL means forever).
     *
     * @return int|null
     */
    public function getBanDuration()
    {
        return $this->banDuration;
    }

    /**
     * Set the duration (in seconds) of the ban when the maximum number of events occur in the time window (NULL means forever).
     *
     * @param int|null $value
     *
     * @return $this
     */
    public function setBanDuration($value)
    {
        $this->banDuration = ((string) $value === '') ? null : (int) $value;

        return $this;
    }

    /**
     * Is this IP Access Control Category site-specific?
     *
     * @return bool
     */
    public function isSiteSpecific()
    {
        return $this->siteSpecific;
    }

    /**
     * Is this IP Access Control Category site-specific?
     *
     * @param bool $value
     *
     * @return $this
     */
    public function setSiteSpecific($value)
    {
        $this->siteSpecific = (bool) $value;

        return $this;
    }

    /**
     * Get the log channel handle (empty string if log is disabled).
     *
     * @return string
     */
    public function getLogChannelHandle()
    {
        return $this->logChannelHandle;
    }

    /**
     * Set the log channel handle (empty string if log is disabled).
     *
     * @param string $value
     *
     * @return $this
     */
    public function setLogChannelHandle($value)
    {
        $this->logChannelHandle = (string) $value;

        return $this;
    }

    /**
     * Get the list of recorded events associated to this category.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection|\Concrete\Core\Entity\Permission\IpAccessControlEvent[]
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Get the list of defined ranges associated to this category.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection|\Concrete\Core\Entity\Permission\IpAccessControlRange[]
     */
    public function getRanges()
    {
        return $this->ranges;
    }

    /**
     * Get the representation of a time window expressed in seconds.
     *
     * @return array index 0 is the number of the new units, index 1 is the Unicode CLSR name of the number of seconds in the new units (one of the keys returned by getTimeWindowUnits). If the time window is empty, you'll get NULL
     *
     * @example splitTimeWindowUnits(300) returns [5, 'duration/minute']
     */
    public static function splitTimeWindow(?int $timeWindow): ?array
    {
        if ($timeWindow === null || $timeWindow <= 0) {
            return null;
        }
        $seconds = array_map('intval', array_keys(static::TIMEWINDOW_UNITS));
        rsort($seconds, SORT_NUMERIC);
        $foundSeconds = 1;
        foreach ($seconds as $s) {
            if (($timeWindow % $s) === 0) {
                $foundSeconds = $s;
                break;
            }
        }

        return [
            (int) ($timeWindow / $foundSeconds),
            static::TIMEWINDOW_UNITS[$foundSeconds],
        ];
    }

    public function describeTimeWindow(): string
    {
        $splittedTimeWindow = static::splitTimeWindow($this->getTimeWindow());
        if ($splittedTimeWindow === null) {
            return t2(
                /* i18n: %s is a number */
                '%s event', '%s events',
                $this->getMaxEvents()
            );
        }
        [$value, $unit] = $splittedTimeWindow;

        return t2(
            /* i18n: %1$s is a number; %2$s is a duration (like for example 2 minutes) */
            '%1$s event in %2$s', '%1$s events in %2$s',
            $this->getMaxEvents(),
            Unit::format($value, $unit, 'long')
        );
    }
}
