<?php
namespace Concrete\Controller\Element\Search;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Search\Field\ManagerInterface;
use Concrete\Core\Search\ProviderInterface;

class SearchFieldSelector extends ElementController
{

    /**
     * @var string
     */
    protected $addFieldAction;

    protected $fieldManager;

    protected $query;

    public function __construct(ManagerInterface $fieldManager, $addFieldAction, Query $query = null)
    {
        parent::__construct();
        $this->fieldManager = $fieldManager;
        $this->query = $query;
        $this->addFieldAction = $addFieldAction;
    }


    public function getElement()
    {
        return 'search/search_field_selector';
    }

    /**
     * @return string
     */
    public function getAddFieldAction()
    {
        return $this->addFieldAction;
    }

    /**
     * @param string $addFieldAction
     */
    public function setAddFieldAction(string $addFieldAction)
    {
        $this->addFieldAction = $addFieldAction;
    }

    /**
     * @return ManagerInterface
     */
    public function getFieldManager()
    {
        return $this->fieldManager;
    }

    /**
     * @param ManagerInterface $fieldManager
     */
    public function setFieldManager(ManagerInterface $fieldManager)
    {
        $this->fieldManager = $fieldManager;
    }

    /**
     * @return Query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param Query $query
     */
    public function setQuery(Query $query)
    {
        $this->query = $query;
    }



    public function view()
    {
        $this->requireAsset('selectize');
        $this->set('manager', $this->fieldManager);
        $this->set('addFieldAction', $this->addFieldAction);
        $this->set('query', $this->query);
    }
}
