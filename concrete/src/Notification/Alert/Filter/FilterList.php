<?php
namespace Concrete\Core\Notification\Alert\Filter;

class FilterList
{

    /**
     * @var FilterInterface[]
     */
    protected $filters = [];

    /**
     * @param FilterInterface $filter
     */
    public function addFilter(FilterInterface $filter)
    {
        $this->filters[] = $filter;
    }

    /**
     * @return FilterInterface[]
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @return void
     */
    public function orderEntries()
    {
        usort(
            $this->filters,
            function ($a, $b) {
                return strcasecmp($a->getName(), $b->getName());
            }
        );
    }

    /**
     * @param string $key
     * @return FilterInterface|null
     */
    public function getFilterByKey($key)
    {
        foreach($this->filters as $filter) {
            if ($filter->getKey() == $key) {
                return $filter;
            }
        }
    }

}
