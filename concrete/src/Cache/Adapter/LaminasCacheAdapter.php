<?php

namespace Concrete\Core\Cache\Adapter;

use Laminas\Cache\Storage\Adapter\AbstractAdapter;
use Laminas\Cache\Storage\Adapter\AdapterOptions;
use Psr\Cache\CacheItemPoolInterface;

class LaminasCacheAdapter extends AbstractAdapter
{

    public function __construct(protected CacheItemPoolInterface $cache, array|AdapterOptions $options = null)
    {
        parent::__construct($options);
    }

    protected function normalizeKey(&$key): void
    {
        parent::normalizeKey($key);

        if (!preg_match('~^[^{}()/\\@:]+$~', $key)) {
            throw new \Laminas\Cache\Exception\InvalidArgumentException(
                "The key '{$key}' contains disallowed PSR-6 characters: '{}()/\\@:'"
            );
        }
    }

    protected function internalGetItem(&$normalizedKey, &$success = null, mixed &$casToken = null): mixed
    {
        $item = $this->cache->getItem($normalizedKey);
        if ($item->isHit()) {
            $success = true;
            return $item->get();
        }

        $success = false;
        return null;
    }

    protected function internalSetItem(&$normalizedKey, mixed &$value): bool
    {
        $ttl = $this->getOptions()->getTtl();
        $item = $this->cache->getItem($normalizedKey)->set($value);

        if ($ttl > 0) {
            $item = $item->expiresAfter($ttl);
        }

        return $this->cache->save($item);
    }

    protected function internalRemoveItem(&$normalizedKey): bool
    {
        return $this->cache->deleteItem($normalizedKey);
    }
}