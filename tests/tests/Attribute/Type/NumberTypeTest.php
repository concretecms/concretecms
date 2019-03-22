<?php
namespace Concrete\Tests\Attribute\Controller;

use Concrete\Core\Page\Page;
use Concrete\Tests\Attribute\CollectionAttributeTest;

class NumberTypeTest extends CollectionAttributeTest
{
    protected $keys = [
        'test_number' => ['akName' => 'Test Number', 'type' => 'number'],
    ];

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->metadatas = array_merge($this->metadatas, [
            'Concrete\Core\Entity\Attribute\Value\Value\NumberValue',
        ]);
    }

    /**
     * Tests that clearing the target attribute sets its database value to null.
     */
    public function testClearAttribute()
    {
        $object = $this->getAttributeObjectForSet();

        $object->setAttribute('test_number', 1);
        $object->clearAttribute('test_number');

        $afterClear = $this->getSearchIndexAttributeDatabaseValue($object);
        $this->assertNull($afterClear);
    }

    /**
     * Tests that the number attribute's database column's default value matches
     * with the default value set to it after clearing the attribute.
     */
    public function testDefaultValueConsistencyWithDatabase()
    {
        $object = $this->getAttributeObjectForSet();

        // Propagate the search index table with the database default values
        // first.
        $this->insertSearchIndexDatabaseDefaults($object);

        // Get the default value for the attribute in the search index table
        // assigned by the database column's default settings.
        $databaseDefault = $this->getSearchIndexAttributeDatabaseValue($object);

        // Set an actual value for the attribute to be able to clear it later.
        // Note that the attribute values may not be cleared in case they don't
        // have an assigned value.
        $object->setAttribute('test_number', 1);

        // And after the set, clear the attribute which should now work as
        // expected as there is now something to clear.
        $object->clearAttribute('test_number');

        // After clearing the attribute back to its "default" value, we assume
        // the database column's value would match the original value assigned
        // by the database column's defaults.
        $afterClear = $this->getSearchIndexAttributeDatabaseValue($object);
        $this->assertEquals($databaseDefault, $afterClear);
    }

    public function attributeValues()
    {
        return [
            ['test_number', '1', '0'],
        ];
    }

    public function attributeIndexTableValues()
    {
        return [
            ['test_number', true, ['ak_test_number' => '1']],
        ];
    }

    public function attributeHandles()
    {
        return [
            ['test_number'],
        ];
    }

    /**
     * Inserts the database default column values for the given object in the
     * search index table.
     *
     * This can happen e.g. when other values than the target attribute are
     * indexed in cases where the target attribute does not have an assigned
     * value.
     *
     * @param  Page   $object The page object for which to insert
     */
    protected function insertSearchIndexDatabaseDefaults(Page $object)
    {
        $this->connection()->query(
            'INSERT INTO CollectionSearchIndexAttributes (cID) VALUES (?)',
            [$object->getCollectionID()]
        );
    }

    /**
     * Gets the database default value for the given object's target attribute
     * from the search index table.
     *
     * @param  Page   $object The page object for which to get the value
     *
     * @return mixed          The value from the database
     */
    protected function getSearchIndexAttributeDatabaseValue(Page $object)
    {
        $selectQuery =
            'SELECT ak_test_number FROM CollectionSearchIndexAttributes ' .
            'WHERE cID =?'
        ;

        // Get the default value for the attribute in the search index table
        // assigned by the database column's default settings.
        return $this->connection()->query(
            $selectQuery,
            [$object->getCollectionID()]
        )->fetchColumn();
    }
}
