<?php

namespace Concrete\Core\Cache\Level;

enum CacheLevel: string
{
    case Expensive = 'expensive';
    case Object = 'object';
    case Overrides = 'overrides';
    case Pages = 'pages';
    case Request = 'request';

    public function getEnabledConfigKey(): string|null
    {
        return match ($this) {
            self::Expensive => 'concrete.cache.enabled',
            self::Request, self::Object => null,
            default => "concrete.cache.{$this->value}",
        };
    }

    public function getOptionsConfigKey(): string|null
    {
        return match ($this) {
            default => "concrete.cache.levels.{$this->value}"
        };
    }

    /**
     * @return class-string
     */
    public function getCacheClass(): string
    {
        return match ($this) {
            self::Pages => PageCache::class,
            default => \Concrete\Core\Cache\Level::class . '\\' . $this->name . 'Cache',
        };
    }
}
