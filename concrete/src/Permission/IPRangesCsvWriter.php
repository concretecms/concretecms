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
     * One of the IPService::IPRANGETYPE_... constants.
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
     * @param int $type One of the IPService::IPRANGETYPE_... constants
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
     * Insert a list of IPRange instances.
     *
     * @param IPRange[]|Generator $ranges
     */
    public function insertRanges($ranges)
    {
        $this->writer->insertAll($this->projectRanges($ranges));
    }

    /**
     * Insert an IPRange instance.
     *
     * @param IPRange $range
     */
    public function insertRange(IPRange $range)
    {
        $this->writer->insertOne($this->projectRange($range));
    }

    /**
     * A generator that takes a collection of IPRange ranges and converts it to CSV rows.
     *
     * @param IPRange[]|Generator $list
     *
     * @return array[]|Generator
     */
    private function projectRanges($ranges)
    {
        foreach ($ranges as $range) {
            yield $this->projectRange($range);
        }
    }

    /**
     * Turn an IPRange instance into an array.
     *
     * @param IPRange $range
     *
     * @return string[]
     */
    private function projectRange(IPRange $range)
    {
        $result = [
            $range->getIpRange()->toString(),
            $range->getIpRange()->getComparableStartString(),
            $range->getIpRange()->getComparableEndString(),
        ];
        if ($this->type === IPService::IPRANGETYPE_BLACKLIST_AUTOMATIC) {
            $dt = $range->getExpires();
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
        if ($this->type === IPService::IPRANGETYPE_BLACKLIST_AUTOMATIC) {
            $headers[] = t('Expiration');
        }

        return $headers;
    }
}
