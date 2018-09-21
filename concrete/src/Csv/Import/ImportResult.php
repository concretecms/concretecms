<?php

namespace Concrete\Core\Csv\Import;

use Concrete\Core\Error\ErrorList\ErrorList;
use JsonSerializable;

class ImportResult implements JsonSerializable
{
    /**
     * The list of import errros.
     *
     * @var ErrorList
     */
    protected $errors;

    /**
     * The list of import warnings.
     *
     * @var ErrorList
     */
    protected $warnings;

    /**
     * The index of the last processed row (if any).
     *
     * @var int|null
     */
    protected $lastDataRowIndex;

    /**
     * The total number of the data rows processed.
     *
     * @var int
     */
    protected $totalDataRowsProcessed;

    /**
     * The total number successfully imported rows.
     *
     * @var int
     */
    protected $importSuccessCount;

    /**
     * The data collected during the import process.
     *
     * @var array|null
     */
    protected $dataCollected;

    /**
     * Initialize the instance.
     *
     * @param ErrorList $errors
     * @param ErrorList $warnings
     */
    public function __construct(ErrorList $errors, ErrorList $warnings)
    {
        $this->errors = $errors;
        $this->warnings = $warnings;
        $this->lastDataRowIndex = null;
        $this->totalDataRowsProcessed = 0;
        $this->importSuccessCount = 0;
        $this->dataCollected = null;
    }

    /**
     * Get the list of import errros.
     *
     * @return ErrorList
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get the list of import warnings.
     *
     * @return ErrorList
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    /**
     * Count a row as processed.
     *
     * @param int $rowIndex the index of the processed data row
     *
     * @return $this
     */
    public function countRowProcessed($rowIndex)
    {
        $this->lastDataRowIndex = (int) $rowIndex;
        ++$this->totalDataRowsProcessed;
    }

    /**
     * Get the index of the last processed row (if any).
     *
     * @return int|null
     */
    public function getLastDataRowIndex()
    {
        return $this->lastDataRowIndex;
    }

    /**
     * Get the total number of the data rows processed.
     *
     * @return int
     */
    public function getTotalDataRowsProcessed()
    {
        return $this->totalDataRowsProcessed;
    }

    /**
     * Set the total number successfully imported rows.
     *
     * @param int $value
     *
     * @return $this
     */
    public function setImportSuccessCount($value)
    {
        $this->importSuccessCount = (int) $value;

        return $this;
    }

    /**
     * Increase the total number successfully imported rows.
     *
     * @param int $count
     *
     * @return $this
     */
    public function increaseImportSuccessCount($count = 1)
    {
        $this->importSuccessCount += (int) $count;

        return $this;
    }

    /**
     * Get the total number successfully imported rows.
     *
     * @return int
     */
    public function getImportSuccessCount()
    {
        return $this->importSuccessCount;
    }

    /**
     * Add an error message to the errors/warnings list.
     *
     * @param bool $isError Is this a warning (false) or an error (true)?
     * @param string $problem the problem message
     * @param int|null $rowIndex the 0-index line index (if null: we'll assume the last row index)
     *
     * @return $this
     */
    public function addLineProblem($isError, $problem, $rowIndex = null)
    {
        $list = $isError ? $this->errors : $this->warnings;
        $rowIndex = $rowIndex === null ? $this->lastDataRowIndex : $rowIndex;
        $list->add(t('Line #%s: %s', $rowIndex + 1, $problem));

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \JsonSerializable::jsonSerialize()
     */
    public function jsonSerialize()
    {
        $jsonErrors = $this->errors->jsonSerialize();
        $jsonWarnings = $this->warnings->jsonSerialize();

        return [
            'errors' => $jsonErrors && !empty($jsonErrors['errors']) ? $jsonErrors['errors'] : [],
            'warnings' => $jsonWarnings && !empty($jsonWarnings['errors']) ? $jsonWarnings['errors'] : [],
            'warnings' => empty($jsonWarnings['errors']) ? [] : $jsonWarnings['errors'],
            'lastDataRowIndex' => $this->lastDataRowIndex,
            'totalDataRowsProcessed' => $this->totalDataRowsProcessed,
            'importSuccessCount' => $this->importSuccessCount,
        ];
    }

    /**
     * Set the data collected during the import process.
     *
     * @param array|null $value
     *
     * @return $this
     */
    public function setDataCollected(array $value = null)
    {
        $this->dataCollected = $value;

        return $this;
    }

    /**
     * Get the data collected during the import process.
     *
     * @return array|null If the data is collected, you'll get an array with the keys:
     * - attributeKeys: the list of attribute keys (\Concrete\Core\Attribute\AttributeKeyInterface[])
     * - attributeControllers: the list of attribute key controllers (\Concrete\Core\Attribute\Controller[])
     * - data: a list of array, whose keys are:
     *   - object: the object associated to the CSV data row (\Concrete\Core\Attribute\ObjectInterface)
     *   - attributeValues: the list of attribute values (array[\Concrete\Core\Entity\Attribute\Value\AbstractValue|null])
     */
    public function getDataCollected()
    {
        return $this->dataCollected;
    }
}
