<?php
namespace Concrete\Core\Express\Search;

use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Express\Entry\Search\Result\Result;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Express\Search\ColumnSet\DefaultSet;
use Concrete\Core\Search\AbstractSearchProvider;
use Concrete\Core\Express\Search\ColumnSet\Available;
use Concrete\Core\Express\Search\ColumnSet\ColumnSet;
use Concrete\Core\Entity\Search\SavedExpressSearch;
use Concrete\Core\Search\Field\ManagerFactory;
use Symfony\Component\HttpFoundation\Session\Session;

class SearchProvider extends AbstractSearchProvider
{
    protected $category;
    protected $entity;
    protected $columnSet;

    public function getFieldManager()
    {
        $manager = ManagerFactory::get('express');
        $manager->setExpressCategory($this->category);
        return $manager;
    }

    /**
     * @param mixed $columnSet
     */
    public function setColumnSet($columnSet)
    {
        $this->columnSet = $columnSet;
    }

    public function getSessionNamespace()
    {
        return 'express_' . $this->entity->getHandle();
    }

    public function __construct(Entity $entity, ExpressCategory $category, Session $session)
    {
        $this->entity = $entity;
        $this->category = $category;
        parent::__construct($session);
    }

    /**
     * @return Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    public function getCustomAttributeKeys()
    {
        return $this->category->getSearchableList();
    }

    public function getAvailableColumnSet()
    {
        return new Available($this->category);
    }

    public function getBaseColumnSet()
    {
        return new ColumnSet($this->category);
    }

    public function getCurrentColumnSet()
    {
        $query = $this->getSessionCurrentQuery();
        if ($query) {
            $this->columnSet = $query->getColumns();
        }

        if (!isset($this->columnSet)) {
            $current = $this->entity->getResultColumnSet();
            if (!is_object($current)) {
                $current = new DefaultSet($this->category);
            }
            $this->columnSet = $current;
        }

        return $this->columnSet;
    }

    public function createSearchResultObject($columns, $list)
    {
        $result = new Result($columns, $list);
        $result->setEntity($this->entity);
        return $result;
    }

    public function getItemList()
    {
        $list = new EntryList($this->entity);
        if (!$this->entity->supportsEntrySpecificPermissions()) {
            $list->ignorePermissions();
        }
        $list->setupAutomaticSorting();
        return $list;
    }

    public function getDefaultColumnSet()
    {
        $defaultSet = $this->entity->getResultColumnSet();
        if (!$defaultSet) {
            $defaultSet = new DefaultSet($this->category);
        }
        return $defaultSet;
    }

    /**
     * Returns the number of items per page.
     * @return int
     */
    public function getItemsPerPage()
    {
        $query = $this->getSessionCurrentQuery();
        if ($query) {
            return $query->getItemsPerPage();
        } else {
            return $this->entity->getItemsPerPage();
        }
    }

    public function getSavedSearch()
    {
        $search = new SavedExpressSearch();
        $search->setEntity($this->getEntity());
        return $search;
    }
}
