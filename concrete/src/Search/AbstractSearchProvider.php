<?php
namespace Concrete\Core\Search;

use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Search\Column\AttributeKeyColumn;
use Concrete\Core\Search\Column\Set;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Search\Result\ResultFactory;
use Symfony\Component\HttpFoundation\Session\Session;
use Concrete\Core\Search\Query\QueryableInterface;

abstract class AbstractSearchProvider implements ProviderInterface, SessionQueryProviderInterface, QueryableInterface
{
    protected $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function setSessionCurrentQuery(Query $query)
    {
        $this->session->set('search/' . $this->getSessionNamespace() . '/query', $query);
    }

    public function clearSessionCurrentQuery()
    {
        $this->session->remove('search/' . $this->getSessionNamespace() . '/query');
    }

    public function getAllColumnSet()
    {
        $columnSet = $this->getBaseColumnSet();
        foreach ($this->getAvailableColumnSet()->getColumns() as $column) {
            $columnSet->addColumn($column);
        }
        foreach ($this->getCustomAttributeKeys() as $ak) {
            $column = new AttributeKeyColumn($ak);
            if (!$columnSet->contains($column)) {
                $columnSet->addColumn($column);
            }
        }

        return $columnSet;
    }

    public function getSessionCurrentQuery()
    {
        $variable = 'search/' . $this->getSessionNamespace() . '/query';
        if ($this->session->has($variable)) {
            return $this->session->get($variable);
        }
    }

    public function getSearchResultFromQuery(Query $query)
    {
        return app(ResultFactory::class)->createFromQuery($this, $query);
    }

    /**
     * Gets items per page from the current preset or from the session.
     *
     * @return int
     */
    public function getItemsPerPage()
    {
        $sessionQuery = $this->getSessionCurrentQuery();
        if ($sessionQuery instanceof Query) {
            return $sessionQuery->getItemsPerPage();
        }
    }

    /**
     * @return array
     */
    public function getItemsPerPageOptions()
    {
        return [10, 25, 50, 100];
    }
}
