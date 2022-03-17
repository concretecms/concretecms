<?php

namespace Concrete\Core\Permission;

use Concrete\Core\Entity\Permission\IpAccessControlCategory;
use Concrete\Core\Entity\Permission\IpAccessControlEvent;
use Concrete\Core\Entity\Permission\IpAccessControlRange;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;
use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use IPLib\Address\AddressInterface;
use IPLib\Factory as IPFactory;
use IPLib\Range\RangeInterface;

class IpAccessControlService implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * Bit mask for denylist ranges.
     *
     * @var int
     */
    public const IPRANGEFLAG_BLACKLIST = 0x0001;

    /**
     * Bit mask for allowlist ranges.
     *
     * @var int
     */
    public const IPRANGEFLAG_WHITELIST = 0x0002;

    /**
     * Bit mask for manually generated ranges.
     *
     * @var int
     */
    public const IPRANGEFLAG_MANUAL = 0x0010;

    /**
     * Bit mask for automatically generated ranges.
     *
     * @var int
     */
    public const IPRANGEFLAG_AUTOMATIC = 0x0020;

    /**
     * IP range type: manually added to the denylist.
     *
     * @var int
     */
    public const IPRANGETYPE_BLACKLIST_MANUAL = self::IPRANGEFLAG_BLACKLIST | self::IPRANGEFLAG_MANUAL;

    /**
     * IP range type: automatically added to the denylist.
     *
     * @var int
     */
    public const IPRANGETYPE_BLACKLIST_AUTOMATIC = self::IPRANGEFLAG_BLACKLIST | self::IPRANGEFLAG_AUTOMATIC;

    /**
     * IP range type: manually added to the allowlist.
     *
     * @var int
     */
    public const IPRANGETYPE_WHITELIST_MANUAL = self::IPRANGEFLAG_WHITELIST | self::IPRANGEFLAG_MANUAL;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Concrete\Core\Entity\Site\Site
     */
    protected $site;

    /**
     * The IP Access Control Category.
     *
     * @var \Concrete\Core\Entity\Permission\IpAccessControlCategory
     */
    protected $category;

    /**
     * @var \IPLib\Address\AddressInterface
     */
    protected $defaultIpAddress;

    /**
     * Initialize the instance.
     *
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Concrete\Core\Entity\Site\Site $site
     * @param \Concrete\Core\Entity\Permission\IpAccessControlCategory $category
     * @param \IPLib\Address\AddressInterface $defaultIpAddress
     */
    public function __construct(EntityManagerInterface $em, Site $site, IpAccessControlCategory $category, AddressInterface $defaultIpAddress)
    {
        $this->em = $em;
        $this->site = $site;
        $this->category = $category;
        $this->defaultIpAddress = $defaultIpAddress;
    }

    /**
     * Get the IP Access Control Category.
     *
     * @return \Concrete\Core\Entity\Permission\IpAccessControlCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @deprecated
     *
     * Check if an IP address is denylisted.
     *
     * @param \IPLib\Address\AddressInterface|null $ipAddress
     *
     * @return bool
     */
    public function isBlacklisted(AddressInterface $ipAddress = null)
    {
        return $this->isDenylisted($ipAddress);
    }

    /**
     * Check if an IP address is denylisted.
     *
     * @param \IPLib\Address\AddressInterface|null $ipAddress
     *
     * @return bool
     */
    public function isDenylisted(AddressInterface $ipAddress = null)
    {
        $range = $this->getRange($ipAddress);

        return $range !== null && ($range->getType() & self::IPRANGEFLAG_BLACKLIST);
    }

    /**
     * @deprecated
     *
     * Check if an IP address is allowlisted.
     *
     * @param \IPLib\Address\AddressInterface|null $ipAddress
     *
     * @return bool
     */
    public function isWhitelisted(AddressInterface $ipAddress = null)
    {
        return $this->isAllowlisted($ipAddress);
    }

    /**
     * Check if an IP address is allowlisted.
     *
     * @param \IPLib\Address\AddressInterface|null $ipAddress
     *
     * @return bool
     */
    public function isAllowlisted(AddressInterface $ipAddress = null)
    {
        $range = $this->getRange($ipAddress);

        return $range !== null && ($range->getType() & self::IPRANGEFLAG_WHITELIST);
    }

    /**
     * Create and save an IP Access Control Event.
     *
     * @param \IPLib\Address\AddressInterface|null $ipAddress the IP address to be recorded (if NULL we'll use the current one)
     * @param bool $evenIfDisabled create the event even if the category is disabled?
     *
     * @return \Concrete\Core\Entity\Permission\IpAccessControlEvent|null
     */
    public function registerEvent(AddressInterface $ipAddress = null, $evenIfDisabled = false)
    {
        return $this->registerEventAt(new DateTime('now'), $ipAddress, $evenIfDisabled ? true : false);
    }

    /**
     * Create and save an IP Access Control Event at a specific date/time.
     *
     * @param \DateTime $dateTime the date/time of the event
     * @param \IPLib\Address\AddressInterface|null $ipAddress the IP address to be recorded (if NULL we'll use the current one)
     * @param bool $evenIfDisabled create the event even if the category is disabled?
     *
     * @return \Concrete\Core\Entity\Permission\IpAccessControlEvent|null return NULL if the category is disabled and $evenIfDisabled is false, the created event otherwise
     */
    public function registerEventAt(DateTime $dateTime, ?AddressInterface $ipAddress = null, bool $evenIfDisabled = false): ?IpAccessControlEvent
    {
        if (!$evenIfDisabled && !$this->getCategory()->isEnabled()) {
            return null;
        }
        $event = new IpAccessControlEvent();
        $event
            ->setCategory($this->getCategory())
            ->setSite($this->site)
            ->setIpAddress($ipAddress ?: $this->defaultIpAddress)
            ->setDateTime($dateTime)
        ;
        $this->em->persist($event);
        $this->em->flush();

        return $event;
    }

    /**
     * Get the number of events registered (in the time window if it's not null, or any events if null).
     *
     * @param \IPLib\Address\AddressInterface|null $ipAddress the IP address to be checked (if null we'll use the current IP address)
     */
    public function getEventsCount(?AddressInterface $ipAddress = null): int
    {
        if ($ipAddress === null) {
            $ipAddress = $this->defaultIpAddress;
        }
        $qb = $this->em->createQueryBuilder();
        $x = $qb->expr();
        $qb
            ->from(IpAccessControlEvent::class, 'e')
            ->select($x->count('e.ipAccessControlEventID'))
            ->andWhere($x->eq('e.ip', ':ip'))
            ->andWhere($x->eq('e.category', ':category'))
            ->setParameter('ip', $ipAddress->getComparableString())
            ->setParameter('category', $this->getCategory()->getIpAccessControlCategoryID())
        ;
        if ($this->getCategory()->getTimeWindow() !== null) {
            $dateTimeLimit = new DateTime('-' . $this->getCategory()->getTimeWindow() . ' seconds');
            $qb
                ->andWhere($x->gt('e.dateTime', ':dateTimeLimit'))
                ->setParameter('dateTimeLimit', $dateTimeLimit->format($this->em->getConnection()->getDatabasePlatform()->getDateTimeFormatString()))
            ;
        }
        if ($this->getCategory()->isSiteSpecific()) {
            $qb
                ->andWhere(
                    $x->orX(
                        $x->isNull('e.site'),
                        $x->eq('e.site', ':site')
                    )
                )
                ->setParameter('site', $this->site->getSiteID())
            ;
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Get the date/time of the last registered events (in the time window if it's not null, or any events if null).
     *
     * @param \IPLib\Address\AddressInterface|null $ipAddress the IP address to be checked (if null we'll use the current IP address)
     */
    public function getLastEvent(?AddressInterface $ipAddress = null): ?DateTimeImmutable
    {
        if ($ipAddress === null) {
            $ipAddress = $this->defaultIpAddress;
        }
        $qb = $this->em->createQueryBuilder();
        $x = $qb->expr();
        $qb
            ->from(IpAccessControlEvent::class, 'e')
            ->select($x->max('e.dateTime'))
            ->andWhere($x->eq('e.ip', ':ip'))
            ->andWhere($x->eq('e.category', ':category'))
            ->setParameter('ip', $ipAddress->getComparableString())
            ->setParameter('category', $this->getCategory()->getIpAccessControlCategoryID())
        ;
        if ($this->getCategory()->getTimeWindow() !== null) {
            $dateTimeLimit = new DateTime('-' . $this->getCategory()->getTimeWindow() . ' seconds');
            $qb
                ->andWhere($x->gt('e.dateTime', ':dateTimeLimit'))
                ->setParameter('dateTimeLimit', $dateTimeLimit->format($this->em->getConnection()->getDatabasePlatform()->getDateTimeFormatString()))
            ;
        }
        if ($this->getCategory()->isSiteSpecific()) {
            $qb
                ->andWhere(
                    $x->orX(
                        $x->isNull('e.site'),
                        $x->eq('e.site', ':site')
                    )
                )
                ->setParameter('site', $this->site->getSiteID())
            ;
        }
        $sqlDateTime = $qb->getQuery()->getSingleScalarResult();
        if ($sqlDateTime === null) {
            return null;
        }

        return DateTimeImmutable::createFromFormat($this->em->getConnection()->getDatabasePlatform()->getDateTimeFormatString(), $sqlDateTime);
    }

    /**
     * Check if the IP address has reached the threshold.
     *
     * @param \IPLib\Address\AddressInterface|null $ipAddress the IP address to be checked (if null we'll use the current IP address)
     * @param bool $evenIfDisabled
     *
     * @return bool
     */
    public function isThresholdReached(AddressInterface $ipAddress = null, $evenIfDisabled = false)
    {
        if (!$evenIfDisabled && !$this->getCategory()->isEnabled()) {
            return false;
        }
        if ($ipAddress === null) {
            $ipAddress = $this->defaultIpAddress;
        }
        if ($this->isAllowlisted($ipAddress)) {
            return false;
        }
        $numEvents = $this->getEventsCount($ipAddress);
        if ($numEvents < $this->getCategory()->getMaxEvents()) {
            return false;
        }
        if ($this->category->getTimeWindow() !== null) {
            return true;
        }
        $banDuration = $this->category->getBanDuration();
        if ($banDuration === null) {
            return true;
        }
        // Here:
        // - the time window is null (which means that we count all events, not only those in a specific time interval)
        // - the ban duration is not null (which means that we ban the IP only for a specific interval)
        // So, we need to check if the ban duration is already passed, and we need to reset the recorded events,
        // otherwise the ban would last forever.
        $lastEvent = $this->getLastEvent($ipAddress);
        $now = new DateTimeImmutable();
        $elapsedSeconds = $now->getTimestamp() - $lastEvent->getTimestamp();
        if ($elapsedSeconds < $banDuration) {
            return true;
        }
        $this->deleteEventsFor($ipAddress);

        return false;
    }

    /**
     * Add an IP address to the list of denylisted IP address when too many events occur.
     *
     * @param \IPLib\Address\AddressInterface|null $ipAddress the IP to add to the denylist (if null, we'll use the current IP address)
     * @param bool $evenIfDisabled if set to true, we'll add the IP address even if the IP ban system is disabled in the configuration
     *
     * @return \Concrete\Core\Entity\Permission\IpAccessControlRange|null
     */
    public function addToDenylistForThresholdReached(AddressInterface $ipAddress = null, $evenIfDisabled = false)
    {
        if (!$evenIfDisabled && !$this->getCategory()->isEnabled()) {
            return null;
        }
        if ($ipAddress === null) {
            $ipAddress = $this->defaultIpAddress;
        }
        if ($this->getCategory()->getBanDuration() === null) {
            $banExpiration = null;
        } else {
            $banExpiration = new DateTime('+' . $this->getCategory()->getBanDuration() . ' seconds');
        }

        $range = $this->createRange(
            IPFactory::getRangeFromBoundaries($ipAddress, $ipAddress),
            static::IPRANGETYPE_BLACKLIST_AUTOMATIC,
            $banExpiration
        );

        if ($this->getCategory()->getLogChannelHandle() !== '') {
            $this->logger->warning(
                t('IP address %1$s added to denylist for the category %2$s.', $ipAddress->toString(), $this->getCategory()->getDisplayName()),
                [
                    'ip_address' => $ipAddress->toString(),
                    'category' => $this->getCategory()->getHandle(),
                ]
            );
        }

        return $range;
    }

    /**
     * Add persist an IP address range type.
     *
     * @param \IPLib\Range\RangeInterface $range the IP address range to persist
     * @param int $type The range type (one of the IpAccessControlService::IPRANGETYPE_... constants)
     * @param \DateTime $expiration The optional expiration of the range type
     *
     * @return \Concrete\Core\Entity\Permission\IpAccessControlRange
     */
    public function createRange(RangeInterface $range, $type, DateTime $expiration = null)
    {
        $result = new IpAccessControlRange();
        $result
            ->setCategory($this->getCategory())
            ->setIpRange($range)
            ->setType($type)
            ->setExpiration($expiration)
            ->setSite($this->site)
        ;
        $this->em->persist($result);
        $this->em->flush($result);

        return $result;
    }

    /**
     * Get the list of currently available ranges.
     *
     * @param int $type (one of the IPService::IPRANGETYPE_... constants)
     * @param bool $includeExpired Include expired records?
     *
     * @return \Doctrine\Common\Collections\Collection|\Concrete\Core\Entity\Permission\IpAccessControlRange[]
     */
    public function getRanges($type, $includeExpired = false)
    {
        $criteria = new Criteria();
        $x = $criteria->expr();
        $criteria->andWhere($x->eq('type', (int) $type));
        if (!$includeExpired) {
            $criteria->andWhere(
                $x->orX(
                    $x->isNull('expiration'),
                    $x->gt('expiration', new DateTime('now'))
                )
            );
        }

        return $this->getCategory()->getRanges()->matching($criteria);
    }

    /**
     * Get a saved range for this category given its ID.
     *
     * @param int $id
     *
     * \Concrete\Core\Entity\Permission\IpAccessControlRange|null
     */
    public function getRangeByID($id)
    {
        if (!$id) {
            return null;
        }
        $entity = $this->em->find(IpAccessControlRange::class, ['ipAccessControlRangeID' => (int) $id]);
        if ($entity === null) {
            return null;
        }
        if ($entity->getCategory() !== $this->getCategory()) {
            return null;
        }

        return $entity;
    }

    /**
     * Delete a saved range given its instance or its ID.
     *
     * @param \Concrete\Core\Entity\Permission\IpAccessControlRange|int $range
     */
    public function deleteRange($range)
    {
        $entity = is_numeric($range) ? $this->getRangeByID($range) : $range;
        if (!($entity instanceof IpAccessControlRange) || $entity->getCategory() !== $this->getCategory()) {
            return;
        }
        $this->em->remove($entity);
        $this->em->flush($entity);
    }

    /**
     * Delete the recorded events.
     *
     * @param int|null $minAge the minimum age (in seconds) of the records (specify an empty value to delete all records)
     *
     * @return int the number of records deleted
     */
    public function deleteEvents($minAge = null)
    {
        return $this->deleteEventsFor(null, $minAge ? (int) $minAge : null);
    }

    /**
     * Delete the recorded events.
     *
     * @param \IPLib\Address\AddressInterface|null $ipAddress delete the records for this specific IP address (or for any address if NULL)
     * @param int|null $minAge the minimum age (in seconds) of the records (specify NULL delete all records)
     *
     * @return int the number of records deleted
     */
    public function deleteEventsFor(?AddressInterface $ipAddress = null, ?int $minAge = null): int
    {
        $qb = $this->em->createQueryBuilder();
        $x = $qb->expr();
        $qb
            ->delete(IpAccessControlEvent::class, 'e')
            ->andWhere($x->eq('e.category', ':category'))
            ->setParameter('category', $this->getCategory()->getIpAccessControlCategoryID())
        ;
        if ($ipAddress !== null) {
            $qb
                ->andWhere($x->eq('e.ip', ':ip'))
                ->setParameter('ip', $ipAddress->getComparableString())
            ;
        }
        if ($minAge !== null) {
            $dateTimeLimit = new DateTime("-{$minAge} seconds");
            $qb
                ->andWhere($x->lte('e.dateTime', ':dateTimeLimit'))
                ->setParameter('dateTimeLimit', $dateTimeLimit->format($this->em->getConnection()->getDatabasePlatform()->getDateTimeFormatString()))
            ;
        }

        return (int) $qb->getQuery()->execute();
    }

    /**
     * Clear the IP addresses automatically denylisted.
     *
     * @param bool $onlyExpired
     *
     * @return int the number of records deleted
     */
    public function deleteAutomaticDenylist($onlyExpired = true)
    {
        $qb = $this->em->createQueryBuilder();
        $x = $qb->expr();
        $qb
            ->delete(IpAccessControlRange::class, 'r')
            ->where($x->eq('r.category', ':category'))
            ->andWhere($x->eq('r.type', ':type'))
            ->setParameter('category', $this->getCategory()->getIpAccessControlCategoryID())
            ->setParameter('type', self::IPRANGETYPE_BLACKLIST_AUTOMATIC)
        ;
        if ($onlyExpired) {
            $dateTimeLimit = new DateTime('now');
            $qb
                ->andWhere($x->lte('r.expiration', ':dateTimeLimit'))
                ->setParameter('dateTimeLimit', $dateTimeLimit->format($this->em->getConnection()->getDatabasePlatform()->getDateTimeFormatString()))
            ;
        }

        return (int) $qb->getQuery()->execute();
    }

    /**
     * Get the (localized) message telling the users that their IP address has been banned.
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return t('Unable to complete action: your IP address has been banned. Please contact the administrator of this site for more information.');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Logging\LoggerAwareInterface::getLoggerChannel()
     */
    public function getLoggerChannel()
    {
        return $this->getCategory()->getLogChannelHandle();
    }

    /**
     * @param \IPLib\Address\AddressInterface $ipAddress
     *
     * @return \Concrete\Core\Entity\Permission\IpAccessControlRange|null
     */
    public function getRange(AddressInterface $ipAddress = null)
    {
        if ($ipAddress === null) {
            $ipAddress = $this->defaultIpAddress;
        }
        $qb = $this->em->createQueryBuilder();
        $x = $qb->expr();
        $qb
            ->from(IpAccessControlRange::class, 'r')
            ->select('r')
            ->andWhere($x->eq('r.category', ':category'))
            ->andWhere($x->lte('r.ipFrom', ':ip'))
            ->andWhere($x->gte('r.ipTo', ':ip'))
            ->andWhere(
                $x->orX(
                    $x->isNull('r.expiration'),
                    $x->gt('r.expiration', ':now')
                )
            )
            ->setParameter('category', $this->getCategory()->getIpAccessControlCategoryID())
            ->setParameter('ip', $ipAddress->getComparableString())
            ->setParameter('now', date($this->em->getConnection()->getDatabasePlatform()->getDateTimeFormatString()))
        ;
        if ($this->getCategory()->isSiteSpecific()) {
            $qb
                ->andWhere(
                    $x->orX(
                        $x->isNull('r.site'),
                        $x->eq('r.site', ':site')
                    )
                )
                ->setParameter('site', $this->site->getSiteID())
            ;
        }
        $query = $qb->getQuery();
        $result = null;
        foreach ($query->getResult() as $range) {
            $result = $range;
            if ($range->getType() & self::IPRANGEFLAG_WHITELIST) {
                break;
            }
        }

        return $result;
    }
}
