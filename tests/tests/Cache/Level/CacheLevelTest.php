<?php

declare(strict_types=1);

namespace Concrete\Tests\Cache\Level;

use Concrete\Core\Cache\Level;
use Concrete\Tests\TestCase;

class CacheLevelTest extends TestCase
{

    /**
     * @dataProvider configKeyProvider
     */
    public function testMethods(
        Level\CacheLevel $level,
        string $expectedClass,
        string|null $expectedEnable,
        string|null $expectedOptions
    ): void {
        $this->assertEquals($expectedClass, $level->getCacheClass());
        $this->assertEquals($expectedEnable, $level->getEnabledConfigKey());
        $this->assertEquals($expectedOptions, $level->getOptionsConfigKey());
    }

    protected function configKeyProvider(): array
    {
        return [
            [Level\CacheLevel::Expensive, Level\ExpensiveCache::class, 'concrete.cache.enabled', 'concrete.cache.levels.expensive'],
            [Level\CacheLevel::Object, Level\ObjectCache::class, null, 'concrete.cache.levels.object'],
            [Level\CacheLevel::Overrides, Level\OverridesCache::class, 'concrete.cache.overrides', 'concrete.cache.levels.overrides'],
            [Level\CacheLevel::Request, Level\RequestCache::class, null, null],
        ];
    }

}