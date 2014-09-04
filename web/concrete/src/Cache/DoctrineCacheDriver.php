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
    private $c5Cache = null;
    
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
        return $this->getC5Cache()->get('doctrine', $id);
    }

    /**
     * @inheritdoc
     */
    protected function doContains($id)
    {
        return $this->getC5Cache()->has('doctrine', 'id');
    }

    /**
     * @inheritdoc
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        if ($lifeTime === 0) {
            $lifeTime = false;
        }
        return $this->getC5Cache()->set('doctrine', $id, $data, $lifeTime);
    }

    /**
     * @inheritdoc
     */
    protected function doDelete($id)
    {
        return $this->getC5Cache()->delete('doctrine', $id);
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