<?
namespace Concrete\Block\ExpressEntryList;

use Concrete\Controller\Element\Search\CustomizeResults;
use \Concrete\Core\Block\BlockController;
use Concrete\Core\Express\Entry\Search\Result\Result;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Search\Result\ItemColumn;
use Concrete\Core\Support\Facade\Facade;
use Symfony\Component\HttpFoundation\JsonResponse;

class Controller extends BlockController
{

    protected $btInterfaceWidth = "640";
    protected $btInterfaceHeight = "400";
    protected $btTable = 'btExpressEntryList';
    protected $entityAttributes = array();

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

    public function add()
    {
        $this->loadData();
        $this->set('searchProperties', []);
        $this->set('searchPropertiesSelected', []);
        $this->set('linkedProperties', []);
    }

    public function edit()
    {
        $this->loadData();
        if ($this->exEntityID) {
            $entity = $this->entityManager->find('Concrete\Core\Entity\Express\Entity', $this->exEntityID);
            if (is_object($entity)) {
                $searchPropertiesSelected = (array) json_decode($this->searchProperties);
                $linkedPropertiesSelected = (array) json_decode($this->linkedProperties);
                $searchProperties = $this->getSearchPropertiesJsonArray($entity);
                $columns = unserialize($this->columns);
                $provider = \Core::make('Concrete\Core\Express\Search\SearchProvider', array($entity));
                if ($columns) {
                    $provider->setColumnSet($columns);
                }

                $element = new CustomizeResults($provider);
                $this->set('customizeElement', $element);
                $this->set('linkedPropertiesSelected', $linkedPropertiesSelected);
                $this->set('searchPropertiesSelected', $searchPropertiesSelected);
                $this->set('searchProperties', $searchProperties);
            }
        }

    }

    protected function getSearchPropertiesJsonArray($entity)
    {
        $attributes = $entity->getAttributeKeyCategory()->getList();
        $select = array();
        foreach($attributes as $ak) {
            $o = new \stdClass;
            $o->akID = $ak->getAttributeKeyID();
            $o->akName = $ak->getAttributeKeyDisplayName();
            $select[] = $o;
        }
        return $select;
    }

    public function view()
    {
        $entity = $this->entityManager->find('Concrete\Core\Entity\Express\Entity', $this->exEntityID);
        if (is_object($entity)) {
            $category = $entity->getAttributeKeyCategory();
            $list = new EntryList($entity);
            $list->setItemsPerPage($this->displayLimit);
            $set = unserialize($this->columns);
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

            if ($this->request->query->has('keywords') && $this->enableSearch) {
                $list->filterByKeywords($this->request->query->get('keywords'));
            }

            $tableSearchProperties = array();
            $searchPropertiesSelected = (array) json_decode($this->searchProperties);
            foreach($searchPropertiesSelected as $akID) {
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

            $result = new Result($set, $list);
            $pagination = $list->getPagination();
            if ($pagination->getTotalPages() > 1) {
                $pagination = $pagination->renderDefaultView();
                $this->set('pagination', $pagination);
            }

            $this->set('list', $list);
            $this->set('result', $result);
            $this->set('entity', $entity);
            $this->set('tableSearchProperties', $tableSearchProperties);
            $this->set('detailPage', $this->getDetailPageObject());
        }
    }

    public function save($data)
    {
        $this->on_start();

        if (isset($data['searchProperties']) && is_array($data['searchProperties'])) {
            $searchProperties = $data['searchProperties'];
            $data['searchProperties'] = json_encode($searchProperties);
        }
        if (isset($data['linkedProperties']) && is_array($data['linkedProperties'])) {
            $linkedProperties = $data['linkedProperties'];
            $data['linkedProperties'] = json_encode($linkedProperties);
        }

        $entity = $this->entityManager->find('Concrete\Core\Entity\Express\Entity', $data['exEntityID']);
        if (is_object($entity)) {

            $provider = $this->app->make('Concrete\Core\Express\Search\SearchProvider', array($entity));
            $set = $this->app->make('Concrete\Core\Express\Search\ColumnSet\ColumnSet');
            $available = $provider->getAvailableColumnSet();
            foreach ($this->request->request->get('column') as $key) {
                $set->addColumn($available->getColumnByKey($key));
            }

            $sort = $available->getColumnByKey($this->request->request->get('fSearchDefaultSort'));
            $set->setDefaultSortColumn($sort, $this->request->request->get('fSearchDefaultSortDirection'));

            $data['columns'] = serialize($set);

        }

        parent::save($data);
    }

    public function action_load_entity_data()
    {
        $exEntityID = $this->request->request->get('exEntityID');
        if ($exEntityID) {
            $entity = $this->entityManager->find('Concrete\Core\Entity\Express\Entity', $exEntityID);
            if (is_object($entity)) {
                $provider = \Core::make('Concrete\Core\Express\Search\SearchProvider', array($entity));
                $element = new CustomizeResults($provider);
                $r = new \stdClass;
                ob_start();
                $element->getViewObject()->render();
                $r->customize = ob_get_contents();
                ob_end_clean();

                $r->attributes = $this->getSearchPropertiesJsonArray($entity);
                return new JsonResponse($r);
            }
        }

        \Core::make('app')->shutdown();
    }


    public function loadData()
    {
        $r = $this->entityManager->getRepository('Concrete\Core\Entity\Express\Entity');
        $entityObjects = $r->findAll();
        $entities = array('' => t("** Choose Entity"));
        foreach($entityObjects as $entity) {
            $entities[$entity->getID()] = $entity->getName();
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

        if ($ak = $column->getColumn()->getAttributeKey()) {
            return in_array($ak->getAttributeKeyID(), $linkedProperties);
        }
    }

}
