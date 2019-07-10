<?php
namespace Concrete\Block\ExpressEntryList;

use Concrete\Controller\Element\Search\CustomizeResults;
use Concrete\Controller\Element\Search\SearchFieldSelector;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Entity\Express\Association;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Entity\Express\ManyToManyAssociation;
use Concrete\Core\Entity\Express\ManyToOneAssociation;
use Concrete\Core\Express\Entry\Search\Result\Result;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Express\Search\Field\AssociationField;
use Concrete\Core\Express\Search\ColumnSet\DefaultSet;
use Concrete\Core\Express\Search\SearchProvider;
use Concrete\Core\Search\Column\AttributeKeyColumn;
use Concrete\Core\Search\Field\ManagerFactory;
use Concrete\Core\Search\Result\ItemColumn;
use Concrete\Core\Support\Facade\Facade;
use Symfony\Component\HttpFoundation\JsonResponse;

class Controller extends BlockController
{
    protected $btInterfaceWidth = "640";
    protected $btInterfaceHeight = "400";
    protected $btTable = 'btExpressEntryList';
    protected $entityAttributes = [];

    public function on_start()
    {
        parent::on_start();
        $this->app = Facade::getFacadeApplication();
        $this->entityManager = $this->app->make('database/orm')->entityManager();
    }

    public function getBlockTypeDescription()
    {
        return t("Add a searchable Express entry list to a page.");
    }

    public function getBlockTypeName()
    {
        return t("Express Entry List");
    }

    public function getBlockTypeInSetName()
    {
        return t("List");
    }

    public function add()
    {
        $this->loadData();
        $this->set('searchProperties', []);
        $this->set('searchPropertiesSelected', []);
        $this->set('searchAssociations', []);
        $this->set('linkedProperties', []);
        $this->set('displayLimit', 20);
    }

    protected function getSearchFieldManager(Entity $entity)
    {
        $fieldManager = ManagerFactory::get('express');
        $fieldManager->setExpressCategory($entity->getAttributeKeyCategory());
        return $fieldManager;
    }

    public function action_add_search_field($entityID = null)
    {
        if (!$entityID) {
            $entityID = $this->exEntityID;
        }
        $entity = $this->entityManager->find(Entity::class, $entityID);

        if ($entity) {
            $manager = $this->getSearchFieldManager($entity);
            if ($manager) {
                $field = $this->request->request->get('field');
                $field = $manager->getFieldByKey($field);
                if (is_object($field)) {
                    return new JsonResponse($field);
    }
            }
        }
    }

    public function edit()
    {
        $this->loadData();
        if ($this->exEntityID) {
            /**
             * @var Entity
             */
            $entity = $this->entityManager->find(Entity::class, $this->exEntityID);
            if (is_object($entity)) {
                if ($this->searchProperties) {
                    $searchPropertiesSelected = (array) json_decode($this->searchProperties);
                } else {
                    $searchPropertiesSelected = [];
                }

                if ($this->searchAssociations) {
                    $searchAssociationsSelected = (array) json_decode($this->searchAssociations);
                } else {
                    $searchAssociationsSelected = [];
                }

                if ($this->linkedProperties) {
                    $linkedPropertiesSelected = (array) json_decode($this->linkedProperties);
                } else {
                    $linkedPropertiesSelected = [];
                }

                $searchProperties = $this->getSearchPropertiesJsonArray($entity);
                $searchAssociations = $this->getSearchAssociationsJsonArray($entity);
                $columns = unserialize($this->columns);
                $provider = \Core::make(SearchProvider::class, [$entity, $entity->getAttributeKeyCategory()]);
                if ($columns) {
                    $provider->setColumnSet($columns);
                }

                $element = new CustomizeResults($provider);
                $element->setIncludeNumberOfResults(false);

                $fieldManager = $this->getSearchFieldManager($entity);
                $fieldSelectorElement = new SearchFieldSelector($fieldManager, $this->getActionURL('add_search_field'));

                if ($this->filterFields) {
                    $filterFields = unserialize($this->filterFields);
                    $query = new Query();
                    $query->setFields($filterFields);
                    $fieldSelectorElement->setQuery($query);
                }

                $this->set('customizeElement', $element);
                $this->set('searchFieldSelectorElement', $fieldSelectorElement);
                $this->set('linkedPropertiesSelected', $linkedPropertiesSelected);
                $this->set('searchPropertiesSelected', $searchPropertiesSelected);
                $this->set('searchProperties', $searchProperties);
                $this->set('searchAssociationsSelected', $searchAssociationsSelected);
                $this->set('searchAssociations', $searchAssociations);
            }
        }
    }

