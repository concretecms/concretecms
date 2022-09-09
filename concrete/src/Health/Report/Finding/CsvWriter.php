<?php
namespace Concrete\Core\Health\Report\Finding;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\Health\Report\Result;
use League\Csv\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvWriter
{

    /**
     * @var Writer
     */
    protected $writer;

    /**
     * @var Repository
     */
    protected $config;

    public function __construct(Writer $writer, Repository $config)
    {
        $this->writer = $writer;
        $this->config = $config;
    }

    public function outputResultFindings(Result $result): StreamedResponse
    {
        $bom = $this->config->get('concrete.export.csv.include_bom') ? $this->config->get('concrete.charset_bom') : '';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . snake_case($result->getName()) . '.csv',
        ];
        return new StreamedResponse(function() use ($bom, $result) {
            $this->writer->setOutputBOM($bom);
            $exporter = $result->getFormatter()->getExporter();
            $columns = $exporter->getColumns();
            $headers = [];
            foreach ($columns as $column) {
                $headers[] = $column->getDisplayName();
            }
            $this->writer->insertOne($headers);
            foreach ($result->getWeightedFindings() as $finding) {
                $content = [];
                foreach ($columns as $column) {
                    $content[] = $exporter->getColumnValue($column, $finding);
                }
                $this->writer->insertOne($content);
            }
        }, 200, $headers);
    }


}
