<?php

namespace Concrete\Core\Permission;

use Concrete\Core\Entity\Permission\IpAccessControlCategory;
use Concrete\Core\Entity\Permission\IpAccessControlEvent;
use Concrete\Core\Entity\Permission\IpAccessControlRange;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;
use DateTime;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use IPLib\Address\AddressInterface;
use IPLib\Factory as IPFactory;
use IPLib\Range\RangeInterface;

class IpAccessControlService implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * Bit mask for blacklist ranges.
     *
     * @var int
     */
    const IPRANGEFLAG_BLACKLIST = 0x0001;

    /**
     * Bit mask for whitelist ranges.
     *
     * @var int
     */
    const IPRANGEFLAG_WHITELIST = 0x0002;

    /**
     * Bit mask for manually generated ranges.
     *
     * @var int
     */
    const IPRANGEFLAG_MANUAL = 0x0010;

    /**
     * Bit mask for automatically generated ranges.
     *
     * @var int
     */
    const IPRANGEFLAG_AUTOMATIC = 0x0020;

    /**
     * IP range type: manually added to the blacklist.
     *
     * @var int
     */
    const IPRANGETYPE_BLACKLIST_MANUAL = 0x0011; // IPRANGEFLAG_BLACKLIST | IPRANGEFLAG_MANUAL

    /**
     * IP range type: automatically added to the blacklist.
     *
     * @var int
     */
    const IPRANGETYPE_BLACKLIST_AUTOMATIC = 0x0021; // IPRANGEFLAG_BLACKLIST | IPRANGEFLAG_AUTOMATIC

    /**
     * IP range type: manually added to the whitelist.
     *
     * @var int
     */
    const IPRANGETYPE_WHITELIST_MANUAL = 0x0012; // IPRANGEFLAG_WHITELIST | IPRANGEFLAG_MANUAL

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
     * Check if an IP address is blacklisted.
     *
     * @param \IPLib\Address\AddressInterface|null $ipAddress
     *
     * @return bool
     */
    public function isBlacklisted(AddressInterface $ipAddress = null)
    {
        $range = $this->getRange($ipAddress);

        return $range !== null && ($range->getType() & self::IPRANGEFLAG_BLACKLIST);
    }

    /**
     * Check if an IP address is whitelisted.
     *
     * @param \IPLib\Address\AddressInterface|null $ipAddress
     *
     * @return bool
     */
    public function isWhitelisted(AddressInterface $ipAddress = null)
    {
        $range = $this->getRange($ipAddress);

        return $range !== null && ($range->getType() & self::IPRANGEFLAG_WHITELIST);
    }

    /**
     * Create and save an IP Access Control Event.
     *
     * @param \IPLib\Address\AddressInterface|null $ipAddress
     * @param bool $evenIfDisabled
     *
     * @return \Concrete\Core\Entity\Permission\IpAccessControlEvent|null
     */
    public function registerEvent(AddressInterface $ipAddress = null, $evenIfDisabled = false)
    {
        if (!$evenIfDisabled && !$this->getCategory()->isEnabled()) {
            return null;
        }
        $event = new IpAccessControlEvent();
        $event
            ->setCategory($this->getCategory())
            ->setSite($this->site)
            ->setIpAddress($ipAddress ?: $this->defaultIpAddress)
            ->setDateTime(new DateTime('now'))
        ;
        $this->em->persist($event);
        $this->em->flush($event);

        return $event;
    }

    /**
     * Check if the IP address has reached the threshold.
     *
     * @param \IPLib\Address\AddressInterface $ipAddress
     * @param bool $evenIfDisabled
     *
     * @return bool
     */
    public function isThresholdReached(AddressInterface $ipAddress = null, $evenIfDisabled = false)
    {
        if (!$evenIfDisabled && !$this->getCategory()->isEnabled()) {
            return false;
        }
        if ($this->isWhitelisted($ipAddress)) {
            return false;
        }
        if ($ipAddress === null) {
            $ipAddress = $this->defaultIpAddress;
        }
        $qb = $this->em->createQueryBuilder();
        $x = $qb->expr();
        $qb
            ->from(IpAccessControlEvent::class, 'e')
            ->select($x->count('e.ipAccessControlEventID'))
            ->where($x->eq('e.ip', ':ip'))
            ->andWhere($x->eq('e.category', ':category'))
            ->setParameter('ip', $ipAddress->getComparableString())
            ->setParameter('category', $this->getCategory()->getIpAccessControlCategoryID())
        ;
        if ($this->getCategory()->getTimeWindow() !== null) {
            $dateTimeLimit = new DateTime('-' . $this->getCategory()->getTimeWindow() . ' seconds');
            $qb
                ->andWhere($x->gt('e.dateTime', ':dateTimeLimit'))
                ->setParameter('dateTimeLimit', $dateTimeLimit)
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
        $numEvents = (int) $qb->getQuery()->getSingleScalarResult();

        return $numEvents >= $this->getCategory()->getMaxEvents();
    }

    /**
     * Add an IP address to the list of blacklisted IP address when too many events occur.
     *
     * @param \IPLib\Address\AddressInterface $ipAddress the IP to add to the blacklist (if null, we'll use the current IP address)
     * @param bool $evenIfDisabled if set to true, we'll add the IP address even if the IP ban system is disabled in the configuration
     *
     * @return \Concrete\Core\Entity\Permission\IpAccessControlRange|null
     */
    public function addToBlacklistForThresholdReached(AddressInterface $ipAddress = null, $evenIfDisabled = false)
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
            $banExpiration = new DateTime('+' . $this->getCategory()->getBanDuration() . ' minutes');
        }

        $range = $this->createRange(
            IPFactory::rangeFromBoundaries($ipAddress, $ipAddress),
            static::IPRANGETYPE_BLACKLIST_AUTOMATIC,
            $banExpiration
        );

        if ($this->getCategory()->getLogChannelHandle() !== '') {
            $this->logger->warning(
                t('IP address %1$s added to blacklist for the category %2$s.', $ipAddress->toString(), $this->getCategory()->getDisplayName()),
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
        $qb = $this->em->createQueryBuilder();
        $x = $qb->expr();
        $qb
            ->delete(IpAccessControlEvent::class, 'e')
            ->where($x->eq('e.category', ':category'))
            ->setParameter('category', $this->getCategory()->getIpAccessControlCategoryID())
        ;
        if ($minAge) {
            $dateTimeLimit = new DateTime('-' . ((int) $minAge) . ' seconds');
            $qb
                ->andWhere($x->lte('e.dateTime', ':dateTimeLimit'))
                ->setParameter('dateTimeLimit', $dateTimeLimit)
            ;
        }

        return (int) $qb->getQuery()->execute();
    }

    /**
     * Clear the IP addresses automatically blacklisted.
     *
     * @param bool $onlyExpired
     *
     * @return int the number of records deleted
     */
    public function deleteAutomaticBlacklist($onlyExpired = true)
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
                ->setParameter('dateTimeLimit', $dateTimeLimit)
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
            ->where($x->lte('r.ipFrom', ':ip'))
            ->andWhere($x->gte('r.ipTo', ':ip'))
            ->andWhere(
                $x->orX(
                    $x->isNull('r.expiration'),
                    $x->gt('r.expiration', ':now')
                )
            )
            ->setParameter('ip', $ipAddress->getComparableString())
            ->setParameter('now', new DateTime('now'))
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
