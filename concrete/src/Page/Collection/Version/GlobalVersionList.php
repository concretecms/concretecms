<?php
namespace Concrete\Core\Page\Collection\Version;

use Concrete\Core\Search\ItemList\Database\ItemList as DatabaseItemList;
use Concrete\Core\Search\StickyRequest;

/**
 * An object that holds a list of collection versions.
 *
 * If you need a list of versions of multiple collections / pages,
 * use this class. Otherwise use the VersionList class.
 */
class GlobalVersionList extends DatabaseItemList
{
    /**
     * @param \Concrete\Core\Search\StickyRequest|null $req
     */
    public function __construct(StickyRequest $req = null)
    {
        parent::__construct($req);
    }

    /**
     * @param array $queryRow
     *
     * @return \Concrete\Core\Page\Collection\Version\Version
     */
    public function getResult($queryRow)
    {
        $version = new Version();
        $version->setPropertiesFromArray($queryRow);

        return $version;
    }

    public function createQuery()
    {
        $this->query->select('cv.*')
            ->from('CollectionVersions', 'cv');
    }

    /**
     * @return int
     */
    public function getTotalResults()
    {
        $query = $this->deliverQueryObject();

        return (int) $query
            ->resetQueryParts(['groupBy', 'orderBy'])
            ->select('count(1)')
            ->setMaxResults(1)
            ->execute()
            ->fetchColumn();
    }

    /**
     * Filter versions that are approved after a certain date.
     *
     * @param \DateTime $date
     */
    public function filterByApprovedAfter(\DateTime $date)
    {
        $this->query->andWhere(
             'cv.cvDateApproved >= ' . $this->query->createNamedParameter($date->format('Y-m-d H:i-s'))
        );
    }

    /**
     * Filter versions that are approved before a certain date.
     *
     * @param \DateTime $date
     */
    public function filterByApprovedBefore(\DateTime $date)
    {
        $this->query->andWhere(
             'cv.cvDateApproved <= ' . $this->query->createNamedParameter($date->format('Y-m-d H:i-s'))
        );
    }

    /**
     * Sort by approval date in descending order.
     */
    public function sortByDateApprovedDesc()
    {
        $this->sortBy('cv.cvDateApproved', 'desc');
    }
}
