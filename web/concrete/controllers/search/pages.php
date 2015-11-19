<?php
namespace Concrete\Controller\Search;

use Concrete\Core\Http\ResponseAssetGroup;
use Concrete\Core\Search\StickyRequest;
use Controller;
use PageList;
use \Concrete\Core\Page\Search\ColumnSet\ColumnSet as PageSearchColumnSet;
use \Concrete\Core\Page\Search\Result\Result as PageSearchResult;
use CollectionAttributeKey;
use Permissions;
use Loader;
use stdClass;
use PageTheme;
use URL;

class Pages extends Controller
{
    protected $fields = array();

    /**
     * @var \Concrete\Core\Page\PageList
     */
    protected $pageList;

    public function __construct()
    {
        $this->searchRequest = new StickyRequest('pages');
        $this->pageList = new PageList($this->searchRequest);
    }

    public function search()
    {
        $dh = Loader::helper('concrete/dashboard/sitemap');
        if (!$dh->canRead()) {
            return false;
        }

        if ($_REQUEST['submitSearch']) {
            $this->searchRequest->resetSearchRequest();
        }

        $req = $this->searchRequest->getSearchRequest();
        $columns = PageSearchColumnSet::getCurrent();

        if (!$this->pageList->getActiveSortColumn()) {
            $col = $columns->getDefaultSortColumn();
            $this->pageList->sanitizedSortBy($col->getColumnKey(), $col->getColumnDefaultSortDirection());
        }

        $cvName = htmlentities($req['cvName'], ENT_QUOTES, APP_CHARSET);

        $this->pageList->setPageVersionToRetrieve(\Concrete\Core\Page\PageList::PAGE_VERSION_RECENT);
        if ($cvName != '') {
            $this->pageList->filterByName($cvName);
        }

        if ($req['numResults'] && Loader::helper('validation/numbers')->integer($req['numResults'])) {
            $this->pageList->setItemsPerPage($req['numResults']);
        }

        if (is_array($req['field'])) {
            foreach ($req['field'] as $i => $item) {
                $this->fields[] = $this->getField($item);
                // due to the way the form is setup, index will always be one more than the arrays
                if ($item != '') {
                    switch ($item) {
                        case 'keywords':
                            $keywords = htmlentities($req['keywords'], ENT_QUOTES, APP_CHARSET);
                            $this->pageList->filterByFulltextKeywords($keywords);
                            break;
                        case 'num_children':
                            $symbol = '=';
                            if ($req['cChildrenSelect'] == 'gt') {
                                $symbol = '>';
                            } elseif ($req['cChildrenSelect'] == 'lt') {
                                $symbol = '<';
                            }
                            $this->pageList->filterByNumberOfChildren($req['cChildren'], $symbol);
                            break;
                        case 'type':
                            $this->pageList->filterByPageTypeID($req['ptID']);
                            break;
                        case 'template':
                            $template = \PageTemplate::getByID($req['pTemplateID']);
                            if (is_object($template)) {
                                $this->pageList->filterByPageTemplate($template);
                            }
                            break;
                        case 'owner':
                            $ui = \UserInfo::getByUserName($req['owner']);
                            if (is_object($ui)) {
                                $this->pageList->filterByUserID($ui->getUserID());
                            } else {
                                $this->pageList->filterByUserID(-1);
                            }
                            break;
                        case 'theme':
                            $this->pageList->filter('pThemeID', $req['pThemeID']);
                            break;
                        case 'parent':
                            if ($req['cParentIDSearchField'] > 0) {
                                if ($req['cParentAll'] == 1) {
                                    $pc = \Page::getByID($req['cParentIDSearchField']);
                                    $cPath = $pc->getCollectionPath();
                                    $this->pageList->filterByPath($cPath);
                                } else {
                                    $this->pageList->filterByParentID($req['cParentIDSearchField']);
                                }
                            }
                            break;
                        case 'version_status':
                            if (in_array($req['versionToRetrieve'], array(
                                \Concrete\Core\Page\PageList::PAGE_VERSION_RECENT,
                                \Concrete\Core\Page\PageList::PAGE_VERSION_ACTIVE
                            ))) {
                                $this->pageList->setPageVersionToRetrieve($req['versionToRetrieve']);
                            }
                            break;
                        case 'permissions_inheritance':
                            $this->pageList->filter('cInheritPermissionsFrom', $req['cInheritPermissionsFrom']);
                            break;
                        case "date_public":
                            $wdt = Loader::helper('form/date_time');
                            /* @var $wdt \Concrete\Core\Form\Service\Widget\DateTime */
                            $dateFrom = $wdt->translate('date_public_from', $req);
                            if ($dateFrom) {
                                $this->pageList->filterByPublicDate($dateFrom, '>=');
                            }
                            $dateTo = $wdt->translate('date_public_to', $req);
                            if ($dateTo != '') {
                                if (preg_match('/^(.+\\d+:\\d+):00$/', $dateTo, $m)) {
                                    $dateTo = $m[1] . ':59';
                                }
                                $this->pageList->filterByPublicDate($dateTo, '<=');
                            }
                            break;
                        case "last_modified":
                            $wdt = Loader::helper('form/date_time');
                            /* @var $wdt \Concrete\Core\Form\Service\Widget\DateTime */
                            $dateFrom = $wdt->translate('last_modified_from', $req);
                            if ($dateFrom) {
                                $this->pageList->filterByDateLastModified($dateFrom, '>=');
                            }
                            $dateTo = $wdt->translate('last_modified_to', $req);
                            if ($dateTo) {
                                if (preg_match('/^(.+\\d+:\\d+):00$/', $dateTo, $m)) {
                                    $dateTo = $m[1] . ':59';
                                }
                                $this->pageList->filterByDateLastModified($dateTo, '<=');
                            }
                            break;
                        case "date_added":
                            $wdt = Loader::helper('form/date_time');
                            /* @var $wdt \Concrete\Core\Form\Service\Widget\DateTime */
                            $dateFrom = $wdt->translate('date_added_from', $req);
                            if ($dateFrom) {
                                $this->pageList->filterByDateAdded($dateFrom, '>=');
                            }
                            $dateTo = $wdt->translate('date_added_to', $req);
                            if ($dateTo) {
                                if (preg_match('/^(.+\\d+:\\d+):00$/', $dateTo, $m)) {
                                    $dateTo = $m[1] . ':59';
                                }
                                $this->pageList->filterByDateAdded($dateTo, '<=');
                            }
                            break;
                        default:
                            $akID = $item;
                            $fak = CollectionAttributeKey::getByID($akID);
                            if (!is_object($fak) || (!($fak instanceof CollectionAttributeKey))) {
                                break;
                            }

                            $type = $fak->getAttributeType();
                            $cnt = $type->getController();
                            $cnt->setRequestArray($req);
                            $cnt->setAttributeKey($fak);
                            $cnt->searchForm($this->pageList);
                            break;
                    }
                }
            }
        }

        $ilr = new PageSearchResult($columns, $this->pageList, URL::to('/ccm/system/search/pages/submit'), $this->fields);
        $this->result = $ilr;
    }

