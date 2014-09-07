<?php

namespace Concrete\Core\Cache;


use Doctrine\Common\Cache\CacheProvider;
use Core;

/**
 * Simple cache driver that enables doctrine to use c5's caching library
 * @package Concrete\Core\Cache
 */
class DoctrineCacheDriver extends CacheProvider
{
    /** @var Cache  */
    private $c5Cache = null;

    /**
     * @return Cache
     */
    private function getC5Cache()
    {
        if ($this->c5Cache === null) {
            $this->c5Cache = Core::make('cache');
        }
        
        return $this->c5Cache;
    }
    
    /**
     * @inheritdoc
     */
    protected function doFetch($id)
    {
        $item = $this->getC5Cache()->getItem('doctrine/' . $id);
        return $item->isMiss() ? false : $item->get();
    }

    /**
     * @inheritdoc
     */
    protected function doContains($id)
    {
        return $this->getC5Cache()->exists('doctrine/' . $id);
    }

    /**
     * @inheritdoc
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        if ($lifeTime === 0) {
            $lifeTime = null;
        }
        return $this->getC5Cache()->getItem('doctrine/' . $id)->set($data, $lifeTime);
    }

    /**
     * @inheritdoc
     */
    protected function doDelete($id)
    {
        return $this->getC5Cache()->delete('doctrine/' . $id);
    }

    /**
     * @inheritdoc
     */
    protected function doFlush()
    {
        return $this->getC5Cache()->flush();
    }

    /**
     * @inheritdoc
     */
    protected function doGetStats()
    {
        return null;
    }
}