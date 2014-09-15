<?php
namespace Concrete\Controller\Search;

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
            $this->pageList->sortBy($col->getColumnKey(), $col->getColumnDefaultSortDirection());
        }

        $cvName = htmlentities($req['cvName'], ENT_QUOTES, APP_CHARSET);

        $this->pageList->setPageVersionToRetrieve(\Concrete\Core\Page\PageList::PAGE_VERSION_RECENT);
        if ($cvName != '') {
            $this->pageList->filterByName($cvName);
        }

        if ($req['numResults'] && Loader::helper('validation/numbers')->integer($req['numResults'])) {
            $this->pageList->setItemsPerPage($req['numResults']);
        }

        if ($req['ptID']) {
            $this->pageList->filterByPageTypeID($req['ptID']);
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
                            if (isset($req['_cParentAll'])) {
                                $req['cParentAll'] = $req['_cParentAll'];
                            }
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
                            if (isset($req['_cvIsApproved'])) {
                                $req['cvIsApproved'] = $req['_cvIsApproved'];
                            }
                            $this->pageList->filterByIsApproved($req['cvIsApproved']);
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
        ob_start();
        switch ($field) {
            case 'keywords':
                print $form->text('keywords', $searchRequest['keywords'], array('style' => 'width: 120px'));
                break;
            case 'date_public':
                echo $wdt->datetime('date_public_from', $wdt->translate('date_public_from')) . t('to') . $wdt->datetime('date_public_to', $wdt->translate('date_public_to'));
                break;
            case 'date_added':
                echo $wdt->datetime('date_added_from', $wdt->translate('date_added_from')) . t('to') . $wdt->datetime('date_added_to', $wdt->translate('date_added_to'));
                break;
            case 'last_modified':
                echo $wdt->datetime('last_modified_from', $wdt->translate('last_modified_from')) . t('to') . $wdt->datetime('last_modified_to', $wdt->translate('last_modified_to'));
                break;
            case 'owner': ?>
                <?=$form->text('owner', array('class'=>'span5'))?>
                <?php break;
            case 'permissions_inheritance': ?>
                <select name="cInheritPermissionsFrom" class="form-control">
                    <option value="PARENT"<?php if ($searchRequest['cInheritPermissionsFrom'] == 'PARENT') { ?> selected <?php } ?>><?=t('Parent Page')?></option>
                    <option value="TEMPLATE" <?php if ($searchRequest['cInheritPermissionsFrom'] == 'TEMPLATE') { ?> selected <?php } ?>><?=t('Page Type')?></option>
                <option value="OVERRIDE"<?php if ($searchRequest['cInheritPermissionsFrom'] == 'OVERRIDE') { ?> selected <?php } ?>><?=t('Itself (Override)')?></option>
                </select>
                <?php break;
            case 'version_status': ?>
                <div class="radio"><label><?=$form->radio('cvIsApproved', 0, false)?> <span><?=t('Unapproved')?></span></label></div>
                <div class="radio"><label><?=$form->radio('cvIsApproved', 1, false)?> <span><?=t('Approved')?></span></label></div>
                <?php break;
            case 'parent': ?>
                <?php $ps = Loader::helper("form/page_selector");
                print $ps->selectPage('cParentIDSearchField');
                ?>
                <div>
                    <div><?=t('Search All Children?')?></div>
                    <label class="radio-inline"><?=$form->radio('cParentAll', 0, false)?> <?=t('No')?></label>
                    <label class="radio-inline"><?=$form->radio('cParentAll', 1, false)?> <?=t('Yes')?></label>
                </div>
                <?php break;
            case 'num_children': ?>
                <select name="cChildrenSelect" class="form-control">
                    <option value="gt"<?php if ($searchRequest['cChildrenSelect'] == 'gt') { ?> selected <?php } ?>><?=t('More Than')?></option>
                    <option value="eq" <?php if ($searchRequest['cChildrenSelect'] == 'eq') { ?> selected <?php } ?>><?=t('Equal To')?></option>
                    <option value="lt"<?php if ($searchRequest['cChildrenSelect'] == 'lt') { ?> selected <?php } ?>><?=t('Fewer Than')?></option>
                </select>
                <input type="text" name="cChildren" class="form-control" value="<?=$searchRequest['cChildren']?>" />
                <?php break;
            case 'theme': ?>
                <select name="pThemeID" class="form-control">
                    <?php $themes = PageTheme::getList(); ?>
                    <?php foreach ($themes as $pt) { ?>
                        <option value="<?=$pt->getThemeID()?>" <?php if ($pt->getThemeID() == $searchRequest['pThemeID']) { ?> selected<?php } ?>><?=$pt->getThemeName()?></option>
                    <?php } ?>
                </select>
                <?php break;
            default:
                if (Loader::helper('validation/numbers')->integer($field)) {
                    $ak = CollectionAttributeKey::getByID($field);
                    $ak->render('search');
                }
                break;
        }
        $contents = ob_get_contents();
        ob_end_clean();
        $r->html = $contents;

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

}
