<?php

namespace Concrete\Core\Permission;

use Concrete\Core\Entity\Permission\IpAccessControlRange;
use DateTime;
use IPLib\Factory as IPFactory;
use IPLib\Range\RangeInterface;

/**
 * @deprecated Use the methods of $app->make('failed_login')
 */
class IPRange
{
    /**
     * Record identifier.
     *
     * @var int
     */
    protected $id;

    /**
     * IP address range.
     *
     * @var RangeInterface
     */
    protected $ipRange;

    /**
     * Range type.
     *
     * @var int
     */
    protected $type;

    /**
     * @var DateTime|null
     */
    protected $expires;

    /**
     * Get the record identifier.
     *
     * @return int
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * Get the IP address range.
     *
     * @return \IPLib\Range\RangeInterface
     */
    public function getIpRange()
    {
        return $this->ipRange;
    }

    /**
     * Get the range type.
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return DateTime|null
     */
    public function getExpires()
    {
        return $this->expires;
    }

    /**
     * @param array $row
     * @param mixed $dateTimeFormat
     *
     * @return static
     */
    public static function createFromRow(array $row, $dateTimeFormat)
    {
        $result = new static();
        $result->id = empty($row['lcirID']) ? null : (int) $row['lcirID'];
        $result->ipRange = IPFactory::getRangeFromBoundaries($row['lcirIpFrom'], $row['lcirIpTo']);
        $result->type = (int) $row['lcirType'];
        $result->expires = empty($row['lcirExpires']) ? null : DateTime::createFromFormat($dateTimeFormat, $row['lcirExpires']);

        return $result;
    }

    /**
     * @param \Concrete\Core\Entity\Permission\IpAccessControlRange $range
     *
     * @return static
     */
    public static function createFromEntity(IpAccessControlRange $range)
    {
        $result = new static();
        $result->id = $range->getIpAccessControlRangeID();
        $result->ipRange = $range->getIpRange();
        $result->type = $range->getType();
        $result->expires = $range->getExpiration();

        return $result;
    }
}
