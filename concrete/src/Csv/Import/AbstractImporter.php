<?php

namespace Concrete\Core\Csv\Import;

use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Attribute\Controller as AttributeController;
use Concrete\Core\Attribute\MulticolumnTextExportableAttributeInterface;
use Concrete\Core\Attribute\ObjectInterface;
use Concrete\Core\Attribute\SimpleTextExportableAttributeInterface;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Error\UserMessageException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use League\Csv\Reader;
use Punic\Misc;
use Throwable;

defined('C5_EXECUTE') or die('Access Denied.');

abstract class AbstractImporter
{
    /**
     * The Application container instance.
     *
     * @var Application
     */
    protected $app;

    /**
     * The EntityManager instance.
     *
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * Is the import process just a test?
     *
     * @var bool
     */
    private $dryRun = false;

    /**
     * The CSV Reader instance.
     *
     * @var Reader
     */
    private $reader;

    /**
     * The attribute category.
     *
     * @var CategoryInterface
     */
    private $category;

    /**
     * The memoized attribute keys and controllers for the attribute category.
     *
     * @var array|null
     */
    private $attributeKeysAndControllers;

    /**
     * The CSV Schema.
     *
     * @var CsvSchema|null
     */
    private $csvSchema;

    /**
     * Initialize the instance.
     *
     * @param CategoryInterface $category the attribute category
     * @param Reader $reader the CSV Reader instance
     * @param Application $app
     */
    protected function __construct(Reader $reader, CategoryInterface $category, Application $app)
    {
        $this->app = $app;
        $this->entityManager = $app->make(EntityManagerInterface::class);
        $this->setReader($reader);
        $this->setCategory($category);
    }

