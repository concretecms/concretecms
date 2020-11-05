<?php

namespace Concrete\Core\User\Point\Action;

use Concrete\Core\Search\ItemList\Database\ItemList as DatabaseItemList;
use Concrete\Core\Search\Pagination\Pagination;
use Pagerfanta\Adapter\DoctrineDbalAdapter;

class ActionList extends DatabaseItemList
{
    protected $autoSortColumns = ['upa.upaName', 'upa.upaHandle', 'upa.upaDefaultPoints', 'upa.gBadgeID'];

    public function createQuery()
    {
        $db = $this->query->getConnection();
        $this->query->select('upa.*', 'g.gName')
            ->from('UserPointActions', 'upa')
            ->leftJoin('upa', $db->getDatabasePlatform()->quoteSingleIdentifier('Groups'), 'g', 'upa.gBadgeID = g.gID')
        ;
    }

    public function filterByIsActive($active)
    {
        $this->query->andWhere('upa.upaIsActive = :upaIsActive');
        $this->query->setParameter('upaIsActive', $active);
    }

    /**
     * The total results of the query.
     *
     * @return int
     */
    public function getTotalResults()
    {
        $query = $this->deliverQueryObject();

        return $query->resetQueryParts(['groupBy', 'orderBy'])->select('count(distinct upa.upaID)')->setMaxResults(1)->execute()->fetchColumn();
    }

    /**
     * Gets the pagination object for the query.
     *
     * @return Pagination
     */
    public function createPaginationObject()
    {
        $adapter = new DoctrineDbalAdapter($this->deliverQueryObject(), function ($query) {
            $query->resetQueryParts(['groupBy', 'orderBy'])->select('count(distinct upa.upaID)')->setMaxResults(1);
        });

        return new Pagination($this, $adapter);
    }

    public function getResult($mixed)
    {
        return $mixed;
    }
}
