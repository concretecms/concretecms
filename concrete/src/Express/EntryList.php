<?php
namespace Concrete\Core\Express;

use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Entity\Express\Association;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Search\ItemList\Database\AttributedItemList as DatabaseItemList;
use Concrete\Core\Search\ItemList\Pager\Manager\ExpressEntryListPagerManager;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Search\ItemList\Pager\QueryString\VariableFactory;
use Concrete\Core\Search\Pagination\PaginationProviderInterface;
use Concrete\Core\Search\PermissionableListItemInterface;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\User;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Concrete\Core\Search\Pagination\Pagination;

class EntryList extends DatabaseItemList implements PagerProviderInterface, PaginationProviderInterface
{

    protected $category;
    protected $entity;

    /**
     * Determines whether the list should automatically always sort by a column that's in the automatic sort.
     * This is the default, but it's better to be able to use the AutoSortColumnRequestModifier on a search
     * result class instead. In order to do that we disable the auto sort here, while still providing the array
     * of possible auto sort columns.
     *
     * @var bool
     */
    protected $enableAutomaticSorting = false;

    protected $permissionsChecker = null;

    /**
     * Columns in this array can be sorted via the request.
     *
     * @var array
     */
    protected $autoSortColumns = [
        'e.exEntryDateCreated',
        'e.exEntryDateModified',
    ];

    public function __construct(Entity $entity)
    {
        $u = app(User::class);
        if ($u->isSuperUser()) {
            $this->ignorePermissions();
        }
        $this->category = $entity->getAttributeKeyCategory();
        $this->entity = $entity;
        $this->setItemsPerPage($entity->getItemsPerPage());
        parent::__construct(null);
        if ($entity->supportsCustomDisplayOrder()) {
            $this->setItemsPerPage(-1);
            $this->sortByDisplayOrderAscending();
        }
    }

    protected function getAttributeKeyClassName()
    {
        return $this->category;
    }

    /**
     * @return Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }


    /**
     * The total results of the query.
     *
     * @return int
     */
    public function getTotalResults()
    {
        $query = $this->deliverQueryObject();
        return $query->resetQueryParts(['groupBy', 'orderBy'])->select('count(distinct e.exEntryID)')->setMaxResults(1)->execute()->fetchColumn();
    }

    public function filterBySite(Site $site)
    {
        if (!$this->entity->usesSeparateSiteResultsBuckets()) {
            // This entity doesn't have site-specific entries, so we this filter by site does not apply.
            return false;
        }

        // This entity does use site specific buckets. So let's figure out which results node we're going to be using for
        // this site.
        $node = $this->entity->getEntityResultsNodeObject($site);
        $this->query->andWhere('resultsNodeID = :resultsNodeID');
        $this->query->setParameter('resultsNodeID', $node->getTreeNodeID());
    }

    public function sortByDisplayOrderAscending()
    {
        $this->query->orderBy('e.exEntryDisplayOrder', 'asc');
    }

    public function filterByKeywords($keywords)
    {
        $keys = $this->category->getSearchableIndexedList();
        if (count($keys)) {
            foreach ($keys as $ak) {
                $cnt = $ak->getController();
                $expressions[] = $cnt->searchKeywords($keywords, $this->query);
            }
            $expr = $this->query->expr();
            $this->query->andWhere(call_user_func_array(array($expr, 'orX'), $expressions));
            $this->query->setParameter('keywords', '%' . $keywords . '%');
        } else {
            $this->query->andWhere('1 = 0');
        }
    }

    public function getPagerManager()
    {
        return new ExpressEntryListPagerManager($this->entity, $this);
    }

    public function getPagerVariableFactory()
    {
        return new VariableFactory($this);
    }


    public function getPaginationAdapter()
    {
        $adapter = new DoctrineDbalAdapter($this->deliverQueryObject(), function ($query) {
            $query->resetQueryParts(['groupBy', 'orderBy'])->select('count(distinct e.exEntryID)')->setMaxResults(1);
        });
        return $adapter;
    }

