<?php

namespace Concrete\Core\Page\Command;

use Concrete\Core\Attribute\Category\PageCategory;
use Concrete\Core\Cache\Page\PageCache;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Summary\Template\Populator;
use Concrete\Core\Search\Index\IndexManagerInterface;

class ReindexPageCommandHandler
{

    /**
     * @var PageCategory
     */
    protected $pageCategory;

    /**
     * @var IndexManagerInterface
     */
    protected $indexManager;

    /**
     * @var Populator
     */
    protected $populator;

    public function __construct(Populator $populator, PageCategory $pageCategory, IndexManagerInterface $indexManager)
    {
        $this->populator = $populator;
        $this->pageCategory = $pageCategory;
        $this->indexManager = $indexManager;
    }

    public function __invoke($command)
    {
        $c = Page::getByID($command->getPageID(), 'ACTIVE');
        if ($c && !$c->isError()) {
            // reindex page attributes
            $indexer = $this->pageCategory->getSearchIndexer();
            $values = $this->pageCategory->getAttributeValues($c);
            foreach ($values as $value) {
                $indexer->indexEntry($this->pageCategory, $value, $c);
            }

            // clear page cache
            $cache = PageCache::getLibrary();
            $cache->purge($c);

            // Populate summary templates
            $this->populator->updateAvailableSummaryTemplates($c);

            // Reindex page content.
            $this->indexManager->index(Page::class, $command->getPageID());
        }
    }


}