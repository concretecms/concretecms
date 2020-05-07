<?php
namespace Concrete\Controller\Element\Files\Search;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\File\Search\SearchProvider;
use Concrete\Core\Foundation\Serializer\JsonSerializer;

class Search extends ElementController
{

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
    public function setQuery(Query $query): void
    {
        $this->query = $query;
    }


    public function view()
    {
        $this->set('form', $this->app->make('helper/form'));
        $this->set('token', $this->app->make('token'));
        if (isset($this->query)) {
            $this->set('query', $this->app->make(JsonSerializer::class)->serialize($this->query, 'json'));
        }
    }

}
