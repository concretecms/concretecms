<?php
namespace Concrete\Core\Health\Report\Finding;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Csv\WriterFactory;
use Concrete\Core\Entity\Health\Report\Result;
use League\Csv\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvWriter
{

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var WriterFactory
     */
    protected $writerFactory;

    public function __construct(WriterFactory $writerFactory, Repository $config)
    {
        $this->writerFactory = $writerFactory;
        $this->config = $config;
    }

    public function populateWriter(Writer $writer, Result $result): Writer
    {
        $bom = $this->config->get('concrete.export.csv.include_bom') ? $this->config->get('concrete.charset_bom') : '';
        $writer->setOutputBOM($bom);
        $exporter = $result->getFormatter()->getExporter();
        $columns = $exporter->getColumns();
        $headers = [];
        foreach ($columns as $column) {
            $headers[] = $column->getDisplayName();
        }
        $writer->insertOne($headers);
        foreach ($result->getWeightedFindings() as $finding) {
            $content = [];
            foreach ($columns as $column) {
                $content[] = $exporter->getColumnValue($column, $finding);
            }
            $writer->insertOne($content);
        }
        return $writer;
    }

    public function getFilenameForResult(Result $result): string
    {
        return snake_case($result->getName()) . '.csv';
    }

    public function outputResultFindings(Result $result): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $this->getFilenameForResult($result) . '.csv',
        ];
        return new StreamedResponse(function() use ($result) {
            $writer = $this->writerFactory->createFromPath('php://output');
            $this->populateWriter($writer, $result);
        }, 200, $headers);
    }


}
