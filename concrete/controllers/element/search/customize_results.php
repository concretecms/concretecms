<?php
namespace Concrete\Controller\Element\Search;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Search\ProviderInterface;

/**
 * @since 8.0.0
 */
class CustomizeResults extends ElementController
{
    protected $provider;

    /**
     * @since 8.2.0
     */
    protected $includeNumberOfResults = true;

    /**
     * @return boolean
     * @since 8.2.0
     */
    public function includeNumberOfResults()
    {
        return $this->includeNumberOfResults;
    }

    /**
     * @param boolean $includeNumberOfResults
     * @since 8.2.0
     */
    public function setIncludeNumberOfResults($includeNumberOfResults)
    {
        $this->includeNumberOfResults = $includeNumberOfResults;
    }

    public function __construct(ProviderInterface $provider)
    {
        parent::__construct();
        $this->provider = $provider;
    }

    public function getElement()
    {
        return 'search/customize_results';
    }

    public function view()
    {
        $this->set('provider', $this->provider);
        $this->set('includeNumberOfResults', $this->includeNumberOfResults());
    }
}
