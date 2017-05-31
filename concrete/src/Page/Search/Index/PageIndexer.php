<?php

namespace Concrete\Core\Page\Search\Index;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Page\Collection\Collection;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Search\IndexedSearch;
use Concrete\Core\Search\Index\Driver\IndexingDriverInterface;
use Concrete\Core\Search\Index\Driver\Iterator;

class PageIndexer implements IndexingDriverInterface, ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    /**
     * @var \Concrete\Core\Page\Search\IndexedSearch
     */
    private $search;

    /**
     * DefaultPageDriver constructor.
     * @param \Concrete\Core\Page\Search\IndexedSearch $search
     */
    public function __construct(IndexedSearch $search)
    {
        $this->search = $search;
    }

    /**
     * Add a page to the index
     * @param string|int|Page $page Page to index. String is path, int is cID
     * @return bool Success or fail
     */
    public function index($page)
    {
        if ($page = $this->getPage($page)) {
            if ($page->getVersionObject()) {
                return $page->reindex($this->search, true);
            }
        }

        return false;
    }

    /**
     * Remove a page from the index
     * @param string|int|Page $page. String is path, int is cID
     * @return bool Success or fail
     */
    public function forget($page)
    {
        if ($page = $this->getPage($page)) {
            /** @todo Implement forgetting pages completely */

            /** @var Connection $database */
            $database = $this->app['database']->connection();
            $database->executeQuery('DELETE FROM PageSearchIndex WHERE cID=?', [$page->getCollectionID()]);
        }

        return false;
    }

    /**
     * Get a page based on criteria
     * @param string|int|Page|Collection $page
     * @return \Concrete\Core\Page\Page
     */
    protected function getPage($page)
    {
        // Handle passed cID
        if (is_numeric($page)) {
            return Page::getByID($page);
        }

        // Handle passed /path/to/collection
        if (is_string($page)) {
            return Page::getByPath($page);
        }

        // If it's a page, just return the page
        if ($page instanceof Page) {
            return $page;
        }

        // If it's not a page but it's a collection, lets try getting a page by id
        if ($page instanceof Collection) {
            return $this->getPage($page->getCollectionID());
        }
    }

}
