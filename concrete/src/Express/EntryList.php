<?php
namespace Concrete\Core\Express;

use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Entity\Express\Association;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Search\ItemList\Database\AttributedItemList as DatabaseItemList;
use Concrete\Core\Search\PermissionableListItemInterface;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Concrete\Core\Search\Pagination\Pagination;

class EntryList extends DatabaseItemList implements PermissionableListItemInterface
{

    protected $category;
    protected $entity;

    public function __construct(Entity $entity)
    {
        $this->category = $entity->getAttributeKeyCategory();
        $this->entity = $entity;
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


    /**
     * Gets the pagination object for the query.
     *
     * @return Pagination
     */
    protected function createPaginationObject()
    {
        $adapter = new DoctrineDbalAdapter($this->deliverQueryObject(), function ($query) {
            $query->resetQueryParts(['groupBy', 'orderBy'])->select('count(distinct e.exEntryID)')->setMaxResults(1);
        });
        $pagination = new Pagination($this, $adapter);

        return $pagination;
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

        if (isset($this->permissionsChecker)) {
            if ($this->permissionsChecker === -1) {
                return true;
            } else {
                return call_user_func_array($this->permissionsChecker, array($mixed));
            }
        }

        $fp = new \Permissions($mixed);
        return $fp->canViewExpressEntry();
    }

    public function setPermissionsChecker(\Closure $checker)
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
        $sourceEntity = $association->getSourceEntity();
        $targetEntity = $association->getTargetEntity();
        foreach($targetEntity->getAssociations() as $targetAssociation) {
            if ($targetAssociation->getTargetEntity() == $sourceEntity) {
                // we have a match.
                $entryAssociation = $entry->getEntryAssociation($targetAssociation);
                if ($entryAssociation) {
                    $table = 'ase' . $entryAssociation->getID();
                    $this->query->innerJoin('e', 'ExpressEntityAssociationSelectedEntries', $table, 'e.exEntryID = ' . $table . '.exSelectedEntryID');
                    $this->query->andWhere($table . '.id = :entryAssociationID' . $entryAssociation->getID());
                    $this->query->setParameter('entryAssociationID' . $entryAssociation->getID(), $entryAssociation->getID());
                } else {
                    $this->query->andWhere('1 = 0');
                }
            }
        }
    }


}
