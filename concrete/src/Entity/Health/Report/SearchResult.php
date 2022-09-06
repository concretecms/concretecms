<?php

namespace Concrete\Core\Entity\Health\Report;
use Concrete\Core\Health\Report\Result\Formatter\FormatterInterface;
use Concrete\Core\Health\Report\Result\Formatter\SearchResultFormatter;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="HealthReportSearchResults")
 */
class SearchResult extends Result
{

    const TYPE_KEYWORDS = 'k';
    const TYPE_TAG = 't';

    /**
     * @ORM\Column(type="string")
     */
    protected $searchString;

    /**
     *
     * @ORM\Column(type="string", length="1")
     */
    protected $searchType;

    public function __construct(string $searchType, string $searchString)
    {
        $this->searchString = $searchString;
        $this->searchType = $searchType;
    }

    /**
     * @return mixed
     */
    public function getSearchString()
    {
        return $this->searchString;
    }

    /**
     * @param mixed $searchString
     */
    public function setSearchString($searchString): void
    {
        $this->searchString = $searchString;
    }

    /**
     * @return mixed
     */
    public function getSearchType()
    {
        return $this->searchType;
    }

    /**
     * @param mixed $searchType
     */
    public function setSearchType($searchType): void
    {
        $this->searchType = $searchType;
    }

    public function getFormatter(): FormatterInterface
    {
        return new SearchResultFormatter();
    }


}
