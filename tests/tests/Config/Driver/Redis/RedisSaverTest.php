<?php

declare(strict_types=1);

namespace Concrete\Tests\Config\Driver\Redis;

use Concrete\Core\Config\Driver\Redis\RedisSaver;
use Concrete\Tests\TestCase;
use Mockery as M;

defined('C5_EXECUTE') or die('Access Denied.');

class RedisSaverTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        require_once __DIR__ . '/Fixtures/Redis.php';
    }

    /**
     * @dataProvider namespacesToTest
     */
    public function testSave(string $namespace): void
    {
        if (\PHP_MAJOR_VERSION < 8) {
            M::getConfiguration()->setInternalClassMethodParamMap('Redis', 'scan', [
                '&$iterator',
                '$pattern = null',
                '$count = null',
                '$type = null',
            ]);
        }
        $expectedSearch = 'cfg=' . $namespace . '::test.foo.*';
        $expectedBatch = 100;
        // The scan is performed until it returns an empty result: the first call returns a key, the second one stops the loop.
        $expectedIterators = [null, 1];
        $returnValues = [['cfg=' . $namespace . '::test.foo.test'], false];

        // Bind expectations
        $redis = M::mock(\Redis::class);
        $redis
            ->shouldReceive('scan')
            ->times(2)
            ->with(
                // The iterator is passed by reference: it's set by Redis, and it's used by the next call.
                M::on(static function (&$iterator) use (&$expectedIterators) {
                    $expected = array_shift($expectedIterators);
                    if ($iterator !== $expected) {
                        return false;
                    }
                    $iterator = head($expectedIterators);

                    return true;
                }),
                $expectedSearch,
                $expectedBatch
            )
            ->andReturnValues($returnValues)
        ;

        // Make sure we try to delete existing keys
        $redis
            ->shouldReceive('del')
            ->once()
            ->with([$namespace . '::test.foo', $namespace . '::test.foo.test'])
            ->andReturn(2)
        ;
        // Make sure we try to set the values as serialized
        $redis
            ->shouldReceive('mset')
            ->once()
            ->with([
                $namespace . '::test.foo.test' => serialize(10),
                $namespace . '::test.foo.subarray.subtest' => serialize(true),
                $namespace . '::test.foo.baz' => serialize('boo'),
            ])
            ->andReturn(true)
        ;

        // Run the method we're testing
        $saver = new RedisSaver($redis);
        $saved = $saver->save('foo', ['test' => 10, 'subarray' => ['subtest' => true], 'baz' => 'boo'], 'testing', 'test', $namespace);

        static::assertTrue($saved);
        static::assertSame([], $expectedIterators, 'The scan has not been performed as expected');
    }

    public static function namespacesToTest(): array
    {
        return [
            [''],
            ['core'],
            ['test'],
        ];
    }
}
