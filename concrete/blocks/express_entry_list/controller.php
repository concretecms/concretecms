<?php
namespace Concrete\Block\ExpressEntryList;

use Concrete\Controller\Element\Search\Express\CustomizeResults;
use Concrete\Controller\Element\Search\SearchFieldSelector;
use Concrete\Core\Api\ApiResourceValueInterface;
use Concrete\Core\Api\ApiValueSchemaInterface;
use Concrete\Core\Api\Block\ApiValueSchemaFactory;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Block\Controller\SaveMode;
use Concrete\Core\Block\ExportDeclarations;
use Concrete\Core\Block\Traits\CustomApiValueTrait;
use Concrete\Core\Entity\Express\Association;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Entity\Express\ManyToManyAssociation;
use Concrete\Core\Entity\Express\ManyToOneAssociation;
use Concrete\Core\Express\Entry\Search\Result\Result;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Express\Search\ColumnSet\ColumnSet;
use Concrete\Core\Express\Search\Field\AssociationField;
use Concrete\Core\Express\Search\ColumnSet\DefaultSet;
use Concrete\Core\Express\Search\SearchProvider;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Search\Column\AttributeKeyColumn;
use Concrete\Core\Search\Column\Column as SearchColumn;
use Concrete\Core\Search\Field\AttributeKeyField;
use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\Search\Field\ManagerFactory;
use Concrete\Core\Search\Query\Modifier\AutoSortColumnRequestModifier;
use Concrete\Core\Search\Query\Modifier\CustomItemsPerPageRequestModifier;
use Concrete\Core\Search\Query\Modifier\ItemsPerPageRequestModifier;
use Concrete\Core\Search\Query\QueryFactory;
use Concrete\Core\Search\Query\QueryModifier;
use Concrete\Core\Search\Result\ItemColumn;
use Concrete\Core\Search\Result\ResultFactory;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Utility\Service\Xml;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Support\Arr;
use SimpleXMLElement;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonDecode;

class Controller extends BlockController implements ApiResourceValueInterface, ApiValueSchemaInterface, UsesFeatureInterface
{
    use CustomApiValueTrait;

    /**
     * The keys of the API value that aren't the plain value of a column of the table of the block, plus the
     * elements that only the CIF representation of the block has.
     *
     * @var string[]
     */
    private const API_STRUCTURED_KEYS = [
        'columns',
        'defaultSortColumn',
        'filterFields',
        'linkedProperties',
        'linkedProperty',
        'searchProperties',
        'searchProperty',
        'searchAssociations',
        'searchAssociation',
    ];

    /**
     * @var string|null
     */
    public $exEntityID;

    /**
     * @var int|string|null
     */
    public $detailPage;

    /**
     * @var string|null
     */
    public $linkedProperties;

    /**
     * @var string|null
     */
    public $searchProperties = false;

    /**
     * @var string|null
     */
    public $searchAssociations;

    /**
     * @var string|null
     */
    public $columns;

    /**
     * @var string|null
     */
    public $filterFields;

    /**
     * @var int|string|null
     */
    public $displayLimit;

    /**
     * @var int|string|null
     */
    public $enableItemsPerPageSelection = false;

    /**
     * @var int|string|null
     */
    public $enablePagination;

    /**
     * @var int|string|null
     */
    public $enableSearch;

    /**
     * @var int|string|null
     */
    public $enableKeywordSearch;

    /**
     * @var string|null
     */
    public $headerBackgroundColor;

    /**
     * @var string|null
     */
    public $headerBackgroundColorActiveSort;

    /**
     * @var string|null
     */
    public $headerTextColor;

    /**
     * @var string|null
     */
    public $tableName;

    /**
     * @var string|null
     */
    public $tableDescription;

    /**
     * @var bool|int|string|null
     */
    public $tableStriped;

    /**
     * @var string|null
     */
    public $rowBackgroundColorAlternate;

    /**
     * @var string|null
     */
    public $titleFormat;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    public $entityManager;

    protected $btInterfaceWidth = "640";
    protected $btInterfaceHeight = "400";
    protected $btTable = 'btExpressEntryList';
    protected $entityAttributes = [];
    protected $btExportPageColumns = ['detailPage'];

