<?php
namespace Concrete\Core\Cache\Block;

use Concrete\Core\Cache\OpCache;
use Concrete\Core\Application\Application;
use Concrete\Core\Application\ApplicationAwareInterface;

class CacheCleaner implements ApplicationAwareInterface
{

    /**
     * @var Application 
     */
    protected $app;
    
    
    /**
     * @param string|array $actionNames
     * @param string $btHandle
     */
    public function registerBlockType($actionNames, $btHandle)
    {
        $config = $this->app->make('config');

        $actionNames = is_array($actionNames) ? $actionNames : [$actionNames];
        foreach ($actionNames as $actionName) {
            $btList = $config->get('concrete.block_cache_cleaner.' . $actionName, []);
            $config->save('concrete.block_cache_cleaner.' . $actionName, $btList + [$btHandle]);
        }
    }

    /**
     * @param string $btHandle
     */
    public function unregisterBlockType($btHandle)
    {
        $config = $this->app->make('config');
        $registerItems = $config->get('concrete.block_cache_cleaner', []);

        foreach ($registerItems as $actionName => $btList) {
            if (in_array($btHandle, $btList)) {
                unset($btList[$btHandle]);
                $config->save('concrete.block_cache_cleaner.' . $actionName, $btList);
                break;
            }
        }
    }

    public function clear($actionName)
    {
        $config = $this->app->make('config');
        $btList = $config->get('concrete.block_cache_cleaner.' . $actionName, []);
        
        if (!empty($btList)) {
            $em = $this->app->make('Doctrine\ORM\EntityManager');
            $btRepo = $em->getRepository('\Concrete\Core\Entity\Block\BlockType\BlockType');
            foreach ($btList as $btHandle) {
                $bt = $btRepo->findOneBy(['btHandle' => $btHandle]);
                if (is_object($bt)) {
                    $bt->clearCache();
                }
            }
            OpCache::clear();
        }
    }

    public function setApplication(Application $application)
    {
        $this->app = $application;
    }
}
