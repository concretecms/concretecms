<?php

namespace Concrete\Tests\Block;

use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Attribute\Key\FileKey;
use Concrete\Core\Attribute\Type as AttributeType;
use Concrete\Core\Cache\CacheLocal;
use Concrete\Core\File\Import\FileImporter;
use Concrete\TestHelpers\File\FileStorageTestCase;
use Core;
use SimpleXMLElement;

class ContentFileTranslateTest extends FileStorageTestCase
{
    protected $fixtures = [];

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->tables = array_merge($this->tables, [
            'Users',
            'PermissionAccessEntityTypes',
            'FileImageThumbnailTypes',
            'FilePermissionAssignments',
            'ConfigStore',
            'AttributeKeys',
            'SystemContentEditorSnippets',
            'AttributeValues',
            'atNumber',
            'FileVersionLog',
        ]);
        $this->metadatas = array_merge($this->metadatas, [
            'Concrete\Core\Entity\File\File',
            'Concrete\Core\Entity\File\Version',
            'Concrete\Core\Entity\Attribute\Key\Settings\EmptySettings',
            'Concrete\Core\Entity\Attribute\Key\FileKey',
            'Concrete\Core\Entity\Attribute\Value\FileValue',
            'Concrete\Core\Entity\Attribute\Key\Key',
            'Concrete\Core\Entity\Attribute\Value\Value',
            'Concrete\Core\Entity\Attribute\Value\Value\Value',
            'Concrete\Core\Entity\Attribute\Value\Value\NumberValue',
            'Concrete\Core\Entity\Attribute\Key\Settings\NumberSettings',
            'Concrete\Core\Entity\Attribute\Key\Settings\EmptySettings',
            'Concrete\Core\Entity\Attribute\Key\Settings\Settings',
            'Concrete\Core\Entity\Attribute\Type',
            'Concrete\Core\Entity\Attribute\Category',
        ]);
    }

    public function setUp()
    {
        parent::setUp();
        \Config::set('concrete.upload.extensions', '*.txt;*.jpg;*.jpeg;*.png');

        Category::add('file');
        $number = AttributeType::add('number', 'Number');
        FileKey::add($number, ['akHandle' => 'width', 'akName' => 'Width']);
        FileKey::add($number, ['akHandle' => 'height', 'akName' => 'Height']);

        CacheLocal::flush();
    }

    public function testFrom()
    {
        $from = '<p>This is really nice.</p><concrete-picture fID="1" alt="Happy Cat" />';
        // create the default storage location first.
        mkdir($this->getStorageDirectory());
        $this->getStorageLocation();

        $fi = Core::make(FileImporter::class);
        $file = DIR_TESTS . '/assets/Block/background-slider-blue-sky.png';
        $r = $fi->importLocalFile($file, 'background-slider-blue-sky.png');
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
        $prefix = $r->getPrefix();
        $this->assertEquals('<p>This is really nice.</p><concrete-picture alt="Happy Cat" file="' . $prefix . ':background-slider-blue-sky.png" />', $content);
    }
}