    /**
     * Process the CSV data.
     *
     * @param int $dataRowsToSkip the number of data rows to be skipped
     * @param int|null $maxDataRows the maximum number of data rows to be processed
     * @param bool|int $collectData Set to false to not collect the imported data, to true to collect the all the imported data, or a number to limit the data to collect
     *
     * @return ImportResult
     */
    public function process($dataRowsToSkip = 0, $maxDataRows = null, $collectData = false)
    {
        $result = $this->app->make(ImportResult::class);
        if ($this->processHeader($result)) {
            if ($this->dryRun !== false) {
                // Let's start a transaction. It shouldn't be necessary, but it doesn't cost a penny ;)
                $this->entityManager->getConnection()->beginTransaction();
            }
            try {
                $this->processData($result, $dataRowsToSkip, $maxDataRows, $collectData);
            } finally {
                if ($this->dryRun !== false) {
                    try {
                        $this->entityManager->getConnection()->rollBack();
                    } catch (Exception $foo) {
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Is the import process just a test?
     *
     * @return bool
     */
    public function isDryRun()
    {
        return $this->dryRun;
    }

    /**
     * Is the import process just a test?
     *
     * @param bool $value
     *
     * @return $this
     */
    public function setDryRun($value)
    {
        $this->dryRun = (bool) $value;

        return $this;
    }

    /**
     * Get the list of fixed headers.
     *
     * @return string[]|\Generator
     */
    abstract protected function getStaticHeaders();

    /**
     * Get or create a ObjectInterface instance starting from its static values.
     *
     * @param array $staticValues
     *
     * @throws UserMessageException throw an UserMessageException in case of problems
     *
     * @return ObjectInterface
     */
    abstract protected function getObjectWithStaticValues(array $staticValues);

    /**
     * Read the header row and initialize the CSV schema.
     *
     * @param ImportResult $importResult
     *
     * @return bool returns true in case of success, false in case of failures (see the getErrors() method of the result)
     */
    protected function processHeader(ImportResult $importResult)
    {
        $result = false;
        $this->csvSchema = null;
        $csvSchema = null;
        $this->reader->each(function ($headerCells, $rowIndex) use (&$csvSchema) {
            $csvSchema = new CsvSchema($rowIndex, $headerCells, $this->getStaticHeaders(), $this->getAttributesMap());

            return false;
        });
        if ($csvSchema === null) {
            $importResult->getErrors()->add(t("There's no row in the CSV."));
        } elseif (!$csvSchema->someHeaderRecognized()) {
            $importResult->addLineProblem(true, t('None of the CSV columns have been recognized.'), $csvSchema->getRowIndex());
        } else {
            $unrecognizedHeaders = $csvSchema->getUnrecognizedHeaders();
            if (count($unrecognizedHeaders) > 0) {
                $importResult->addLineProblem(false, t('Unrecognized CSV headers: %s', Misc::join($unrecognizedHeaders)), $csvSchema->getRowIndex());
            }
            $missingHeaders = $csvSchema->getMissingHeaders();
            if (count($missingHeaders) > 0) {
                $importResult->addLineProblem(false, t('Missing CSV headers: %s', Misc::join($missingHeaders)), $csvSchema->getRowIndex());
            }
            $this->csvSchema = $csvSchema;
            $result = true;
        }

        return $result;
    }

    /**
     * Read the data rows and process them.
     *
     * @param ImportResult $importResult
     * @param int $dataRowsToSkip the number of data rows to be skipped
     * @param int|null $maxDataRows the maximum number of data rows to be processed
     * @param bool|int $collectData Set to false to not collect the imported data, to true to collect the all the imported data, or a number to limit the data to collect
     *
     * @return null|array If $collectData is set to true, the result will be an array with the keys:
     * - attributeKeys: the list of attribute keys (\Concrete\Core\Attribute\AttributeKeyInterface[])
     * - attributeControllers: the list of attribute key controllers (\Concrete\Core\Attribute\Controller[])
     * - data: a list of array, whose keys are:
     *   - object: the object associated to the CSV data row (\Concrete\Core\Attribute\ObjectInterface)
     *   - attributeValues: the list of attribute values (array[\Concrete\Core\Entity\Attribute\Value\AbstractValue|null])
     */
    protected function processData(ImportResult $importResult, $dataRowsToSkip, $maxDataRows, $collectData = false)
    {
        if ($collectData !== false) {
            $dataCollected = [
                'attributeKeys' => [],
                'attributeControllers' => [],
                'data' => [],
            ];
        } else {
            $dataCollected = null;
        }
        if ($this->csvSchema === null) {
            $importResult->getErrors()->add(t('The CSV schema has not been read.'));
        } else {
            if ($dataCollected !== null) {
                $attributeKeysAndControllers = $this->getAttributeKeysAndControllers();
                foreach ($attributeKeysAndControllers as $attributeIndex => list($attributeKey, $attributeController)) {
                    $dataCollected['attributeKeys'][$attributeIndex] = $attributeKey;
                    $dataCollected['attributeControllers'][$attributeIndex] = $attributeController;
                }
            }
            if ($maxDataRows !== null) {
                $maxDataRows = (int) $maxDataRows;
            }
            if ($maxDataRows === null || $maxDataRows > 0) {
                $dataRowsToSkip = (int) $dataRowsToSkip;
                $this->reader->setOffset(1 + ($dataRowsToSkip > 0 ? $dataRowsToSkip : 0));
                $this->reader->each(function ($cells, $rowIndex) use ($importResult, $maxDataRows, &$dataCollected, $collectData) {
                    $importResult->countRowProcessed($rowIndex);
                    $staticValues = $this->csvSchema->getStaticValues($cells);
                    try {
                        $object = $this->getObjectWithStaticValues($staticValues);
                    } catch (UserMessageException $x) {
                        $importResult->addLineProblem(true, $x->getMessage());
                        $object = null;
                    }
                    if ($object !== null) {
                        $attributesValues = $this->csvSchema->getAttributesValues($cells);
                        $attributesValueObjects = $this->assignCsvAttributes($object, $attributesValues, $importResult);
                        $importResult->increaseImportSuccessCount();
                        if ($dataCollected !== null) {
                            if ($collectData === true || count($dataCollected['data']) < $collectData) {
                                $dataCollected['data'][] = [
                                    'object' => $object,
                                    'attributeValues' => $attributesValueObjects,
                                ];
                            }
                        }
                    }

                    return $maxDataRows === null || $importResult->getTotalDataRowsProcessed() < $maxDataRows;
                });
            }
        }
        if ($dataCollected !== null) {
            $importResult->setDataCollected($dataCollected);
        }
    }

    /**
     * Set the CSV Reader instance.
     *
     * @param Reader $reader
     *
     * @return $this
     */
    protected function setReader(Reader $reader)
    {
        $this->reader = $reader;

        return $this;
    }

    /**
     * Get the CSV Reader instance.
     *
     * @return Reader
     */
    protected function getReader()
    {
        return $this->reader;
    }

    /**
     * Set the attribute category to be used to export the data.
     *
     * @param CategoryInterface $category
     *
     * @return $this
     */
    protected function setCategory(CategoryInterface $category)
    {
        $this->category = $category;
        $this->attributeKeysAndControllers = null;
        $this->csvHeader = null;

        return $this;
    }

    /**
     * Get the attribute category to be used to export the data.
     *
     * @return CategoryInterface
     */
    protected function getCategory()
    {
        return $this->category;
    }

    /**
     * @return string[]|string[][]
     */
    protected function getAttributesMap()
    {
        $map = [];
        foreach ($this->getAttributeKeysAndControllers() as $attributeIndex => list($attributeKey, $attributeController)) {
            $handle = 'a:' . $attributeKey->getAttributeKeyHandle();
            if ($attributeController instanceof SimpleTextExportableAttributeInterface) {
                $map[$attributeIndex] = $handle;
            } elseif ($attributeController instanceof MulticolumnTextExportableAttributeInterface) {
                $handles = [];
                foreach ($attributeController->getAttributeTextRepresentationHeaders() as $subHeader) {
                    $handles[$handle . '[' . $subHeader . ']'] = $subHeader;
                }
                $map[$attributeIndex] = $handles;
            }
        }

        return $map;
    }

    /**
     * Get a list the attribute keys and controllers for the current category.
     *
     * @return array
     */
    protected function getAttributeKeysAndControllers()
    {
        if ($this->attributeKeysAndControllers === null) {
            $list = [];
            foreach ($this->category->getList() as $attributeKey) {
                $list[] = [$attributeKey, $attributeKey->getController()];
            }
            $this->attributeKeysAndControllers = $list;
        }

        return $this->attributeKeysAndControllers;
    }

    /**
     * Set/update the object attributes with the data read from the CSV.
     *
     * @param ObjectInterface $object
     * @param array $csvAttributes
     * @param ImportResult $importResult
     *
     * @return array[\Concrete\Core\Entity\Attribute\Value\AbstractValue|null]
     */
    private function assignCsvAttributes(ObjectInterface $object, array $csvAttributes, ImportResult $importResult)
    {
        $attributesWarnings = $this->app->build(ErrorList::class);
        /* @var ErrorList $attributesWarnings */
        $attributeKeysAndControllers = $this->getAttributeKeysAndControllers();
        $result = array_fill(0, count($attributeKeysAndControllers), null);
        foreach ($csvAttributes as $attributeIndex => $attributeData) {
            list($attributeKey, $attributeController) = $attributeKeysAndControllers[$attributeIndex];
            /* @var \Concrete\Core\Attribute\AttributeKeyInterface $attributeKey */
            /* @var \Concrete\Core\Attribute\Controller $attributeController */
            $initialValueObject = $object->getAttributeValueObject($attributeKey, false);
            $attributeController->setAttributeValue($initialValueObject);
            $data = $this->convertCsvDataForAttributeController($attributeController, $attributeData);
            if ($attributeController instanceof SimpleTextExportableAttributeInterface) {
                $newValueObject = $attributeController->updateAttributeValueFromTextRepresentation($data, $attributesWarnings);
            } elseif ($attributeController instanceof MulticolumnTextExportableAttributeInterface) {
                $newValueObject = $attributeController->updateAttributeValueFromTextRepresentation($data, $attributesWarnings);
            } else {
                $newValueObject = null;
            }
            if ($newValueObject !== null && $this->dryRun === false) {
                if ($newValueObject === $initialValueObject) {
                    $this->entityManager->flush();
                } else {
                    $object->setAttribute($attributeKey, $newValueObject);
                }
            }
            $result[$attributeIndex] = $newValueObject;
        }
        foreach ($attributesWarnings->getList() as $warning) {
            if ($warning instanceof Exception || $warning instanceof Throwable) {
                $warning = $warning->getMessage();
            } else {
                $warning = (string) $warning;
            }
            $importResult->addLineProblem(false, $warning);
        }

        return $result;
    }

    /**
     * Convert the data read from CSV to be passed to the attribute controller.
     *
     * @param AttributeController $controller
     * @param string|array $csvData
     * @param AttributeController $attributeController
     *
     * @return string|string[]
     */
    private function convertCsvDataForAttributeController(AttributeController $attributeController, $csvData)
    {
        $result = $csvData;
        if ($attributeController instanceof MulticolumnTextExportableAttributeInterface) {
            $attributeHeaders = $attributeController->getAttributeTextRepresentationHeaders();
            $result = array_pad([], count($attributeHeaders), '');
            foreach ($attributeHeaders as $attributeHeaderIndex => $attributeHeaderName) {
                if (isset($csvData[$attributeHeaderName])) {
                    $result[$attributeHeaderIndex] = $csvData[$attributeHeaderName];
                }
            }
        }

        return $result;
    }
}
