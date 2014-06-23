<?php
namespace Concrete\Core\Pagination;
class FuzzyPagination extends Pagination
{

    const TOTAL_RESULTS_UNKNOWN = -1;

    public function getTotalResults()
    {
        return self::TOTAL_RESULTS_UNKNOWN;
    }

    public function getTotalPages()
    {
        return self::TOTAL_RESULTS_UNKNOWN;
    }

    protected function unmodifiedHasNextPage()
    {
        return parent::hasNextPage();
    }

    public function getCurrentPageResults()
    {
        $results = parent::getCurrentPageResults();

        // now, in fuzzy-land, our $results for current page MAY be fewer than our the typical total results
        // per page, but we may still have results on the next page.
        if (count($results) < $this->getMaxPerPage() && $this->unmodifiedHasNextPage()) {
            // we just keep paging through til we get to the max land.
            $hasNextPage = $this->unmodifiedHasNextPage();
            $currentPage = $this->getCurrentPage();
            while(count($results) < $this->getMaxPerPage() && $hasNextPage) {
                $this->setCurrentPage()
            }
        }


        // now we rewind.
        $this->setCurrentPage($currentPage);
    }

} 