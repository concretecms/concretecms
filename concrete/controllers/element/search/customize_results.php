<?php
namespace Concrete\Controller\Element\Search;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Search\ProviderInterface;

class CustomizeResults extends ElementController
{
    /**
     * @var ProviderInterface
     */
    protected $provider;

    /**
     * @var Query
     */
    protected $query;

    protected $includeNumberOfResults = true;

    /**
     * @return boolean
     */
    public function includeNumberOfResults()
    {
        return $this->includeNumberOfResults;
    }

    /**
     * @param boolean $includeNumberOfResults
     */
    public function setIncludeNumberOfResults($includeNumberOfResults)
    {
        $this->includeNumberOfResults = $includeNumberOfResults;
    }

    public function __construct(ProviderInterface $provider, Query $query = null)
    {
        parent::__construct();
        $this->provider = $provider;
        $this->query = $query;
    }

    public function getElement()
    {
        return 'search/customize_results';
    }

    public function view()
    {
        $this->set('provider', $this->provider);
        $this->set('query', $this->query);
        $this->set('includeNumberOfResults', $this->includeNumberOfResults());
    }
}
