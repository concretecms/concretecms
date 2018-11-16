<?php

namespace Concrete\Tests\Express\Export\EntryList;

use Carbon\Carbon;
use Concrete\Core\Entity\Attribute\Key\ExpressKey;
use Concrete\Core\Entity\Attribute\Value\ExpressValue;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Express\Export\EntryList\CsvWriter;
use Concrete\Core\Localization\Service\Date;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use League\Csv\Writer;
use Mockery as M;

class CsvWriterTest extends \PHPUnit_Framework_TestCase
{

    public function testCsvValueOrder()
    {
        $entityKeys = [
            'foo' => $this->attributeKey('foo', 'Foo'),
            'bar' => $this->attributeKey('bar', 'Bar'),
            'baz' => $this->attributeKey('baz', 'Baz'),
        ];

        $entryKeys = [
            $this->attributeValue($entityKeys['bar'], 'BAR value'),
            $this->attributeValue($entityKeys['baz'], 'Baz value'),
            $this->attributeValue($entityKeys['foo'], 'Foo value'),
        ];

        $entity = M::mock(Entity::class);
        $entity->shouldReceive('getAttributes')->andReturn($entityKeys);

        $entry = M::mock(Entry::class);

        $created = Carbon::now()->subDays(mt_rand(1, 10000));
        $entry->shouldReceive('getDateCreated')->andReturn($created);
        $entry->shouldReceive('getAttributes')->andReturn($entryKeys);

        $list = M::mock(TestEntryList::class);
        $list->shouldReceive('deliverQueryObject')->andReturnSelf();
        $list->shouldReceive('execute')->andReturn([$entry]);
        $list->shouldReceive('getEntity')->andReturn($entity);
        $list->shouldReceive('getResult')->andReturnUsing(function($arg) {
            return $arg;
        });

        $writer = M::mock(TestWriter::class);
        $writer->shouldReceive('insertOne')->passthru();
        $writer->shouldReceive('insertAll')->passthru();

        $dateFormatter = M::mock(Date::class);
        $dateFormatter->shouldReceive('formatCustom')->andReturn('not now');

        $csvWriter = new CsvWriter($writer, $dateFormatter);
        $csvWriter->insertHeaders($entity);
        $csvWriter->insertEntryList($list);

        $this->assertSame([
            'ccm_date_created' => 'dateCreated',
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz',
        ], $writer->headers);

        $this->assertSame([
            [
                'ccm_date_created' => 'not now',
                'foo' => 'Foo value',
                'bar' => 'BAR value',
                'baz' => 'Baz value',
            ]
        ], $writer->entries);
    }

    private function attributeKey($handle, $name)
    {
        $mock = M::mock(ExpressKey::class);
        $mock
            ->shouldReceive('getAttributeKeyHandle')->andReturn($handle)
            ->shouldReceive('getAttributeKeyDisplayName')->andReturn($name);

        return $mock;
    }

    private function attributeValue($key, $plainValue)
    {
        $value = M::mock(ExpressValue::class);
        $value->shouldReceive('getAttributeKey')->andReturn($key);
        $value->shouldReceive('getPlainTextValue')->andReturn($plainValue);

        return $value;
    }
}