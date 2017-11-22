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
     * The map between the CSV field indexes and the object fields/attributes.
     *
     * @var array keys are the CSV field indexes, values are arrays describing the mapped object fields/attributes
     */
    protected $fieldsMap;

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
     * Get the values of the cells associated to the static headers.
     *
     * @param array $cells
     *
     * @return array keys are the static field names, values are the field values (strings)
     */
    public function getStaticValues(array $cells)
    {
        $result = [];
        foreach ($this->fieldsMap as $cellIndex => $info) {
            if ($info['kind'] === 'staticHeader') {
                $fieldName = $info['staticHeaderName'];
                $result[$fieldName] = isset($cells[$cellIndex]) ? $cells[$cellIndex] : '';
            }
        }

        return $result;
    }

    /**
     * Get the values of the cells associated to the attributes.
     *
     * @param array $cells
     *
     * @return array keys are the index of the attribute keys, values are the field values.
     * In case of single-line attributes, values are strings.
     * In case of multi-column attributes, values are arrays whose keys are the sub-headers and values the strings.
     */
    public function getAttributesValues(array $cells)
    {
        $result = [];
        foreach ($this->fieldsMap as $cellIndex => $info) {
            switch ($info['kind']) {
                case 'singleAttributeHeader':
                    $attributeIndex = $info['attributeIndex'];
                    $result[$attributeIndex] = isset($cells[$cellIndex]) ? $cells[$cellIndex] : '';
                    break;
                case 'multipleAttributeHeader':
                    $attributeIndex = $info['attributeIndex'];
                    $attributeSubHeader = $info['attributeSubHeader'];
                    $value = isset($cells[$cellIndex]) ? $cells[$cellIndex] : '';
                    if (isset($result[$attributeIndex])) {
                        $result[$attributeIndex][$attributeSubHeader] = $value;
                    } else {
                        $result[$attributeIndex] = [$attributeSubHeader => $value];
                    }
                    break;
            }
        }

        return $result;
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
                $this->fieldsMap[$index] = ['kind' => 'staticHeader', 'staticHeaderName' => $staticHeader];
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
                        $this->fieldsMap[$index] = ['kind' => 'multipleAttributeHeader', 'attributeIndex' => $attributeIndex, 'attributeSubHeader' => $attributeSubHeader];
                    }
                }
            }
        }
    }
}