    public function getSearchResultObject()
    {
        return $this->result;
    }

    public function getListObject()
    {
        return $this->pageList;
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
        $form = Loader::helper('form');
        $wdt = Loader::helper('form/date_time');
        /* @var $wdt \Concrete\Core\Form\Service\Widget\DateTime */
        $html = '';
        switch ($field) {
            case 'keywords':
                $html .= $form->text('keywords', $searchRequest['keywords']);
                break;
            case 'date_public':
                $html .= $wdt->datetime('date_public_from', $wdt->translate('date_public_from', $searchRequest)) . t('to') . $wdt->datetime('date_public_to', $wdt->translate('date_public_to', $searchRequest));
                break;
            case 'date_added':
                $html .= $wdt->datetime('date_added_from', $wdt->translate('date_added_from', $searchRequest)) . t('to') . $wdt->datetime('date_added_to', $wdt->translate('date_added_to', $searchRequest));
                break;
            case 'last_modified':
                $html .= $wdt->datetime('last_modified_from', $wdt->translate('last_modified_from', $searchRequest)) . t('to') . $wdt->datetime('last_modified_to', $wdt->translate('last_modified_to', $searchRequest));
                break;
            case 'owner':
                $html .= $form->text('owner');
                break;
            case 'permissions_inheritance':
                $html .= '<select name="cInheritPermissionsFrom" class="form-control">';
                    $html .= '<option value="PARENT"' . ($searchRequest['cInheritPermissionsFrom'] == 'PARENT' ? ' selected' : '') . '>' . t('Parent Page') . '</option>';
                    $html .= '<option value="TEMPLATE"' . ($searchRequest['cInheritPermissionsFrom'] == 'TEMPLATE' ? ' selected' : '') . '>' . t('Page Type') . '</option>';
                    $html .= '<option value="OVERRIDE"' . ($searchRequest['cInheritPermissionsFrom'] == 'OVERRIDE' ? ' selected' : '') . '>' . t('Itself (Override)') . '</option>';
                $html .= '</select>';
                break;
            case 'type':
                $html .= $form->select('ptID', array_reduce(
                    \PageType::getList(), function($types, $type) {
                        $types[$type->getPageTypeID()] = $type->getPageTypeDisplayName();
                        return $types;
                    }
                ), $searchRequest['ptID']);
                break;
            case 'template':
                $html .= $form->select('pTemplateID', array_reduce(
                    \PageTemplate::getList(), function($templates, $template) {
                    $templates[$template->getPageTemplateID()] = $template->getPageTemplateDisplayName();
                    return $templates;
                }
                ), $searchRequest['pTemplateID']);
                break;
            case 'version_status':
                $versionToRetrieve = \Concrete\Core\Page\PageList::PAGE_VERSION_RECENT;
                if ($searchRequest['versionToRetrieve']) {
                    $versionToRetrieve = $searchRequest['versionToRetrieve'];
                }
                $html .= '<div class="radio"><label>' . $form->radio('versionToRetrieve', \Concrete\Core\Page\PageList::PAGE_VERSION_RECENT, $versionToRetrieve) . t('All') . '</label></div>';
                $html .= '<div class="radio"><label>' . $form->radio('versionToRetrieve', \Concrete\Core\Page\PageList::PAGE_VERSION_ACTIVE, $versionToRetrieve) . t('Approved') . '</label></div>';
                break;
            case 'parent':
                $ps = Loader::helper("form/page_selector");
                $html .= $ps->selectPage('cParentIDSearchField', $searchRequest['cParentIDSearchField']);
                $html .= '<div class="form-group">';
                    $html .= '<label class="control-label">' . t('Search All Children?') . '</label>';
                    $html .= '<div class="radio"><label>' . $form->radio('cParentAll', 0, false) . ' ' . t('No') . '</label></div>';
                    $html .= '<div class="radio"><label>' . $form->radio('cParentAll', 1, false) . ' ' . t('Yes') . '</label></div>';
                $html .= '</div>';
                break;
            case 'num_children':
                $html .= '<div class="form-inline"><select name="cChildrenSelect" class="form-control">';
                    $html .= '<option value="gt"' . ($searchRequest['cChildrenSelect'] == 'gt' ? ' selected' : '') . '>' . t('More Than') . '</option>';
                    $html .= '<option value="eq"' . ($searchRequest['cChildrenSelect'] == 'eq' ? ' selected' : '') . '>' . t('Equal To') . '</option>';
                    $html .= '<option value="lt"' . ($searchRequest['cChildrenSelect'] == 'lt' ? ' selected' : '') . '>' . t('Fewer Than') . '</option>';
                $html .= '</select>';
                $html .= ' <input type="text" name="cChildren" class="form-control" value="' . $searchRequest['cChildren'] . '" /></div>';
                break;
            case 'theme':
                $html .= '<select name="pThemeID" class="form-control">';
                $themes = PageTheme::getList();
                foreach ($themes as $pt) {
                    $html .= '<option value="' . $pt->getThemeID() . '" ' . ($pt->getThemeID() == $searchRequest['pThemeID'] ? ' selected' : '') . '>' . $pt->getThemeName() . '</option>';
                }
                $html .= '</select>';
                break;
            default:
                if (Loader::helper('validation/numbers')->integer($field)) {
                    $ak = CollectionAttributeKey::getByID($field);
                    $html .= $ak->render('search', null, true);
                }
                break;
        }
        $r->html = $html;
        $ag = ResponseAssetGroup::get();
        $r->assets = array();
        foreach ($ag->getAssetsToOutput() as $position => $assets) {
            foreach ($assets as $asset) {
                if (is_object($asset)) {
                    // have to do a check here because we might be included a dumb javascript call like i18n_js
                    $r->assets[$asset->getAssetType()][] = $asset->getAssetURL();
                }
            }
        }
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

    public static function getSearchFields()
    {
        $r = array(
            'parent' => t('Parent Page'),
            'type' => t('Page Type'),
            'template' => t('Page Template'),
            'keywords' => t('Full Page Index'),
            'date_added' => t('Date Added'),
            'theme' => t('Theme'),
            'last_modified' => t('Last Modified'),
            'date_public' => t('Public Date'),
            'owner' => t('Page Owner'),
            'num_children' => t('# Children'),
            'version_status' => t('Approved Version')
        );
        $sfa = CollectionAttributeKey::getSearchableList();
        foreach ($sfa as $ak) {
            $r[$ak->getAttributeKeyID()] = $ak->getAttributeKeyDisplayName();
        }
        natcasesort($r);

        return $r;
    }

}
