<?php

use Concrete\Core\Cache\CacheLocal;
use \Concrete\Core\Attribute\Type as AttributeType;
use \Concrete\Core\Attribute\Key\FileKey;
use \Concrete\Core\Attribute\Key\Category;

class ContentFileTranslateTest extends FileStorageTestCase
{
    protected $fixtures = array();

    protected function setUp()
    {
        $this->tables = array_merge($this->tables, array(
            'Files',
            'FileVersions',
            'Users',
            'PermissionAccessEntityTypes',
            'FileAttributeValues',
            'FileImageThumbnailTypes',
            'FilePermissionAssignments',
            'AttributeKeyCategories',
            'AttributeTypes',
            'ConfigStore',
            'AttributeKeys',
            'SystemContentEditorSnippets',
            'AttributeValues',
            'atNumber',
            'FileVersionLog',
        ));
        parent::setUp();
        \Config::set('concrete.upload.extensions', '*.txt;*.jpg;*.jpeg;*.png');

        Category::add('file');
        $number = AttributeType::add('number', 'Number');
        FileKey::add($number, array('akHandle' => 'width', 'akName' => 'Width'));
        FileKey::add($number, array('akHandle' => 'height', 'akName' => 'Height'));

        CacheLocal::flush();
    }

    public function testFrom()
    {
        $from = '<p>This is really nice.</p><concrete-picture fID="1" alt="Happy Cat" />';
        // create the default storage location first.
        mkdir($this->getStorageDirectory());
        $this->getStorageLocation();

        $fi = new \Concrete\Core\File\Importer();
        $file = dirname(__FILE__) . '/fixtures/background-slider-blue-sky.png';
        $r = $fi->import($file, 'background-slider-blue-sky.png');
        $path = $r->getRelativePath();

        $translated = \Concrete\Core\Editor\LinkAbstractor::translateFrom($from);

        $to = '<p>This is really nice.</p><img src="' . $path . '" alt="Happy Cat" width="48" height="20">';

        $this->assertEquals('background-slider-blue-sky.png', $r->getFilename());
        $this->assertEquals($to, $translated);

        $c = new \Concrete\Block\Content\Controller();
        $c->content = $from;
        $sx = new SimpleXMLElement('<test />');
        $c->export($sx);

        $content = (string) $sx->data->record->content;
        $this->assertEquals('<p>This is really nice.</p><concrete-picture alt="Happy Cat" file="background-slider-blue-sky.png" />', $content);
    }
}
