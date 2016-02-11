<?php
namespace Concrete\Tests\Core\File;

use Concrete\Core\File\Importer;
use Concrete\Core\Attribute\Type as AttributeType;
use Concrete\Core\Attribute\Key\FileKey;
use Concrete\Core\Attribute\Key\Category;

class FileListTest extends \FileStorageTestCase
{
    /** @var \Concrete\Core\File\FileList */
    protected $list;

    protected function setUp()
    {
        $this->tables = array_merge($this->tables, array(
            'Files',
            'FileVersions',
            'Users',
            'PermissionAccessEntityTypes',
            'FileAttributeValues',
            'AttributeKeyCategories',
            'AttributeSetKeys',
            'Packages',
            'AttributeSets',
            'FileImageThumbnailTypes',
            'AttributeTypes',
            'ConfigStore',
            'AttributeKeys',
            'AttributeValues',
            'FileSets',
            'atNumber',
            'FileVersionLog',
            'FileSetFiles',
        ));
        parent::setUp();
        \Config::set('concrete.upload.extensions', '*.txt;*.jpg;*.jpeg;*.png');

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
            'test.png' => $image,
        );

        foreach ($files as $filename => $pointer) {
            $fi->import($pointer, $filename);
        }

        $this->list = new \Concrete\Core\File\FileList();
        $this->list->ignorePermissions();
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
        $this->assertInstanceOf('\Concrete\Core\Search\Pagination\Pagination', $pagination);
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
        $nl->ignorePermissions();
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
        $req->query->set($this->list->getQuerySortColumnParameter(), 'fv.fvFilename');
        $req->query->set($this->list->getQuerySortDirectionParameter(), 'desc');
        $nl = new \Concrete\Core\File\FileList();
        $nl->ignorePermissions();
        $results = $nl->getResults();

        $this->assertEquals(6, $results[0]->getFileID());
        $this->assertEquals('testing.txt', $results[0]->getFilename());

        $req->query->set($this->list->getQuerySortColumnParameter(), null);
        $req->query->set($this->list->getQuerySortDirectionParameter(), null);
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
        // first lets make some more files.
        $sample = dirname(__FILE__) . '/StorageLocation/fixtures/sample.txt';
        $image = DIR_BASE . '/concrete/images/logo.png';
        $fi = new Importer();

        $files = array(
            'another.txt' => $sample,
            'funtime.txt' => $sample,
            'funtime2.txt' => $sample,
            'awesome-o' => $sample,
            'image.png' => $image,
        );

        foreach ($files as $filename => $pointer) {
            $fi->import($pointer, $filename);
        }

        $nl = new \Concrete\Core\File\FileList();
        $nl->setPermissionsChecker(function ($file) {
            if ($file->getTypeObject()->getGenericType() == \Concrete\Core\File\Type\Type::T_IMAGE) {
                return true;
            } else {
                return false;
            }
        });
        $nl->sortByFilenameAscending();
        $results = $nl->getResults();
        $pagination = $nl->getPagination();
        $this->assertEquals(-1, $nl->getTotalResults());
        $this->assertEquals(6, $pagination->getTotalResults());
        $this->assertEquals(6, count($results));

        // so there are six "real" results, and 15 total results without filtering.
        $pagination->setMaxPerPage(4)->setCurrentPage(1);

        $this->assertEquals(2, $pagination->getTotalPages());

        $this->assertTrue($pagination->hasNextPage());
        $this->assertFalse($pagination->hasPreviousPage());

        // Ok, so the results ought to be the following files, broken up into pages of four, in this order:
        // foobley.png
        // image.png
        // logo1.png
        // logo2.png
        // -- page break --
        // logo3.png
        // test.png

        $results = $pagination->getCurrentPageResults();

        $this->assertInstanceOf('\Concrete\Core\Search\Pagination\PermissionablePagination', $pagination);
        $this->assertEquals(4, count($results));
        $this->assertEquals('foobley.png', $results[0]->getFilename());
        $this->assertEquals('image.png', $results[1]->getFilename());
        $this->assertEquals('logo1.png', $results[2]->getFilename());
        $this->assertEquals('logo2.png', $results[3]->getFilename());

        $pagination->setCurrentPage(2);

        $results = $pagination->getCurrentPageResults();

        $this->assertEquals('logo3.png', $results[0]->getFilename());
        $this->assertEquals('test.png', $results[1]->getFilename());
        $this->assertEquals(2, count($results));

        $this->assertTrue($pagination->hasPreviousPage());
        $this->assertFalse($pagination->hasNextPage());
    }

    public function testFileSearchDefaultColumnSet()
    {
        $set = \Concrete\Core\File\Search\ColumnSet\ColumnSet::getCurrent();

        $this->assertInstanceOf('\Concrete\Core\File\Search\ColumnSet\DefaultSet', $set);

        $columns = $set->getColumns();

        $this->assertEquals(5, count($columns));
    }
}
