<?php

namespace Concrete\Tests\Express\Export\EntryList;

use Carbon\Carbon;
use Concrete\Core\Attribute\Controller as AttributeTypeController;
use Concrete\Core\Attribute\MulticolumnTextExportableAttributeInterface;
use Concrete\Core\Entity\Attribute\Key\ExpressKey;
use Concrete\Core\Entity\Attribute\Value\ExpressValue;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Entity\User\User;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Express\Export\EntryList\CsvWriter;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Site\Service;
use Concrete\Core\User\UserInfo;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use League\Csv\Writer;
use Mockery as M;
use Concrete\Tests\TestCase;

class CsvWriterTest extends TestCase
{

    public function testCsvValueOrder()
    {
        $entityKeys = [
            'foo' => $this->attributeKey('foo', 'Foo'),
            'bar' => $this->attributeKey('bar', 'Bar'),
            'baz' => $this->attributeKey('baz', 'Baz'),
        ];

        $values = [
            $this->attributeValue($entityKeys['bar'], 'BAR value'),
            $this->attributeValue($entityKeys['baz'], 'Baz value'),
            $this->attributeValue($entityKeys['foo'], 'Foo value'),
        ];

        list($entity, $list, $writer, $csvWriter) = $this->getCsvWriterMock($entityKeys, $values);

        $csvWriter->insertHeaders($entity);
        $csvWriter->insertEntryList($list);

        $this->assertSame([
            'publicIdentifier' => 'publicIdentifier',
            'ccm_date_created' => 'dateCreated',
            'ccm_date_modified' => 'dateModified',
            'site' => 'site',
            'author_name' => 'authorName',
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz',
        ], $writer->headers);

        $this->assertSame([
            [
                'publicIdentifier' => 'abc',
                'ccm_date_created' => 'not now',
                'ccm_date_modified' => 'not now',
                'site' => 'foo',
                'author_name' => 'author name',
                'foo' => 'Foo value',
                'bar' => 'BAR value',
                'baz' => 'Baz value',
            ]
        ], $writer->entries);
    }

    /**
     * Test an entry being exported that has attributes with mutlicolumn text export enabled
     */
    public function testMultiTextAttributeHeaders()
    {
        $controller = M::mock(AttributeTypeController::class, MulticolumnTextExportableAttributeInterface::class);
        $controller->shouldReceive('getAttributeTextRepresentationHeaders')->andReturn(['foo', 'bar', 'baz']);
        $controller->shouldReceive('getAttributeValueTextRepresentation')->andReturn(['Fooz', 'Barz', 'Bazz']);

        $keys = [
            'foo' => $this->attributeKey('foo', 'Foo', $controller),
        ];

        $values = [
            $this->attributeValue($keys['foo'], 'Foo value', $controller),
        ];

        list($entity, $list, $writer, $csvWriter) = $this->getCsvWriterMock($keys, $values);
        $csvWriter->insertHeaders($entity);
        $csvWriter->insertEntryList($list);

        $this->assertSame([
            'publicIdentifier' => 'publicIdentifier',
            'ccm_date_created' => 'dateCreated',
            'ccm_date_modified' => 'dateModified',
            'site' => 'site',
            'author_name' => 'authorName',
            'foo' => 'Foo',
            'foo.foo' => 'Foo - foo',
            'foo.bar' => 'Foo - bar',
            'foo.baz' => 'Foo - baz',
        ], $writer->headers);

        $this->assertSame([
            [
                'publicIdentifier' => 'abc',
                'ccm_date_created' => 'not now',
                'ccm_date_modified' => 'not now',
                'site' => 'foo',
                'author_name' => 'author name',
                'foo' => 'Foo value',
                'foo.foo' => 'Fooz',
                'foo.bar' => 'Barz',
                'foo.baz' => 'Bazz',
            ]
        ], $writer->entries);
    }

    private function attributeKey($handle, $name, $controller = null)
    {
        $mock = M::mock(ExpressKey::class);
        $mock->shouldReceive('getAttributeKeyHandle')->andReturn($handle);
        $mock->shouldReceive('getAttributeKeyDisplayName')->andReturn($name);
        $mock->shouldReceive('getController')->andReturn($controller);

        return $mock;
    }

    private function attributeValue($key, $plainValue, $controller = null)
    {
        $value = M::mock(ExpressValue::class);
        $value->shouldReceive('getAttributeKey')->andReturn($key);
        $value->shouldReceive('getPlainTextValue')->andReturn($plainValue);
        $value->shouldReceive('getController')->andReturn($controller);

        return $value;
    }

    /**
     * @param array $entityKeys
     * @param array $entryKeys
     * @return array
     */
    private function getCsvWriterMock(array $keys, array $values): array
    {
        $entity = M::mock(Entity::class);
        $entity->shouldReceive('getAttributes')->andReturn($keys);
        $entity->shouldReceive('getAssociations')->andReturn([]);

        $entry = M::mock(Entry::class);

        $entry->shouldReceive('getResultsNodeID')->andReturn(1122);

        $created = Carbon::now()->subDays(mt_rand(1, 10000));
        $userInfo = M::mock(UserInfo::class);
        $userInfo->shouldReceive('getUserDisplayName')->andReturn('author name');
        $author = M::mock(User::class);
        $author->shouldReceive('getUserInfoObject')->andReturn($userInfo);
        $entry->shouldReceive('getDateCreated')->andReturn($created);
        $entry->shouldReceive('getDateModified')->andReturn($created->copy()->addDays(20));
        $entry->shouldReceive('getAuthor')->andReturn($author);
        $entry->shouldReceive('getAttributes')->andReturn($values);
        $entry->shouldReceive('getPublicIdentifier')->andReturn('abc');
        $entry->shouldReceive('getAssociations')->andReturn([]);

        $list = M::mock(TestEntryList::class);
        $list->shouldReceive('deliverQueryObject')->andReturnSelf();
        $list->shouldReceive('execute')->andReturn([$entry]);
        $list->shouldReceive('getEntity')->andReturn($entity);
        $list->shouldReceive('getResult')->andReturnUsing(function ($arg) {
            return $arg;
        });

        $writer = M::mock(TestWriter::class);
        $writer->shouldReceive('insertOne')->passthru();
        $writer->shouldReceive('insertAll')->passthru();

        $dateFormatter = M::mock(Date::class);
        $dateFormatter->shouldReceive('formatCustom')->andReturn('not now');

        $entityManager = M::mock(EntityManager::class);

        $siteService = M::mock(Service::class);
        $site = M::mock(Site::class);
        $siteService->shouldReceive('getSiteByExpressResultsNodeID')->withArgs([1122])->andReturn($site);
        $site->shouldReceive('getSiteHandle')->andReturn('foo');

        $csvWriter = new CsvWriter($writer, $dateFormatter, $entityManager);
        // Mock the site service
        $csvWriter->setSiteService($siteService);
        return array($entity, $list, $writer, $csvWriter);
    }
}