    public function on_start()
    {
        parent::on_start();
        $this->app = Facade::getFacadeApplication();
        $this->entityManager = $this->app->make(EntityManagerInterface::class);
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

    public function getRequiredFeatures(): array
    {
        return [
            Features::EXPRESS
        ];
    }

    public function add()
    {
        $this->loadData();
        $this->set('searchProperties', []);
        $this->set('searchPropertiesSelected', []);
        $this->set('searchAssociations', []);
        $this->set('linkedProperties', []);
        $this->set('displayLimit', 20);
        $this->set('titleFormat', 'h2');
        $this->set('enablePagination', 1);
        $this->set('exEntityID', null);
        $this->set('enableSearch', false);
        $this->set('enableKeywordSearch', false);
        $this->set('searchFieldSelectorElement', null);
        $this->set('customizeElement', null);
        $this->set('tableName', null);
        $this->set('tableDescription', null);
        $this->set('headerBackgroundColor', null);
        $this->set('headerBackgroundColorActiveSort', null);
        $this->set('headerTextColor', null);
        $this->set('rowBackgroundColorAlternate', null);
        $this->set('tableStriped', false);
        $this->set('enableItemsPerPageSelection', null);
        $this->set('detailPage', null);
        $this->set('searchAssociationsSelected', []);
        $this->set('linkedPropertiesSelected', []);
    }

    /**
     * @return \Concrete\Core\Search\Field\ManagerInterface
     */
    protected function getSearchFieldManager(Entity $entity)
    {
        $fieldManager = ManagerFactory::get('express');
        $fieldManager->setExpressCategory($entity->getAttributeKeyCategory());
        return $fieldManager;
    }

    protected function isSearchListRequest()
    {
        return $this->getAction() == 'view' && $this->request->query->has('search');
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
                $provider = $this->app->make(SearchProvider::class, ['entity' => $entity, 'category' => $entity->getAttributeKeyCategory()]);

                $fieldManager = $this->getSearchFieldManager($entity);
                $fieldSelectorElement = new SearchFieldSelector($fieldManager, $this->getActionURL('add_search_field'));

                $query = new Query();
                if ($this->filterFields) {
                    $filterFields = unserialize($this->filterFields);
                    $query->setFields($filterFields);
                }

                $columns = unserialize($this->columns);
                if ($columns) {
                    $query->setColumns($columns);
                }

                $fieldSelectorElement->setQuery($query);
                $element = new CustomizeResults($provider, $query);
                $element->setIncludeNumberOfResults(false);

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

    /**
     * @param \Concrete\Core\Entity\Express\Entity $entity
     *
     * @return \stdClass[]
     */
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

    /**
     * @param \Concrete\Core\Entity\Express\Entity $entity
     *
     * @return \stdClass[]
     */
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
            $filterFields = [];
            if ($this->filterFields) {
                $filterFieldsUnserialized = unserialize($this->filterFields);
                if (is_array($filterFieldsUnserialized)) {
                    $filterFields = $filterFieldsUnserialized;
                }
            }

            $category = $entity->getAttributeKeyCategory();

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
                    if ($this->isSearchListRequest()) {
                        $attributeKeyField = new AttributeKeyField($ak);
                        $filterFields[] = $attributeKeyField;
                    }
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
                    $associationField = new AssociationField($association);
                    $associationField->loadDataFromRequest($this->getRequest()->query->all());
                    $filterFields[] = $associationField;
                }
            }

            if ($this->request->query->get('keywords') && $this->enableSearch) {
                $keywordsField = new KeywordsField($this->request->query->get('keywords'));
                $filterFields[] = $keywordsField;
            }

            $searchProvider = new SearchProvider($entity, $category, $this->app->make('session'));
            $queryFactory = new QueryFactory();
            $resultFactory = new ResultFactory();
            $query = $queryFactory->createQuery($searchProvider, $filterFields);

            $queryModifier = new QueryModifier();
            $queryModifier->addModifier(new AutoSortColumnRequestModifier($searchProvider, $this->request, Request::METHOD_GET));
            $itemsPerPageSpecified = null;
            if ($this->enableItemsPerPageSelection) {
                $maxItemsPerPage = max($this->getItemsPerPageOptions());
                if ($this->request->query->get('itemsPerPage')) {
                    $itemsPerPageSpecified = (int) $this->request->query->get('itemsPerPage');
                    if ($itemsPerPageSpecified <= $maxItemsPerPage) {
                        $queryModifier->addModifier(new CustomItemsPerPageRequestModifier(
                            $maxItemsPerPage, $this->request, Request::METHOD_GET)
                        );
                    } else {
                        unset($itemsPerPageSpecified);
                    }
                }
            }

