<?php

namespace Concrete\Tests\Block;

use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Attribute\Key\FileKey;
use Concrete\Core\Attribute\Type as AttributeType;
use Concrete\Core\Cache\CacheLocal;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\File\Import\FileImporter;
use Concrete\Core\Utility\Service\Xml;
use Concrete\TestHelpers\File\FileStorageTestCase;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Mockery as M;
use SimpleXMLElement;

class ContentFileTranslateTest extends FileStorageTestCase
{
    protected $fixtures = [];

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Database\ConcreteDatabaseTestCase::getTables()
     */
    protected function getTables()
    {
        return array_merge(parent::getTables(), [
            'PermissionAccessEntityTypes',
            'FilePermissionAssignments',
            'ConfigStore',
            'SystemContentEditorSnippets',
            'FileVersionLog',
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Database\ConcreteDatabaseTestCase::getEntityClassNames()
     */
    protected function getEntityClassNames(): array
    {
        return array_merge(parent::getEntityClassNames(), [
            'Concrete\Core\Entity\File\File',
            'Concrete\Core\Entity\File\Version',
            'Concrete\Core\Entity\Attribute\Key\Settings\EmptySettings',
            'Concrete\Core\Entity\Attribute\Key\FileKey',
            'Concrete\Core\Entity\Attribute\Value\FileValue',
            'Concrete\Core\Entity\Attribute\Key\Key',
            'Concrete\Core\Entity\Attribute\Value\Value\Value',
            'Concrete\Core\Entity\Attribute\Value\Value\NumberValue',
            'Concrete\Core\Entity\Attribute\Key\Settings\EmptySettings',
            'Concrete\Core\Entity\Attribute\Key\Settings\Settings',
            'Concrete\Core\Entity\Attribute\Type',
            'Concrete\Core\Entity\Attribute\Category',
        ]);
    }

    public function setUp(): void
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

        $fi = app(FileImporter::class);
        $file = DIR_TESTS . '/assets/Block/background-slider-blue-sky.png';
        $r = $fi->importLocalFile($file, 'background-slider-blue-sky.png');
        $this->assertEquals('background-slider-blue-sky.png', $r->getFilename());

        $path = $r->getRelativePath();
        $config = app('site')->getSite()->getConfigRepository();
        $config->withKey('misc.img_src_absolute', true, static function() use ($from, $path) {
            $translated = \Concrete\Core\Editor\LinkAbstractor::translateFrom($from);
            $to = '<p>This is really nice.</p><img src="http://www.dummyco.com' . $path . '" alt="Happy Cat" width="48" height="20">';
            self::assertEquals($to, $translated);
        });
        $translated = \Concrete\Core\Editor\LinkAbstractor::translateFrom($from);

        $to = '<p>This is really nice.</p><img src="' . $path . '" alt="Happy Cat" width="48" height="20">';

        $this->assertEquals($to, $translated);

        $c = app(\Concrete\Block\Content\Controller::class);
        $btSchema = \DoctrineXml\Parser::fromFile(DIR_BASE_CORE . '/blocks/content/db.xml', new MySqlPlatform());
        $btTables = $btSchema->getTables();
        $tableName = reset($btTables)->getName();
        $mRecordset = M::mock(\Doctrine\DBAL\Result::class);
        $mRecordset
            ->shouldReceive('fetchAssociative')->twice()->andReturn(['bID' => 1, 'content' => $from], false)
        ;
        $mConn = M::mock(Connection::class);
        $mConn
            ->shouldReceive('MetaColumns')->once()->with($tableName)->andReturn($btSchema->getTable($tableName)->getColumns())
            ->shouldReceive('executeQuery')->once()->andReturn($mRecordset)
        ;
        $mApp = M::mock(Application::class);
        $mApp
            ->shouldReceive('make')->once()->with(Connection::class)->andReturn($mConn)
            ->shouldReceive('make')->once()->with(Xml::class)->andReturn(app(Xml::class))
        ;
        $c->setApplication($mApp);
        $c->content = $from;
        $sx = new SimpleXMLElement('<test />');
        $c->export($sx);

        $content = (string) $sx->data->record->content;
        $prefix = $r->getPrefix();
        $this->assertEquals('<p>This is really nice.</p><concrete-picture alt="Happy Cat" file="' . $prefix . ':background-slider-blue-sky.png" />', $content);
    }
}
