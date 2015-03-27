<?php
namespace Concrete\Core\Page;

use Concrete\Core\Search\ItemList\Database\AttributedItemList as DatabaseItemList;
use Concrete\Core\Search\Pagination\Pagination;
use Concrete\Core\Search\Pagination\PermissionablePagination;
use Page as ConcretePage;
use Concrete\Core\Search\PermissionableListItemInterface;
use Pagerfanta\Adapter\DoctrineDbalAdapter;

/**
 *
 * An object that allows a filtered list of pages to be returned.
 *
 */
class PageList extends DatabaseItemList implements PermissionableListItemInterface
{

    const PAGE_VERSION_ACTIVE = 1;
    const PAGE_VERSION_RECENT = 2;

    protected function getAttributeKeyClassName()
    {
        return '\\Concrete\\Core\\Attribute\\Key\\CollectionKey';
    }

    /** @var  \Closure | integer | null */
    protected $permissionsChecker;

    /**
     * Columns in this array can be sorted via the request.
     * @var array
     */
    protected $autoSortColumns = array('cv.cvName', 'cv.cvDatePublic', 'c.cDateAdded', 'c.cDateModified');

    /**
     * Which version to attempt to retrieve.
     * @var int
     */
    protected $pageVersionToRetrieve = self::PAGE_VERSION_ACTIVE;

    /**
     * Whether this is a search using fulltext.
     */
    protected $isFulltextSearch = false;

    /**
     * Whether to include system pages (login, etc...) in this query.
     * @var bool
     */
    protected $includeSystemPages = false;

    /**
     * Whether to include aliases in the result set.
     */
    protected $includeAliases = false;

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

    public function includeAliases()
    {
        $this->includeAliases = true;
    }

    public function includeInactivePages()
    {
        $this->includeInactivePages = true;
    }

    public function includeSystemPages()
    {
        $this->includeSystemPages = true;
    }

    public function isFulltextSearch()
    {
        return $this->isFulltextSearch;
    }

    public function setPageVersionToRetrieve($pageVersionToRetrieve)
    {
        $this->pageVersionToRetrieve = $pageVersionToRetrieve;
    }

    public function createQuery()
    {
        $this->query->select('p.cID');
    }

