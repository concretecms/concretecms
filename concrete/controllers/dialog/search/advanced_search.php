<?php
namespace Concrete\Controller\Dialog\Search;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Support\Facade\Application;
use Concrete\Controller\Element\Search\CustomizeResults;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Entity\Search\SavedSearch;
use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\Search\ProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManager;
use Exception;

abstract class AdvancedSearch extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/search/advanced_search';
    protected $supportsSavedSearch = true;
    public $objectID = null;

    public function getObjectID()
    {
        return (string) $this->objectID;
    }

    abstract public function getFieldManager();

    abstract public function getSavedSearchBaseURL(SavedSearch $search);

    abstract public function getBasicSearchBaseURL();

    abstract public function getCurrentSearchBaseURL();

    abstract public function getSavedSearchEntity();

    public function getAddFieldAction()
    {
        return $this->action('add_field');
    }

    public function getSubmitAction()
    {
        return $this->action('submit');
    }

    /**
     * @return ProviderInterface
     */
    abstract public function getSearchProvider();

    protected function onAfterSavePreset(SavedSearch $search)
    {
        return;
    }

    public function getSearchQuery()
    {
        $query = new Query();
        $query->setFields([new KeywordsField()]);

        return $query;
    }

    protected function getRequestDefaultSort()
    {
        return $this->request->request->get('fSearchDefaultSort');
    }

    protected function getRequestDefaultSortDirection()
    {
        return $this->request->request->get('fSearchDefaultSortDirection');
    }

    protected function getCustomizeResultsElement()
    {
        $provider = $this->getSearchProvider();
        $element = new CustomizeResults($provider);

        return $element;
    }

    protected function getSearchPresets()
    {
        $searchPresets = [];
        $app = Application::getFacadeApplication();
        $searchEntity = $this->getSavedSearchEntity();
        if (!empty($searchEntity)) {
            $searchPresets = $searchEntity->findAll();
        }

        return $searchPresets;
    }

    public function view()
    {
        $app = Application::getFacadeApplication();
        $this->requireAsset('selectize');

        $manager = $this->getFieldManager();
        $query = $this->getSearchQuery();

        $this->set('defaultQuery', json_encode($query));
        $this->set('customizeElement', $this->getCustomizeResultsElement());
        if ($this->supportsSavedSearch) {
            $this->set('searchPresets', $this->getSearchPresets());
        }
        $this->set('manager', $manager);
        $this->set('supportsSavedSearch', $this->supportsSavedSearch);
        $this->set('form', $app->make('helper/form'));
    }

    public function addField()
    {
        $manager = $this->getFieldManager();
        $field = $this->request->request->get('field');
        $field = $manager->getFieldByKey($field);
        if (is_object($field)) {
            return new JsonResponse($field);
        }
    }

    protected function getQueryFromRequest()
    {
        $query = new Query();
        $manager = $this->getFieldManager();
        $fields = $manager->getFieldsFromRequest($this->request->request->all());
        $provider = $this->getSearchProvider();

        $set = $provider->getBaseColumnSet();
        $available = $provider->getAvailableColumnSet();

        foreach ($this->request->request->get('column') as $key) {
            $set->addColumn($available->getColumnByKey($key));
        }

        $sort = $available->getColumnByKey($this->getRequestDefaultSort());
        $set->setDefaultSortColumn($sort, $this->getRequestDefaultSortDirection());

        $query->setFields($fields);
        $query->setColumns($set);

        $itemsPerPage = $this->getItemsPerPage();
        $query->setItemsPerPage($itemsPerPage);

        return $query;
    }

    public function savePreset()
    {
        if ($this->validateAction() && $this->supportsSavedSearch) {
            $app = Application::getFacadeApplication();
            $query = $this->getQueryFromRequest();
            if (is_object($query)) {
                $provider = $this->getSearchProvider();
                if (is_object($provider)) {
                    $em = $app->make(EntityManager::class);
                    $search = $provider->getSavedSearch();
                    if (is_object($search)) {
                        $search->setQuery($query);
                        $search->setPresetName($this->request->request->get('presetName'));
                        $em->persist($search);
                        $em->flush();

                        $this->onAfterSavePreset($search);

                        $result = $provider->getSearchResultFromQuery($query);
                        $result->setBaseURL($this->getSavedSearchBaseURL($search));

                        return new JsonResponse($result->getJSONObject());
                    }
                }
            }
        }
        throw new Exception(t('An error occurred while saving the search preset.'));
    }

    public function submit()
    {
        if ($this->validateAction()) {
            $query = $this->getQueryFromRequest();

            $provider = $this->getSearchProvider();
            $provider->setSessionCurrentQuery($query);

            $result = $provider->getSearchResultFromQuery($query);
            $result->setBaseURL($this->getCurrentSearchBaseURL());

            return new JsonResponse($result->getJSONObject());
        }
    }

    /**
     * @return int
     */
    private function getItemsPerPage()
    {
        return $this->request->request->get('fSearchItemsPerPage');
    }
}
