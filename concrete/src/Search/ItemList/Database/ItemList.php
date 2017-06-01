<?php
namespace Concrete\Core\Search\ItemList\Database;

use Concrete\Core\Search\ItemList\ItemList as AbstractItemList;
use Concrete\Core\Search\StickyRequest;
use Database;
use Doctrine\DBAL\Logging\EchoSQLLogger;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Base class for all the list-related classes that use the database.
 */
abstract class ItemList extends AbstractItemList
{
    /**
     * The Doctrine QueryBuilder instance.
     *
     * @var QueryBuilder
     */
    protected $query;

    /**
     * The (optional) StickyRequest instance to use to get the list state.
     *
     * @var StickyRequest|null
     */
    protected $searchRequest;

    /**
     * Initialize the QueryBuilder instance stored as $this->query.
     */
    abstract public function createQuery();

    /**
     * Initialize this instance.
     *
     * @param StickyRequest $req the (optional) StickyRequest instance to use to get the list state
     */
    public function __construct(StickyRequest $req = null)
    {
        $this->query = Database::get()->createQueryBuilder();
        $this->searchRequest = $req;
        $this->createQuery();
    }

    /**
     * Permorm operations right after the instance has been cloned.
     */
    public function __clone()
    {
        $this->query = clone $this->query;
    }

    /**
     * Get the current Doctrine QueryBuilder instance.
     *
     * @return QueryBuilder
     */
    public function getQueryObject()
    {
        return $this->query;
    }

    /**
     * Setup sorting (inspecting the StickyRequest or the current query string parameters) and builds a finalized clone of the query.
     *
     * @return QueryBuilder
     */
    public function deliverQueryObject()
    {
        // setup the default sorting based on the request.
        $this->setupAutomaticSorting($this->searchRequest);
        $query = clone $this->query;
        $query = $this->finalizeQuery($query);

        return $query;
    }

    /**
     * Override this method to setup a QueryBuilder instance right before executing it.
     *
     * @param QueryBuilder $query a clone of the $query property of the class
     *
     * @return QueryBuilder
     */
    public function finalizeQuery(QueryBuilder $query)
    {
        return $query;
    }

    /**
     * {@inheritdoc}
     *
     * @see AbstractItemList::executeGetResults()
     */
    public function executeGetResults()
    {
        return $this->deliverQueryObject()->execute()->fetchAll();
    }

    /**
     * {@inheritdoc}
     *
     * @see AbstractItemList::executeSortBy()
     */
    protected function executeSortBy($column, $direction = 'asc')
    {
        if (in_array(strtolower($direction), ['asc', 'desc'])) {
            $this->query->orderBy($column, $direction);
        } else {
            throw new \Exception(t('Invalid SQL in order by'));
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see AbstractItemList::executeSanitizedSortBy()
     */
    protected function executeSanitizedSortBy($column, $direction = 'asc')
    {
        if (preg_match('/[^0-9a-zA-Z\$\.\_\x{0080}-\x{ffff}]+/u', $column) === 0) {
            $this->executeSortBy($column, $direction);
        } else {
            throw new \Exception(t('Invalid SQL in order by'));
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see AbstractItemList::debugStart()
     */
    public function debugStart()
    {
        if ($this->isDebugged()) {
            Database::get()->getConfiguration()->setSQLLogger(new EchoSQLLogger());
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see AbstractItemList::debugStop()
     */
    public function debugStop()
    {
        if ($this->isDebugged()) {
            Database::get()->getConfiguration()->setSQLLogger(null);
        }
    }

    /**
     * @deprecated
     */
    public function filter($field, $value, $comparison = '=')
    {
        if ($field == false) {
            $this->query->andWhere($value); // ugh
        } else {
            $this->query->andWhere(implode(' ', [
                $field, $comparison, $this->query->createNamedParameter($value),
            ]));
        }
    }
}
