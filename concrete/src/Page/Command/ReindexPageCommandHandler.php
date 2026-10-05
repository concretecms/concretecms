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
            $presentKeyIds = [];
            foreach ($values as $value) {
                $presentKeyIds[$value->getAttributeKey()->getAttributeKeyID()] = true;
                $indexer->indexEntry($this->pageCategory, $value, $c);
            }
            foreach ($this->pageCategory->getSearchableList() as $key) {
                if (!isset($presentKeyIds[$key->getAttributeKeyID()])) {
                    $indexer->clearIndexEntryForAttributeKey($this->pageCategory, $key, $c);
                }
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