    protected function getSearchPropertiesJsonArray($entity)
    {
        $attributes = $entity->getAttributeKeyCategory()->getList();
        $select = [];
        foreach ($attributes as $ak) {
            $o = new \stdClass();
            $o->akID = $ak->getAttributeKeyID();
            $o->akName = $ak->getAttributeKeyDisplayName();
            $select[] = $o;
        }

        return $select;
    }

    protected function getSearchAssociationsJsonArray($entity)
    {
        $associations = $entity->getAssociations();
        $select = [];
        foreach ($associations as $association) {
            if ($association instanceof ManyToManyAssociation || $association instanceof ManyToOneAssociation) {
                $o = new \stdClass();
                $o->associationID = $association->getId();
                $o->associationName = $association->getTargetEntity()->getEntityDisplayName();
                $select[] = $o;
            }
        }

        return $select;
    }

    public function view()
    {
        $entity = $this->entityManager->find(Entity::class, $this->exEntityID);
        if (is_object($entity)) {
            $category = $entity->getAttributeKeyCategory();
            $list = new EntryList($entity);
            if ($this->displayLimit > 0) {
                $list->setItemsPerPage(intval($this->displayLimit));
            }
            
            // Filter by any pre-set search criteria
            if ($this->filterFields) {
                $filterFields = unserialize($this->filterFields);
                if (is_array($filterFields)) {
                    foreach($filterFields as $field) {
                        $field->filterList($list);
                    }
                }
            }
            $set = unserialize($this->columns);
            if (!$set) {
                $set = new DefaultSet($category);
            }
            $defaultSortColumn = $set->getDefaultSortColumn();
            if ($this->request->query->has($list->getQuerySortDirectionParameter())) {
                $direction = $this->request->query->get($list->getQuerySortDirectionParameter());
            } else {
                $direction = $defaultSortColumn->getColumnDefaultSortDirection();
            }

            if ($this->request->query->has($list->getQuerySortColumnParameter())) {
                $value = $this->request->query->get($list->getQuerySortColumnParameter());
                $column = $entity->getResultColumnSet();
                $value = $column->getColumnByKey($value);
                if (is_object($value)) {
                    $list->sanitizedSortBy($value->getColumnKey(), $direction);
                }
            } else {
                $list->sanitizedSortBy($defaultSortColumn->getColumnKey(), $direction);
            }

            if ($this->request->query->get('keywords') && $this->enableSearch) {
                $keywords = preg_split('/\s+/', $this->request->query->get('keywords'), -1, PREG_SPLIT_NO_EMPTY);
                foreach ($keywords  as $keyword) {
                    $list->filterByKeywords($keyword);
                }
            }

            $tableSearchProperties = [];
            if ($this->searchProperties) {
                $searchPropertiesSelected = (array) json_decode($this->searchProperties);
            } else {
                $searchPropertiesSelected = [];
            }
            foreach ($searchPropertiesSelected as $akID) {
                $ak = $category->getAttributeKeyByID($akID);
                if (is_object($ak)) {
                    $tableSearchProperties[] = $ak;
                    $type = $ak->getAttributeType();
                    $cnt = $type->getController();
                    $cnt->setRequestArray($_REQUEST);
                    $cnt->setAttributeKey($ak);
                    $cnt->searchForm($list);
                }
            }

            $tableSearchAssociations = [];
            if ($this->searchAssociations) {
                $searchAssociationsSelected = (array) json_decode($this->searchAssociations);
            } else {
                $searchAssociationsSelected = [];
            }
            foreach ($searchAssociationsSelected as $associationID) {
                $association = $this->entityManager->find(Association::class, $associationID);
                if (is_object($association)) {
                    $tableSearchAssociations[] = $association;
                    $field = new AssociationField($association);
                    $field->loadDataFromRequest($this->getRequest()->query->all());
                    $field->filterList($list);
                }
            }

            $result = new Result($set, $list);
            $pagination = $list->getPagination();
            if ($pagination->haveToPaginate()) {
                $pagination = $pagination->renderDefaultView();
                $this->set('pagination', $pagination);
                $this->requireAsset('css', 'core/frontend/pagination');
            }

            $this->set('list', $list);
            $this->set('result', $result);
            $this->set('entity', $entity);
            $this->set('tableSearchProperties', $tableSearchProperties);
            $this->set('tableSearchAssociations', $tableSearchAssociations);
            $this->set('detailPage', $this->getDetailPageObject());
        }
    }

