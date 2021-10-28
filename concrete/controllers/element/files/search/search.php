<?php
namespace Concrete\Controller\Element\Files\Search;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\File\Search\SearchProvider;
use Concrete\Core\Foundation\Serializer\JsonSerializer;

class Search extends ElementController
{

    /**
     * This is where the header search bar in the page should point. This search bar allows keyword searching in
     * different contexts. Valid options are `view` and `folder`.
     *
     * @var string
     */
    protected $headerSearchAction;

    /**
     * @var Query
     */
    protected $query;

    public function getElement()
    {
        return 'files/search/search';
    }

    /**
     * @param Query $query
     */
    public function setQuery(Query $query = null): void
    {
        $this->query = $query;
    }

    /**
     * @param string $headerSearchAction
     */
    public function setHeaderSearchAction(string $headerSearchAction): void
    {
        $this->headerSearchAction = $headerSearchAction;
    }

    public function view()
    {
        $this->set('form', $this->app->make('helper/form'));
        $this->set('token', $this->app->make('token'));
        if (isset($this->headerSearchAction)) {
            $this->set('headerSearchAction', $this->headerSearchAction);
        } else {
            $this->set('headerSearchAction', $this->app->make('url')->to('/dashboard/files/search'));
        }
        if (isset($this->query)) {
            $this->set('query', $this->app->make(JsonSerializer::class)->serialize($this->query, 'json'));
        }
    }

}
