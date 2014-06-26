<?php
namespace Concrete\Core\Page;
use Concrete\Core\Search\DatabaseItemList;
use Concrete\Core\Search\Pagination\Pagination;
use Concrete\Core\Search\Pagination\PermissionablePagination;
use Page as ConcretePage;
use Pagerfanta\Adapter\DoctrineDbalAdapter;

/**
*
* An object that allows a filtered list of pages to be returned.
*
*/
class PageList extends DatabaseItemList
{

    /** @var  \Closure | integer | null */
    protected $permissionsChecker;

    /**
     * Columns in this array can be sorted via the request.
     * @var array
     */
    protected $autoSortColumns = array('cv.cvName', 'cv.cvDatePublic', 'c.cDateAdded', 'c.cDateModified');

    protected $attributeClass = 'FileAttributeKey';

    /**
     * Whether to include system pages (login, etc...) in this query.
     * @var bool
     */
    protected $includeSystemPages = false;

    /**
     * Whether to include inactive (deleted) pages in the query.
     * @var bool
     */
    protected $includeInactivePages = false;

    public function setPermissionsChecker(\Closure $checker)
    {
        $this->permissionsChecker = $checker;
    }

    public function ignorePermissions()
    {
        $this->permissionsChecker = -1;
    }

    public function includeInactivePages()
    {
        $this->includeInactivePages = true;
    }

    public function includeSystemPages()
    {
        $this->includeSystemPages = true;
    }

    public function createQuery()
    {
        $this->query->select('p.cID')
            ->from('Pages', 'p')
            ->innerJoin('p', 'Collections', 'c', 'p.cID = c.cID')
            ->innerJoin('c', 'CollectionVersions', 'cv', 'c.cID = cv.cID and cvIsApproved = 1')
            ->leftJoin('c', 'CollectionSearchIndexAttributes', 'csi', 'c.cID = csi.cID')
            ->leftJoin('p', 'PageTypes', 'pt', 'p.ptID = pt.ptID')
            ->leftJoin('p', 'PageSearchIndex', 'ps', 'p.cID = ps.cID');
    }

    public function getTotalResults()
    {
        if ($this->permissionsChecker == -1) {
            $query = $this->deliverQueryObject();
            return $query->select('count(distinct c.cID)')->setMaxResults(1)->execute()->fetchColumn();
        } else {
            return -1; // unknown
        }
    }

    public function finalizeQuery(\Doctrine\DBAL\Query\QueryBuilder $query)
    {
        if (!$this->includeInactivePages) {
            $query->andWhere('p.cIsActive = :cIsActive');
            $query->setParameter('cIsActive', true);
        }
        if (!$this->includeSystemPages) {
            $query->andWhere('p.cIsSystemPage = :cIsSystemPage');
            $query->setParameter('cIsSystemPage', false);
        }
        return $query;
    }

    protected function createPaginationObject()
    {
        if ($this->permissionsChecker == -1) {
            $adapter = new DoctrineDbalAdapter($this->deliverQueryObject(), function($query) {
                $query->select('count(distinct c.cID)')->setMaxResults(1);
            });
            $pagination = new Pagination($this, $adapter);
        } else {
            $pagination = new PermissionablePagination($this);
        }
        return $pagination;
    }

    /**
     * @param $queryRow
     * @return \Concrete\Core\File\File
     */
    public function getResult($queryRow)
    {
        $c = ConcretePage::getByID($queryRow['cID']);
        if (is_object($c) && $this->checkPermissions($c)) {
            return $c;
        }
    }

    public function checkPermissions($mixed)
    {
        if (isset($this->permissionsChecker)) {
            if ($this->permissionsChecker === -1) {
                return true;
            } else {
                return call_user_func_array($this->permissionsChecker, array($mixed));
            }
        }

        $fp = new \Permissions($mixed);
        return $fp->canViewPage();
    }

    /**
     * Filters by type of collection (using the handle field)
     * @param mixed $ptHandle
     */
    public function filterByPageTypeHandle($ptHandle)
    {
        $db = \Database::get();
        if (is_array($ptHandle)) {
            $this->query->andWhere(
                $this->query->expr()->in('ptHandle', array_map(array($db, 'quote'), $ptHandle))
            );
        } else {
            $this->query->andWhere('pt.ptHandle = :ptHandle');
            $this->query->setParameter('ptHandle', $ptHandle);
        }
    }

    /**
     * Filters by parent ID
     * @param array | integer $cParentID
     */
    public function filterByParentID($cParentID)
    {
        $db = \Database::get();
        if (is_array($cParentID)) {
            $this->query->andWhere(
                $this->query->expr()->in('p.cParentID', array_map(array($db, 'quote'), $cParentID))
            );
        } else {
            $this->query->andWhere('p.cParentID = :cParentID');
            $this->query->setParameter('cParentID', $cParentID, \PDO::PARAM_INT);
        }
    }

    public function filterByKeywords($keywords)
    {
        $expressions = array(
            $this->query->expr()->like('ps.cName', ':keywords'),
            $this->query->expr()->like('ps.cDescription', ':keywords'),
            $this->query->expr()->like('ps.content', ':keywords')
        );

        $keys = \CollectionAttributeKey::getSearchableIndexedList();
        foreach ($keys as $ak) {
            $cnt = $ak->getController();
            $expressions[] = $cnt->searchKeywords($keywords, $this->query);
        }
        $expr = $this->query->expr();
        $this->query->andWhere(call_user_func_array(array($expr, 'orX'), $expressions));
        $this->query->setParameter('keywords', '%' . $keywords . '%');
    }

    /**
     * Sorts by ID in ascending order.
     */
    public function sortByCollectionIDAscending()
    {
        $this->query->orderBy('p.cID', 'asc');
    }

    /**
     * Sorts by name in ascending order.
     */
    public function sortByName()
    {
        $this->query->orderBy('cv.cvName', 'asc');
    }


    public function __call($nm, $a)
    {
        if (substr($nm, 0, 8) == 'filterBy') {
            $handle = uncamelcase(substr($nm, 8));
            if (count($a) == 2) {
                $this->filterByAttribute($attrib, $a[0], $a[1]);
            } else {
                $this->filterByAttribute($attrib, $a[0]);
            }
        }
        if (substr($nm, 0, 6) == 'sortBy') {
            $handle = uncamelcase(substr($nm, 6));
            if (count($a) == 1) {
                $this->sortBy($attrib, $a[0]);
            }
            else {
                $this->sortBy($attrib);
            }
        }
    }

    /**
     * @deprecated
     */
    public function filterByCollectionTypeHandle($ctHandle) {
        $this->filterByPageTypeHandle($ctHandle);
    }

}
