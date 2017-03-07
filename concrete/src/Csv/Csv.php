<?php
namespace Concrete\Core\Csv;

use Concrete\Core\Csv\Strategy\StrategyInterface;
use Port\Csv\CsvWriter;

/**
 * Class Csv.
 *
 * Helper class to generate and download CSV files.
 * A class implementing StrategyInterface is needed to transform any content into rows.
 */
class Csv
{
    private $writer;
    private $strategy;
    private $filename;

    /**
     * Csv constructor.
     *
     * @param CsvWriter $writer
     * @param StrategyInterface $strategy
     * @param null $filename
     */
    public function __construct(CsvWriter $writer, StrategyInterface $strategy, $filename = null)
    {
        $this->writer = $writer;
        $writer->setStream(fopen('php://output', 'w'));

        $this->strategy = $strategy;
        $this->filename = $filename ? $filename : 'data_export';
    }

    /**
     * Set correct headers for CSV and output the file.
     */
    public function generate()
    {
        header("Content-Type: text/csv");
        header("Cache-control: private");
        header("Pragma: public");
        $date = date('Ymd');
        header("Content-Disposition: attachment; filename=" . $this->filename . "_{$date}.csv");

        foreach ($this->strategy->getRows() as $row) {
            $this->writer->writeItem($row);
        }

        $this->writer->finish();
        die;
    }
}
