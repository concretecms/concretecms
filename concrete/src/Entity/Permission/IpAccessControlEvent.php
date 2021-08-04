<?php

namespace Concrete\Core\Entity\Permission;

use Concrete\Core\Entity\Site\Site;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use IPLib\Address\AddressInterface;
use IPLib\Factory;

/**
 * Represent an IP Access Control Event.
 *
 * @ORM\Entity()
 * @ORM\Table(
 *     name="IpAccessControlEvents",
 *     options={
 *         "comment": "List of IP Access Control Events"
 *     }
 * )
 */
class IpAccessControlEvent
{
    /**
     * The IP Access Control Event identifier.
     *
     * @ORM\Column(name="iaceID", type="integer", nullable=false, options={"unsigned":true , "comment": "The IP Access Control Event identifier"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int|null NULL when the record has not been saved yet
     */
    protected $ipAccessControlEventID;

    /**
     * The associated IP Access Control Category.
     *
     * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\Permission\IpAccessControlCategory", inversedBy="events")
     * @ORM\JoinColumn(name="iaceCategory", referencedColumnName="iaccID", nullable=false, onDelete="CASCADE")
     *
     * @var \Concrete\Core\Entity\Permission\IpAccessControlCategory
     */
    protected $category;

    /**
     * The Site where this event occurred (if applicable).
     *
     * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\Site\Site")
     * @ORM\JoinColumn(name="iaceSite", referencedColumnName="siteID", nullable=true, onDelete="CASCADE")
     *
     * @var \Concrete\Core\Entity\Site\Site|null
     */
    protected $site;

    /**
     * The IP address associated to this event.
     *
     * @ORM\Column(name="iaceIp", type="string", length=40, nullable=false, options={"comment": "The IP address associated to this event"})
     *
     * @var string
     */
    protected $ip;

    /**
     * The date/time when this event occurred.
     *
     * @ORM\Column(name="iaceDateTime", type="datetime", nullable=false, options={"comment": "The date/time when this event occurred"})
     *
     * @var \DateTime
     */
    protected $dateTime;

    /**
     * the IP address associated to this event.
     *
     * @var \IPLib\Address\AddressInterface|null
     */
    private $ipAddress;

    /**
     * Get the IP Access Control Event identifier.
     *
     * @return int|null returns NULL when the record has not been saved yet
     */
    public function getIpAccessControlEventID()
    {
        return $this->ipAccessControlEventID;
    }

    /**
     * Get the associated IP Access Control Category.
     *
     * @return \Concrete\Core\Entity\Permission\IpAccessControlCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set the associated IP Access Control Category.
     *
     * @param \Concrete\Core\Entity\Permission\IpAccessControlCategory $value
     *
     * @return $this
     */
    public function setCategory(IpAccessControlCategory $value)
    {
        $this->category = $value;

        return $this;
    }

    /**
     * Get the Site where this event occurred (if applicable).
     *
     * @return \Concrete\Core\Entity\Site\Site|null
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set the Site where this event occurred (if applicable).
     *
     * @param \Concrete\Core\Entity\Site\Site|null $value
     *
     * @return $this
     */
    public function setSite(Site $value = null)
    {
        $this->site = $value;

        return $this;
    }

    /**
     * Get the IP address associated to this event.
     *
     * @return \IPLib\Address\AddressInterface
     */
    public function getIpAddress()
    {
        if ($this->ipAddress === null) {
            $ip = (string) $this->getIp();
            if ($ip !== '') {
                $this->ipAddress = Factory::parseAddressString($ip);
            }
        }

        return $this->ipAddress;
    }

    /**
     * Set the IP address associated to this event.
     *
     * @param \IPLib\Address\AddressInterface $value
     *
     * @return $this
     */
    public function setIpAddress(AddressInterface $value)
    {
        $this->setIp($value->getComparableString());
        $this->ipAddress = $value;

        return $this;
    }

    /**
     * Get the date/time when this event occurred.
     *
     * @return \DateTime
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * Set the date/time when this event occurred.
     *
     * @param \DateTime $value
     *
     * @return $this
     */
    public function setDateTime(DateTime $value)
    {
        $this->dateTime = $value;

        return $this;
    }

    /**
     * Get the IP address associated to this event.
     *
     * @return string
     */
    protected function getIp()
    {
        return $this->ip;
    }

    /**
     * Set the IP address associated to this event.
     *
     * @param string $value
     *
     * @return $this
     */
    protected function setIp($value)
    {
        $this->ip = (string) $value;
        $this->ipAddress = null;

        return $this;
    }
}
