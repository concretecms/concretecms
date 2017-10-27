<?php
namespace Concrete\Core\Csv\Import;

class CsvSchema
{
    /**
     * The row index of the CSV header.
     *
     * @var int
     */
    protected $rowIndex;

    /**
     * The header cells.
     *
     * @var string[]
     */
    protected $headerCells;

    /**
     * The list of missing headers.
     *
     * @var string[]
     */
    protected $missingHeaders;

    /**
     * The list of unrecognized headers.
     *
     * @var string[]
     */
    protected $unrecognizedHeaders;

    /**
     * Initialize the instance.
     *
     * @param int $rowIndex the row index of the CSV header
     * @param string[] $headerCells the list of the header cells
     * @param string[]|\Generator $staticHeaders
     * @param array $attributesMap
     */
    public function __construct($rowIndex, array $headerCells, $staticHeaders, array $attributesMap)
    {
        $this->rowIndex = (int) $rowIndex;
        $this->headerCells = $headerCells;
        $this->fieldsMap = [];
        $this->missingHeaders = [];
        $this->parseStaticHeaders($staticHeaders);
        $this->parseAttributeHeaders($attributesMap);
        $this->unrecognizedHeaders = [];
        $this->unrecognizedHeaders = array_values(array_diff_key($this->headerCells, $this->fieldsMap));
    }

    /**
     * Get the row index of the CSV header.
     *
     * @return int
     */
    public function getRowIndex()
    {
        return $this->rowIndex;
    }

    /**
     * Do some of the headers have been recognized?
     *
     * @return bool
     */
    public function someHeaderRecognized()
    {
        return !empty($this->fieldsMap);
    }

    /**
     * Get the list of missing headers.
     *
     * @return string[]
     */
    public function getMissingHeaders()
    {
        return $this->missingHeaders;
    }

    /**
     * Get the list of unrecognized headers.
     *
     * @return string[]
     */
    public function getUnrecognizedHeaders()
    {
        return $this->unrecognizedHeaders;
    }

    /**
     * @param string[]|\Generator $staticHeaders
     */
    private function parseStaticHeaders($staticHeaders)
    {
        foreach ($staticHeaders as $staticHeader) {
            $index = array_search($staticHeader, $this->headerCells, true);
            if ($index === false) {
                $this->missingHeaders[] = $staticHeader;
            } else {
                $this->fieldsMap[$index] = ['kind' => 'staticHeader', 'staticHeader' => $staticHeader];
            }
        }
    }

    /**
     * @param array $attributesMap
     */
    private function parseAttributeHeaders($attributesMap)
    {
        foreach ($attributesMap as $attributeIndex => $attributeHeaders) {
            if (is_string($attributeHeaders)) {
                $index = array_search($attributeHeaders, $this->headerCells, true);
                if ($index === false) {
                    $this->missingHeaders[] = $attributeHeaders;
                } else {
                    $this->fieldsMap[$index] = ['kind' => 'singleAttributeHeader', 'attributeIndex' => $attributeIndex];
                }
            } else {
                foreach ($attributeHeaders as $attributeHeader => $attributeSubHeader) {
                    $index = array_search($attributeHeader, $this->headerCells, true);
                    if ($index === false) {
                        $this->missingHeaders[] = $attributeHeader;
                    } else {
                        $this->fieldsMap[$index] = ['kind' => 'multipleAttributeHeader', 'attributeIndex' => $attributeIndex, 'attributeSubPart' => $attributeSubHeader];
                    }
                }
            }
        }
    }
}