    public function save($data)
    {
        $this->on_start();

        if (isset($data['enableSearch']) && $data['enableSearch']) {
            if (isset($data['searchProperties']) && is_array($data['searchProperties'])) {
                $searchProperties = $data['searchProperties'];
            } else {
                $searchProperties = [];
            }

            $data['searchProperties'] = json_encode($searchProperties);

            if (isset($data['searchAssociations']) && is_array($data['searchAssociations'])) {
                $searchAssociations = $data['searchAssociations'];
            } else {
                $searchAssociations = [];
            }

            $data['searchAssociations'] = json_encode($searchAssociations);

            if (empty($searchProperties) && empty($searchAssociations) && empty($linkedProperties) && empty($data['enableKeywordSearch'])) {
                $data['enableSearch'] = 0;
            }
        } else {
            $data['searchProperties'] = [];
            $data['searchAssociations'] = [];
            $data['enableKeywordSearch'] = 0;
            $data['enableSearch'] = 0;
        }

        if (isset($data['linkedProperties']) && is_array($data['linkedProperties'])) {
            $linkedProperties = $data['linkedProperties'];
        } else {
            $linkedProperties = [];
        }
        $data['linkedProperties'] = json_encode($linkedProperties);

        if (empty($data['enableKeywordSearch'])) {
            $data['enableKeywordSearch'] = 0;
        }

        $entity = $this->entityManager->find('Concrete\Core\Entity\Express\Entity', $data['exEntityID']);
        if (is_object($entity) && is_array($this->request->request->get('column'))) {
            $provider = $this->app->make(SearchProvider::class, [$entity, $entity->getAttributeKeyCategory()]);
            $set = $this->app->make('Concrete\Core\Express\Search\ColumnSet\ColumnSet');
            $available = $provider->getAvailableColumnSet();
            foreach ($this->request->request->get('column') as $key) {
                $set->addColumn($available->getColumnByKey($key));
            }

            $sort = $available->getColumnByKey($this->request->request->get('fSearchDefaultSort'));
            $set->setDefaultSortColumn($sort, $this->request->request->get('fSearchDefaultSortDirection'));

            $data['columns'] = serialize($set);
        }

        if ($entity) {
            $manager = $this->getSearchFieldManager($entity);
            $filterFields = $manager->getFieldsFromRequest($this->request->request->all());
            $data['filterFields'] = serialize($filterFields);
        }


        parent::save($data);
    }

    public function action_load_entity_data()
    {
        $exEntityID = $this->request->request->get('exEntityID');
        if ($exEntityID) {
            $entity = $this->entityManager->find(Entity::class, $exEntityID);
            if (is_object($entity)) {
                $provider = \Core::make(SearchProvider::class, [$entity, $entity->getAttributeKeyCategory()]);
                $element = new CustomizeResults($provider);
                $element->setIncludeNumberOfResults(false);
                $r = new \stdClass();
                ob_start();
                $element->getViewObject()->render();
                $r->customize = ob_get_contents();
                ob_end_clean();

                $fieldManager = $this->getSearchFieldManager($entity);
                $addFieldAction = $this->getActionURL('add_search_field', $exEntityID);
                $fieldSelectorElement = new SearchFieldSelector($fieldManager, $addFieldAction);
                ob_start();
                $fieldSelectorElement->getViewObject()->render();
                $r->searchFields = ob_get_contents();
                ob_end_clean();

                $r->attributes = $this->getSearchPropertiesJsonArray($entity);
                $r->associations = $this->getSearchAssociationsJsonArray($entity);

                return new JsonResponse($r);
            }
        }

        \Core::make('app')->shutdown();
    }

    public function loadData()
    {
        $r = $this->entityManager->getRepository(Entity::class);
        $entityObjects = $r->findAll();
        $entities = ['' => t("** Choose Entity")];
        foreach ($entityObjects as $entity) {
            $entities[$entity->getID()] = $entity->getEntityDisplayName();
        }
        $this->set('entities', $entities);
    }

    protected function getDetailPageObject()
    {
        $detailPage = false;
        if ($this->detailPage) {
            $c = \Page::getByID($this->detailPage);
            if (is_object($c) && !$c->isError()) {
                $detailPage = $c;
            }
        }

        return $detailPage;
    }

    public function linkThisColumn(ItemColumn $column)
    {
        $detailPage = $this->getDetailPageObject();
        if (!$detailPage) {
            return false;
        }

        $linkedProperties = (array) json_decode($this->linkedProperties);

        if ($column->getColumn() instanceof AttributeKeyColumn) {
            if ($ak = $column->getColumn()->getAttributeKey()) {
                return in_array($ak->getAttributeKeyID(), $linkedProperties);
            }
        }
    }
}
