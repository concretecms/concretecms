<?php

namespace Concrete\Core\User\Point;

use Concrete\Core\Search\ItemList\Database\ItemList as DatabaseItemList;
use Concrete\Core\Search\Pagination\Pagination;
use Concrete\Core\User\Point\Entry as UserPointEntry;
use Pagerfanta\Adapter\DoctrineDbalAdapter;

class EntryList extends DatabaseItemList
{
    protected $autoSortColumns = ['u.uName', 'upa.upaName', 'uph.upPoints', 'uph.timestamp'];

    /**
     * @param int $gID
     */
    public function filterByGroupID($gID)
    {
        $this->query->andWhere('upa.gBadgeID = :gBadgeID');
        $this->query->setParameter('gBadgeID', $gID);
    }

    /**
     * @param string $uName
     */
    public function filterByUserName($uName)
    {
        $this->query->andWhere('u.uName = :uName');
        $this->query->setParameter('uName', $uName);
    }

    /**
     * @param string $upaName
     */
    public function filterByUserPointActionName($upaName)
    {
        $this->query->andWhere($this->query->expr()->like('upa.upaName', ':upaName'));
        $this->query->setParameter('upaName', '%' . $upaName . '%');
    }

    /**
     * @param int $uID
     */
    public function filterByUserID($uID)
    {
        $this->query->andWhere('uph.upuID = :upuID');
        $this->query->setParameter('upuID', (int) $uID);
    }

    /**
     * @param $queryRow
     *
     * @return UserPointEntry
     */
    public function getResult($queryRow)
    {
        $up = new UserPointEntry();
        $up->load($queryRow['upID']);

        return $up;
    }

    /**
     * The total results of the query.
     *
     * @return int
     */
    public function getTotalResults()
    {
        $query = $this->deliverQueryObject();

        return $query->resetQueryParts(['groupBy', 'orderBy'])->select('count(distinct uph.upID)')->setMaxResults(1)->execute()->fetchColumn();
    }

    public function createQuery()
    {
        $db = $this->query->getConnection();
        $this->query->select('uph.upID')
            ->from('UserPointHistory', 'uph')
            ->leftJoin('uph', 'UserPointActions', 'upa', 'uph.upaID = upa.upaID')
            ->leftJoin('upa', $db->getDatabasePlatform()->quoteSingleIdentifier('Groups'), 'g', 'upa.gBadgeID = g.gID')
            ->leftJoin('uph', 'Users', 'u', 'uph.upuID = u.uID')
        ;
    }

    /**
     * Gets the pagination object for the query.
     *
     * @return Pagination
     */
    public function createPaginationObject()
    {
        $adapter = new DoctrineDbalAdapter($this->deliverQueryObject(), function ($query) {
            $query->resetQueryParts(['groupBy', 'orderBy'])->select('count(distinct uph.upID)')->setMaxResults(1);
        });

        return new Pagination($this, $adapter);
    }
}
