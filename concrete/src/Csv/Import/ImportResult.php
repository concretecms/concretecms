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
        ];
	}
}
