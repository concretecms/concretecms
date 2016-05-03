<?php
namespace Concrete\Controller\Search;

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
        $this->searchRequest = new StickyRequest('files');
        $this->fileList = new FileList($this->searchRequest);
    }

    public function search()
    {
        $cp = FilePermissions::getGlobal();
        if ($cp->canSearchFiles() || $cp->canAddFile()) {
            if ($_REQUEST['submitSearch']) {
                $this->searchRequest->resetSearchRequest();
            }

            $req = $this->searchRequest->getSearchRequest();
            $columns = FileSearchColumnSet::getCurrent();

            if (!$this->fileList->getActiveSortColumn()) {
                $col = $columns->getDefaultSortColumn();
                $this->fileList->sanitizedSortBy($col->getColumnKey(), $col->getColumnDefaultSortDirection());
            }

            // first thing, we check to see if a saved search is being used
            /*
            if (isset($req['fssID'])) {
                $fs = FileSet::getByID($req['fssID']);
                if ($fs->getFileSetType() == FileSet::TYPE_SAVED_SEARCH) {
                    $req = $fs->getSavedSearchRequest();
                    $columns = $fs->getSavedSearchColumns();
                    $colsort = $columns->getDefaultSortColumn();
                    $this->fileList->addToSearchRequest('ccm_order_dir', $colsort->getColumnDefaultSortDirection());
                    $this->fileList->addToSearchRequest('ccm_order_by', $colsort->getColumnKey());
                }
            }
            */

            $keywords = htmlentities($req['fKeywords'], ENT_QUOTES, APP_CHARSET);

            if ($keywords != '') {
                $this->fileList->filterByKeywords($keywords);
            }

            if ($req['numResults']) {
                $this->fileList->setItemsPerPage(intval($req['numResults']));
            }

            if ((isset($req['fsIDNone']) && $req['fsIDNone'] == 1) || (is_array($req['fsID']) && in_array(-1, $req['fsID']))) {
                $this->fileList->filterByNoSet();
            } else {
                if (is_array($req['fsID'])) {
                    foreach ($req['fsID'] as $fsID) {
                        $fs = FileSet::getByID($fsID);
                        if (is_object($fs)) {
                            $this->fileList->filterBySet($fs);
                        }
                    }
                } elseif (isset($req['fsID']) && $req['fsID'] != '' && $req['fsID'] > 0) {
                    $set = $req['fsID'];
                    $fs = FileSet::getByID($set);
                    if (is_object($fs)) {
                        $this->fileList->filterBySet($fs);
                    }
                }
            }

            $manager = ManagerFactory::get('file');
            $manager->filterListByRequest($this->fileList, $req);

            if (isset($req['numResults'])) {
                $this->fileList->setItemsPerPage(intval($req['numResults']));
            }

            $this->fileList->setPermissionsChecker(function ($file) {
                $cp = new \Permissions($file);

                return $cp->canViewFileInFileManager();
            });

            $ilr = new FileSearchResult($columns, $this->fileList, URL::to('/ccm/system/search/files/submit'), $this->fields);
            $this->result = $ilr;
        } else {
            return false;
        }
    }

    public function getSearchResultObject()
    {
        return $this->result;
    }

    public function getListObject()
    {
        return $this->fileList;
    }

    public function submit()
    {
        $this->search();
        Loader::helper('ajax')->sendResult($this->result->getJSONObject());
    }


}