    public function getResult($queryRow)
    {
        $r = $this->category->getEntityManager()->getRepository('Concrete\Core\Entity\Express\Entry');
        $entry = $r->findOneById($queryRow['exEntryID']);
        if (is_object($entry)) {
            if ($this->checkPermissions($entry)) {
                return $entry;
            }
        }
    }

    public function checkPermissions($mixed)
    {

        if ($this->permissionsChecker != null) {
            if ($this->permissionsChecker === -1) {
                return true;
            } else {
                return call_user_func_array($this->permissionsChecker, array($mixed));
            }
        }

        $fp = new \Permissions($mixed);
        return $fp->canViewExpressEntry();
    }

    public function setPermissionsChecker(\Closure $checker = null)
    {
        $this->permissionsChecker = $checker;
    }

    public function getPermissionsChecker()
    {
        return $this->permissionsChecker;
    }

    public function enablePermissions()
    {
        unset($this->permissionsChecker);
    }

    public function ignorePermissions()
    {
        $this->permissionsChecker = -1;
    }

    /**
     * Sorts this list by date added ascending.
     */
    public function sortByDateAdded()
    {
        $this->query->orderBy('e.exEntryDateCreated', 'asc');
    }

    /**
     * Sorts this list by date added descending.
     */
    public function sortByDateAddedDescending()
    {
        $this->query->orderBy('e.exEntryDateCreated', 'desc');
    }

    public function createQuery()
    {
        $table = $this->category->getIndexedSearchTable();
        $this->query->select('e.exEntryID')
            ->from('ExpressEntityEntries', 'e')
            ->leftJoin('e', $table, 'ea', 'e.exEntryID = ea.exEntryID');
    }


    public function finalizeQuery(\Doctrine\DBAL\Query\QueryBuilder $query)
    {
        $query->andWhere('e.exEntryEntityID = :entityID');
        $query->setParameter('entityID', $this->entity->getID());
        return $query;
    }

    public function filterByAssociatedEntry(Association $association, Entry $entry)
    {
        // Find the inverse association to this one.
        $matches = 0;
        $sourceEntity = $association->getSourceEntity();
        $targetEntity = $association->getTargetEntity();
        foreach($targetEntity->getAssociations() as $targetAssociation) {
            if ($targetAssociation->getTargetEntity() == $sourceEntity) {
                // we have a match.
                $entryAssociation = $entry->getEntryAssociation($targetAssociation);
                if ($entryAssociation) {
                    $matches++;
                    $entryAssociationTable = 'a' . $entryAssociation->getID();
                    $entryAssociationEntriesTable = 'ae' . $entryAssociation->getID();

                    $this->query->innerJoin('e', 'ExpressEntityEntryAssociations', $entryAssociationTable, 'e.exEntryID = ' . $entryAssociationTable . '.exEntryID');
                    $this->query->innerJoin($entryAssociationTable, 'ExpressEntityAssociationEntries', $entryAssociationEntriesTable, $entryAssociationTable . '.id = ' . $entryAssociationEntriesTable . '.association_id');

                    $this->query->andWhere($entryAssociationTable . '.association_id = :entryAssociationID' . $entryAssociation->getID());
                    $this->query->andWhere($entryAssociationEntriesTable . '.exEntryID = :selectedEntryID' . $entryAssociation->getID());
                    $this->query->setParameter('entryAssociationID' . $entryAssociation->getID(), $association->getID());
                    $this->query->setParameter('selectedEntryID' . $entryAssociation->getID(), $entry->getID());
                }
            }
        }
        if (!$matches) {
            $this->query->andWhere('1 = 0');
        }
    }

    /**
     * Filters by a user ID.
     *
     * @param integer $userID
     */
    public function filterByAuthorUserID($userID)
    {
        $this->query->andWhere('e.uID = :userID');
        $this->query->setParameter('userID', $userID);
    }



}
