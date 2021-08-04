<?php

namespace Concrete\Core\Entity\Permission;

use Concrete\Core\Entity\Site\Site;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use IPLib\Factory;
use IPLib\Range\RangeInterface;

/**
 * Represent an IP Access Control Range.
 *
 * @ORM\Entity()
 * @ORM\Table(
 *     name="IpAccessControlRanges",
 *     indexes={
 *         @ORM\Index(name="IPIntervalExpiration", columns={"iacrIpFrom", "iacrIpTo", "iacrExpiration"})
 *     },
 *     options={
 *         "comment": "List of IP Access Control Ranges"
 *     }
 * )
 */
class IpAccessControlRange
{
    /**
     * The IP Access Control Range identifier.
     *
     * @ORM\Column(name="iacrID", type="integer", nullable=false, options={"unsigned":true , "comment": "The IP Access Control Range identifier"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int|null NULL when the record has not been saved yet
     */
    protected $ipAccessControlRangeID;

    /**
     * The associated IP Access Control Category.
     *
     * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\Permission\IpAccessControlCategory", inversedBy="ranges")
     * @ORM\JoinColumn(name="iacrCategory", referencedColumnName="iaccID", nullable=false, onDelete="CASCADE")
     *
     * @var \Concrete\Core\Entity\Permission\IpAccessControlCategory
     */
    protected $category;

    /**
     * The Site where this range is defined occurred (if applicable).
     *
     * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\Site\Site")
     * @ORM\JoinColumn(name="iacrSite", referencedColumnName="siteID", nullable=true, onDelete="CASCADE")
     *
     * @var \Concrete\Core\Entity\Site\Site|null
     */
    protected $site;

    /**
     * The initial IP address of the range.
     *
     * @ORM\Column(name="iacrIpFrom", type="string", length=40, nullable=false, options={"comment": "The initial IP address of the range"})
     *
     * @var string
     */
    protected $ipFrom;

    /**
     * The final IP address of the range.
     *
     * @ORM\Column(name="iacrIpTo", type="string", length=40, nullable=false, options={"comment": "The final IP address of the range"})
     *
     * @var string
     */
    protected $ipTo;

    /**
     * The type of this range.
     *
     * @ORM\Column(name="iacrType", type="integer", nullable=false, options={"unsigned": true, "comment": "The type of this range"})
     *
     * @var int
     */
    protected $type;

    /**
     * The date/time when this range expires (NULL means no expiration).
     *
     * @ORM\Column(name="iacrExpiration", type="datetime", nullable=true, options={"comment": "The date/time when this range expires (NULL means no expiration)"})
     *
     * @var \DateTime|null
     */
    protected $expiration;

    /**
     * The RangeInterface instance associated to this range.
     *
     * @var \IPLib\Range\RangeInterface|null
     */
    private $ipRange;

    /**
     * Get the IP Access Control Range identifier.
     *
     * @return int|null returns NULL when the record has not been saved yet
     */
    public function getIpAccessControlRangeID()
    {
        return $this->ipAccessControlRangeID;
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
     * Get the Site where this range is defined occurred (if applicable).
     *
     * @return \Concrete\Core\Entity\Site\Site|null
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
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
     * Get the RangeInterface instance associated to this range.
     *
     * @return \IPLib\Range\RangeInterface
     */
    public function getIpRange()
    {
        if ($this->ipRange === null) {
            $ipFrom = (string) $this->getIpFrom();
            if ($ipFrom !== '') {
                $ipTo = (string) $this->getIpTo();
                if ($ipTo !== '') {
                    $this->ipRange = Factory::getRangeFromBoundaries($ipFrom, $ipTo);
                }
            }
        }

        return $this->ipRange;
    }

    /**
     * Set the RangeInterface instance associated to this range.
     *
     * @param \IPLib\Address\AddressInterface $value
     *
     * @return $this
     */
    public function setIpRange(RangeInterface $value)
    {
        $this->setIpFrom($value->getComparableStartString());
        $this->setIpTo($value->getComparableEndString());
        $this->ipRange = $value;

        return $this;
    }

    /**
     * Get the type of this range.
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the type of this range.
     *
     * @param int $value
     *
     * @return $this
     */
    public function setType($value)
    {
        $this->type = (int) $value;

        return $this;
    }

    /**
     * Get the date/time when this range expires (NULL means no expiration).
     *
     * @return \DateTime|null
     */
    public function getExpiration()
    {
        return $this->expiration;
    }

    /**
     * Set the date/time when this range expires (NULL means no expiration).
     *
     * @param \DateTime| $value
     *
     * @return $this
     */
    public function setExpiration(DateTime $value = null)
    {
        $this->expiration = $value;

        return $this;
    }

    /**
     * Get the initial IP address of the range.
     *
     * @return string
     */
    protected function getIpFrom()
    {
        return $this->ipFrom;
    }

    /**
     * Set the initial IP address of the range.
     *
     * @param string $value
     *
     * @return $this
     */
    protected function setIpFrom($value)
    {
        $this->ipFrom = (string) $value;

        return $this;
    }

    /**
     * Get the final IP address of the range.
     *
     * @return string
     */
    protected function getIpTo()
    {
        return $this->ipTo;
    }

    /**
     * Set the final IP address of the range.
     *
     * @param string $value
     *
     * @return $this
     */
    protected function setIpTo($value)
    {
        $this->ipTo = (string) $value;

        return $this;
    }
}