    public function finalizeQuery(\Doctrine\DBAL\Query\QueryBuilder $query)
    {
        if ($this->includeAliases) {
            $query->from('Pages', 'p')
                ->leftJoin('p', 'Pages', 'pa', 'p.cPointerID = pa.cID')
                ->leftJoin('p', 'PagePaths', 'pp', 'p.cID = pp.cID and pp.ppIsCanonical = true')
                ->leftJoin('pa', 'PageSearchIndex', 'psi', 'psi.cID = if(pa.cID is null, p.cID, pa.cID)')
                ->leftJoin('p', 'PageTypes', 'pt', 'pt.ptID = if(pa.cID is null, p.ptID, pa.ptID)')
                ->leftJoin('p', 'CollectionSearchIndexAttributes', 'csi', 'csi.cID = if(pa.cID is null, p.cID, pa.cID)')
                ->innerJoin('p', 'CollectionVersions', 'cv', 'cv.cID = if(pa.cID is null, p.cID, pa.cID)')
                ->innerJoin('p', 'Collections', 'c', 'p.cID = c.cID')
                ->andWhere('p.cIsTemplate = 0 or pa.cIsTemplate = 0');
        } else {
            $query->from('Pages', 'p')
                ->leftJoin('p', 'PagePaths', 'pp', '(p.cID = pp.cID and pp.ppIsCanonical = true)')
                ->leftJoin('p', 'PageSearchIndex', 'psi', 'p.cID = psi.cID')
                ->leftJoin('p', 'PageTypes', 'pt', 'p.ptID = pt.ptID')
                ->leftJoin('c', 'CollectionSearchIndexAttributes', 'csi', 'c.cID = csi.cID')
                ->innerJoin('p', 'Collections', 'c', 'p.cID = c.cID')
                ->innerJoin('p', 'CollectionVersions', 'cv', 'p.cID = cv.cID')
                ->andWhere('p.cPointerID < 1')
                ->andWhere('p.cIsTemplate = 0');
        }

        if ($this->pageVersionToRetrieve == self::PAGE_VERSION_RECENT) {
            $query->andWhere('cvID = (select max(cvID) from CollectionVersions where cID = cv.cID)');
        } else {
            $query->andWhere('cvIsApproved = 1');
        }

        if ($this->isFulltextSearch) {
            $query->addSelect('match(psi.cName, psi.cDescription, psi.content) against (:fulltext) as cIndexScore');
        }

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

    public function getTotalResults()
    {
        $u = new \User();
        if ($this->permissionsChecker == -1) {
            $query = $this->deliverQueryObject();

            return $query->select('count(distinct p.cID)')->setMaxResults(1)->execute()->fetchColumn();
        } else {
            return -1; // unknown
        }
    }

    protected function createPaginationObject()
    {
        $u = new \User();
        if ($this->permissionsChecker == -1) {
            $adapter = new DoctrineDbalAdapter($this->deliverQueryObject(), function ($query) {
                $query->select('count(distinct p.cID)')->setMaxResults(1);
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
        $c = ConcretePage::getByID($queryRow['cID'], 'ACTIVE');
        if (is_object($c) && $this->checkPermissions($c)) {
            if ($this->pageVersionToRetrieve == self::PAGE_VERSION_RECENT) {
                $cp = new \Permissions($c);
                if ($cp->canViewPageVersions()) {
                    $c->loadVersionObject('RECENT');
                }
            }
            if (isset($queryRow['cIndexScore'])) {
                $c->setPageIndexScore($queryRow['cIndexScore']);
            }

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

        $cp = new \Permissions($mixed);

        return $cp->canViewPage();
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
     * Filters by page template
     * @param mixed $ptHandle
     */
    public function filterByPageTemplate(Template $template)
    {
        $this->query->andWhere('cv.pTemplateID = :pTemplateID');
        $this->query->setParameter('pTemplateID', $template->getPageTemplateID());
    }

    /**
     * Filters by date added
     * @param string $date
     */
    public function filterByDateAdded($date, $comparison = '=')
    {
        $this->query->andWhere($this->query->expr()->comparison('c.cDateAdded', $comparison, $this->query->createNamedParameter($date)));
    }

    /**
     * Filter by number of children.
     * @param $number
     * @param string $comparison
     */
    public function filterByNumberOfChildren($number, $comparison = '>')
    {
        $number = intval($number);
        if ($this->includeAliases) {
            $this->query->andWhere(
                $this->query->expr()->orX(
                    $this->query->expr()->comparison('p.cChildren', $comparison, ':cChildren'),
                    $this->query->expr()->comparison('pa.cChildren', $comparison, ':cChildren')
                )
            );
        } else {
            $this->query->andWhere($this->query->expr()->comparison('p.cChildren', $comparison, ':cChildren'));
        }
        $this->query->setParameter('cChildren', $number);
    }

    /**
     * Filter by last modified date.
     * @param $date
     * @param string $comparison
     */
    public function filterByDateLastModified($date, $comparison = '=')
    {
        $this->query->andWhere($this->query->expr()->comparison('c.cDateModified', $comparison, $this->query->createNamedParameter($date)));
    }

    /**
     * Filters by public date
     * @param string $date
     */
    public function filterByPublicDate($date, $comparison = '=')
    {
        $this->query->andWhere($this->query->expr()->comparison('cv.cvDatePublic', $comparison, $this->query->createNamedParameter($date)));
    }

    /**
     * Displays only those pages that have style customizations
     */
    public function filterByPagesWithCustomStyles()
    {
        $this->query->innerJoin('cv', 'CollectionVersionThemeCustomStyles', 'cvStyles',
            'cv.cID = cvStyles.cID');
    }

    /**
     * Filters by user ID)
     * @param mixed $uID
     */
    public function filterByUserID($uID)
    {
        $this->query->andWhere('p.uID = :uID');
        $this->query->setParameter('uID', $uID);
    }

    /**
     * Filters by page type ID
     * @param array | integer $cParentID
     */
    public function filterByPageTypeID($ptID)
    {
        $db = \Database::get();
        if (is_array($ptID)) {
            $this->query->andWhere(
                $this->query->expr()->in('pt.ptID', array_map(array($db, 'quote'), $ptID))
            );
        } else {
            $this->query->andWhere($this->query->expr()->comparison('pt.ptID', '=', ':ptID'));
            $this->query->setParameter('ptID', $ptID, \PDO::PARAM_INT);
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

    /**
     * Filters a list by page name.
     * @param $name
     * @param bool $exact
     */
    public function filterByName($name, $exact = false)
    {
        if ($exact) {
            $this->query->andWhere('cv.cvName = :cvName');
            $this->query->setParameter('cvName', $name);
        } else {
            $this->query->andWhere(
                $this->query->expr()->like('cv.cvName', ':cvName')
            );
            $this->query->setParameter('cvName', '%' . $name . '%');
        }
    }

    /**
     * Filter a list by page path.
     * @param $path
     * @param bool $includeAllChildren
     */
    public function filterByPath($path, $includeAllChildren = true)
    {
        if (!$includeAllChildren) {
            $this->query->andWhere('pp.cPath = :cPath');
            $this->query->setParameter('cPath', $path);
        } else {
            $this->query->andWhere(
                $this->query->expr()->like('pp.cPath', ':cPath')
            );
            $this->query->setParameter('cPath', $path . '/%');
        }
        $this->query->andWhere('pp.ppIsCanonical = 1');
    }

    /**
     * Filters keyword fields by keywords (including name, description, content, and attributes.
     * @param $keywords
     */
    public function filterByKeywords($keywords)
    {
        $expressions = array(
            $this->query->expr()->like('psi.cName', ':keywords'),
            $this->query->expr()->like('psi.cDescription', ':keywords'),
            $this->query->expr()->like('psi.content', ':keywords')
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

    public function filterByFulltextKeywords($keywords)
    {
        $this->isFulltextSearch = true;
        $this->autoSortColumns[] = 'cIndexScore';
        $this->query->where('match(psi.cName, psi.cDescription, psi.content) against (:fulltext)');
        $this->query->setParameter('fulltext', $keywords);
    }

    /**
     * Filters by topic. Doesn't look at specific attributes –instead, actually joins to the topics table.
     */
    public function filterByTopic($topic)
    {
        if (is_object($topic)) {
            $treeNodeID = $topic->getTreeNodeID();
        } else {
            $treeNodeID = $topic;
        }
        $this->query->innerJoin('cv', 'CollectionAttributeValues', 'cavTopics',
            'cv.cID = cavTopics.cID and cv.cvID = cavTopics.cvID');
        $this->query->innerJoin('cavTopics', 'atSelectedTopics', 'atst', 'cavTopics.avID = atst.avID');
        $this->query->andWhere('atst.TopicNodeID = :TopicNodeID');
        $this->query->setParameter('TopicNodeID', $treeNodeID);
    }

    /**
     * Sorts this list by display order
     */
    public function sortByDisplayOrder()
    {
        $this->query->orderBy('p.cDisplayOrder', 'asc');
    }

    /**
     * Sorts this list by display order descending
     */
    public function sortByDisplayOrderDescending()
    {
        $this->query->orderBy('p.cDisplayOrder', 'desc');
    }

    /**
     * Sorts by ID in ascending order.
     */
    public function sortByCollectionIDAscending()
    {
        $this->query->orderBy('p.cID', 'asc');
    }

    /**
     * Sorts this list by public date ascending order
     */
    public function sortByPublicDate()
    {
        $this->query->orderBy('cv.cvDatePublic', 'asc');
    }

    /**
     * Sorts by name in ascending order.
     */
    public function sortByName()
    {
        $this->query->orderBy('cv.cvName', 'asc');
    }

    /**
     * Sorts by name in descending order.
     */
    public function sortByNameDescending()
    {
        $this->query->orderBy('cv.cvName', 'desc');
    }

    /**
     * Sorts this list by public date descending order
     */
    public function sortByPublicDateDescending()
    {
        $this->query->orderBy('cv.cvDatePublic', 'desc');
    }

    /**
     * Sorts by fulltext relevance (requires that the query be fulltext-based
     */
    public function sortByRelevance()
    {
        if ($this->isFulltextSearch) {
            $this->query->orderBy('cIndexScore', 'desc');
        }
    }

    /**
     * @deprecated
     */
    public function filterByCollectionTypeHandle($ctHandle)
    {
        $this->filterByPageTypeHandle($ctHandle);
    }

    /**
     * @deprecated
     */
    public function filterByCollectionTypeID($ctID)
    {
        $this->filterByPageTypeID($ctID);
    }

    /**
     * This does nothing.
     * @deprecated
     */
    public function ignoreAliases()
    {
        return false;
    }

    /**
     * @deprecated
     */
    public function displayUnapprovedPages()
    {
        $this->setPageVersionToRetrieve(self::PAGE_VERSION_RECENT);
    }
}
