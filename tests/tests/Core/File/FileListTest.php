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
            'FileVersionLog',
            'FileSetFiles'
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

    public function testGetPaginationObject()
    {
        $pagination = $this->list->getPagination();
        $this->assertInstanceOf('\Concrete\Core\Pagination\Pagination', $pagination);
    }

    public function testGetUnfilteredTotal()
    {
        $this->assertEquals(11, $this->list->getTotalResults());
    }

    public function testGetUnfilteredTotalFromPagination()
    {
        $pagination = $this->list->getPagination();
        $this->assertEquals(11, $pagination->getTotalResults());
    }

    public function testFilterByTypeValid1()
    {
        $this->list->filterByType(\Concrete\Core\File\Type\Type::T_IMAGE);
        $this->assertEquals(5, $this->list->getTotalResults());
        $pagination = $this->list->getPagination();
        $this->assertEquals(5, $pagination->getTotalResults());
        $pagination->setMaxPerPage(3)->setCurrentPage(1);
        $results = $pagination->getCurrentPageResults();
        $this->assertEquals(3, count($results));
        $this->assertInstanceOf('\Concrete\Core\File\File', $results[0]);
    }

    public function testFilterByExtensionAndType()
    {
        $this->list->filterByType(\Concrete\Core\File\Type\Type::T_TEXT);
        $this->list->filterByExtension('txt');
        $this->assertEquals(6, $this->list->getTotalResults());
    }

    public function testFilterByKeywords()
    {
        $this->list->filterByKeywords('le');
        $pagination = $this->list->getPagination();
        $this->assertEquals(5, $pagination->getTotalResults());
    }

    public function testFilterBySet()
    {
        $fs = \FileSet::add('test');
        $f = \File::getByID(1);
        $f2 = \File::getByID(4);
        $fs->addFileToSet($f);
        $fs->addFileToSet($f2);

        $fs2 = \FileSet::add('test2');
        $fs2->addFiletoSet($f);

        $this->list->filterBySet($fs);
        $pagination = $this->list->getPagination();
        $this->assertEquals(2, $pagination->getTotalResults());
        $results = $this->list->getResults();
        $this->assertEquals(2, count($results));
        $this->assertEquals(4, $results[1]->getFileID());

        $this->list->filterBySet($fs2);
        $results = $this->list->getResults();

        $this->assertEquals(1, count($results));
        $this->assertEquals(1, $results[0]->getFileID());

        $nl = new \Concrete\Core\File\FileList();
        $nl->filterByNoSet();
        $results = $nl->getResults();
        $this->assertEquals(9, count($results));
    }

    public function testSortByFilename()
    {
        $this->list->sortByFilenameAscending();
        $pagination = $this->list->getPagination();
        $pagination->setMaxPerPage(2);
        $results = $pagination->getCurrentPageResults();
        $this->assertEquals(2, count($results));
        $this->assertEquals(5, $results[0]->getFileID());
    }

    public function testAutoSort()
    {
        $req = \Request::getInstance();
        $req->query->set($this->list->getQuerySortColumnParameter(), 'fvFilename');
        $req->query->set($this->list->getQuerySortDirectionParameter(), 'desc');

        $nl = new \Concrete\Core\File\FileList();
        $results = $nl->getResults();

        $this->assertEquals(6, $results[0]->getFileID());
        $this->assertEquals('testing.txt', $results[0]->getFilename());
    }

    public function testPaginationPagesWithoutPermissions()
    {
        $pagination = $this->list->getPagination();
        $pagination->setMaxPerPage(2)->setCurrentPage(1);

        $this->assertEquals(6, $pagination->getTotalPages());

        $this->list->filterByType(\Concrete\Core\File\Type\Type::T_IMAGE);
        $pagination = $this->list->getPagination();
        $this->assertEquals(5, $pagination->getTotalResults());
        $pagination->setMaxPerPage(2)->setCurrentPage(2);

        $this->assertEquals(3, $pagination->getTotalPages());
        $this->assertTrue($pagination->hasNextPage());
        $this->assertTrue($pagination->hasPreviousPage());

        $pagination->setCurrentPage(1);
        $this->assertTrue($pagination->hasNextPage());
        $this->assertFalse($pagination->hasPreviousPage());

        $pagination->setCurrentPage(3);
        $this->assertFalse($pagination->hasNextPage());
        $this->assertTrue($pagination->hasPreviousPage());

        $results = $pagination->getCurrentPageResults();
        $this->assertInstanceOf('\Concrete\Core\File\File', $results[0]);
        $this->assertEquals(1, count($results[0]));
    }

    public function testPaginationWithPermissions()
    {
        
    }
}
 