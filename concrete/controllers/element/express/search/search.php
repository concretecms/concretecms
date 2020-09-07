<?php

namespace Concrete\Controller\Element\Express\Search;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\File\Search\SearchProvider;
use Concrete\Core\Foundation\Serializer\JsonSerializer;

class Search extends ElementController
{

    /**
     * This is where the header search bar in the page should point. This search bar allows keyword searching in
     * different contexts.
     *
     * @var string
     */
    protected $headerSearchAction;

    /**
     * @var Entity
     */
    protected $entity;

    /**
     * @var Query
     */
    protected $query;

    public function getElement()
    {
        return 'express/search/search';
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

    /**
     * @param Entity $entity
     */
    public function setEntity(Entity $entity): void
    {
        $this->entity = $entity;
    }

    public function view()
    {
        $this->set('entity', $this->entity);
        $this->set('form', $this->app->make('helper/form'));
        $this->set('token', $this->app->make('token'));
        if (isset($this->headerSearchAction)) {
            $this->set('headerSearchAction', $this->headerSearchAction);
        } else {
            $this->set(
                'headerSearchAction',
                $this->app->make('url')->to('/dashboard/express/entries/', 'view', $this->entity->getId())
            );
        }
        if (isset($this->query)) {
            $this->set('query', $this->app->make(JsonSerializer::class)->serialize($this->query, 'json'));
        }
    }

}
