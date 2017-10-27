<?php
namespace Concrete\Core\Csv\Import;

use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Attribute\MulticolumnTextExportableAttributeInterface;
use Concrete\Core\Attribute\ObjectInterface;
use Concrete\Core\Attribute\SimpleTextExportableAttributeInterface;
use Concrete\Core\Error\ErrorList\ErrorList;
use League\Csv\Reader;
use Punic\Misc;

defined('C5_EXECUTE') or die('Access Denied.');

abstract class AbstractImporter
{
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
        $this->setReader($reader);
        $this->setCategory($category);
        $this->errors = $app->build(ErrorList::class);
        $this->warnings = $app->build(ErrorList::class);
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
     * Read the header row and initialize the CSV schema.
     *
     * @return $this
     */
    public function readHeader()
    {
        $this->csvSchema = null;
        $csvSchema = null;
        $this->reader->each(function ($headerCells, $rowIndex) use (&$csvSchema) {
            $csvSchema = new CsvSchema($rowIndex, $headerCells, $this->getStaticHeaders(), $this->getAttributesMap());

            return false;
        });
        if ($csvSchema === null) {
            $this->errors->add(t("There's no row in the CSV"));
        } elseif (!$csvSchema->someHeaderRecognized()) {
            $this->errors->add(t('None of the CSV columns have been recognized'));
        } else {
            $unrecognizedHeaders = $csvSchema->getUnrecognizedHeaders();
            if (count($unrecognizedHeaders) > 0) {
                $this->warnings->add(t('Unrecognized CSV headers: %s', Misc::join($unrecognizedHeaders)));
            }
            $missingHeaders = $csvSchema->getMissingHeaders();
            if (count($missingHeaders) > 0) {
                $this->warnings->add(t('Missing CSV headers: %s', Misc::join($missingHeaders)));
            }

            $this->csvSchema = $csvSchema;
        }

        return $this;
    }

    /**
     * Insert a row for a specific object instance.
     *
     * @param ObjectInterface $object
     *
     * @return $this
     */
    public function readNextRow(ObjectInterface $object)
    {
        $this->reader->insertOne(iterator_to_array($this->projectObject($object)));

        return $this;
    }

    /**
     * Get the list of fixed headers.
     *
     * @return string[]|\Generator
     */
    abstract protected function getStaticHeaders();

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
}
