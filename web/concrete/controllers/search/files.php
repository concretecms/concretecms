<?php
namespace Concrete\Controller\Search;

use Controller;
use FileList;
use \Concrete\Core\Search\StickyRequest;
use \Concrete\Core\File\Search\ColumnSet\ColumnSet as FileSearchColumnSet;
use \Concrete\Core\File\Search\Result\Result as FileSearchResult;
use FileAttributeKey;
use Permissions;
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
        if (!$cp->canSearchFiles()) {
            return false;
        }

        if ($_REQUEST['submitSearch']) {
            $this->searchRequest->resetSearchRequest();
        }

        $req = $this->searchRequest->getSearchRequest();
        $columns = FileSearchColumnSet::getCurrent();

        if (!$this->fileList->getActiveSortColumn()) {
            $col = $columns->getDefaultSortColumn();
            $this->fileList->sortBy($col->getColumnKey(), $col->getColumnDefaultSortDirection());
        }

        // first thing, we check to see if a saved search is being used
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
                    $this->fileList->filterBySet($fs);
                }
            } elseif (isset($req['fsID']) && $req['fsID'] != '' && $req['fsID'] > 0) {
                $set = $req['fsID'];
                $fs = FileSet::getByID($set);
                $this->fileList->filterBySet($fs);
            }
        }

        if (isset($req['fType']) && $req['fType'] != '') {
            $type = $req['fType'];
            $this->fileList->filterByType($type);
        }

        if (isset($req['fExtension']) && $req['fExtension'] != '') {
            $ext = $_GET['fExtension'];
            $fileList->filterByExtension($ext);
        }

        $selectedSets = array();
        if (is_array($req['field'])) {
            foreach ($req['field'] as $i => $item) {
                $this->fields[] = $this->getField($item);
                // due to the way the form is setup, index will always be one more than the arrays
                if ($item != '') {
                    switch ($item) {
                        case "extension":
                            $extension = $req['extension'];
                            $this->fileList->filterByExtension($extension);
                            break;
                        case "type":
                            $type = $req['type'];
                            $this->fileList->filterByType($type);
                            break;
                        case "date_added":
                            $wdt = Loader::helper('form/date_time');
                            /* @var $wdt \Concrete\Core\Form\Service\Widget\DateTime */
                            $dateFrom = $wdt->translate('date_added_from', $req);
                            if ($dateFrom) {
                                $this->fileList->filterByDateAdded($dateFrom, '>=');
                            }
                            $dateTo = $wdt->translate('date_added_to', $req);
                            if ($dateTo) {
                                if (preg_match('/^(.+\\d+:\\d+):00$/', $dateTo, $m)) {
                                    $dateTo = $m[1] . ':59';
                                }
                                $this->fileList->filterByDateAdded($dateTo, '<=');
                            }
                            break;
                        case 'added_to':
                            $ocID = $req['ocIDSearchField'];
                            if ($ocID > 0) {
                                $this->fileList->filterByOriginalPageID($ocID);
                            }
                            break;
                        case "size":
                            $from = $req['size_from'];
                            $to = $req['size_to'];
                            $this->fileList->filterBySize($from, $to);
                            break;
                        default:
                            $akID = $item;
                            $fak = FileAttributeKey::get($akID);
                            $type = $fak->getAttributeType();
                            $cnt = $type->getController();
                            $cnt->setRequestArray($req);
                            $cnt->setAttributeKey($fak);
                            $cnt->searchForm($this->fileList);
                            break;
                    }
                }
            }
        }
        if (isset($req['numResults'])) {
            $this->fileList->setItemsPerPage(intval($req['numResults']));
        }

        $ilr = new FileSearchResult($columns, $this->fileList, URL::to('/ccm/system/search/files/submit'), $this->fields);
        $this->result = $ilr;
    }

    public function getSearchResultObject()
    {
        return $this->result;
    }

    public function field($field)
    {
        $r = $this->getField($field);
        Loader::helper('ajax')->sendResult($r);
    }

    protected function getField($field)
    {
        $r = new stdClass();
        $r->field = $field;
        $searchRequest = $this->searchRequest->getSearchRequest();
        $wdt = Loader::helper('form/date_time');
        /* @var $wdt \Concrete\Core\Form\Service\Widget\DateTime */
        $html = '';
        switch ($field) {
            case 'size':
                $form = Loader::helper('form');
                $html .= $form->text('size_from', $searchRequest['size_from'], array('style' => 'width:  60px'));
                $html .= t('to');
                $html .= $form->text('size_to', $searchRequest['size_to'], array('style' => 'width: 60px'));
                $html .= t('KB');
                break;
            case 'type':
                $form = Loader::helper('form');
                $t1 = FileType::getUsedTypeList();
                $types = array();
                foreach ($t1 as $value) {
                    $types[$value] = FileType::getGenericTypeText($value);
                }
                $html .= $form->select('type', $types, $searchRequest['type'], array('style' => 'width: 120px'));
                break;
            case 'extension':
                $form = Loader::helper('form');
                $ext1 = FileType::getUsedExtensionList();
                $extensions = array();
                foreach ($ext1 as $value) {
                    $extensions[$value] = $value;
                }
                $html .= $form->select('extension', $extensions, $searchRequest['extensions'], array('style' => 'width: 120px'));
                break;
            case 'date_added':
                $html .= $wdt->datetime('date_added_from', $wdt->translate('date_added_from', $searchRequest)) . t('to') . $wdt->datetime('date_added_to', $wdt->translate('date_added_to', $searchRequest));
                break;
            case 'added_to':
                $ps = Loader::helper("form/page_selector");
                $html .= $ps->selectPage('ocIDSearchField');
                break;
            default:
                if (Loader::helper('validation/numbers')->integer($field)) {
                    $ak = FileAttributeKey::getByID($field);
                    $html .= $ak->render('search', null, true);
                }
                break;
        }
        $r->html = $html;

        return $r;
    }

    public function submit()
    {
        $this->search();
        $result = $this->result;
        Loader::helper('ajax')->sendResult($this->result->getJSONObject());
    }

    public function getFields()
    {
        return $this->fields;
    }


    const FILTER_BY_TYPE = 'type';                  //!< @javascript-exported
    const FILTER_BY_SIZE = 'size';                  //!< @javascript-exported
    const FILTER_BY_EXTENSION = 'extension';        //!< @javascript-exported
    const FILTER_BY_ADDED_DATE = 'date_added';      //!< @javascript-exported
    const FILTER_BY_ADDED_TO_PAGE = 'added_to';     //!< @javascript-exported

    public static function getSearchFields()
    {
        // Warning:
        // concrete/js/build/core/file-manager/search.js uses those array keys too 
        $r = array(
            self::FILTER_BY_SIZE          => t('Size'),
            self::FILTER_BY_TYPE          => t('Type'),
            self::FILTER_BY_EXTENSION     => t('Extension'),
            self::FILTER_BY_ADDED_DATE    => t('Added Between'),
            self::FILTER_BY_ADDED_TO_PAGE => t('Added to Page')
        );
        $sfa = FileAttributeKey::getSearchableList();
        foreach ($sfa as $ak) {
            $r[$ak->getAttributeKeyID()] = $ak->getAttributeKeyDisplayName();
        }
        natcasesort($r);

        return $r;
    }

}
