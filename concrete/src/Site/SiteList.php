<?php
namespace Concrete\Core\Site;

use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Search\ItemList\Database\AttributedItemList as DatabaseItemList;
use Concrete\Core\Search\PermissionableListItemInterface;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Concrete\Core\Search\Pagination\Pagination;

class SiteList extends DatabaseItemList
{

    public function filterBySiteTypeHandle($siteTypeHandle)
    {
        $this->query->join('s', 'SiteTypes', 'st', 's.siteTypeID = st.siteTypeID');
        $this->query->andWhere('siteTypeHandle = :siteTypeHandle');
        $this->query->setParameter('siteTypeHandle', $siteTypeHandle);
    }

    protected function getAttributeKeyClassName()
    {
        return '\\Concrete\\Core\\Attribute\\Key\\SiteKey';
    }

    /**
     * The total results of the query.
     *
     * @return int
     */
    public function getTotalResults()
    {
        $query = $this->deliverQueryObject();
        return $query->resetQueryParts(['groupBy', 'orderBy'])->select('count(distinct s.siteID)')->setMaxResults(1)->execute()->fetchColumn();
    }

    /**
     * Gets the pagination object for the query.
     *
     * @return Pagination
     */
    protected function createPaginationObject()
    {
        $adapter = new DoctrineDbalAdapter($this->deliverQueryObject(), function ($query) {
            $query->resetQueryParts(['groupBy', 'orderBy'])->select('count(distinct s.siteID)')->setMaxResults(1);
        });
        $pagination = new Pagination($this, $adapter);

        return $pagination;
    }

    public function getResult($queryRow)
    {
        $service = \Core::make('site');
        $entry = $service->getByID($queryRow['siteID']);
        return $entry;
    }

    public function createQuery()
    {
        $this->query->select('s.siteID')
            ->from('Sites', 's')
            ->leftJoin('s', 'SiteSearchIndexAttributes', 'sa', 's.siteID = sa.siteID');
    }

}
