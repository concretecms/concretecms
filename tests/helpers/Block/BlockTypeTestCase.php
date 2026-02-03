<?php

namespace Concrete\TestHelpers\Block;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Filesystem\FileLocator;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Filesystem\Filesystem;

abstract class BlockTypeTestCase extends ConcreteDatabaseTestCase
{
    protected $fixtures = [];

    protected $tables = [
        'btCoreStackDisplay',
        'Blocks',
        'Pages',
        'CollectionVersionBlocks',
        'Collections',
        'Config',
    ];

    protected $entityClassNames = [
        'Concrete\Core\Entity\Package',
        'Concrete\Core\Entity\Page\PagePath',
        'Concrete\Core\Entity\Block\BlockType\BlockType',
    ];

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Database\ConcreteDatabaseTestCase::tearDown()
     */
    public function tearDown(): void
    {
        $fileLocator = app(FileLocator::class);
        $record = $fileLocator->getRecord(DIRNAME_BLOCKS . '/' . $this->btHandle . '/' . FILENAME_BLOCK_DB);
        if ($record) {
            $dbXml = $record->getFile();
            $dbXmlOriginal = $dbXml . '-original';
            $fs = new Filesystem();
            if ($fs->isFile($dbXmlOriginal)) {
                if ($fs->isFile($dbXml)) {
                    $fs->delete($dbXml);
                }
                $fs->move($dbXmlOriginal, $dbXml);
            }
        }
        parent::tearDown();
    }

    public function testInstall()
    {
        $bt = BlockType::installBlockType($this->btHandle);
        $this->assertSame($this->btHandle, $bt->getBlockTypeHandle());
        $btx = BlockType::getByID(1);
        $this->assertNotNull($btx);
        $this->assertSame(1, (int) $btx->getBlockTypeID());
    }

    public function testSave()
    {
        $bt = BlockType::installBlockType($this->btHandle);
        $btx = BlockType::getByID(1);
        $class = $btx->getBlockTypeClass();
        $btc = new $class();
        $bID = 1;
        $db = app(Connection::class);
        foreach ($this->requestData as $type => $requestData) {
            $nb = $bt->add($requestData);
            $data = $this->expectedRecordData[$type];
            $r = $db->fetchAssociative('select * from `' . $btc->getBlockTypeDatabaseTable() . '` where bID = ?', [$bID]);
            foreach ($data as $key => $value) {
                $this->assertTrue($r[$key] == $value, "Key `{$key}` did not equal expected value `{$value}` instead equalled `{$r[$key]}` (type `{$type}`)");
            }
            $bID++;
            ob_start();
            $bv = new BlockView($nb);
            $bv->render('view');
            $contents = ob_get_contents();
            ob_end_clean();
            $contents = trim($contents);
            if (isset($this->expectedOutput[$type])) {
                $this->assertSame($this->expectedOutput[$type], $contents, "Output did not equal expected output (type `{$type}`)");
            }
        }
    }

    public function testRefresh()
    {
        $fs = new Filesystem();
        $fileLocator = app(FileLocator::class);
        $record = $fileLocator->getRecord(DIRNAME_BLOCKS . '/' . $this->btHandle . '/' . FILENAME_BLOCK_DB);
        $dbXmlFile = $record ? $record->getFile() : null;
        if (!$dbXmlFile || !$fs->isFile($dbXmlFile)) {
            $this->markTestSkipped('This test tries to add a column to the block type tables, but this block type does not have a ' . FILENAME_BLOCK_DB . ' file.');
        }
        $tableColumns = [];
        $xDoc = new DOMDocument();
        $xDoc->loadXML($fs->get($dbXmlFile));
        $xPath = new DOMXPath($xDoc);
        $xPath->registerNamespace('dx', 'http://www.concrete5.org/doctrine-xml/0.5');
        $xTables = $xPath->query('/dx:schema/dx:table');
        if ($xTables->length > 0) {
            foreach ($xTables as $xTable) {
                /** @var \DOMElement $xTable */
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
        if ($tableColumns === []) {
            $this->markTestSkipped('This test tries to add a column to the block type tables, but the ' . FILENAME_BLOCK_DB . ' does not define any table.');
        }
        BlockType::installBlockType($this->btHandle);
        $btx = BlockType::getByID(1);
        $btx->refresh();
        $sm = app(Connection::class)->getSchemaManager();
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
