<?php

namespace Concrete\Core\Permission;

use Concrete\Core\Localization\Service\Date;
use League\Csv\Writer;

class IPRangesCsvWriter
{
    /**
     * The writer we use to output.
     *
     * @var Writer
     */
    protected $writer;

    /**
     * One of the IpAccessControlService::IPRANGETYPE_... constants.
     *
     * @var int
     */
    protected $type;

    /**
     * The date localization service.
     *
     * @var Date
     */
    protected $dateHelper;

    /**
     * @param Writer $writer the writer we use to output
     * @param int $type One of the IpAccessControlService::IPRANGETYPE_... constants
     * @param Date $dateHelper the Date localization service
     */
    public function __construct(Writer $writer, $type, Date $dateHelper)
    {
        $this->writer = $writer;
        $this->type = $type;
        $this->dateHelper = $dateHelper;
    }

    /**
     * Insert a header row for this result set.
     */
    public function insertHeaders()
    {
        $this->writer->insertOne($this->getHeaders());
    }

    /**
     * Insert a list of IPRange/IpAccessControlRange instances.
     *
     * @param \Concrete\Core\Permission\IPRange[]|\Concrete\Core\Entity\Permission\IpAccessControlRange[]|\Generator $ranges
     */
    public function insertRanges($ranges)
    {
        $this->writer->insertAll($this->projectRanges($ranges));
    }

    /**
     * Insert an IPRange/IpAccessControlRange instance.
     *
     * @param \Concrete\Core\Permission\IPRange|\Concrete\Core\Entity\Permission\IpAccessControlRange $range
     */
    public function insertRange($range)
    {
        $this->writer->insertOne($this->projectRange($range));
    }

    /**
     * A generator that takes a collection of IPRange/IpAccessControlRange ranges and converts it to CSV rows.
     *
     * @param \Concrete\Core\Permission\IPRange[]|\Concrete\Core\Entity\Permission\IpAccessControlRange[]|\Generator $ranges
     *
     * @return array[]|\Generator
     */
    private function projectRanges($ranges)
    {
        foreach ($ranges as $range) {
            yield $this->projectRange($range);
        }
    }

    /**
     * Turn an IPRange/IpAccessControlRange instance into an array.
     *
     * @param \Concrete\Core\Permission\IPRange|\Concrete\Core\Entity\Permission\IpAccessControlRange $range
     *
     * @return string[]
     */
    private function projectRange($range)
    {
        $ipRange = $range->getIpRange();
        $result = [
            $ipRange->toString(),
            $ipRange->getComparableStartString(),
            $ipRange->getComparableEndString(),
        ];
        if ($this->type === IpAccessControlService::IPRANGETYPE_BLACKLIST_AUTOMATIC) {
            $dt = $range instanceof IPRange ? $range->getExpires() : $range->getExpiration();
            if ($dt === null) {
                $result[] = '';
            } else {
                $result[] = $this->dateHelper->formatCustom('c', $dt);
            }
        }

        return $result;
    }

    /**
     * Get the headers of the CSV.
     *
     * @return string[]
     */
    private function getHeaders()
    {
        $headers = [t('IP Range'), t('Start address'), t('End address')];
        if ($this->type === IpAccessControlService::IPRANGETYPE_BLACKLIST_AUTOMATIC) {
            $headers[] = t('Expiration');
        }

        return $headers;
    }
}