            // Use the columns saved in the instance
            $columnSet = unserialize($this->columns);
            if (!$columnSet) {
                $columnSet = new DefaultSet($category);
            }

            $query->setColumns($columnSet);
            $query = $queryModifier->process($query);

            $result = $resultFactory->createFromQuery($searchProvider, $query);
            $list = $result->getItemListObject();
            if (!isset($itemsPerPageSpecified)) {
                if ($this->displayLimit > 0) {
                    $list->setItemsPerPage(intval($this->displayLimit));
                }
            }

            $result = new Result($columnSet, $list, $result->getBaseURL());
            $pagination = $result->getPagination();
            if ($pagination->haveToPaginate()) {
                $pagination = $pagination->renderDefaultView();
                $this->set('pagination', $pagination);
            } else {
                $this->set('pagination', null);
            }

            if ($this->enableItemsPerPageSelection) {
                $this->set('itemsPerPageOptions', $this->getItemsPerPageOptions());
            }
            $this->set('list', $list);
            $this->set('result', $result);
            $this->set('entity', $entity);
            $this->set('itemsPerPageSelected', $itemsPerPageSpecified ?: $this->displayLimit);
            $this->set('tableSearchProperties', $tableSearchProperties);
            $this->set('tableSearchAssociations', $tableSearchAssociations);
            $this->set('detailPage', $this->getDetailPageObject());
        }
    }

    protected function getItemsPerPageOptions()
    {
        $entity = $this->entityManager->find(Entity::class, $this->exEntityID);
        $category = $entity->getAttributeKeyCategory();
        $category = $entity->getAttributeKeyCategory();
        $itemsPerPageOptions = [];
        $itemsPerPageOptions[] = $this->displayLimit;
        $searchProvider = new SearchProvider($entity, $category, $this->app->make('session'));
        foreach($searchProvider->getItemsPerPageOptions() as $option) {
            if (!in_array($option, $itemsPerPageOptions)) {
                $itemsPerPageOptions[] = $option;
            }
        }
        sort($itemsPerPageOptions);
        return $itemsPerPageOptions;
    }

    public function save($data)
    {
        $this->on_start();
        $fromCIF = $this->saveMode === SaveMode::SAVE_MODE_IMPORT;
        if (!$fromCIF) {
            $data['columns'] = '';
            $data['filterFields'] = '';
        }

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

            if (empty($searchProperties) && empty($searchAssociations) && empty($data['enableKeywordSearch'])) {
                $data['enableSearch'] = 0;
            }
        } else {
            $data['searchProperties'] = null;
            $data['searchAssociations'] = null;
            $data['enableKeywordSearch'] = 0;
            $data['enableSearch'] = 0;
        }

        if (isset($data['linkedProperties']) && is_array($data['linkedProperties'])) {
            $linkedProperties = $data['linkedProperties'];
        } else {
            $linkedProperties = [];
        }
        $data['linkedProperties'] = json_encode($linkedProperties);

        $data['enableKeywordSearch'] = $data['enableKeywordSearch'] ?? 0;

        $data['enablePagination'] = $data['enablePagination'] ?? 0;

        $data['enableItemsPerPageSelection'] = $data['enableItemsPerPageSelection'] ?? 0;

        $data['displayLimit'] = (int) $data['displayLimit'];

        $entity = $this->entityManager->find('Concrete\Core\Entity\Express\Entity', $data['exEntityID']);
        if (!$fromCIF && is_object($entity) && is_array($this->request->request->get('column'))) {
            $provider = $this->app->make(SearchProvider::class, ['entity' => $entity, 'category' => $entity->getAttributeKeyCategory()]);
            $set = $this->app->make(ColumnSet::class);
            $available = $provider->getAvailableColumnSet();
            foreach ($this->request->request->get('column') as $key) {
                $set->addColumn($available->getColumnByKey($key));
            }

            $sort = $available->getColumnByKey($this->request->request->get('fSearchDefaultSort'));
            $set->setDefaultSortColumn($sort, $this->request->request->get('fSearchDefaultSortDirection'));

            $data['columns'] = serialize($set);
        }

        if (!$fromCIF && $entity) {
            $manager = $this->getSearchFieldManager($entity);
            $filterFields = $manager->getFieldsFromRequest($this->request->request->all());
            $data['filterFields'] = serialize($filterFields);
        }

        parent::save($data);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::getImportData()
     */
    protected function getImportData($blockNode, $page)
    {
        return $this->app->make(Localization::class)->withContext(Localization::CONTEXT_SYSTEM, function() use ($blockNode, $page) {
            return $this->doGetImportData($blockNode, $page);
        });
    }

    private function doGetImportData(SimpleXMLElement $blockNode, $page): array
    {
        $args = parent::getImportData($blockNode, $page);
        $this->saveMode = SaveMode::SAVE_MODE_IMPORT;
        $xRecord = $blockNode->data[0]->record[0];

        $entityID = (string) ($args['exEntityID'] ?? '');
        if ($entityID !== '') {
            $entity = $this->app->make(EntityManagerInterface::class)->find(Entity::class, $entityID);
            if ($entity === null) {
                $entityHandle = (string) $xRecord->exEntityID[0]['handle'];
                if ($entityHandle !== '') {
                    $entity = $this->app->make(EntityManagerInterface::class)->getRepository(Entity::class)->findOneBy(['handle' => $entityHandle]);
                    if ($entity !== null) {
                        $args['exEntityID'] = $entity->getId();
                    }
                }
            }
        }
        $entityAttributes = $entity ? $entity->getAttributes()->toArray() : [];
        $entityAssociations = $entity ? $entity->getAssociations()->toArray() : [];

        $linkedPropertyIDs = [];
        foreach ($xRecord->linkedProperty as $xLinkedProperty) {
            $linkedPropertyHandle = (string) $xLinkedProperty;
            if ($linkedPropertyHandle === '') {
                continue;
            }
            $linkedProperty = Arr::first(
                $entityAttributes,
                static function($attribute) use ($linkedPropertyHandle) {
                    return $linkedPropertyHandle === $attribute->getAttributeKeyHandle();
                }
            );
            if ($linkedProperty !== null) {
                $linkedPropertyIDs[] = $linkedProperty->getAttributeKeyID();
            }
        }
        $args['linkedProperties'] = $linkedPropertyIDs;

        $searchPropertyIDs = [];
        foreach ($xRecord->searchProperty as $xSearchProperty) {
            $searchPropertyHandle = (string) $xSearchProperty;
            if ($searchPropertyHandle === '') {
                continue;
            }
            $searchProperty = Arr::first(
                $entityAttributes,
                static function($attribute) use ($searchPropertyHandle) {
                    return $searchPropertyHandle === $attribute->getAttributeKeyHandle();
                }
            );
            if ($searchProperty !== null) {
                $searchPropertyIDs[] = $searchProperty->getAttributeKeyID();
            }
        }
        $args['searchProperties'] = $searchPropertyIDs;

        unset($args['searchAssociation']);
        $searchAssociations = [];
        foreach ($xRecord->searchAssociation as $xSearchAssociation) {
            $searchAssociationUUID = (string) $xSearchAssociation;
            $association = Arr::first(
                $entityAssociations,
                static function ($association) use ($searchAssociationUUID): bool {
                    return $searchAssociationUUID !== '' && $association->getId() === $searchAssociationUUID;
                }
            );
            if ($association === null) {
                $searchAssociationName = (string) $xSearchAssociation['target-property-name'];
                $association = Arr::first(
                    $entityAssociations,
                    static function ($association) use ($searchAssociationName): bool {
                        return $searchAssociationName !== '' && $association->getTargetPropertyName() === $searchAssociationName;
                    }
                );
            }
            if ($association !== null) {
                $searchAssociations[] = $association->getId();
            }
        }
        $args['searchAssociations'] = $searchAssociations;

        $filterFields = [];
        if ($entity && isset($xRecord->filterFields[0]->field)) {
            $searchFieldManager = $this->getSearchFieldManager($entity);
            foreach ($xRecord->filterFields[0]->field as $xField) {
                $field = $searchFieldManager->getFieldByKey((string) $xField['key']);
                if ($field) {
                    $field->loadDataFromImport($xField);
                    $filterFields[] = $field;
                }
            }
        }
        $args['filterFields'] = serialize($filterFields);

        $set = $this->app->make(ColumnSet::class);
        if ($entity) {
            $provider = $this->app->make(SearchProvider::class, ['entity' => $entity, 'category' => $entity->getAttributeKeyCategory()]);
            $availableColumns = $provider->getAvailableColumnSet()->getColumns();
            $findColumn = static function(string $key, string $name) use ($availableColumns) {
                if ($key !== '') {
                    $column = Arr::first(
                        $availableColumns,
                        static function ($column) use ($key) { return $column->getColumnKey() === $key; }
                    );
                    if ($column !== null) {
                        return $column;
                    }
                }
                if ($name !== '') {
                    $column = Arr::first(
                        $availableColumns,
                        static function ($column) use ($name) { return $column->getColumnName() === $name; }
                    );
                    if ($column !== null) {
                        return $column;
                    }
                }
                return null;
            };
            $xColumns = $xRecord->columns[0];
            $defaultSortColumn = $findColumn((string) $xColumns['default-sort-column-key'],  (string) $xColumns['default-sort-column-name']);
            if ($defaultSortColumn instanceof SearchColumn) {
                $set->setDefaultSortColumn($defaultSortColumn, (string) $xColumns['default-sort-column-direction']);
            }
            foreach ($xColumns->column as $xColumn) {
                $column = $findColumn((string) $xColumn['key'],  (string) $xColumn['name']);
                if ($column instanceof SearchColumn) {
                    $column->setColumnSortDirection((string) $xColumn['sort-direction']);
                    $set->addColumn($column);
                }
            }
        }
        $args['columns'] = serialize($set);

        return $args;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Api\ApiValueSchemaInterface::getApiValueSchema()
     */
    public function getApiValueSchema(): array
    {
        $schemaFactory = $this->app->make(ApiValueSchemaFactory::class);

        return [
            'type' => 'object',
            'properties' => [
                'exEntityID' => [
                    'type' => 'string',
                    'description' => 'The ID of the Express entity whose entries are listed.',
                ],
                'detailPage' => $schemaFactory->describeReference(ExportDeclarations::REFERENCE_PAGE, [
                    'type' => ['string', 'integer'],
                    'description' => 'The page displaying the details of an entry, linked by the linkedProperties columns (0 for none).',
                ]),
                'columns' => [
                    'type' => 'array',
                    'description' => 'The columns of the table, in the order they are displayed.',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'key' => [
                                'type' => 'string',
                                'description' => 'The key identifying the property of the entries displayed in the column.',
                            ],
                            'sortDirection' => [
                                'type' => 'string',
                                'enum' => ['asc', 'desc'],
                                'description' => 'The direction the entries are sorted in when the visitors sort them by this column.',
                            ],
                        ],
                    ],
                ],
                'defaultSortColumn' => [
                    'type' => ['object', 'null'],
                    'description' => 'The column the entries are sorted by (NULL if the entries aren\'t sorted).',
                    'properties' => [
                        'key' => [
                            'type' => 'string',
                            'description' => 'The key of one of the columns.',
                        ],
                        'direction' => [
                            'type' => 'string',
                            'enum' => ['asc', 'desc'],
                            'description' => 'The direction the entries are sorted in.',
                        ],
                    ],
                ],
                'linkedProperties' => [
                    'type' => 'array',
                    'description' => 'The IDs of the attributes of the entity whose columns link to the detailPage page.',
                    'items' => ['type' => ['string', 'integer']],
                ],
                'titleFormat' => [
                    'type' => 'string',
                    'enum' => array_keys(BlockController::$btTitleFormats),
                    'default' => 'h2',
                    'description' => 'The HTML element wrapping the title of the table.',
                ],
                'tableName' => [
                    'type' => ['string', 'null'],
                    'maxLength' => 255,
                    'description' => 'The title displayed above the table.',
                ],
                'tableDescription' => [
                    'type' => ['string', 'null'],
                    'maxLength' => 255,
                    'description' => 'The text displayed below the title of the table.',
                ],
                'tableStriped' => [
                    'type' => ['string', 'integer'],
                    'description' => 'Set it to 1 to give the rows of the table two alternating colors.',
                ],
                'rowBackgroundColorAlternate' => [
                    'type' => ['string', 'null'],
                    'description' => 'The color of the even rows of the table (it\'s used only when tableStriped is 1).',
                ],
                'headerBackgroundColor' => [
                    'type' => ['string', 'null'],
                    'description' => 'The background color of the header of the table.',
                ],
                'headerBackgroundColorActiveSort' => [
                    'type' => ['string', 'null'],
                    'description' => 'The background color of the column of the header the entries are sorted by.',
                ],
                'headerTextColor' => [
                    'type' => ['string', 'null'],
                    'description' => 'The color of the text of the header of the table.',
                ],
                'displayLimit' => [
                    'type' => ['string', 'integer', 'null'],
                    'description' => 'The number of entries displayed in a page.',
                ],
                'enablePagination' => [
                    'type' => ['string', 'integer', 'null'],
                    'description' => 'Set it to 1 to let the visitors move to the other pages of the list.',
                ],
                'enableItemsPerPageSelection' => [
                    'type' => ['string', 'integer', 'null'],
                    'description' => 'Set it to 1 to let the visitors choose how many entries a page holds.',
                ],
                'enableSearch' => [
                    'type' => ['string', 'integer', 'null'],
                    'description' => 'Set it to 1 to let the visitors search the entries (it\'s ignored when there is nothing to search by).',
                ],
                'enableKeywordSearch' => [
                    'type' => ['string', 'integer', 'null'],
                    'description' => 'Set it to 1 to let the visitors search the entries by keywords (it\'s used only when enableSearch is 1).',
                ],
                'searchProperties' => [
                    'type' => 'array',
                    'description' => 'The IDs of the attributes of the entity the visitors can search the entries by (they are used only when enableSearch is 1).',
                    'items' => ['type' => ['string', 'integer']],
                ],
                'searchAssociations' => [
                    'type' => 'array',
                    'description' => 'The IDs of the associations of the entity the visitors can search the entries by (they are used only when enableSearch is 1).',
                    'items' => ['type' => 'string'],
                ],
                'filterFields' => [
                    'type' => 'array',
                    'description' => 'The filters the listed entries always satisfy, whatever the visitors search.',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'key' => [
                                'type' => 'string',
                                'description' => 'The key identifying what the filter works on.',
                            ],
                            'data' => [
                                'type' => 'object',
                                'description' => 'The configuration of the filter, whose keys depend on what it works on.',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::getImportDataFromApiValue()
     */
    public function getImportDataFromApiValue($page, array $value): array
    {
        if ($this->bID) {
            // the save() method resets the settings that it doesn't receive: let's keep the current ones
            $value += $this->serializeValueForApi();
        }
        // the getImportData() method below reads the columns and the filters out of the XML of a CIF file
        $blockNode = new SimpleXMLElement('<block></block>');
        $dataNode = $blockNode->addChild('data');
        $dataNode->addAttribute('table', (string) $this->getBlockTypeDatabaseTable());
        $recordNode = $dataNode->addChild('record');
        $xml = $this->app->make(Xml::class);
        foreach ($value as $key => $keyValue) {
            if (!in_array($key, self::API_STRUCTURED_KEYS, true) && (is_string($keyValue) || is_int($keyValue) || is_float($keyValue))) {
                $xml->createChildElement($recordNode, (string) $key, (string) $keyValue);
            }
        }
        foreach ((array) ($value['searchAssociations'] ?? []) as $searchAssociation) {
            if (is_string($searchAssociation)) {
                $xml->createChildElement($recordNode, 'searchAssociation', $searchAssociation);
            }
        }
        $this->serializeApiColumnsForImport($recordNode, $value);
        $this->serializeApiFilterFieldsForImport($recordNode, $xml, $value);
        $args = $this->getImportData($blockNode, $page);
        // a CIF file refers to the attributes of the entity by their handle, the API by their ID
        foreach (['linkedProperties', 'searchProperties'] as $key) {
            $args[$key] = [];
            foreach ((array) ($value[$key] ?? []) as $attributeKeyID) {
                if (is_numeric($attributeKeyID)) {
                    $args[$key][] = (int) $attributeKeyID;
                }
            }
        }

        return $args;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\Traits\CustomApiValueTrait::serializeValueForApi()
     */
    protected function serializeValueForApi(): array
    {
        // the export() method below writes the columns and the filters as XML elements, since a CIF file
        // can't hold the serialized PHP objects that this block stores
        $blockNode = new SimpleXMLElement('<block></block>');
        $this->export($blockNode);
        $mainTable = (string) $this->getBlockTypeDatabaseTable();
        $result = [];
        $recordNode = null;
        foreach ($blockNode->data as $data) {
            if (strcasecmp((string) $data['table'], $mainTable) === 0 && isset($data->record)) {
                $recordNode = $data->record[0];
                $result = $this->serializeRecordForApi($mainTable, $recordNode);
                break;
            }
        }
        foreach (self::API_STRUCTURED_KEYS as $key) {
            unset($result[$key]);
        }
        // a CIF file refers to the attributes of the entity by their handle, the API by their ID
        foreach (['linkedProperties', 'searchProperties'] as $key) {
            $result[$key] = array_values(array_map('intval', array_filter((array) json_decode((string) $this->{$key}), 'is_numeric')));
        }
        $result['searchAssociations'] = [];
        foreach ((array) json_decode((string) $this->searchAssociations) as $searchAssociation) {
            if (is_string($searchAssociation)) {
                $result['searchAssociations'][] = $searchAssociation;
            }
        }
        $result['columns'] = [];
        $result['defaultSortColumn'] = null;
        $result['filterFields'] = [];
        if ($recordNode !== null) {
            foreach ($recordNode->columns[0]->column ?? [] as $columnNode) {
                $result['columns'][] = [
                    'key' => (string) $columnNode['key'],
                    'sortDirection' => (string) $columnNode['sort-direction'],
                ];
            }
            $defaultSortColumnKey = (string) ($recordNode->columns[0]['default-sort-column-key'] ?? '');
            if ($defaultSortColumnKey !== '') {
                $result['defaultSortColumn'] = [
                    'key' => $defaultSortColumnKey,
                    'direction' => (string) ($recordNode->columns[0]['default-sort-column-direction'] ?? ''),
                ];
            }
            foreach ($recordNode->filterFields[0]->field ?? [] as $fieldNode) {
                $data = json_decode((string) $fieldNode->data, true);
                $result['filterFields'][] = [
                    'key' => (string) $fieldNode['key'],
                    'data' => is_array($data) ? $data : [],
                ];
            }
        }

        return $result;
    }

    /**
     * Write in the XML of a CIF file the columns held by a value received via the API.
     *
     * @param array<string,mixed> $value
     */
    private function serializeApiColumnsForImport(SimpleXMLElement $recordNode, array $value): void
    {
        $columnsNode = $recordNode->addChild('columns');
        $defaultSortColumn = $value['defaultSortColumn'] ?? null;
        if (is_array($defaultSortColumn) && isset($defaultSortColumn['key'])) {
            $columnsNode['default-sort-column-key'] = (string) $defaultSortColumn['key'];
            $columnsNode['default-sort-column-direction'] = (string) ($defaultSortColumn['direction'] ?? '');
        }
        foreach ((array) ($value['columns'] ?? []) as $column) {
            if (!is_array($column) || !isset($column['key'])) {
                continue;
            }
            $columnNode = $columnsNode->addChild('column');
            $columnNode->addAttribute('key', (string) $column['key']);
            $columnNode->addAttribute('sort-direction', (string) ($column['sortDirection'] ?? ''));
        }
    }

    /**
     * Write in the XML of a CIF file the filters held by a value received via the API.
     *
     * @param array<string,mixed> $value
     */
    private function serializeApiFilterFieldsForImport(SimpleXMLElement $recordNode, Xml $xml, array $value): void
    {
        $filterFieldsNode = $recordNode->addChild('filterFields');
        foreach ((array) ($value['filterFields'] ?? []) as $filterField) {
            if (!is_array($filterField) || !isset($filterField['key'])) {
                continue;
            }
            $fieldNode = $filterFieldsNode->addChild('field');
            $fieldNode->addAttribute('key', (string) $filterField['key']);
            $data = $filterField['data'] ?? [];
            $xml->createChildElement($fieldNode, 'data', json_encode(is_array($data) ? $data : []));
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::export()
     */
    public function export(SimpleXMLElement $blockNode)
    {
        $this->app->make(Localization::class)->withContext(Localization::CONTEXT_SYSTEM, function() use ($blockNode) {
            $this->doExport($blockNode);
        });
    }

    private function doExport(SimpleXMLElement $blockNode): void
    {
        parent::export($blockNode);
        $this->on_start();
        $xRecord = $blockNode->data[0]->record[0];
        $xml = $this->app->make(Xml::class);
        $entity = $this->entityManager->find(Entity::class, $this->exEntityID);
        if ($entity !== null) {
            $xRecord->exEntityID[0]['handle'] = $entity->getHandle();
        }
        $entityAttributes = $entity ? $entity->getAttributes()->toArray() : [];
        $entityAssociations = $entity ? $entity->getAssociations()->toArray() : [];

        unset($xRecord->linkedProperties[0]);
        $linkedPropertyIDs = array_map('intval', (array) json_decode($this->linkedProperties));
        foreach ($linkedPropertyIDs as $linkedPropertyID) {
            $linkedProperty = Arr::first(
                $entityAttributes,
                static function($attribute) use ($linkedPropertyID) {
                    return $linkedPropertyID === (int) $attribute->getAttributeKeyID();
                }
            );
            if ($linkedProperty !== null) {
                $xml->createChildElement($xRecord, 'linkedProperty', $linkedProperty->getAttributeKeyHandle());
            }
        }

        unset($xRecord->searchProperties[0]);
        $searchPropertyIDs = array_map('intval', (array) json_decode($this->searchProperties));
        foreach ($searchPropertyIDs as $searchPropertyID) {
            $searchProperty = Arr::first(
                $entityAttributes,
                static function($attribute) use ($searchPropertyID): bool {
                    return $searchPropertyID === (int) $attribute->getAttributeKeyID();
                }
                );
            if ($searchProperty !== null) {
                $xml->createChildElement($xRecord, 'searchProperty', $searchProperty->getAttributeKeyHandle());
            }
        }

        $searchAssociationUUIDs = (array) json_decode($this->searchAssociations);
        foreach ($searchAssociationUUIDs as $searchAssociationUUID) {
            $association = Arr::first(
                $entityAssociations,
                static function ($association) use ($searchAssociationUUID): bool {
                    return $association->getId() === $searchAssociationUUID;
                }
            );
            $xNode = $xml->createChildElement($xRecord, 'searchAssociation', $searchAssociationUUID);
            if ($association !== null) {
                $xNode->addAttribute('target-property-name', (string) $association->getTargetPropertyName());
            }
        }
        unset($xRecord->searchAssociations[0]);

        unset($xRecord->filterFields[0]);
        $xFilterFields = $xRecord->addChild('filterFields');
        $filterFields = $this->filterFields ? unserialize($this->filterFields) : [];
        if (is_array($filterFields)) {
            foreach ($filterFields as $filterField) {
                if ($filterField instanceof \Concrete\Core\Search\Field\FieldInterface) {
                    $filterField->export($xFilterFields);
                }
            }
        }

        unset($xRecord->columns[0]);
        $xColumns = $xRecord->addChild('columns');
        $columnSet = $this->columns ? unserialize($this->columns) : null;
        if ($columnSet instanceof ColumnSet) {
            $defaultSortColumn = $columnSet->getDefaultSortColumn();
            if ($defaultSortColumn) {
                $xColumns['default-sort-column-key'] = $defaultSortColumn->getColumnKey();
                $xColumns['default-sort-column-name'] = $defaultSortColumn->getColumnName();
                $xColumns['default-sort-column-direction'] = $defaultSortColumn->getColumnSortDirection();
            }
            foreach ($columnSet->getColumns() as $column) {
                $xColumn = $xColumns->addChild('column');
                $xColumn->addAttribute('key', (string) $column->getColumnKey());
                $xColumn->addAttribute('name', (string) $column->getColumnName());
                $xColumn->addAttribute('sort-direction', (string) $column->getColumnSortDirection());
            }
        }
    }

    public function action_load_entity_data()
    {
        $exEntityID = $this->request->request->get('exEntityID');
        if ($exEntityID) {
            $entity = $this->entityManager->find(Entity::class, $exEntityID);
            if (is_object($entity)) {
                $provider = $this->app->make(SearchProvider::class, ['entity' => $entity, 'category' => $entity->getAttributeKeyCategory()]);
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

        $this->app->shutdown();
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
