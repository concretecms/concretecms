<?php
namespace Concrete\Tests\Core\File;
use \Concrete\Core\File\Importer;
use \Concrete\Core\Attribute\Type as AttributeType;
use \Concrete\Core\Attribute\Key\FileKey;
use Core;
use \Concrete\Core\Attribute\Key\Category;

class FileListTest extends \FileStorageTestCase {

    protected function setUp()
    {
        $this->tables = array_merge($this->tables, array(
            'Files',
            'FileVersions',
            'Users',
            'PermissionAccessEntityTypes',
            'FileAttributeValues',
            'AttributeKeyCategories',
            'AttributeTypes',
            'Config',
            'AttributeKeys',
            'AttributeValues',
            'FileSets',
            'atNumber',
            'FileVersionLog'
        ));
        parent::setUp();
        define('UPLOAD_FILE_EXTENSIONS_ALLOWED', '*.txt;*.jpg;*.jpeg;*.png');

        Category::add('file');
        \Concrete\Core\Permission\Access\Entity\Type::add('file_uploader', 'File Uploader');
        $number = AttributeType::add('number', 'Number');
        FileKey::add($number, array('akHandle' => 'width', 'akName' => 'Width'));
        FileKey::add($number, array('akHandle' => 'height', 'akName' => 'Height'));

        mkdir($this->getStorageDirectory());
        $this->getStorageLocation();

        $sample = dirname(__FILE__) . '/StorageLocation/fixtures/sample.txt';
        $image = DIR_BASE . '/concrete/images/logo.png';
        $fi = new Importer();

        $files = array(
            'sample1.txt' => $sample,
            'sample2.txt' => $sample,
            'sample4.txt' => $sample,
            'sample5.txt' => $sample,
            'awesome.txt' => $sample,
            'testing.txt' => $sample,
            'logo1.png' => $image,
            'logo2.png' => $image,
            'logo3.png' => $image,
            'foobley.png' => $image,
            'test.png' => $image
        );

        foreach($files as $filename => $pointer) {
            $fi->import($pointer, $filename);
        }


        //$this->list = new \Concrete\Core\Legacy\FileList();
        //$this->list->setPermissionLevel(false);
        $this->list = new \Concrete\Core\File\FileList();
    }

    protected function cleanup()
    {
        parent::cleanup();
        if (file_exists(dirname(__FILE__) . '/test.txt')) {
            unlink(dirname(__FILE__) . '/test.txt');
        }
        if (file_exists(dirname(__FILE__) . '/test.invalid')) {
            unlink(dirname(__FILE__) . '/test.invalid');
        }
    }

    public function testGetUnfilteredTotal()
    {
        $this->assertEquals(11, $this->list->getTotal());
    }

    public function testFilterByTypeValid1()
    {
        $this->list->filterByType(\Concrete\Core\File\Type\Type::T_IMAGE);
        $this->assertEquals(5, $this->list->getTotal());
        $this->list->getPaginator()->setMaxPerPage(3);
        $results = $this->list->getPage();
        $this->assertEquals(3, count($results));
        $this->assertInstanceOf('\Concrete\Core\File\File', $results[0]);
    }

    public function testFilterByExtensionAndType()
    {
        $this->list->filterByType(\Concrete\Core\File\Type\Type::T_TEXT);
        $this->list->filterByExtension('txt');
        $this->assertEquals(6, $this->list->getTotal());
    }

    public function testFilterByKeywords()
    {
        $this->list->filterByKeywords('le');
        $this->assertEquals(5, $this->list->getTotal());
    }


}
 