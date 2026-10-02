<?php

declare(strict_types=1);

namespace Concrete\TestHelpers\Csv\Import;

use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Attribute\ObjectInterface;
use Concrete\Core\Csv\Import\AbstractImporter;
use Concrete\Core\Error\UserMessageException;
use League\Csv\Reader;
use Mockery as M;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * A minimal CSV importer with a "name" column: rows named "invalid" are rejected.
 */
class TestImporter extends AbstractImporter
{
    /**
     * The values of the "name" column of the imported rows.
     *
     * @var string[]
     */
    public $importedNames = [];

    public function __construct(Reader $reader, CategoryInterface $category, Application $app)
    {
        parent::__construct($reader, $category, $app);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Csv\Import\AbstractImporter::getStaticHeaders()
     */
    protected function getStaticHeaders()
    {
        return ['name'];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Csv\Import\AbstractImporter::getObjectWithStaticValues()
     */
    protected function getObjectWithStaticValues(array $staticValues)
    {
        if ($staticValues['name'] === 'invalid') {
            throw new UserMessageException('Invalid name');
        }
        $this->importedNames[] = $staticValues['name'];

        return M::mock(ObjectInterface::class);
    }
}
