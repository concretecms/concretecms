<?php
namespace Concrete\Controller\Search;

use Concrete\Core\Http\ResponseAssetGroup;
use Concrete\Core\Search\StickyRequest;
use Concrete\Core\User\Group\GroupSetList;
use Controller;
use UserList;
use \Concrete\Core\User\Search\ColumnSet\ColumnSet as UserSearchColumnSet;
use \Concrete\Core\User\Search\Result\Result as UserSearchResult;
use GroupList;
use UserAttributeKey;
use Permissions;
use Loader;
use GroupSet;
use stdClass;
use User;
use URL;
use Group;

class Users extends Controller
{
    protected $fields = array();

    /**
     * @var \Concrete\Core\User\UserList
     */
    protected $userList;

    public function __construct()
    {
        $this->searchRequest = new StickyRequest('users');
        $this->userList = new UserList($this->searchRequest);
    }

    public function search()
    {
        $dh = Loader::helper('concrete/user');
        if (!$dh->canAccessUserSearchInterface()) {
            return false;
        }

        if ($_REQUEST['submitSearch']) {
            $this->searchRequest->resetSearchRequest();
        }

        $req = $this->searchRequest->getSearchRequest();
        $columns = UserSearchColumnSet::getCurrent();

        if (!$this->userList->getActiveSortColumn()) {
            $col = $columns->getDefaultSortColumn();
            $this->userList->sanitizedSortBy($col->getColumnKey(), $col->getColumnDefaultSortDirection());
        }

        $this->userList->includeInactiveUsers();
        $this->userList->includeUnvalidatedUsers();

        $columns = UserSearchColumnSet::getCurrent();
        $this->set('columns', $columns);

        if ($req['keywords'] != '') {
            $this->userList->filterByKeywords($req['keywords']);
        }

        if ($req['numResults'] && Loader::helper('validation/numbers')->integer($req['numResults'])) {
            $this->userList->setItemsPerPage($req['numResults']);
        }

        $u = new User();

        if (!$u->isSuperUser()) {
            $gIDs = array(-1);
            $gs = new GroupList();
            $groups = $gs->getResults();
            foreach ($groups as $g) {
                $gp = new Permissions($g);
                if ($gp->canSearchUsersInGroup()) {
                    $gIDs[] = $g->getGroupID();
                }
            }
            $this->userList->getQueryObject()->leftJoin("u", "UserGroups", "ugRequired", "ugRequired.uID = u.uID");
            $groups = 'ugRequired.gID in (' . implode(',', $gIDs) . ')';
            $gg = Group::getByID(REGISTERED_GROUP_ID);
            $ggp = new Permissions($gg);
            if ($ggp->canSearchUsersInGroup()) {
                $null = 'ugRequired.gID is null';
            }
            $this->userList->getQueryObject()->select('distinct (u.uID)');
            $expr = $this->userList->getQueryObject()->expr()->orX($groups, $null);
            $this->userList->getQueryObject()->andwhere($expr);
        }

        $filterGIDs = array();
        if (isset($req['gID']) && is_array($req['gID'])) {
            foreach ($req['gID'] as $gID) {
                $g = Group::getByID($gID);
                if (is_object($g)) {
                    $gp = new Permissions($g);
                    if ($gp->canSearchUsersInGroup()) {
                        $filterGIDs[] = $g->getGroupID();
                    }
                }
            }
        }
        foreach ($filterGIDs as $gID) {
            $this->userList->filterByGroupID($gID);
        }

        if (is_array($req['field'])) {
            foreach ($req['field'] as $i => $item) {
                $this->fields[] = $this->getField($item);
                // due to the way the form is setup, index will always be one more than the arrays
                if ($item != '') {
                    switch ($item) {
                        case 'is_active':
                            if ($req['active'] === '0') {
                                $this->userList->filterByIsActive(0);
                            } elseif ($req['active'] === '1') {
                                $this->userList->filterByIsActive(1);
                            }
                            break;
                        case "date_added":
                            $wdt = Loader::helper('form/date_time');
                            /* @var $wdt \Concrete\Core\Form\Service\Widget\DateTime */
                            $dateFrom = $wdt->translate('date_added_from', $_REQUEST);
                            if ($dateFrom) {
                                $this->userList->filterByDateAdded($dateFrom, '>=');
                            }
                            $dateTo = $wdt->translate('date_added_to', $_REQUEST);
                            if ($dateTo) {
                                if (preg_match('/^(.+\\d+:\\d+):00$/', $dateTo, $m)) {
                                    $dateTo = $m[1] . ':59';
                                }
                                $this->userList->filterByDateAdded($dateTo, '<=');
                            }
                            break;
                        case "group_set":
                            $gsID = $_REQUEST['gsID'];
                            $gs = GroupSet::getByID($gsID);
                            $groupsetids = array(-1);
                            if (is_object($gs)) {
                                $groups = $gs->getGroups();
                            }
                            $this->userList->addToQuery('left join UserGroups ugs on u.uID = ugs.uID');
                            foreach ($groups as $g) {
                                if ($pk->validate($g) && (!in_array($g->getGroupID(), $groupsetids))) {
                                    $groupsetids[] = $g->getGroupID();
                                }
                            }
                            $instr = 'ugs.gID in (' . implode(',', $groupsetids) . ')';
                            $this->userList->filter(false, $instr);
                            break;

                        default:
                            $akID = $item;
                            $fak = UserAttributeKey::getByID($akID);
                            $type = $fak->getAttributeType();
                            $cnt = $type->getController();
                            $cnt->setAttributeKey($fak);
                            $cnt->searchForm($this->userList);
                            break;
                    }
                }
            }
        }

        $ilr = new UserSearchResult($columns, $this->userList, URL::to('/ccm/system/search/users/submit'), $this->fields);
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
        $html = '';
        switch ($field) {
            case 'date_added':
                $html .= $wdt->datetime('date_added_from', $wdt->translate('date_added_from', $searchRequest)) . t('to') . $wdt->datetime('date_added_to', $wdt->translate('date_added_to', $searchRequest));
                break;
            case 'is_active':
                $html .= $form->select('active', array('0' => t('Inactive Users'), '1' => t('Active Users')), array('style' => 'vertical-align: middle'));
                break;
            case 'group_set':
                $gsl = new GroupSetList();
                $groupsets = array();
                foreach ($gsl->get() as $gs) {
                    $groupsets[$gs->getGroupSetID()] = $gs->getGroupSetDisplayName();
                }
                $html .= $form->select('gsID', $groupsets);
                break;
            default:
                if (Loader::helper('validation/numbers')->integer($field)) {
                    $ak = UserAttributeKey::getByID($field);
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
            'date_added' => t('Registered Between'),
            'is_active' => t('Activated Users')
        );
        $sfa = UserAttributeKey::getSearchableList();
        foreach ($sfa as $ak) {
            $r[$ak->getAttributeKeyID()] = $ak->getAttributeKeyDisplayName();
        }
        natcasesort($r);

        return $r;
    }

}
