<?php
namespace Concrete\Controller\Search;

use Concrete\Core\Entity\Search\Query;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\Search\ColumnSet\DefaultSet;
use Concrete\Core\File\Search\Field\Field\KeywordsField;
use Concrete\Core\Http\ResponseAssetGroup;
use Concrete\Core\Search\Field\ManagerFactory;
use Concrete\Core\Tree\Node\Type\SearchPreset;
use Controller;
use FileList;
use Concrete\Core\Search\StickyRequest;
use Concrete\Core\File\Search\ColumnSet\ColumnSet as FileSearchColumnSet;
use Concrete\Core\File\Search\Result\Result as FileSearchResult;
use FileAttributeKey;
use Loader;
use FileSet;
use Symfony\Component\HttpFoundation\JsonResponse;
use URL;
use Concrete\Core\File\Type\Type as FileType;
use FilePermissions;
use stdClass;

class Files extends Controller
{
    protected $fields = array();

    /** @var \Concrete\Core\File\FileList */
    protected $fileList;

    public function __construct()
    {
        parent::__construct();
        $this->fileList = new FileList();
    }

    public function searchBasic()
    {
        $cp = FilePermissions::getGlobal();
        if ($cp->canSearchFiles() || $cp->canAddFile()) {

            $provider = \Core::make('Concrete\Core\File\Search\SearchProvider');
            $keywords = htmlentities($this->request->get('fKeywords'), ENT_QUOTES, APP_CHARSET);
            $query = new Query();
            $fields = array();
            if ($keywords) {
                $fields[] = new KeywordsField($keywords);
            }

            // If we are passing in something like "filter by images, it will be here.
            $manager = ManagerFactory::get('file');
            $fields = array_merge($fields, $manager->getFieldsFromRequest($this->request->query->all()));

            $query->setFields($fields);
            $query->setColumns(new DefaultSet());
            $result = $provider->getSearchResultFromQuery($query);
            $result->setBaseURL((string) \URL::to('/ccm/system/search/files/basic'));

            // Also, if the request contains "fields", then that means we're doing something like
            // passing "filter by images" from the file selector into the search.

            return new JsonResponse($result->getJSONObject());
        } else {
            return false;
        }
    }

    public function clearSearch()
    {
        $cp = FilePermissions::getGlobal();
        if ($cp->canSearchFiles() || $cp->canAddFile()) {
            $provider = $this->app->make('Concrete\Core\File\Search\SearchProvider');
            $provider->clearSessionCurrentQuery();

            // Fall back to file folders search.

            $search = new FileFolder();
            $search->search();
            $result = $search->getSearchResultObject();

            return new JsonResponse($result->getJSONObject());
        } else {
            return false;
        }
    }


    public function searchCurrent()
    {
        $cp = FilePermissions::getGlobal();
        if ($cp->canSearchFiles()) {

            $provider = $this->app->make('Concrete\Core\File\Search\SearchProvider');
            $query = $provider->getSessionCurrentQuery();
            if (is_object($query)) {
                $result = $provider->getSearchResultFromQuery($query);
                $result->setBaseURL(\URL::to('/ccm/system/search/files/current'));
                return new JsonResponse($result->getJSONObject());
            }
        }

    }

    public function searchPreset($presetID)
    {
        $cp = FilePermissions::getGlobal();
        if ($cp->canSearchFiles()) {

            $em = \Database::connection()->getEntityManager();
            $preset = $em->find('Concrete\Core\Entity\Search\SavedFileSearch', $presetID);
            if (is_object($preset)) {
                $query = $preset->getQuery();
                if (is_object($query)) {
                    $provider = $this->app->make('Concrete\Core\File\Search\SearchProvider');
                    $result = $provider->getSearchResultFromQuery($query);
                    $result->setBaseURL(\URL::to('/ccm/system/search/files/preset', $presetID));

                    $filesystem = new Filesystem();
                    $root = $filesystem->getRootFolder();
                    $breadcrumb = [];
                    $breadcrumb[] = [
                        'active' => false,
                        'name' => $root->getTreeNodeDisplayName(),
                        'folder' => $root->getTreeNodeID(),
                        'menu' => $root->getTreeNodeMenu(),
                        'url' => (string) \URL::to('/ccm/system/file/folder/contents'),
                    ];

                    $node = SearchPreset::getNodeBySavedSearchID($presetID);

                    $breadcrumb[] = [
                        'active' => true,
                        'name' => $node->getTreeNodeDisplayName(),
                        'folder' => $node->getTreeNodeID(),
                        'menu' => $node->getTreeNodeMenu(),
                        'url' => false
                    ];

                    $result->setBreadcrumb($breadcrumb);

                    return new JsonResponse($result->getJSONObject());
                }
            }
        }

    }



}
