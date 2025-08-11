<?php
namespace Concrete\Core\File\Component\Chooser;

class FilterCollection implements FilterCollectionInterface
{

    /**
     * @var FilterInterface[]
     */
    protected $filters = [];

    /**
     * @return FilterInterface[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    public function addFilter(FilterInterface $filter)
    {
        $this->filters[] = $filter;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->filters;
    }



}