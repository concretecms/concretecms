<?php
namespace Concrete\Controller\Dialog\File;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Controller\Element\Search\CustomizeResults;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Entity\Search\SavedFileSearch;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\Search\ColumnSet\Available;
use Concrete\Core\File\Search\ColumnSet\ColumnSet;
use Concrete\Core\File\Search\Result\Result;
use Concrete\Core\Search\Field\ManagerFactory;
use FilePermissions;
use Loader;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdvancedSearch extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/file/advanced_search';

    protected function canAccess()
    {
        $cp = FilePermissions::getGlobal();
        if ($cp->canSearchFiles()) {
            return true;
        } else {
            return false;
        }
    }

    public function view()
    {
        $manager = ManagerFactory::get('file');
        $provider = $this->app->make('Concrete\Core\File\Search\SearchProvider');
        $element = new CustomizeResults($provider);
        $this->set('customizeElement', $element);
        $this->set('manager', $manager);
    }

    public function addField()
    {
        $manager = ManagerFactory::get('file');
        $field = $this->request->request->get('field');
        $field = $manager->getFieldByKey($field);
        if (is_object($field)) {
            return new JsonResponse($field);
        }
    }

    protected function getQueryFromRequest()
    {
        $query = new Query();
        $manager = ManagerFactory::get('file');
        $fields = $manager->getFieldsFromRequest($this->request->request->all());

        $set = new ColumnSet();
        $available = new Available();
        foreach ($this->request->request->get('column') as $key) {
            $set->addColumn($available->getColumnByKey($key));
        }
        $sort = $available->getColumnByKey($this->request->request->get('fSearchDefaultSort'));
        $set->setDefaultSortColumn($sort, $this->request->request->get('fSearchDefaultSortDirection'));

        $query->setFields($fields);
        $query->setColumns($set);
        return $query;
    }

    public function savePreset()
    {
        if ($this->validateAction()) {
            $query = $this->getQueryFromRequest();

            $em = \Database::connection()->getEntityManager();
            $search = new SavedFileSearch();
            $search->setQuery($query);
            $search->setPresetName($this->request->request->get('presetName'));
            $em->persist($search);
            $em->flush();

            $filesystem = new Filesystem();
            $folder = $filesystem->getRootFolder();
            $node = \Concrete\Core\Tree\Node\Type\SearchPreset::add($search, $folder);

            $provider = $this->app->make('Concrete\Core\File\Search\SearchProvider');
            $result = $provider->getSearchResultFromQuery($query);
            $result->setBaseURL(\URL::to('/ccm/system/search/files/preset', $search->getID()));

            return new JsonResponse($result->getJSONObject());
        }
    }

    public function submit()
    {
        if ($this->validateAction()) {
            $query = $this->getQueryFromRequest();

            $provider = $this->app->make('Concrete\Core\File\Search\SearchProvider');
            $provider->setSessionCurrentQuery($query);

            $result = $provider->getSearchResultFromQuery($query);
            $result->setBaseURL(\URL::to('/ccm/system/search/files/current'));
            return new JsonResponse($result->getJSONObject());
        }
    }
}
