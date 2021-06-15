<?php
namespace Concrete\Controller\Dialog\Search;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Controller\Element\Search\SearchFieldSelector;
use Concrete\Core\Foundation\Serializer\JsonSerializer;
use Concrete\Core\Search\Query\QueryFactory;
use Concrete\Core\Search\Result\ResultFactory;
use Concrete\Core\Support\Facade\Application;
use Concrete\Controller\Element\Search\CustomizeResults;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Entity\Search\SavedSearch;
use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\Search\ProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManager;
use Exception;
use Symfony\Component\HttpFoundation\Request;

abstract class AdvancedSearch extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/search/advanced_search';
    protected $supportsSavedSearch = true;

    abstract public function getFieldManager();

    abstract public function getSearchPresets();

    abstract public function getSavedSearchBaseURL(SavedSearch $search);

    public function getAddFieldAction()
    {
        return $this->action('add_field');
    }

    public function getSubmitAction()
    {
        return $this->action('submit');
    }

    public function getSavePresetAction()
    {
        return $this->action('save_preset');
    }

    public function getSubmitMethod()
    {
        return 'post';
    }

    /**
     * @return ProviderInterface
     */
    abstract public function getSearchProvider();

    protected function onAfterSavePreset(SavedSearch $search)
    {
        return;
    }

    public function deserializeQuery($jsonQuery)
    {
        return $this->app->make(JsonSerializer::class)
            ->deserialize($jsonQuery, Query::class, 'json', [
                    'searchProvider' => $this->getSearchProvider()
                ]
            );
    }

    protected function getSearchFieldSelectorElement()
    {
        $bag = $this->request->getMethod() === Request::METHOD_POST ? $this->request->request : $this->request->query;
        if ($bag->has('query')) {
            $query = $this->deserializeQuery($bag->get('query'));
        } else {
            $query = new Query();
        }

        $element = new SearchFieldSelector($this->getFieldManager(), $this->getAddFieldAction(), $query);
        return $element;
    }


    protected function getCustomizeResultsElement()
    {
        $query = null;
        $bag = $this->request->getMethod() === Request::METHOD_POST ? $this->request->request : $this->request->query;
        if ($bag->has('query')) {
            $query = $this->deserializeQuery($bag->get('query'));
        }
        $provider = $this->getSearchProvider();
        $element = new CustomizeResults($provider, $query);
        return $element;
    }

    public function view()
    {
        $app = Application::getFacadeApplication();
        $this->set('customizeElement', $this->getCustomizeResultsElement());
        $this->set('searchFieldSelectorElement', $this->getSearchFieldSelectorElement());
        if ($this->supportsSavedSearch) {
            $this->set('searchPresets', $this->getSearchPresets());
        }
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

    public function savePreset()
    {
        if ($this->validateAction() && $this->supportsSavedSearch) {
            $provider = $this->getSearchProvider();
            $query = $this->app->make(QueryFactory::class)
                ->createFromAdvancedSearchRequest($provider, $this->request, Request::METHOD_POST);
            if (is_object($query)) {
                $em = $this->app->make(EntityManager::class);
                $search = $provider->getSavedSearch();
                if (is_object($search)) {
                    $search->setQuery($query);
                    $search->setPresetName($this->request->request->get('presetName'));
                    $em->persist($search);
                    $em->flush();

                    $this->onAfterSavePreset($search);

                    $result = $this->app->make(ResultFactory::class)->createFromQuery($provider, $query);

                    return new JsonResponse($result->getJSONObject());
                }
            }
        }
        throw new Exception(t('An error occurred while saving the search preset.'));
    }

    /**
     * @deprecated. Remove when all advanced search controllers use the files method.
     * @return Query
     */
    public function getSearchQuery()
    {
        $query = new Query();
        $query->setFields([new KeywordsField()]);

        return $query;
    }


    /**
     * @deprecated. Remove when all advanced search controllers that extend this one no longer need the submit() method
     * @return mixed
     */
    protected function getQueryFromRequest()
    {
        $queryFactory = $this->app->make(QueryFactory::class);
        return $queryFactory->createFromAdvancedSearchRequest($this->getSearchProvider(), $this->request);
    }

    /**
     * @deprecated. Remove when all advanced search controllers that extend this one no longer need the submit() method
     * Files already doesn't use this.
     * @return mixed
     */
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

}
