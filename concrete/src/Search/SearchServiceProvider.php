<?php

namespace Concrete\Core\Search;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Search\Index\EntityIndex;
use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Search\Index\PageIndex;
use Concrete\Core\Search\Index\DefaultManager;
use Concrete\Core\Search\Index\IndexManagerInterface;

class SearchServiceProvider extends Provider
{

    /**
     * Registers the services provided by this provider.
     */
    public function register()
    {
        $this->app->singleton(DefaultManager::class, function($app) {
            $manager = new DefaultManager();
            $manager->addIndex(Page::class, PageIndex::class);
            $manager->addIndex(Entry::class, EntityIndex::class);
            return $manager;
        });
        $this->app->bindIf(IndexManagerInterface::class, DefaultManager::class);
    }

}
