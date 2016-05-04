<?php
namespace Concrete\Controller\Search;

use Concrete\Core\File\Search\ColumnSet\DefaultSet;
use Concrete\Core\Http\ResponseAssetGroup;
use Concrete\Core\Search\Field\ManagerFactory;
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

            $keywords = htmlentities($this->request->get('fKeywords'), ENT_QUOTES, APP_CHARSET);

            if ($keywords != '') {
                $this->fileList->filterByKeywords($keywords);
            }

            $columns = new DefaultSet();
            $result = new FileSearchResult($columns, $this->fileList);
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
                    return new JsonResponse($result->getJSONObject());
                }
            }
        }

    }



}
