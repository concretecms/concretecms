<?php
namespace Concrete\Core\Express;

use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Entity\Express\Entity;
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

        return $query->select('count(distinct e.exEntryID)')->setMaxResults(1)->execute()->fetchColumn();
    }

    public function filterByKeywords($keywords)
    {

        $keys = $this->category->getSearchableIndexedList();
        foreach ($keys as $ak) {
            $cnt = $ak->getController();
            $expressions[] = $cnt->searchKeywords($keywords, $this->query);
        }
        $expr = $this->query->expr();
        $this->query->andWhere(call_user_func_array(array($expr, 'orX'), $expressions));
        $this->query->setParameter('keywords', '%' . $keywords . '%');
    }


    /**
     * Gets the pagination object for the query.
     *
     * @return Pagination
     */
    protected function createPaginationObject()
    {
        $adapter = new DoctrineDbalAdapter($this->deliverQueryObject(), function ($query) {
            $query->select('count(distinct e.exEntryID)')->setMaxResults(1);
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


}
