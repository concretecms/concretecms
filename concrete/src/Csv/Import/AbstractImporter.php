<?php
namespace Concrete\Core\Csv\Import;

use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Attribute\Controller as AttributeController;
use Concrete\Core\Attribute\MulticolumnTextExportableAttributeInterface;
use Concrete\Core\Attribute\ObjectInterface;
use Concrete\Core\Attribute\SimpleTextExportableAttributeInterface;
use Concrete\Core\Error\ErrorList\ErrorList;
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
     * The list of processing errors.
     *
     * @var ErrorList
     */
    private $errors;

    /**
     * The list of processing warnings.
     *
     * @var ErrorList
     */
    private $warnings;

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
        $this->resetErrors();
    }

    /**
     * Process the CSV data.
     *
     * @return bool returns true on success, false on failure (see the getErrors() method)
     */
    public function process()
    {
        $this->resetErrors();
        $result = true;
        $result = $result && $this->processHeader();
        if ($this->dryRun !== false) {
            // Let's start a transaction. It shouldn't be necessary, but it doesn't cost a penny ;)
            $this->entityManager->getConnection()->beginTransaction();
        }
        try {
            $result = $result && $this->processData();
        } finally {
            if ($this->dryRun !== false) {
                try {
                    $this->entityManager->getConnection()->rollBack();
                } catch (Exception $foo) {
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
     * Get the list of processing errors.
     *
     * @return ErrorList
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get the list of processing warnings.
     *
     * @return ErrorList
     */
    public function getWarnings()
    {
        return $this->warnings;
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
     * @throws Exception throw an Exception in case of problems
     *
     * @return ObjectInterface
     */
    abstract protected function getObjectWithStaticValues(array $staticValues);

    /**
     * Reset the errors/warnings.
     */
    protected function resetErrors()
    {
        $this->errors = $this->app->build(ErrorList::class);
        $this->warnings = $this->app->build(ErrorList::class);
    }

    /**
     * Add an error message to the errors/warnings list.
     *
     * @param bool $isError Is this a warning (false) or an error (true)?
     * @param int $rowIndex the 0-index line index
     * @param string $problem the problem message
     */
    protected function addLineProblem($isError, $rowIndex, $problem)
    {
        $list = $isError ? $this->errors : $this->warnings;
        $list->add(t('Line #%s: %s', $rowIndex + 1, $problem));
    }

    /**
     * Read the header row and initialize the CSV schema.
     *
     * @return bool returns true in case of success, false in case of failures (see the getErrors() method)
     */
    protected function processHeader()
    {
        $result = false;
        $this->csvSchema = null;
        $csvSchema = null;
        $this->reader->each(function ($headerCells, $rowIndex) use (&$csvSchema) {
            $csvSchema = new CsvSchema($rowIndex, $headerCells, $this->getStaticHeaders(), $this->getAttributesMap());

            return false;
        });
        if ($csvSchema === null) {
            $this->errors->add(t("There's no row in the CSV."));
        } elseif (!$csvSchema->someHeaderRecognized()) {
            $this->addLineProblem(true, $csvSchema->getRowIndex(), t('None of the CSV columns have been recognized.'));
        } else {
            $unrecognizedHeaders = $csvSchema->getUnrecognizedHeaders();
            if (count($unrecognizedHeaders) > 0) {
                $this->addLineProblem(false, $csvSchema->getRowIndex(), t('Unrecognized CSV headers: %s', Misc::join($unrecognizedHeaders)));
            }
            $missingHeaders = $csvSchema->getMissingHeaders();
            if (count($missingHeaders) > 0) {
                $this->addLineProblem(false, $csvSchema->getRowIndex(), t('Missing CSV headers: %s', Misc::join($missingHeaders)));
            }
            $this->csvSchema = $csvSchema;
            $result = true;
        }

        return $result;
    }

    /**
     * Read the data rows and process them.
     *
     * @return bool returns true in case of success, false in case of failures (see the getErrors() method)
     */
    protected function processData()
    {
        $result = false;
        if ($this->csvSchema === null) {
            $this->errors->add(t('The CSV schema has not beed read.'));
        } else {
            $this->reader->setOffset($this->csvSchema->getRowIndex() + 1);
            $someData = false;
            $this->reader->each(function ($cells, $rowIndex) use (&$someData) {
                $staticValues = $this->csvSchema->getStaticValues($cells);
                try {
                    $object = $this->getObjectWithStaticValues($staticValues);
                } catch (Exception $x) {
                    $this->addLineProblem(true, $rowIndex, $x->getMessage());

                    return true;
                }
                $someData = true;
                $attributesValues = $this->csvSchema->getAttributesValues($cells);
                $this->assignCsvAttributes($object, $attributesValues, $rowIndex);
            });
            if ($someData === false) {
                $this->errors->add(t('No data row has been processed.'));
            } else {
                $result = true;
            }
        }

        return $result;
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
     * @param int $rowIndex
     */
    private function assignCsvAttributes(ObjectInterface $object, array $csvAttributes, $rowIndex)
    {
        $attributesWarnings = $this->app->build(ErrorList::class);
        /* @var ErrorList $attributesWarnings */
        $attributeKeysAndControllers = $this->getAttributeKeysAndControllers();
        foreach ($csvAttributes as $attributeIndex => $attributeData) {
            list($attributeKey, $attributeController) = $attributeKeysAndControllers[$attributeIndex];
            $value = $object->getAttributeValueObject($attributeKey, false);
            $attributeController->setAttributeValue($value);
            $data = $this->convertCsvDataForAttributeController($attributeController, $attributeData);
            if ($attributeController instanceof SimpleTextExportableAttributeInterface) {
                $newValueObject = $attributeController->updateAttributeValueFromTextRepresentation($data, $attributesWarnings);
            } elseif ($attributeController instanceof MulticolumnTextExportableAttributeInterface) {
                $newValueObject = $attributeController->updateAttributeValueFromTextRepresentation($data, $attributesWarnings);
            } else {
                $newValueObject = null;
            }
        }
        if ($newValueObject !== null && $this->dryRun === false) {
            $object->setAttribute($attributeKey, $newValueObject);
            $this->entityManager->persist($newValueObject);
            $this->entityManager->flush();
        }
        foreach ($attributesWarnings->getList() as $warning) {
            if ($warning instanceof Exception || $warning instanceof Throwable) {
                $warning = $warning->getMessage();
            } else {
                $warning = (string) $warning;
            }
            $this->addLineProblem(false, $rowIndex, $warning);
        }
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
