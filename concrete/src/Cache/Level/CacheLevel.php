<?php

namespace Concrete\Core\Cache\Level;

enum CacheLevel: string
{
    case Expensive = 'expensive';
    case Object = 'object';
    case Overrides = 'overrides';
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
            self::Request => null,
            default => "concrete.cache.levels.{$this->value}"
        };
    }

    /**
     * @return class-string
     */
    public function getCacheClass(): string
    {
        return match ($this) {
            default => \Concrete\Core\Cache\Level::class . '\\' . $this->name . 'Cache',
        };
    }
}
