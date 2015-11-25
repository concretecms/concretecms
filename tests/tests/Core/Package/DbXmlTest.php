<?php
namespace Concrete\Tests\Core\Package;

use Database;
use Package;

class DbXmlTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Tests that the table is properly updated according to the updated
     * database schema XML file when the database table already exists.
     */
    public function testTableUpdates()
    {
        // The easiest way to test that the DB is updated properly is to make
        // two consecutive calls to Package::installDB(). This is actually what
        // the package also internally does during the install() and upgrade()
        // calls and also what the block types do when the refresh() method is
        // called on them. So, this should cover the actual scenarios we want
        // to cover here.
        $db = Database::get();

        // Create the table initially.
        Package::installDB(__DIR__ . '/fixtures/db-1.xml');

        // Make sure the table was properly created and it does NOT contain the
        // column we are about to add. We are not interested here whether the
        // table otherwise follows the defined schema, so no need to test
        // anything else here.
        $schema = $db->getSchemaManager()->createSchema();
        $this->assertTrue($schema->hasTable('TestPackageTable'));

        $tbl = $schema->getTable('TestPackageTable');
        $this->assertFalse($tbl->hasColumn('newColumn'));

        // db-2.xml modifies the already existing table.
        Package::installDB(__DIR__ . '/fixtures/db-2.xml');

        // Make sure the column exists in the updated database that is added in
        // db-2.xml. Also make sure that the column we drop in db-2.xml no
        // longer exists in the table. This is what we actually want to test
        // here.
        $schema = $db->getSchemaManager()->createSchema();

        $tbl = $schema->getTable('TestPackageTable');
        $this->assertTrue($tbl->hasColumn('newColumn'));
        $this->assertFalse($tbl->hasColumn('testColumn'));
    }

}
