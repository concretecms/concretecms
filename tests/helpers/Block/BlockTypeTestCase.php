<?php

namespace Concrete\TestHelpers\Block;

use BlockType;
use Concrete\Core\Block\View\BlockView;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;
use Database;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Environment;
use Illuminate\Filesystem\Filesystem;

abstract class BlockTypeTestCase extends ConcreteDatabaseTestCase
{
    protected $fixtures = [];
    protected $tables = [
        'BlockTypes',
        'Blocks',
        'Pages',
        'CollectionVersionBlocks',
        'Collections',
        'Config',
    ];

    protected $metadatas = [
        'Concrete\Core\Entity\Package',
        'Concrete\Core\Entity\Page\PagePath',
        'Concrete\Core\Entity\Block\BlockType\BlockType',
    ];

    public function tearDown()
    {
        $env = Environment::get();
        $r = $env->getRecord(DIRNAME_BLOCKS . '/' . $this->btHandle . '/' . FILENAME_BLOCK_DB);
        $dbXml = $r->getFile();
        $dbXmlOriginal = $dbXml . '-original';
        $fs = new Filesystem();
        if ($fs->isFile($dbXmlOriginal)) {
            if ($fs->isFile($dbXml)) {
                $fs->delete($dbXml);
            }
            $fs->move($dbXmlOriginal, $dbXml);
        }
        parent::tearDown();
    }

    public function testInstall()
    {
        $bt = BlockType::installBlockType($this->btHandle);
        $btx = BlockType::getByID(1);
        if ($this->assertTrue($bt->getBlockTypeHandle() == $this->btHandle) && $btx->getBlockTypeID() == 1);
    }

    public function testSave()
    {
        $bt = BlockType::installBlockType($this->btHandle);
        $btx = BlockType::getByID(1);
        $class = $btx->getBlockTypeClass();
        $btc = new $class();
        $bID = 1;
        foreach ($this->requestData as $type => $requestData) {
            $nb = $bt->add($requestData);
            $data = $this->expectedRecordData[$type];
            $db = Database::connection();
            $r = $db->GetRow('select * from `' . $btc->getBlockTypeDatabaseTable() . '` where bID = ?', [$bID]);
            foreach ($data as $key => $value) {
                $this->assertTrue($r[$key] == $value, 'Key `' . $key . '` did not equal expected value `' . $value . '` instead equalled `' . $r[$key] . '` (type `' . $type . '`)');
            }
            ++$bID;

            ob_start();
            $bv = new BlockView($nb);
            $bv->render('view');
            $contents = ob_get_contents();
            ob_end_clean();

            $contents = trim($contents);

            if (isset($this->expectedOutput[$type])) {
                $this->assertTrue($this->expectedOutput[$type] == $contents, 'Output `' . $contents . '` did not equal expected output `' . $this->expectedOutput[$type] . '` (type `' . $type . '`)');
            }
        }
    }

    public function testRefresh()
    {
        $env = Environment::get();
        $r = $env->getRecord(DIRNAME_BLOCKS . '/' . $this->btHandle . '/' . FILENAME_BLOCK_DB);
        $tableColumns = [];
        $fs = new Filesystem();
        $dbXmlFile = $r->getFile();
        if ($fs->isFile($dbXmlFile)) {
            $xDoc = new DOMDocument();
            $xDoc->loadXML($fs->get($dbXmlFile));
            $xPath = new DOMXPath($xDoc);
            $xPath->registerNamespace('dx', 'http://www.concrete5.org/doctrine-xml/0.5');
            $xTables = $xPath->query('/dx:schema/dx:table');
            if ($xTables->length > 0) {
                foreach ($xTables as $xTable) {
                    /* @var DOMElement $xTable */
                    $tableName = (string) $xTable->getAttribute('name');
                    $tableColumns[$tableName] = [];
                    foreach ($xPath->query('dx:field', $xTable) as $xField) {
                        if ($xField instanceof DOMElement) {
                            $tableColumns[$tableName][] = strtolower((string) $xField->getAttribute('name'));
                        }
                    }
                    $newField = $xDoc->createElement('field');
                    $attr = $xDoc->createAttribute('name');
                    $attr->value = 'ThisIsATestFieldAddedForTestPurposes__';
                    $tableColumns[$tableName][] = strtolower((string) $attr->value);
                    $newField->appendChild($attr);
                    $attr = $xDoc->createAttribute('type');
                    $attr->value = 'string';
                    $newField->appendChild($attr);
                    $attr = $xDoc->createAttribute('size');
                    $attr->value = '255';
                    $newField->appendChild($attr);
                    $xTable->insertBefore($newField, $xField);
                }
                $dbXmlFileOriginal = $dbXmlFile . '-original';
                $fs->move($dbXmlFile, $dbXmlFileOriginal);
                $fs->put($dbXmlFile, $xDoc->saveXML());
            }
        }
        if (empty($tableColumns)) {
            $this->markTestSkipped('This test tries to add a column to the block type tables, but this block type does not have any table.');
        }
        $bt = BlockType::installBlockType($this->btHandle);
        $btx = BlockType::getByID(1);
        $btx->refresh();
        $sm = Database::connection()->getSchemaManager();
        foreach ($tableColumns as $tableName => $columnNames) {
            $dbColumns = [];
            foreach ($sm->listTableColumns($tableName) as $dbColumn) {
                $dbColumns[] = strtolower($dbColumn->getName());
            }
            $columnNames = array_filter($columnNames, 'strtolower');
            sort($columnNames);
            $dbColumns = array_filter($dbColumns, 'strtolower');
            sort($dbColumns);
            $this->assertSame($columnNames, $dbColumns);
        }
    }
}
