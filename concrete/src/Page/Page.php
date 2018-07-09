<?php
namespace Concrete\Core\Page;

use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Page\Template as TemplateEntity;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Entity\Site\SiteTree;
use Concrete\Core\Export\ExportableInterface;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Permission\AssignableObjectTrait;
use Concrete\Core\Site\SiteAggregateInterface;
use Concrete\Core\Site\Tree\TreeInterface;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Page\Type\Composer\Control\BlockControl;
use Concrete\Core\Page\Type\Composer\FormLayoutSetControl;
use Concrete\Core\Page\Type\Type;
use Concrete\Core\Permission\AssignableObjectInterface;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Support\Facade\Route;
use Concrete\Core\Permission\Access\Entity\PageOwnerEntity;
use Database;
use CacheLocal;
use Collection;
use Request;
use Concrete\Core\Page\Statistics as PageStatistics;
use PageCache;
use PageTemplate;
use Events;
use Core;
use Config;
use PageController;
use User;
use Block;
use UserInfo;
use PageType;
use PageTheme;
use Concrete\Core\Localization\Locale\Service as LocaleService;
use Concrete\Core\Permission\Key\PageKey as PagePermissionKey;
use Concrete\Core\Permission\Access\Access as PermissionAccess;
use Concrete\Core\Package\PackageList;
use Concrete\Core\Permission\Access\Entity\Entity as PermissionAccessEntity;
use Concrete\Core\Permission\Access\Entity\GroupEntity as GroupPermissionAccessEntity;
use Concrete\Core\Permission\Access\Entity\GroupCombinationEntity as GroupCombinationPermissionAccessEntity;
use Concrete\Core\Permission\Access\Entity\UserEntity as UserPermissionAccessEntity;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Entity\StyleCustomizer\CustomCssRecord;
use Area;
use Concrete\Core\Entity\Page\PagePath;
use Queue;
use Log;
use Environment;
use Group;
use Session;
use Concrete\Core\Attribute\ObjectInterface as AttributeObjectInterface;

/**
 * The page object in Concrete encapsulates all the functionality used by a typical page and their contents
 * including blocks, page metadata, page permissions.
 */
class Page extends Collection implements \Concrete\Core\Permission\ObjectInterface, AttributeObjectInterface, AssignableObjectInterface, TreeInterface, SiteAggregateInterface, ExportableInterface
{
    protected $controller;
    protected $blocksAliasedFromMasterCollection = null;
    protected $cPointerOriginalID = null;
    protected $cPointerExternalLink = null;
    protected $cPointerExternalLinkNewWindow = null;
    protected $isMasterCollection = null;
    protected $cInheritPermissionsFromCID = null;
    protected $cIsSystemPage = false;
    protected $siteTreeID;
    public $siteTree;

    use AssignableObjectTrait;
    /**
     * @param string $path /path/to/page
     * @param string $version ACTIVE or RECENT
     *
     * @return \Concrete\Core\Page\Page
     */
    public static function getByPath($path, $version = 'RECENT', TreeInterface $tree = null)
    {
        $path = rtrim($path, '/'); // if the path ends in a / remove it.
        $cache = \Core::make('cache/request');

        if ($tree) {
            $item = $cache->getItem(sprintf('site/page/path/%s/%s', $tree->getSiteTreeID(), trim($path, '/')));
            $cID = $item->get();
            if ($item->isMiss()) {
                $db = Database::connection();
                $cID = $db->fetchColumn('select Pages.cID from PagePaths inner join Pages on Pages.cID = PagePaths.cID where cPath = ? and siteTreeID = ?', [$path, $tree->getSiteTreeID()]);
                $cache->save($item->set($cID));
            }
        } else {
            $item = $cache->getItem(sprintf('page/path/%s', trim($path, '/')));
            $cID = $item->get();
            if ($item->isMiss()) {
                $db = Database::connection();
                $cID = $db->fetchColumn('select cID from PagePaths where cPath = ?', [$path]);
                $cache->save($item->set($cID));
            }
        }

        return self::getByID($cID, $version);
    }

    public function getObjectAttributeCategory()
    {
        $app = Facade::getFacadeApplication();
        return $app->make('\Concrete\Core\Attribute\Category\PageCategory');
    }

    /**
     * @param int $cID Collection ID of a page
     * @param string $version ACTIVE or RECENT
     *
     * @return \Concrete\Core\Page\Page
     */
    public static function getByID($cID, $version = 'RECENT')
    {
        $class = get_called_class();
        if ($cID && $version) {
            $c = CacheLocal::getEntry('page', $cID.'/'.$version.'/'.$class);
            if ($c instanceof $class) {
                return $c;
            }
        }

        $where = 'where Pages.cID = ?';
        $c = new $class();
        $c->populatePage($cID, $where, $version);

        // must use cID instead of c->getCollectionID() because cID may be the pointer to another page
        if ($cID && $version) {
            CacheLocal::set('page', $cID.'/'.$version.'/'.$class, $c);
        }

        return $c;
    }

    public function __construct()
    {
        $this->loadError(COLLECTION_INIT); // init collection until we populate.
    }

    public function getExporter()
    {
        return new Exporter();
    }

    protected function populatePage($cInfo, $where, $cvID)
    {
        $db = Database::connection();

        $this->loadError(false);

        $q0 = 'select Pages.cID, Pages.pkgID, Pages.siteTreeID, Pages.cPointerID, Pages.cPointerExternalLink, Pages.cIsDraft, Pages.cIsActive, Pages.cIsSystemPage, Pages.cPointerExternalLinkNewWindow, Pages.cFilename, Pages.ptID, Collections.cDateAdded, Pages.cDisplayOrder, Collections.cDateModified, cInheritPermissionsFromCID, cInheritPermissionsFrom, cOverrideTemplatePermissions, cCheckedOutUID, cIsTemplate, uID, cPath, cParentID, cChildren, cCacheFullPageContent, cCacheFullPageContentOverrideLifetime, cCacheFullPageContentLifetimeCustom from Pages inner join Collections on Pages.cID = Collections.cID left join PagePaths on (Pages.cID = PagePaths.cID and PagePaths.ppIsCanonical = 1) ';
        //$q2 = "select cParentID, cPointerID, cPath, Pages.cID from Pages left join PagePaths on (Pages.cID = PagePaths.cID and PagePaths.ppIsCanonical = 1) ";

        $v = [$cInfo];
        $r = $db->executeQuery($q0.$where, $v);
        $row = $r->fetchRow();
        if ($row['cPointerID'] > 0) {
            $q1 = $q0.'where Pages.cID = ?';
            $cPointerOriginalID = $row['cID'];
            $v = [$row['cPointerID']];
            $cParentIDOverride = $row['cParentID'];
            $cPathOverride = $row['cPath'];
            $cIsActiveOverride = $row['cIsActive'];
            $cPointerID = $row['cPointerID'];
            $cDisplayOrderOverride = $row['cDisplayOrder'];
            $r = $db->executeQuery($q1, $v);
            $row = $r->fetchRow();
        }

        if ($r) {
            if ($row) {
                foreach ($row as $key => $value) {
                    $this->{$key} = $value;
                }
                if (isset($cParentIDOverride)) {
                    $this->cPointerID = $cPointerID;
                    $this->cIsActive = $cIsActiveOverride;
                    $this->cPointerOriginalID = $cPointerOriginalID;
                    $this->cPath = $cPathOverride;
                    $this->cParentID = $cParentIDOverride;
                    $this->cDisplayOrder = $cDisplayOrderOverride;
                }
                $this->isMasterCollection = $row['cIsTemplate'];
            } else {
                // there was no record of this particular collection in the database
                $this->loadError(COLLECTION_NOT_FOUND);
            }
            $r->free();
        } else {
            $this->loadError(COLLECTION_NOT_FOUND);
        }

        if ($cvID != false && !$this->isError()) {
            $this->loadVersionObject($cvID);
        }

        unset($r);
    }

    public function getPermissionResponseClassName()
    {
        return '\\Concrete\\Core\\Permission\\Response\\PageResponse';
    }

    public function getPermissionAssignmentClassName()
    {
        return '\\Concrete\\Core\\Permission\\Assignment\\PageAssignment';
    }

    public function getPermissionObjectKeyCategoryHandle()
    {
        return 'page';
    }

    /**
     * Return a representation of the Page object as something easily serializable.
     */
    public function getJSONObject()
    {
        $r = new \stdClass();
        $r->name = $this->getCollectionName();
        if ($this->isAlias() && !$this->isExternalLink()) {
            $r->cID = $this->getCollectionPointerOriginalID();
        } else {
            $r->cID = $this->getCollectionID();
        }

        return $r;
    }

    /**
     * @return PageController
     */
    public function getPageController()
    {
        if (!isset($this->controller)) {
            $env = Environment::get();
            if ($this->getPageTypeID() > 0) {
                $pt = $this->getPageTypeObject();
                $ptHandle = $pt->getPageTypeHandle();
                $r = $env->getRecord(DIRNAME_CONTROLLERS.'/'.DIRNAME_PAGE_TYPES.'/'.$ptHandle.'.php', $pt->getPackageHandle());
                $prefix = $r->override ? true : $pt->getPackageHandle();
                $class = core_class('Controller\\PageType\\'.camelcase($ptHandle), $prefix);
            } elseif ($this->isGeneratedCollection()) {
                $file = $this->getCollectionFilename();
                if (strpos($file, '/'.FILENAME_COLLECTION_VIEW) !== false) {
                    $path = substr($file, 0, strpos($file, '/'.FILENAME_COLLECTION_VIEW));
                } else {
                    $path = substr($file, 0, strpos($file, '.php'));
                }
                $r = $env->getRecord(DIRNAME_CONTROLLERS.'/'.DIRNAME_PAGE_CONTROLLERS.$path.'.php', $this->getPackageHandle());
                $prefix = $r->override ? true : $this->getPackageHandle();
                $class = core_class('Controller\\SinglePage\\'.str_replace('/', '\\', camelcase($path, true)), $prefix);
            }

            if (isset($class) && class_exists($class)) {
                $this->controller = Core::make($class, [$this]);
            } else {
                $this->controller = Core::make('\PageController', [$this]);
            }
        }

        return $this->controller;
    }

    public function getPermissionObjectIdentifier()
    {
        // this is a hack but it's a really good one for performance
        // if the permission access entity for page owner exists in the database, then we return the collection ID. Otherwise, we just return the permission collection id
        // this is because page owner is the ONLY thing that makes it so we can't use getPermissionsCollectionID, and for most sites that will DRAMATICALLY reduce the number of queries.
        // Drafts are exceptions to this rule because some permission keys of these pages are inherited from "Edit Page Type Draft" permission.
        if (\Concrete\Core\Permission\Access\PageAccess::usePermissionCollectionIDForIdentifier() && !$this->isPageDraft()) {
            return $this->getPermissionsCollectionID();
        } else {
            return $this->getCollectionID();
        }
    }

    /**
     * Is the page in edit mode.
     *
     * @return bool
     */
    public function isEditMode()
    {
        if ($this->getCollectionPath() == STACKS_LISTING_PAGE_PATH) {
            return true;
        }
        if ($this->getPageTypeHandle() == STACKS_PAGE_TYPE) {
            return true;
        }

        return $this->isCheckedOutByMe();
    }

    /**
     * Get the package ID for a page (page thats added by a package) (returns 0 if its not in a package).
     *
     * @return int
     */
    public function getPackageID()
    {
        return $this->pkgID;
    }

    /**
     * Get the package handle for a page (page thats added by a package).
     *
     * @return string
     */
    public function getPackageHandle()
    {
        if (!isset($this->pkgHandle)) {
            $this->pkgHandle = PackageList::getHandle($this->pkgID);
        }

        return $this->pkgHandle;
    }

    /**
     * Returns 1 if the page is in arrange mode.
     *
     * @return bool
     */
    public function isArrangeMode()
    {
        return $this->isCheckedOutByMe() && isset($_REQUEST['btask']) && $_REQUEST['btask'] === 'arrange';
    }

    /**
     * Forces the page to be checked in if its checked out.
     */
    public function forceCheckIn()
    {
        // This function forces checkin to take place
        $db = Database::connection();
        $q = 'update Pages set cIsCheckedOut = 0, cCheckedOutUID = null, cCheckedOutDatetime = null, cCheckedOutDatetimeLastEdit = null where cID = ?';
        $db->executeQuery($q, [$this->cID]);
    }

    /**
     * @private
     * Forces all pages to be checked in and edit mode to be reset.
     * @TODO – move this into a command in version 9.
     */
    public static function forceCheckInForAllPages()
    {
        $db = Database::connection();
        $q = 'update Pages set cIsCheckedOut = 0, cCheckedOutUID = null, cCheckedOutDatetime = null, cCheckedOutDatetimeLastEdit = null';
        $db->executeQuery($q);
    }

    /**
     * Checks if the page is a dashboard page, returns true if it is.
     *
     * @return bool
     */
    public function isAdminArea()
    {
        if ($this->isGeneratedCollection()) {
            $pos = strpos($this->getCollectionFilename(), '/'.DIRNAME_DASHBOARD);

            return $pos > -1;
        }

        return false;
    }

    /**
     * Uses a Request object to determine which page to load. queries by path and then
     * by cID.
     */
    public static function getFromRequest(Request $request)
    {
        // if something has already set a page object, we return it
        $c = $request->getCurrentPage();
        if (is_object($c)) {
            return $c;
        }
        if ($request->getPath() != '') {
            $path = $request->getPath();
            $db = Database::connection();
            $cID = false;
            $ppIsCanonical = false;
            $site = \Core::make('site')->getSite();
            $treeIDs = [0];
            foreach($site->getLocales() as $locale) {
                $tree = $locale->getSiteTree();
                if (is_object($tree)) {
                    $treeIDs[] = $tree->getSiteTreeID();
                }
            }

            $treeIDs = implode(',', $treeIDs);

            while ((!$cID) && $path) {
                $row = $db->fetchAssoc('select pp.cID, ppIsCanonical from PagePaths pp inner join Pages p on pp.cID = p.cID where cPath = ? and siteTreeID in (' . $treeIDs . ')', [$path]);
                if (!empty($row)) {
                    $cID = $row['cID'];
                    if ($cID) {
                        $cPath = $path;
                        $ppIsCanonical = (bool) $row['ppIsCanonical'];
                        break;
                    }
                }
                $path = substr($path, 0, strrpos($path, '/'));
            }
            if ($cID && $cPath) {
                $c = self::getByID($cID, 'ACTIVE');
                $c->cPathFetchIsCanonical = $ppIsCanonical;
            } else {
                $c = new self();
                $c->loadError(COLLECTION_NOT_FOUND);
            }

            return $c;
        } else {
            $cID = $request->query->get('cID');
            if (!$cID) {
                $cID = $request->request->get('cID');
            }
            $cID = Core::make('helper/security')->sanitizeInt($cID);
            if ($cID) {
                $c = self::getByID($cID, 'ACTIVE');
            } else {
                $site = \Core::make('site')->getSite();
                $c = $site->getSiteHomePageObject('ACTIVE');
            }
            $c->cPathFetchIsCanonical = true;
        }

        return $c;
    }

    public function processArrangement($area_id, $moved_block_id, $block_order)
    {
        $area_handle = Area::getAreaHandleFromID($area_id);
        $db = Database::connection();

        // Remove the moved block from its old area, and all blocks from the destination area.
        $db->executeQuery('UPDATE CollectionVersionBlockStyles SET arHandle = ?  WHERE cID = ? and cvID = ? and bID = ?',
                     [$area_handle, $this->getCollectionID(), $this->getVersionID(), $moved_block_id]);
        $db->executeQuery('UPDATE CollectionVersionBlocks SET arHandle = ?  WHERE cID = ? and cvID = ? and bID = ?',
                     [$area_handle, $this->getCollectionID(), $this->getVersionID(), $moved_block_id]);

        $update_query = 'UPDATE CollectionVersionBlocks SET cbDisplayOrder = CASE bID';
        $when_statements = [];
        $update_values = [];
        foreach ($block_order as $key => $block_id) {
            $when_statements[] = 'WHEN ? THEN ?';
            $update_values[] = $block_id;
            $update_values[] = $key;
        }

        $update_query .= ' '.implode(' ', $when_statements).' END WHERE bID in ('.
            implode(',', array_pad([], count($block_order), '?')).') AND cID = ? AND cvID = ?';
        $values = array_merge($update_values, $block_order);
        $values = array_merge($values, [$this->getCollectionID(), $this->getVersionID()]);

        $db->executeQuery($update_query, $values);
    }

    /**
     * checks if the page is checked out, if it is return true.
     *
     * @return bool
     */
    public function isCheckedOut()
    {
        // function to inform us as to whether the current collection is checked out
        $db = Database::connection();
        if (isset($this->isCheckedOutCache)) {
            return $this->isCheckedOutCache;
        }

        $q = "select cIsCheckedOut, cCheckedOutDatetimeLastEdit from Pages where cID = '{$this->cID}'";
        $r = $db->executeQuery($q);

        if ($r) {
            $row = $r->fetchRow();
            // If cCheckedOutDatetimeLastEdit is present, get the time span in seconds since it's last edit.
            if (! empty($row['cCheckedOutDatetimeLastEdit'])) {
                $dh = Core::make('helper/date');
                $timeSinceCheckout = ($dh->getOverridableNow(true) - strtotime($row['cCheckedOutDatetimeLastEdit']));
            }

            if ($row['cIsCheckedOut'] == 0) {
                return false;
            } else {
                if (isset($timeSinceCheckout) && $timeSinceCheckout > CHECKOUT_TIMEOUT) {
                    $this->forceCheckIn();
                    $this->isCheckedOutCache = false;

                    return false;
                } else {
                    $this->isCheckedOutCache = true;

                    return true;
                }
            }
        }
    }

    /**
     * Gets the user that is editing the current page.
     * $return string $name.
     */
    public function getCollectionCheckedOutUserName()
    {
        $db = Database::connection();
        $query = 'select cCheckedOutUID from Pages where cID = ?';
        $vals = [$this->cID];
        $checkedOutId = $db->fetchColumn($query, $vals);
        if (is_object(UserInfo::getByID($checkedOutId))) {
            $ui = UserInfo::getByID($checkedOutId);
            $name = $ui->getUserName();
        } else {
            $name = t('Unknown User');
        }

        return $name;
    }

    /**
     * Checks if the page is checked out by the current user.
     *
     * @return bool
     */
    public function isCheckedOutByMe()
    {
        $u = new User();

        return $this->getCollectionCheckedOutUserID() > 0 && $this->getCollectionCheckedOutUserID() == $u->getUserID();
    }

    /**
     * Checks if the page is a single page.
     *
     * @return bool
     */
    public function isGeneratedCollection()
    {
        // generated collections are collections without templates, that have special cFilename attributes
        return $this->getCollectionFilename() && !$this->getPageTemplateID();
    }

    public function setPermissionsToOverride()
    {
        if ($this->cInheritPermissionsFrom != 'OVERRIDE') {
            $this->setPermissionsToManualOverride();
        }
    }

    public function setChildPermissionsToOverride()
    {
        foreach($this->getCollectionChildren() as $child) {
            $child->setPermissionsToManualOverride();
        }
    }

    public function removePermissions($userOrGroup, $permissions = [])
    {
        if ($this->cInheritPermissionsFrom != 'OVERRIDE') {
            return;
        }

        if (is_array($userOrGroup)) {
            $pe = GroupCombinationPermissionAccessEntity::getOrCreate($userOrGroup);
            // group combination
        } elseif ($userOrGroup instanceof User || $userOrGroup instanceof \Concrete\Core\User\UserInfo) {
            $pe = UserPermissionAccessEntity::getOrCreate($userOrGroup);
        } else {
            // group;
            $pe = GroupPermissionAccessEntity::getOrCreate($userOrGroup);
        }

        foreach ($permissions as $pkHandle) {
            $pk = PagePermissionKey::getByHandle($pkHandle);
            $pk->setPermissionObject($this);
            $pa = $pk->getPermissionAccessObject();
            if (is_object($pa)) {
                if ($pa->isPermissionAccessInUse()) {
                    $pa = $pa->duplicate();
                }
                $pa->removeListItem($pe);
                $pt = $pk->getPermissionAssignmentObject();
                $pt->assignPermissionAccess($pa);
            }
        }
    }

    public static function getDraftsParentPage(Site $site = null)
    {
        $db = Database::connection();
        $site = $site ? $site : \Core::make('site')->getSite();
        $cParentID = $db->fetchColumn('select p.cID from PagePaths pp inner join Pages p on pp.cID = p.cID inner join SiteLocales sl on p.siteTreeID = sl.siteTreeID where cPath = ? and sl.siteID = ?', [Config::get('concrete.paths.drafts'), $site->getSiteID()]);
        return Page::getByID($cParentID);
    }

    public static function getDrafts(Site $site)
    {
        $db = Database::connection();
        $nc = self::getDraftsParentPage($site);
        $r = $db->executeQuery('select Pages.cID from Pages inner join Collections c on Pages.cID = c.cID where cParentID = ? order by cDateAdded desc', [$nc->getCollectionID()]);
        $pages = [];
        while ($row = $r->FetchRow()) {
            $entry = self::getByID($row['cID']);
            if (is_object($entry)) {
                $pages[] = $entry;
            }
        }

        return $pages;
    }

    public function isPageDraft()
    {
        if (isset($this->cIsDraft) && $this->cIsDraft) {
            return true;
        } else {
            return false;
        }
    }

    private static function translatePermissionsXMLToKeys($node)
    {
        $pkHandles = [];
        if ($node['canRead'] == '1') {
            $pkHandles[] = 'view_page';
            $pkHandles[] = 'view_page_in_sitemap';
        }
        if ($node['canWrite'] == '1') {
            $pkHandles[] = 'view_page_versions';
            $pkHandles[] = 'edit_page_properties';
            $pkHandles[] = 'edit_page_contents';
            $pkHandles[] = 'edit_page_multilingual_settings';
            $pkHandles[] = 'approve_page_versions';
            $pkHandles[] = 'move_or_copy_page';
            $pkHandles[] = 'preview_page_as_user';
            $pkHandles[] = 'add_subpage';
        }
        if ($node['canAdmin'] == '1') {
            $pkHandles[] = 'edit_page_speed_settings';
            $pkHandles[] = 'edit_page_permissions';
            $pkHandles[] = 'edit_page_theme';
            $pkHandles[] = 'schedule_page_contents_guest_access';
            $pkHandles[] = 'edit_page_page_type';
            $pkHandles[] = 'edit_page_template';
            $pkHandles[] = 'delete_page';
            $pkHandles[] = 'delete_page_versions';
        }

        return $pkHandles;
    }

    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * @deprecated
     */
    public function getController()
    {
        return $this->getPageController();
    }

    /**
     * @private
     */
    public function assignPermissionSet($px)
    {
        // this is the legacy function that is called just by xml. We pass these values in as though they were the old ones.
        if (isset($px->guests)) {
            $pkHandles = self::translatePermissionsXMLToKeys($px->guests);
            $this->assignPermissions(Group::getByID(GUEST_GROUP_ID), $pkHandles);
        }
        if (isset($px->registered)) {
            $pkHandles = self::translatePermissionsXMLToKeys($px->registered);
            $this->assignPermissions(Group::getByID(REGISTERED_GROUP_ID), $pkHandles);
        }
        if (isset($px->administrators)) {
            $pkHandles = self::translatePermissionsXMLToKeys($px->administrators);
            $this->assignPermissions(Group::getByID(ADMIN_GROUP_ID), $pkHandles);
        }
        if (isset($px->group)) {
            foreach ($px->group as $g) {
                $pkHandles = self::translatePermissionsXMLToKeys($px->administrators);
                $this->assignPermissions(Group::getByID($g['gID']), $pkHandles);
            }
        }
        if (isset($px->user)) {
            foreach ($px->user as $u) {
                $pkHandles = self::translatePermissionsXMLToKeys($px->administrators);
                $this->assignPermissions(UserInfo::getByID($u['uID']), $pkHandles);
            }
        }
    }

    /**
     * Make an alias to a page.
     *
     * @param Collection $c
     *
     * @return int $newCID
     */
    public function addCollectionAlias($c)
    {
        $db = Database::connection();
        // the passed collection is the parent collection
        $cParentID = $c->getCollectionID();

        $u = new User();
        $uID = $u->getUserID();

        $handle = $this->getCollectionHandle();
        $cDisplayOrder = $c->getNextSubPageDisplayOrder();

        $_cParentID = $c->getCollectionID();
        $q = 'select PagePaths.cPath from PagePaths where cID = ?';
        $v = [$_cParentID];
        if ($_cParentID != static::getHomePageID()) {
            $q .= ' and ppIsCanonical = ?';
            $v[] = 1;
        }
        $cPath = $db->fetchColumn($q, $v);

        $data = [
            'handle' => $this->getCollectionHandle(),
            'name' => $this->getCollectionName(),
        ];
        $cobj = parent::addCollection($data);
        $newCID = $cobj->getCollectionID();
        $siteTreeID = $c->getSiteTreeID();

        $v = [$newCID, $siteTreeID, $cParentID, $uID, $this->getCollectionID(), $cDisplayOrder];
        $q = "insert into Pages (cID, siteTreeID, cParentID, uID, cPointerID, cDisplayOrder) values (?, ?, ?, ?, ?, ?)";
        $r = $db->prepare($q);

        $r->execute($v);

        PageStatistics::incrementParents($newCID);

        $q2 = 'insert into PagePaths (cID, cPath, ppIsCanonical, ppGeneratedFromURLSlugs) values (?, ?, ?, ?)';
        $v2 = [$newCID, $cPath.'/'.$handle, 1, 1];
        $db->executeQuery($q2, $v2);

        return $newCID;
    }

    /**
     * Update the name, link, and to open in a new window for an external link.
     *
     * @param string $cName
     * @param string $cLink
     * @param bool $newWindow
     */
    public function updateCollectionAliasExternal($cName, $cLink, $newWindow = 0)
    {
        if ($this->cPointerExternalLink != '') {
            $db = Database::connection();
            $this->markModified();
            if ($newWindow) {
                $newWindow = 1;
            } else {
                $newWindow = 0;
            }
            $db->executeQuery('update CollectionVersions set cvName = ? where cID = ?', [$cName, $this->cID]);
            $db->executeQuery('update Pages set cPointerExternalLink = ?, cPointerExternalLinkNewWindow = ? where cID = ?', [$cLink, $newWindow, $this->cID]);
        }
    }

    /**
     * Add a new external link.
     *
     * @param string $cName
     * @param string $cLink
     * @param bool $newWindow
     *
     * @return int $newCID
     */
    public function addCollectionAliasExternal($cName, $cLink, $newWindow = 0)
    {
        $db = Database::connection();
        $dt = Core::make('helper/text');
        $ds = Core::make('helper/security');
        $u = new User();

        $cParentID = $this->getCollectionID();
        $uID = $u->getUserID();

        $handle = $this->getCollectionHandle();

        // make the handle out of the title
        $cLink = $ds->sanitizeURL($cLink);
        $handle = $dt->urlify($cLink);
        $data = [
            'handle' => $handle,
            'name' => $cName,
        ];
        $cobj = parent::addCollection($data);
        $newCID = $cobj->getCollectionID();

        if ($newWindow) {
            $newWindow = 1;
        } else {
            $newWindow = 0;
        }

        $cInheritPermissionsFromCID = $this->getPermissionsCollectionID();
        $cInheritPermissionsFrom = 'PARENT';

        $siteTreeID = \Core::make('site')->getSite()->getSiteTreeID();

        $v = [$newCID, $siteTreeID, $cParentID, $uID, $cInheritPermissionsFrom, (int) $cInheritPermissionsFromCID, $cLink, $newWindow];
        $q = 'insert into Pages (cID, siteTreeID, cParentID, uID, cInheritPermissionsFrom, cInheritPermissionsFromCID, cPointerExternalLink, cPointerExternalLinkNewWindow) values (?, ?, ?, ?, ?, ?, ?, ?)';
        $r = $db->prepare($q);

        $r->execute($v);

        PageStatistics::incrementParents($newCID);

        self::getByID($newCID)->movePageDisplayOrderToBottom();

        return $newCID;
    }

    /**
     * Returns true if a page is a system page. A system page is either a page that is outside the site tree (has a site tree ID of 0)
     * or a page that is in the site tree, but whose parent starts at 0. That means its a root level page. Why do we need this
     * separate boolean then? Because we need to easily be able to filter all pages by whether they're a system page even
     * if we don't necessarily know where their starting page is.
     *
     * @return bool
     */
    public function isSystemPage()
    {
        return (bool) $this->cIsSystemPage;
    }

    /**
     * Gets the icon for a page (also fires the on_page_get_icon event).
     *
     * @return string $icon Path to the icon
     */
    public function getCollectionIcon()
    {
        // returns a fully qualified image link for this page's icon, either based on its collection type or if icon.png appears in its view directory
        $pe = new Event($this);
        $pe->setArgument('icon', '');
        Events::dispatch('on_page_get_icon', $pe);
        $icon = $pe->getArgument('icon');

        if ($icon) {
            return $icon;
        }

        if (\Core::make('multilingual/detector')->isEnabled()) {
            $icon = \Concrete\Core\Multilingual\Service\UserInterface\Flag::getDashboardSitemapIconSRC($this);
        }

        if ($this->isGeneratedCollection()) {
            if ($this->getPackageID() > 0) {
                if (is_dir(DIR_PACKAGES.'/'.$this->getPackageHandle())) {
                    $dirp = DIR_PACKAGES;
                    $url = \Core::getApplicationURL();
                } else {
                    $dirp = DIR_PACKAGES_CORE;
                    $url = ASSETS_URL;
                }
                $file = $dirp.'/'.$this->getPackageHandle().'/'.DIRNAME_PAGES.$this->getCollectionPath().'/'.FILENAME_PAGE_ICON;
                if (file_exists($file)) {
                    $icon = $url.'/'.DIRNAME_PACKAGES.'/'.$this->getPackageHandle().'/'.DIRNAME_PAGES.$this->getCollectionPath().'/'.FILENAME_PAGE_ICON;
                }
            } elseif (file_exists(DIR_FILES_CONTENT.$this->getCollectionPath().'/'.FILENAME_PAGE_ICON)) {
                $icon = \Core::getApplicationURL().'/'.DIRNAME_PAGES.$this->getCollectionPath().'/'.FILENAME_PAGE_ICON;
            } elseif (file_exists(DIR_FILES_CONTENT_REQUIRED.$this->getCollectionPath().'/'.FILENAME_PAGE_ICON)) {
                $icon = ASSETS_URL.'/'.DIRNAME_PAGES.$this->getCollectionPath().'/'.FILENAME_PAGE_ICON;
            }
        } else {
        }

        return $icon;
    }

    /**
     * Remove an external link/alias.
     *
     * @return int $cIDRedir cID for the original page if the page was an alias
     */
    public function removeThisAlias()
    {
        $cIDRedir = $this->getCollectionPointerID();
        $cPointerExternalLink = $this->getCollectionPointerExternalLink();

        if ($cPointerExternalLink != '') {
            $this->delete();
        } elseif ($cIDRedir > 0) {
            $db = Database::connection();

            PageStatistics::decrementParents($this->getCollectionPointerOriginalID());

            $args = [$this->getCollectionPointerOriginalID()];
            $q = 'delete from Pages where cID = ?';
            $db->executeQuery($q, $args);

            $q = 'delete from Collections where cID = ?';
            $db->executeQuery($q, $args);

            $q = 'delete from CollectionVersions where cID = ?';
            $db->executeQuery($q, $args);

            $q = 'delete from PagePaths where cID = ?';
            $db->executeQuery($q, $args);

            return $cIDRedir;
        }
    }

    public function populateRecursivePages($pages, $pageRow, $cParentID, $level, $includeThisPage = true)
    {
        $db = Database::connection();
        $children = $db->GetAll('select cID, cDisplayOrder from Pages where cParentID = ? order by cDisplayOrder asc', [$pageRow['cID']]);
        if ($includeThisPage) {
            $pages[] = [
                'cID' => $pageRow['cID'],
                'cDisplayOrder' => $pageRow['cDisplayOrder'],
                'cParentID' => $cParentID,
                'level' => $level,
                'total' => count($children),
            ];
        }
        ++$level;
        $cParentID = $pageRow['cID'];
        if (count($children) > 0) {
            foreach ($children as $pageRow) {
                $pages = $this->populateRecursivePages($pages, $pageRow, $cParentID, $level);
            }
        }

        return $pages;
    }

    public function queueForDeletionSort($a, $b)
    {
        if ($a['level'] > $b['level']) {
            return -1;
        }
        if ($a['level'] < $b['level']) {
            return 1;
        }

        return 0;
    }

    public function queueForDuplicationSort($a, $b)
    {
        if ($a['level'] > $b['level']) {
            return 1;
        }
        if ($a['level'] < $b['level']) {
            return -1;
        }
        if ($a['cDisplayOrder'] > $b['cDisplayOrder']) {
            return 1;
        }
        if ($a['cDisplayOrder'] < $b['cDisplayOrder']) {
            return -1;
        }
        if ($a['cID'] > $b['cID']) {
            return 1;
        }
        if ($a['cID'] < $b['cID']) {
            return -1;
        }

        return 0;
    }

    public function queueForDeletion()
    {
        $pages = [];
        $includeThisPage = true;
        if ($this->getCollectionPath() == Config::get('concrete.paths.trash')) {
            // we're in the trash. we can't delete the trash. we're skipping over the trash node.
            $includeThisPage = false;
        }
        $pages = $this->populateRecursivePages($pages, ['cID' => $this->getCollectionID()], $this->getCollectionParentID(), 0, $includeThisPage);
        // now, since this is deletion, we want to order the pages by level, which
        // should get us no funny business if the queue dies.
        usort($pages, ['Page', 'queueForDeletionSort']);
        $q = Queue::get('delete_page');
        foreach ($pages as $page) {
            $q->send(serialize($page));
        }
    }

    public function queueForDeletionRequest($queue = null, $includeThisPage = true)
    {
        $pages = [];
        $pages = $this->populateRecursivePages($pages, ['cID' => $this->getCollectionID()], $this->getCollectionParentID(), 0, $includeThisPage);
        // now, since this is deletion, we want to order the pages by level, which
        // should get us no funny business if the queue dies.
        usort($pages, ['Page', 'queueForDeletionSort']);
        if (!$queue) {
            $queue = Queue::get('delete_page_request');
        }
        foreach ($pages as $page) {
            $queue->send(serialize($page));
        }
    }

    public function queueForDuplication($destination, $includeParent = true)
    {
        $pages = [];
        $pages = $this->populateRecursivePages($pages, ['cID' => $this->getCollectionID()], $this->getCollectionParentID(), 0, $includeParent);
        // we want to order the pages by level, which should get us no funny
        // business if the queue dies.
        usort($pages, ['Page', 'queueForDuplicationSort']);
        $q = Queue::get('copy_page');
        foreach ($pages as $page) {
            $page['destination'] = $destination->getCollectionID();
            $q->send(serialize($page));
        }
    }

    /**
     * @deprecated
     */
    public function export($pageNode)
    {
        $exporter = new Exporter();
        $exporter->export($this, $pageNode);
    }

    /**
     * Returns the uID for a page that is checked out.
     *
     * @return int
     */
    public function getCollectionCheckedOutUserID()
    {
        return $this->cCheckedOutUID;
    }

    /**
     * Returns the path for the current page.
     *
     * @return string
     */
    public function getCollectionPath()
    {
        return isset($this->cPath) ? $this->cPath : null;
    }

    /**
     * Returns the PagePath object for the current page.
     */
    public function getCollectionPathObject()
    {
        $em = \ORM::entityManager();
        $cID = ($this->getCollectionPointerOriginalID() > 0) ? $this->getCollectionPointerOriginalID() : $this->cID;
        $path = $em->getRepository('\Concrete\Core\Entity\Page\PagePath')->findOneBy(
            ['cID' => $cID, 'ppIsCanonical' => true,
        ]);

        return $path;
    }

    /**
     * Adds a non-canonical page path to the current page.
     */
    public function addAdditionalPagePath($cPath, $commit = true)
    {
        $em = \ORM::entityManager();
        $path = new \Concrete\Core\Entity\Page\PagePath();
        $path->setPagePath('/'.trim($cPath, '/'));
        $path->setPageObject($this);
        $em->persist($path);
        if ($commit) {
            $em->flush();
        }

        return $path;
    }

    /**
     * Sets the canonical page path for a page.
     */
    public function setCanonicalPagePath($cPath, $isAutoGenerated = false)
    {
        $em = \ORM::entityManager();
        $path = $this->getCollectionPathObject();
        if (is_object($path)) {
            $path->setPagePath($cPath);
        } else {
            $path = new \Concrete\Core\Entity\Page\PagePath();
            $path->setPagePath($cPath);
            $path->setPageObject($this);
        }
        $path->setPagePathIsAutoGenerated($isAutoGenerated);
        $path->setPagePathIsCanonical(true);
        $em->persist($path);
        $em->flush();
    }

    public function getPagePaths()
    {
        $em = \ORM::entityManager();

        return $em->getRepository('\Concrete\Core\Entity\Page\PagePath')->findBy(
            ['cID' => $this->getCollectionID()], ['ppID' => 'asc']
        );
    }

    public function getAdditionalPagePaths()
    {
        $em = \ORM::entityManager();

        return $em->getRepository('\Concrete\Core\Entity\Page\PagePath')->findBy(
            ['cID' => $this->getCollectionID(), 'ppIsCanonical' => false,
        ]);
    }

    /**
     * Clears all page paths for a page.
     */
    public function clearPagePaths()
    {
        $em = \ORM::entityManager();
        $paths = $this->getPagePaths();
        foreach ($paths as $path) {
            $em->remove($path);
        }
        $em->flush();
    }

    /**
     * Returns full url for the current page.
     *
     * @return string
     */
    public function getCollectionLink($appendBaseURL = false)
    {
        return Core::make('helper/navigation')->getLinkToCollection($this, $appendBaseURL);
    }

    public function getSiteTreeID()
    {
        return $this->siteTreeID;
    }

    public function getSite()
    {
        $tree = $this->getSiteTreeObject();
        if ($tree instanceof SiteTree) {
            return $tree->getSite();
        }
    }

    /**
     * {@inheritDoc}
     *
     * @see \Concrete\Core\Site\Tree\TreeInterface::getSiteTreeObject()
     */
    public function getSiteTreeObject()
    {
        if (!isset($this->siteTree) && $this->getSiteTreeID()) {
            $em = \ORM::entityManager();
            $this->siteTree = $em->find('\Concrete\Core\Entity\Site\Tree', $this->getSiteTreeID());
        }
        return $this->siteTree;
    }

    /**
     * Returns the path for a page from its cID.
     *
     * @param int cID
     *
     * @return string $path
     */
    public static function getCollectionPathFromID($cID)
    {
        $db = Database::connection();
        $path = $db->fetchColumn('select cPath from PagePaths inner join CollectionVersions on (PagePaths.cID = CollectionVersions.cID and CollectionVersions.cvIsApproved = 1) where PagePaths.cID = ? order by PagePaths.ppIsCanonical desc', [$cID]);

        return $path;
    }

    /**
     * Returns the uID for a page ownder.
     *
     * @return int
     */
    public function getCollectionUserID()
    {
        return $this->uID;
    }

    /**
     * Returns the page's handle.
     *
     * @return string
     */
    public function getCollectionHandle()
    {
        return $this->vObj->cvHandle;
    }

    /**
     * @deprecated
     */
    public function getCollectionTypeName()
    {
        return $this->getPageTypeName();
    }

    public function getPageTypeName()
    {
        if (!isset($this->pageType)) {
            $this->pageType = $this->getPageTypeObject();
        }
        if (is_object($this->pageType)) {
            return $this->pageType->getPageTypeDisplayName();
        }
    }

    /**
     * @deprecated
     */
    public function getCollectionTypeID()
    {
        return $this->getPageTypeID();
    }

    /**
     * Returns the Collection Type ID.
     *
     * @return int
     */
    public function getPageTypeID()
    {
        return isset($this->ptID) ? $this->ptID : null;
    }

    public function getPageTypeObject()
    {
        return PageType::getByID($this->getPageTypeID());
    }

    /**
     * Returns the Page Template ID.
     *
     * @return int
     */
    public function getPageTemplateID()
    {
        return $this->vObj->pTemplateID;
    }

    /**
     * Returns the Page Template Object.
     *
     * @return PageTemplate
     */
    public function getPageTemplateObject()
    {
        return PageTemplate::getByID($this->getPageTemplateID());
    }

    /**
     * Returns the Page Template handle.
     *
     * @return string
     */
    public function getPageTemplateHandle()
    {
        $pt = $this->getPageTemplateObject();
        if ($pt instanceof TemplateEntity) {
            return $pt->getPageTemplateHandle();
        }

        return false;
    }

    /**
     * Returns the Collection Type handle.
     *
     * @return string
     */
    public function getPageTypeHandle()
    {
        if (!isset($this->ptHandle)) {
            $this->ptHandle = false;
            $ptID = $this->getPageTypeID();
            if ($ptID) {
                $pt = Type::getByID($ptID);
                if (is_object($pt)) {
                    $this->ptHandle = $pt->getPageTypeHandle();
                }
            }
        }

        return $this->ptHandle;
    }

    public function getCollectionTypeHandle()
    {
        return $this->getPageTypeHandle();
    }

    /**
     * Returns theme id for the collection.
     *
     * @return int
     */
    public function getCollectionThemeID()
    {
        $theme = $this->getCollectionThemeObject();
        if (is_object($theme)) {
            return $theme->getThemeID();
        }
    }

    /**
     * Check if a block is an alias from a page default.
     *
     * @param Block $b
     *
     * @return bool
     */
    public function isBlockAliasedFromMasterCollection($b)
    {
        if (!$b->isAlias()) {
            return false;
        }
        //Retrieve info for all of this page's blocks at once (and "cache" it)
        // so we don't have to query the database separately for every block on the page.
        if (is_null($this->blocksAliasedFromMasterCollection)) {
            $db = Database::connection();
            $q = 'SELECT cvb.bID FROM CollectionVersionBlocks AS cvb
                    INNER JOIN CollectionVersionBlocks AS cvb2
                        ON cvb.bID = cvb2.bID
                            AND cvb2.cID = ?
                    WHERE cvb.cID = ?
                        AND cvb.isOriginal = 0
                        AND cvb.cvID = ?
                    GROUP BY cvb.bID
                    ;';
            $v = [$this->getMasterCollectionID(), $this->getCollectionID(), $this->getVersionObject()->getVersionID()];
            $this->blocksAliasedFromMasterCollection = $db->GetCol($q, $v);
        }

        return in_array($b->getBlockID(), $this->blocksAliasedFromMasterCollection);
    }

    /**
     * Returns Collection's theme object.
     *
     * @return Theme
     */
    public function getCollectionThemeObject()
    {
        if (!isset($this->themeObject)) {
            $tmpTheme = Route::getThemeByRoute($this->getCollectionPath());
            if (isset($tmpTheme[0])) {
                switch ($tmpTheme[0]) {
                    case VIEW_CORE_THEME:
                        $this->themeObject = new \Concrete\Theme\Concrete\PageTheme();
                        break;
                    case 'dashboard':
                        $this->themeObject = new \Concrete\Theme\Dashboard\PageTheme();
                        break;
                    default:
                        $this->themeObject = PageTheme::getByHandle($tmpTheme[0]);
                        break;
                }
            } elseif ($this->vObj->pThemeID < 1) {
                $this->themeObject = PageTheme::getSiteTheme();
            } else {
                $this->themeObject = PageTheme::getByID($this->vObj->pThemeID);
            }
        }
        if (!$this->themeObject) {
            $this->themeObject = PageTheme::getSiteTheme();
        }

        return $this->themeObject;
    }

    /**
     * Returns the page's name.
     *
     * @return string
     */
    public function getCollectionName()
    {
        if (isset($this->vObj)) {
            return isset($this->vObj->cvName) ? $this->vObj->cvName : null;
        }

        return isset($this->cvName) ? $this->cvName : null;
    }

    /**
     * Returns the collection ID for the aliased page (returns 0 unless used on an actual alias).
     *
     * @return int
     */
    public function getCollectionPointerID()
    {
        return isset($this->cPointerID) ? (int) $this->cPointerID : 0;
    }

    /**
     * Returns link for the aliased page.
     *
     * @return string
     */
    public function getCollectionPointerExternalLink()
    {
        return $this->cPointerExternalLink;
    }

    /**
     * Returns if the alias opens in a new window.
     *
     * @return bool
     */
    public function openCollectionPointerExternalLinkInNewWindow()
    {
        return $this->cPointerExternalLinkNewWindow;
    }

    /**
     * Checks to see if the page is an alias.
     *
     * @return bool
     */
    public function isAlias()
    {
        return $this->getCollectionPointerID() > 0 || $this->cPointerExternalLink != null;
    }

    /**
     * Checks if a page is an external link.
     *
     * @return bool
     */
    public function isExternalLink()
    {
        return $this->cPointerExternalLink != null;
    }

    /**
     * Get the original cID of a page.
     *
     * @return int
     */
    public function getCollectionPointerOriginalID()
    {
        return $this->cPointerOriginalID;
    }

    /**
     * Get the file name of a page (single pages).
     *
     * @return string
     */
    public function getCollectionFilename()
    {
        return $this->cFilename;
    }

    /**
     * Gets the date a the current version was made public,.
     *
     * @return string date formated like: 2009-01-01 00:00:00
     */
    public function getCollectionDatePublic()
    {
        return $this->vObj->cvDatePublic;
    }

    /**
     * @return \DateTime|null Returns the \DateTime instance (or null if the current version doesn't have public date)
     */
    public function getCollectionDatePublicObject()
    {
        return Core::make('date')->toDateTime($this->getCollectionDatePublic());
    }

    /**
     * Get the description of a page.
     *
     * @return string
     */
    public function getCollectionDescription()
    {
        return $this->vObj->cvDescription;
    }

    /**
     * Gets the cID of the page's parent.
     *
     * @return int
     */
    public function getCollectionParentID()
    {
        if (isset($this->cParentID)) {
            return $this->cParentID;
        }
    }

    /**
     * Get the Parent cID from a page by using a cID.
     *
     * @param int $cID
     *
     * @return int
     */
    public static function getCollectionParentIDFromChildID($cID)
    {
        $db = Database::connection();
        $q = 'select cParentID from Pages where cID = ?';
        $cParentID = $db->fetchColumn($q, [$cID]);

        return $cParentID;
    }

    /**
     * Returns an array of this cParentID and aliased parentIDs.
     *
     * @return array $cID
     */
    public function getCollectionParentIDs()
    {
        $cIDs = [$this->cParentID];
        $db = Database::connection();
        $aliasedParents = $db->fetchAll('SELECT cParentID FROM Pages WHERE cPointerID = ?', [$this->cID]);
        foreach ($aliasedParents as $aliasedParent) {
            $cIDs[] = $aliasedParent['cParentID'];
        }

        return $cIDs;
    }

    /**
     * Checks if a page is a page default.
     *
     * @return bool
     */
    public function isMasterCollection()
    {
        return $this->isMasterCollection;
    }

    /**
     * Gets the template permissions.
     *
     * @return string
     */
    public function overrideTemplatePermissions()
    {
        return $this->cOverrideTemplatePermissions;
    }

    /**
     * Gets the position of the page in the sitemap.
     *
     * @return int
     */
    public function getCollectionDisplayOrder()
    {
        return $this->cDisplayOrder;
    }

    /**
     * Set the theme for a page using the page object.
     *
     * @param PageTheme $pl
     */
    public function setTheme($pl)
    {
        $db = Database::connection();
        $db->executeQuery('update CollectionVersions set pThemeID = ? where cID = ? and cvID = ?', [$pl->getThemeID(), $this->cID, $this->vObj->getVersionID()]);
    }

    /**
     * Set the theme for a page using the page object.
     *
     * @param PageType $pl
     */
    public function setPageType(\Concrete\Core\Page\Type\Type $type = null)
    {
        $ptID = 0;
        if (is_object($type)) {
            $ptID = $type->getPageTypeID();
        }
        $db = Database::connection();
        $db->executeQuery('update Pages set ptID = ? where cID = ?', [$ptID, $this->cID]);
        $this->ptID = $ptID;
    }

    /**
     * Set the permissions of sub-collections added beneath this permissions to inherit from the template.
     */
    public function setPermissionsInheritanceToTemplate()
    {
        $db = Database::connection();
        if ($this->cID) {
            $db->executeQuery('update Pages set cOverrideTemplatePermissions = 0 where cID = ?', [$this->cID]);
        }
    }

    /**
     * Set the permissions of sub-collections added beneath this permissions to inherit from the parent.
     */
    public function setPermissionsInheritanceToOverride()
    {
        $db = Database::connection();
        if ($this->cID) {
            $db->executeQuery('update Pages set cOverrideTemplatePermissions = 1 where cID = ?', [$this->cID]);
        }
    }

    public function getPermissionsCollectionID()
    {
        return $this->cInheritPermissionsFromCID;
    }

    public function getCollectionInheritance()
    {
        return $this->cInheritPermissionsFrom;
    }

    public function getParentPermissionsCollectionID()
    {
        $db = Database::connection();
        $cParentID = $this->cParentID;
        if (!$cParentID) {
            $cParentID = $this->getSiteHomePageID();
        }

        $v = [$cParentID];
        $q = 'select cInheritPermissionsFromCID from Pages where cID = ?';
        $ppID = $db->fetchColumn($q, $v);

        return $ppID;
    }

    public function getPermissionsCollectionObject()
    {
        return self::getByID($this->cInheritPermissionsFromCID, 'RECENT');
    }

    /**
     * Given the current page's template and page type, we return the master page.
     */
    public function getMasterCollectionID()
    {
        $pt = PageType::getByID($this->getPageTypeID());
        if (!is_object($pt)) {
            return 0;
        }
        $template = PageTemplate::getByID($this->getPageTemplateID());
        if (!is_object($template)) {
            return 0;
        }
        $c = $pt->getPageTypePageTemplateDefaultPageObject($template);

        return $c->getCollectionID();
    }

    public function getOriginalCollectionID()
    {
        // this is a bit weird...basically, when editing a master collection, we store the
        // master collection ID in session, along with the collection ID we were looking at before
        // moving to the master collection. This allows us to get back to that original collection
        return Session::get('ocID');
    }

    public function getNumChildren()
    {
        return $this->cChildren;
    }

    public function getNumChildrenDirect()
    {
        // direct children only
        $db = Database::connection();
        $v = [$this->cID];
        $num = $db->fetchColumn('select count(cID) as total from Pages where cParentID = ?', $v);
        if ($num) {
            return $num;
        }

        return 0;
    }

    /**
     * Returns the first child of the current page, or null if there is no child.
     *
     * @param string $sortColumn
     *
     * @return Page
     */
    public function getFirstChild($sortColumn = 'cDisplayOrder asc')
    {
        $db = Database::connection();
        $cID = $db->fetchColumn("select Pages.cID from Pages inner join CollectionVersions on Pages.cID = CollectionVersions.cID where cvIsApproved = 1 and cParentID = ? order by {$sortColumn}", [$this->cID]);
        if ($cID && $cID != $this->getSiteHomePageID()) {
            return self::getByID($cID, 'ACTIVE');
        }

        return false;
    }

    public function getCollectionChildrenArray($oneLevelOnly = 0)
    {
        $this->childrenCIDArray = [];
        $this->_getNumChildren($this->cID, $oneLevelOnly);

        return $this->childrenCIDArray;
    }

    /**
     * Returns the immediate children of the current page.
     */
    public function getCollectionChildren()
    {
        $children = [];
        $db = Database::connection();
        $q = 'select cID from Pages where cParentID = ? and cIsTemplate = 0 order by cDisplayOrder asc';
        $r = $db->executeQuery($q, [$this->getCollectionID()]);
        if ($r) {
            while ($row = $r->fetchRow()) {
                if ($row['cID'] > 0) {
                    $c = self::getByID($row['cID']);
                    $children[] = $c;
                }
            }
        }

        return $children;
    }

    protected function _getNumChildren($cID, $oneLevelOnly = 0, $sortColumn = 'cDisplayOrder asc')
    {
        $db = Database::connection();
        $q = "select cID from Pages where cParentID = {$cID} and cIsTemplate = 0 order by {$sortColumn}";
        $r = $db->query($q);
        if ($r) {
            while ($row = $r->fetchRow()) {
                if ($row['cID'] > 0) {
                    $this->childrenCIDArray[] = $row['cID'];
                    if (!$oneLevelOnly) {
                        $this->_getNumChildren($row['cID']);
                    }
                }
            }
        }
    }

    public function canMoveCopyTo($cobj)
    {
        // ensures that we're not moving or copying to a collection inside our part of the tree
        $children = $this->getCollectionChildrenArray();
        $children[] = $this->getCollectionID();

        return !in_array($cobj->getCollectionID(), $children);
    }

    public function updateCollectionName($name)
    {
        $db = Database::connection();
        $vo = $this->getVersionObject();
        $cvID = $vo->getVersionID();
        $this->markModified();
        if (is_object($this->vObj)) {
            $this->vObj->cvName = $name;

            $txt = Core::make('helper/text');
            $cHandle = $txt->urlify($name);
            $cHandle = str_replace('-', Config::get('concrete.seo.page_path_separator'), $cHandle);

            $db->executeQuery('update CollectionVersions set cvName = ?, cvHandle = ? where cID = ? and cvID = ?', [$name, $cHandle, $this->getCollectionID(), $cvID]);

            $cache = PageCache::getLibrary();
            $cache->purge($this);

            $pe = new Event($this);
            Events::dispatch('on_page_update', $pe);
        }
    }

    public function hasPageThemeCustomizations()
    {
        $db = Database::connection();

        return $db->fetchColumn('select count(cID) from CollectionVersionThemeCustomStyles where cID = ? and cvID = ?', [
            $this->cID, $this->getVersionID(),
        ]) > 0;
    }

    public function resetCustomThemeStyles()
    {
        $db = Database::connection();
        $db->executeQuery('delete from CollectionVersionThemeCustomStyles where cID = ? and cvID = ?', [$this->getCollectionID(), $this->getVersionID()]);
        $this->writePageThemeCustomizations();
    }

    public function setCustomStyleObject(\Concrete\Core\Page\Theme\Theme $pt, \Concrete\Core\StyleCustomizer\Style\ValueList $valueList, $selectedPreset = false, CustomCssRecord $customCssRecord = null)
    {
        $db = Database::connection();
        $db->delete('CollectionVersionThemeCustomStyles', ['cID' => $this->getCollectionID(), 'cvID' => $this->getVersionID()]);
        $preset = false;
        if ($selectedPreset) {
            $preset = $selectedPreset->getPresetHandle();
        }
        $sccRecordID = 0;
        if ($customCssRecord !== null) {
            $sccRecordID = $customCssRecord->getRecordID();
        }
        $db->insert(
            'CollectionVersionThemeCustomStyles',
            [
                'cID' => $this->getCollectionID(),
                'cvID' => $this->getVersionID(),
                'pThemeID' => $pt->getThemeID(),
                'sccRecordID' => $sccRecordID,
                'preset' => $preset,
                'scvlID' => $valueList->getValueListID(),
            ]
        );

        $scc = new \Concrete\Core\Page\CustomStyle();
        $scc->setThemeID($pt->getThemeID());
        $scc->setValueListID($valueList->getValueListID());
        $scc->setPresetHandle($preset);
        $scc->setCustomCssRecordID($sccRecordID);

        return $scc;
    }

    public function getPageWrapperClass()
    {
        $pt = $this->getPageTypeObject();

        $view = $this->getPageController()->getViewObject();
        if ($view) {
            $ptm = $view->getPageTemplate();
        } else {
            $ptm = $this->getPageTemplateObject();
        }

        $classes = ['ccm-page'];
        if (is_object($pt)) {
            $classes[] = 'page-type-'.str_replace('_', '-', $pt->getPageTypeHandle());
        }
        if (is_object($ptm)) {
            $classes[] = 'page-template-'.str_replace('_', '-', $ptm->getPageTemplateHandle());
        }

        return implode(' ', $classes);
    }

    public function writePageThemeCustomizations()
    {
        $theme = $this->getCollectionThemeObject();
        if (is_object($theme) && $theme->isThemeCustomizable()) {
            $style = $this->getCustomStyleObject();
            $scl = is_object($style) ? $style->getValueList() : null;

            $theme->setStylesheetCachePath(Config::get('concrete.cache.directory').'/pages/'.$this->getCollectionID());
            $theme->setStylesheetCacheRelativePath(REL_DIR_FILES_CACHE.'/pages/'.$this->getCollectionID());
            $sheets = $theme->getThemeCustomizableStyleSheets();
            foreach ($sheets as $sheet) {
                if (is_object($scl)) {
                    $sheet->setValueList($scl);
                    $sheet->output();
                } else {
                    $sheet->clearOutputFile();
                }
            }
        }
    }

    public static function resetAllCustomStyles()
    {
        $db = Database::connection();
        $db->delete('CollectionVersionThemeCustomStyles', ['1' => 1]);
        Core::make('app')->clearCaches();
    }

    public function update($data)
    {
        $db = Database::connection();

        $vo = $this->getVersionObject();
        $cvID = $vo->getVersionID();
        $this->markModified();

        $cName = $this->getCollectionName();
        $cDescription = $this->getCollectionDescription();
        $cDatePublic = $this->getCollectionDatePublic();
        $uID = $this->getCollectionUserID();
        $pkgID = $this->getPackageID();
        $cFilename = $this->getCollectionFilename();
        $pTemplateID = $this->getPageTemplateID();
        $ptID = $this->getPageTypeID();
        $existingPageTemplateID = $pTemplateID;

        $cCacheFullPageContent = $this->cCacheFullPageContent;
        $cCacheFullPageContentLifetimeCustom = $this->cCacheFullPageContentLifetimeCustom;
        $cCacheFullPageContentOverrideLifetime = $this->cCacheFullPageContentOverrideLifetime;

        if (isset($data['cName'])) {
            $cName = $data['cName'];
        }
        if (isset($data['cCacheFullPageContent'])) {
            $cCacheFullPageContent = $data['cCacheFullPageContent'];
        }
        if (isset($data['cCacheFullPageContentLifetimeCustom'])) {
            $cCacheFullPageContentLifetimeCustom = intval($data['cCacheFullPageContentLifetimeCustom']);
        }
        if (isset($data['cCacheFullPageContentOverrideLifetime'])) {
            $cCacheFullPageContentOverrideLifetime = $data['cCacheFullPageContentOverrideLifetime'];
        }
        if (isset($data['cDescription'])) {
            $cDescription = $data['cDescription'];
        }
        if (isset($data['cDatePublic'])) {
            $cDatePublic = $data['cDatePublic'];
        }
        if (isset($data['uID'])) {
            $uID = $data['uID'];
        }
        if (isset($data['pTemplateID'])) {
            $pTemplateID = $data['pTemplateID'];
        }
        if (isset($data['ptID'])) {
            $ptID = $data['ptID'];
        }

        if (!$cDatePublic) {
            $cDatePublic = Core::make('helper/date')->getOverridableNow();
        }
        $txt = Core::make('helper/text');
        $isHomePage = $this->isHomePage();
        if (!isset($data['cHandle']) && ($this->getCollectionHandle() != '')) {
            // No passed cHandle, and there is an existing handle.
            $cHandle = $this->getCollectionHandle();
        } elseif (!$isHomePage && !Core::make('helper/validation/strings')->notempty($data['cHandle'])) {
            // no passed cHandle, and no existing handle
            // make the handle out of the title
            $cHandle = $txt->urlify($cName);
            $cHandle = str_replace('-', Config::get('concrete.seo.page_path_separator'), $cHandle);
        } else {
            // passed cHandle, no existing handle
            $cHandle = isset($data['cHandle']) ? $txt->slugSafeString($data['cHandle']) : ''; // we DON'T run urlify
            $cHandle = str_replace('-', Config::get('concrete.seo.page_path_separator'), $cHandle);
        }
        $cName = $txt->sanitize($cName);

        if ($this->isGeneratedCollection()) {
            if (isset($data['cFilename'])) {
                $cFilename = $data['cFilename'];
            }
            // we only update a subset
            $v = [$cName, $cHandle, $cDescription, $cDatePublic, $cvID, $this->cID];
            $q = 'update CollectionVersions set cvName = ?, cvHandle = ?, cvDescription = ?, cvDatePublic = ? where cvID = ? and cID = ?';
            $r = $db->prepare($q);
            $r->execute($v);
        } else {
            if ($existingPageTemplateID && $pTemplateID && ($existingPageTemplateID != $pTemplateID) && $this->getPageTypeID() > 0 && $this->isPageDraft()) {
                // we are changing a page template in this operation.
                // when that happens, we need to get the new defaults for this page, remove the other blocks
                // on this page that were set by the old defaults master page
                $pt = $this->getPageTypeObject();
                if (is_object($pt)) {
                    $template = PageTemplate::getbyID($pTemplateID);
                    $existingPageTemplate = PageTemplate::getByID($existingPageTemplateID);

                    $oldMC = $pt->getPageTypePageTemplateDefaultPageObject($existingPageTemplate);
                    $newMC = $pt->getPageTypePageTemplateDefaultPageObject($template);

                    $currentPageBlocks = $this->getBlocks();
                    $newMCBlocks = $newMC->getBlocks();
                    $oldMCBlocks = $oldMC->getBlocks();
                    $oldMCBlockIDs = [];
                    foreach ($oldMCBlocks as $ob) {
                        $oldMCBlockIDs[] = $ob->getBlockID();
                    }

                    // now, we default all blocks on the current version of the page.
                    $db->executeQuery('delete from CollectionVersionBlocks where cID = ? and cvID = ?', [$this->getCollectionID(), $cvID]);

                    // now, we go back and we alias blocks from the new master collection onto the page.
                    foreach ($newMCBlocks as $b) {
                        $bt = $b->getBlockTypeObject();
                        if ($bt->getBlockTypeHandle() == BLOCK_HANDLE_PAGE_TYPE_OUTPUT_PROXY) {
                            continue;
                        }
                        if ($bt->isCopiedWhenPropagated()) {
                            $b->duplicate($this, true);
                        } else {
                            $b->alias($this);
                        }
                    }

                    // now, we go back and re-add the blocks we originally had on the page
                    // but only if they're not present in the oldMCBlocks array
                    foreach ($currentPageBlocks as $b) {
                        if (!in_array($b->getBlockID(), $oldMCBlockIDs)) {
                            $newBlockDisplayOrder = $this->getCollectionAreaDisplayOrder($b->getAreaHandle());
                            $db->executeQuery('insert into CollectionVersionBlocks (cID, cvID, bID, arHandle, cbDisplayOrder, isOriginal, cbOverrideAreaPermissions, cbIncludeAll) values (?, ?, ?, ?, ?, ?, ?, ?)', [
                                $this->getCollectionID(), $cvID, $b->getBlockID(), $b->getAreaHandle(), $newBlockDisplayOrder, intval($b->isAlias()), $b->overrideAreaPermissions(), $b->disableBlockVersioning(),
                            ]);
                        }
                    }

                    // Now, we need to change the default styles on the page, in case we are inheriting any from the
                    // defaults (for areas)
                    if ($template) {
                        $this->acquireAreaStylesFromDefaults($template);
                    }
                }
            }

            $v = [$cName, $cHandle, $pTemplateID, $cDescription, $cDatePublic, $cvID, $this->cID];
            $q = 'update CollectionVersions set cvName = ?, cvHandle = ?, pTemplateID = ?, cvDescription = ?, cvDatePublic = ? where cvID = ? and cID = ?';
            $r = $db->prepare($q);
            $r->execute($v);
        }

        // load new version object
        $this->loadVersionObject($cvID);

        $db->executeQuery('update Pages set ptID = ?, uID = ?, pkgID = ?, cFilename = ?, cCacheFullPageContent = ?, cCacheFullPageContentLifetimeCustom = ?, cCacheFullPageContentOverrideLifetime = ? where cID = ?', [$ptID, $uID, $pkgID, $cFilename, $cCacheFullPageContent, $cCacheFullPageContentLifetimeCustom, $cCacheFullPageContentOverrideLifetime, $this->cID]);

        $cache = PageCache::getLibrary();
        $cache->purge($this);

        $this->refreshCache();

        $pe = new Event($this);
        Events::dispatch('on_page_update', $pe);
    }

    public function clearPagePermissions()
    {
        $db = Database::connection();
        $db->executeQuery('delete from PagePermissionAssignments where cID = ?', [$this->cID]);
        $this->permissionAssignments = [];
    }

    public function inheritPermissionsFromParent()
    {
        $db = Database::connection();
        $cpID = $this->getParentPermissionsCollectionID();
        $this->updatePermissionsCollectionID($this->cID, $cpID);
        $v = ['PARENT', (int) $cpID, $this->cID];
        $q = 'update Pages set cInheritPermissionsFrom = ?, cInheritPermissionsFromCID = ? where cID = ?';
        $db->executeQuery($q, $v);
        $this->cInheritPermissionsFrom = 'PARENT';
        $this->cInheritPermissionsFromCID = $cpID;
        $this->clearPagePermissions();
        $this->rescanAreaPermissions();
    }

    public function inheritPermissionsFromDefaults()
    {
        $db = Database::connection();
        $type = $this->getPageTypeObject();
        if (is_object($type)) {
            $master = $type->getPageTypePageTemplateDefaultPageObject();
            if (is_object($master)) {
                $cpID = $master->getCollectionID();
                $this->updatePermissionsCollectionID($this->cID, $cpID);
                $v = ['TEMPLATE', (int) $cpID, $this->cID];
                $q = 'update Pages set cInheritPermissionsFrom = ?, cInheritPermissionsFromCID = ? where cID = ?';
                $db->executeQuery($q, $v);
                $this->cInheritPermissionsFrom = 'TEMPLATE';
                $this->cInheritPermissionsFromCID = $cpID;
                $this->clearPagePermissions();
                $this->rescanAreaPermissions();
            }
        }
    }

    public function setPermissionsToManualOverride()
    {
        if ($this->cInheritPermissionsFrom != 'OVERRIDE') {
            $db = Database::connection();
            $this->acquirePagePermissions($this->getPermissionsCollectionID());
            $this->acquireAreaPermissions($this->getPermissionsCollectionID());

            $cpID = $this->cID;
            $this->updatePermissionsCollectionID($this->cID, $cpID);
            $v = ['OVERRIDE', (int) $cpID, $this->cID];
            $q = 'update Pages set cInheritPermissionsFrom = ?, cInheritPermissionsFromCID = ? where cID = ?';
            $db->executeQuery($q, $v);
            $this->cInheritPermissionsFrom = 'OVERRIDE';
            $this->cInheritPermissionsFromCID = $cpID;
            $this->rescanAreaPermissions();
        }
    }

    public function rescanAreaPermissions()
    {
        $db = Database::connection();
        $r = $db->executeQuery('select arHandle, arIsGlobal from Areas where cID = ?', [$this->getCollectionID()]);
        while ($row = $r->FetchRow()) {
            $a = Area::getOrCreate($this, $row['arHandle'], $row['arIsGlobal']);
            $a->rescanAreaPermissionsChain();
        }
    }

    public function setOverrideTemplatePermissions($cOverrideTemplatePermissions)
    {
        $db = Database::connection();
        $v = [$cOverrideTemplatePermissions, $this->cID];
        $q = 'update Pages set cOverrideTemplatePermissions = ? where cID = ?';
        $db->executeQuery($q, $v);
        $this->cOverrideTemplatePermissions = $cOverrideTemplatePermissions;
    }

    public function updatePermissionsCollectionID($cParentIDString, $npID)
    {
        // now we iterate through
        $db = Database::connection();
        $pcID = $this->getPermissionsCollectionID();
        $q = "select cID from Pages where cParentID in ({$cParentIDString}) and cInheritPermissionsFromCID = {$pcID}";
        $r = $db->query($q);
        $cList = [];
        while ($row = $r->fetchRow()) {
            $cList[] = $row['cID'];
        }
        if (count($cList) > 0) {
            $cParentIDString = implode(',', $cList);
            $q2 = "update Pages set cInheritPermissionsFromCID = {$npID} where cID in ({$cParentIDString})";
            $db->query($q2);
            $this->updatePermissionsCollectionID($cParentIDString, $npID);
        }
    }

    public function acquireAreaPermissions($permissionsCollectionID)
    {
        $v = [$this->cID];
        $db = Database::connection();
        $q = 'delete from AreaPermissionAssignments where cID = ?';
        $db->executeQuery($q, $v);

        // ack - we need to copy area permissions from that page as well
        $v = [$permissionsCollectionID];
        $q = 'select cID, arHandle, paID, pkID from AreaPermissionAssignments where cID = ?';
        $r = $db->executeQuery($q, $v);
        while ($row = $r->fetchRow()) {
            $v = [$this->cID, $row['arHandle'], $row['paID'], $row['pkID']];
            $q = 'insert into AreaPermissionAssignments (cID, arHandle, paID, pkID) values (?, ?, ?, ?)';
            $db->executeQuery($q, $v);
        }

        // any areas that were overriding permissions on the current page need to be overriding permissions
        // on the NEW page as well.
        $v = [$permissionsCollectionID];
        $q = 'select * from Areas where cID = ? and arOverrideCollectionPermissions';
        $r = $db->executeQuery($q, $v);
        while ($row = $r->fetchRow()) {
            $v = [$this->cID, $row['arHandle'], $row['arOverrideCollectionPermissions'], $row['arInheritPermissionsFromAreaOnCID'], $row['arIsGlobal']];
            $q = 'insert into Areas (cID, arHandle, arOverrideCollectionPermissions, arInheritPermissionsFromAreaOnCID, arIsGlobal) values (?, ?, ?, ?, ?)';
            $db->executeQuery($q, $v);
        }
    }

    public function acquirePagePermissions($permissionsCollectionID)
    {
        $v = [$this->cID];
        $db = Database::connection();
        $q = 'delete from PagePermissionAssignments where cID = ?';
        $db->executeQuery($q, $v);

        $v = [$permissionsCollectionID];
        $q = 'select cID, paID, pkID from PagePermissionAssignments where cID = ?';
        $r = $db->executeQuery($q, $v);
        while ($row = $r->fetchRow()) {
            $v = [$this->cID, $row['paID'], $row['pkID']];
            $q = 'insert into PagePermissionAssignments (cID, paID, pkID) values (?, ?, ?)';
            $db->executeQuery($q, $v);
        }
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    public function updateGroupsSubCollection($cParentIDString)
    {
        // now we iterate through
        $db = Database::connection();
        $this->getPermissionsCollectionID();
        $q = "select cID from Pages where cParentID in ({$cParentIDString}) and cInheritPermissionsFrom = 'PARENT'";
        $r = $db->query($q);
        $cList = [];
        while ($row = $r->fetchRow()) {
            $cList[] = $row['cID'];
        }
        if (count($cList) > 0) {
            $cParentIDString = implode(',', $cList);
            $q2 = "update Pages set cInheritPermissionsFromCID = {$this->cID} where cID in ({$cParentIDString})";
            $db->query($q2);
            $this->updateGroupsSubCollection($cParentIDString);
        }
    }

    /**
     * Adds a block to the page.
     *
     * @param \Concrete\Core\Block\BlockType\BlockType $bt   The type of block to be added. 
     * @param \Concrete\Core\Area\Area $a    The area the block will appear. 
     * @param array $data   An array of settings for the block.
     * 
     * @return Block
     */
    public function addBlock($bt, $a, $data)
    {
        $b = parent::addBlock($bt, $a, $data);
        $btHandle = $bt->getBlockTypeHandle();
        if ($b->getBlockTypeHandle() == BLOCK_HANDLE_PAGE_TYPE_OUTPUT_PROXY) {
            $bi = $b->getInstance();
            $output = $bi->getComposerOutputControlObject();
            $control = FormLayoutSetControl::getByID($output->getPageTypeComposerFormLayoutSetControlID());
            $object = $control->getPageTypeComposerControlObject();
            if ($object instanceof BlockControl) {
                $_bt = $object->getBlockTypeObject();
                $btHandle = $_bt->getBlockTypeHandle();
            }
        }
        $theme = $this->getCollectionThemeObject();
        if ($btHandle && $theme) {
            $areaTemplates = [];
            $pageTypeTemplates = [];
            if (is_object($a)) {
                $areaTemplates = $a->getAreaCustomTemplates();
            }
            $themeTemplates = $theme->getThemeDefaultBlockTemplates();
            if (!is_array($themeTemplates)) {
                $themeTemplates = [];
            } else {
                foreach($themeTemplates as $key => $template) {
                    $pt = ($this->getPageTemplateHandle()) ? $this->getPageTemplateHandle() : 'default';
                    if(is_array($template) && $key == $pt) {
                        $pageTypeTemplates = $template;
                        unset($themeTemplates[$key]);
                    }
                }
            }
            $templates = array_merge($pageTypeTemplates, $themeTemplates, $areaTemplates);
            if (count($templates) && isset($templates[$btHandle])) {
                $template = $templates[$btHandle];
                $b->updateBlockInformation(['bFilename' => $template]);
            }
        }

        return $b;
    }

    public function getPageRelations()
    {
        $em = \Database::connection()->getEntityManager();
        $r = $em->getRepository('Concrete\Core\Entity\Page\Relation\SiblingRelation');
        $relation = $r->findOneBy(['cID' => $this->getCollectionID()]);
        $relations = array();
        if (is_object($relation)) {
            $allRelations = $r->findBy(['mpRelationID' => $relation->getPageRelationID()]);
            foreach($allRelations as $relation) {
                if ($relation->getPageID() != $this->getCollectionID() && $relation->getPageObject()->getSiteTreeObject() instanceof SiteTree) {
                    $relations[] = $relation;
                }
            }
        }
        return $relations;
    }

    public function move($nc)
    {
        $db = Database::connection();
        $newCParentID = $nc->getCollectionID();
        $dh = Core::make('helper/date');

        $cID = ($this->getCollectionPointerOriginalID() > 0) ? $this->getCollectionPointerOriginalID() : $this->cID;

        PageStatistics::decrementParents($cID);

        $cDateModified = $dh->getOverridableNow();
//      if ($this->getPermissionsCollectionID() != $this->getCollectionID() && $this->getPermissionsCollectionID() != $this->getMasterCollectionID()) {
        if ($this->getPermissionsCollectionID() != $cID) {
            // implicitly, we're set to inherit the permissions of wherever we are in the site.
            // as such, we'll change to inherit whatever permissions our new parent has
            $npID = $nc->getPermissionsCollectionID();
            if ($npID != $this->getPermissionsCollectionID()) {
                //we have to update the existing collection with the info for the new
                //as well as all collections beneath it that are set to inherit from this parent
                // first we do this one
                $q = 'update Pages set cInheritPermissionsFromCID = ? where cID = ?';
                $r = $db->executeQuery($q, [(int) $npID, $cID]);
                $this->updatePermissionsCollectionID($cID, $npID);
            }
        }

        $oldParent = self::getByID($this->getCollectionParentID(), 'RECENT');

        $db->executeQuery('update Collections set cDateModified = ? where cID = ?', [$cDateModified, $cID]);
        $v = [$newCParentID, $cID];
        $q = 'update Pages set cParentID = ? where cID = ?';
        $r = $db->prepare($q);
        $r->execute($v);

        PageStatistics::incrementParents($cID);
        if (!$this->isActive()) {
            $this->activate();
            // if we're moving from the trash, we have to activate recursively
            if ($this->isInTrash()) {
                $childPages = $this->populateRecursivePages([], ['cID' => $cID], $this->getCollectionParentID(), 0, false);
                foreach ($childPages as $page) {
                    $db->executeQuery('update Pages set cIsActive = 1 where cID = ?', [$page['cID']]);
                }
            }
        }

        if ($nc->getSiteTreeID() != $this->getSiteTreeID()) {
            $db->executeQuery('update Pages set siteTreeID = ? where cID = ?', [$nc->getSiteTreeID(), $cID]);
            if (!isset($childPages)) {
                $childPages = $this->populateRecursivePages([], ['cID' => $cID], $this->getCollectionParentID(), 0, false);
            }
            foreach ($childPages as $page) {
                $db->executeQuery('update Pages set siteTreeID = ? where cID = ?', [$nc->getSiteTreeID(), $page['cID']]);
            }
        }

        $this->siteTreeID = $nc->getSiteTreeID();
        $this->siteTree = null; // in case we need to get the updated one
        $this->cParentID = $newCParentID;
        $this->movePageDisplayOrderToBottom();
        // run any event we have for page move. Arguments are
        // 1. current page being moved
        // 2. former parent
        // 3. new parent

        $newParent = self::getByID($newCParentID, 'RECENT');

        $pe = new MovePageEvent($this);
        $pe->setOldParentPageObject($oldParent);
        $pe->setNewParentPageObject($newParent);
        Events::dispatch('on_page_move', $pe);

        $multilingual = \Core::make('multilingual/detector');
        if ($multilingual->isEnabled()) {
            Section::registerMove($this, $oldParent, $newParent);
        }

        // now that we've moved the collection, we rescan its path
        $this->rescanCollectionPath();
    }

    public function duplicateAll($nc = null, $preserveUserID = false, Site $site = null)
    {
        $nc2 = $this->duplicate($nc, $preserveUserID, $site);
        self::_duplicateAll($this, $nc2, $preserveUserID, $site);

        return $nc2;
    }

    protected function _duplicateAll($cParent, $cNewParent, $preserveUserID = false, Site $site = null)
    {
        $db = Database::connection();
        $cID = $cParent->getCollectionID();
        $q = 'select cID, ptHandle from Pages p left join PageTypes pt on p.ptID = pt.ptID where cParentID = ? order by cDisplayOrder asc';
        $r = $db->executeQuery($q, [$cID]);
        if ($r) {
            while ($row = $r->fetchRow()) {
                // This is a terrible hack.
                if ($row['ptHandle'] === STACKS_PAGE_TYPE) {
                    $tc = Stack::getByID($row['cID']);
                } else {
                    $tc = self::getByID($row['cID']);
                }
                $nc = $tc->duplicate($cNewParent, $preserveUserID, $site);
                $tc->_duplicateAll($tc, $nc, $preserveUserID, $site);
            }
        }
    }

    public function duplicate($nc = null, $preserveUserID = false, TreeInterface $site = null)
    {
        $db = Database::connection();
        // the passed collection is the parent collection
        $cParentID = is_object($nc) ? $nc->getCollectionID() : 0;

        $u = new User();
        $uID = $u->getUserID();
        if ($preserveUserID) {
            $uID = $this->getCollectionUserID();
        }
        $cobj = parent::getByID($this->cID);
        // create new name

        $newCollectionName = $this->getCollectionName();
        $index = 1;
        $nameCount = 1;

        while ($nameCount > 0) {
            // if we have a node at the new level with the same name, we keep incrementing til we don't
            $nameCount = $db->fetchColumn('select count(Pages.cID) from CollectionVersions inner join Pages on (CollectionVersions.cID = Pages.cID and CollectionVersions.cvIsApproved = 1) where Pages.cParentID = ? and CollectionVersions.cvName = ?',
                [$cParentID, $newCollectionName]
            );
            if ($nameCount > 0) {
                ++$index;
                $newCollectionName = $this->getCollectionName().' '.$index;
            }
        }

        $newC = $cobj->duplicateCollection();
        $newCID = $newC->getCollectionID();

        if (is_object($nc)) {
            $siteTreeID = $nc->getSiteTreeID();
        } else {
            $siteTreeID = is_object($site) ? $site->getSiteTreeID() : \Core::make('site')->getSite()->getSiteTreeID();
        }

        $v = [$newCID, $siteTreeID, $this->getPageTypeID(), $cParentID, $uID, $this->overrideTemplatePermissions(), (int) $this->getPermissionsCollectionID(), $this->getCollectionInheritance(), $this->cFilename, $this->getCollectionPointerID(), $this->cPointerExternalLink, $this->cPointerExternalLinkNewWindow, $this->cDisplayOrder, $this->pkgID];
        $q = 'insert into Pages (cID, siteTreeID, ptID, cParentID, uID, cOverrideTemplatePermissions, cInheritPermissionsFromCID, cInheritPermissionsFrom, cFilename, cPointerID, cPointerExternalLink, cPointerExternalLinkNewWindow, cDisplayOrder, pkgID) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $res = $db->executeQuery($q, $v);

        // Composer specific
        $rows = $db->fetchAll('select cID, cvID, arHandle, cbDisplayOrder, ptComposerFormLayoutSetControlID, bID from PageTypeComposerOutputBlocks where cID = ?',
            [$this->cID]);
        if ($rows && is_array($rows)) {
            foreach ($rows as $row) {
                if (is_array($row) && $row['cID']) {
                    $db->insert('PageTypeComposerOutputBlocks', [
                        'cID' => $newCID,
                        'cvID' => $row['cvID'],
                        'arHandle' => $row['arHandle'],
                        'cbDisplayOrder' => $row['cbDisplayOrder'],
                        'ptComposerFormLayoutSetControlID' => $row['ptComposerFormLayoutSetControlID'],
                        'bID' => $row['bID'],
                        ]);
                }
            }
        }

        PageStatistics::incrementParents($newCID);

        if ($res) {
            // rescan the collection path
            $nc2 = self::getByID($newCID);

            // now with any specific permissions - but only if this collection is set to override
            if ($this->getCollectionInheritance() == 'OVERRIDE') {
                $nc2->acquirePagePermissions($this->getPermissionsCollectionID());
                $nc2->acquireAreaPermissions($this->getPermissionsCollectionID());
                // make sure we update the proper permissions pointer to the new page ID
                $q = 'update Pages set cInheritPermissionsFromCID = ? where cID = ?';
                $v = [(int) $newCID, $newCID];
                $db->executeQuery($q, $v);
                $nc2->cInheritPermissionsFromCID = $newCID;
            } elseif ($this->getCollectionInheritance() == 'PARENT') {
                // we need to clear out any lingering permissions groups (just in case), and set this collection to inherit from the parent
                $npID = $nc->getPermissionsCollectionID();
                $q = 'update Pages set cInheritPermissionsFromCID = ? where cID = ?';
                $db->executeQuery($q, [(int) $npID, $newCID]);
                $nc2->cInheritPermissionsFromCID = $npID;
            }

            $args = [];
            if ($index > 1) {
                $args['cName'] = $newCollectionName;
                if ($nc2->getCollectionHandle()) {
                    $args['cHandle'] = $nc2->getCollectionHandle().'-'.$index;
                }
            }
            $nc2->update($args);

            // arguments for event
            // 1. new page
            // 2. old page
            $pe = new DuplicatePageEvent($this);
            $pe->setNewPageObject($nc2);

            Section::registerDuplicate($nc2, $this);

            Events::dispatch('on_page_duplicate', $pe);

            $nc2->rescanCollectionPath();
            $nc2->movePageDisplayOrderToBottom();

            return $nc2;
        }
    }

    public function delete()
    {
        $cID = $this->getCollectionID();

        if ($this->isAlias() && !$this->isExternalLink()) {
            $this->removeThisAlias();

            return;
        }

        if ($cID < 1 || $cID == static::getHomePageID()) {
            return false;
        }

        $db = Database::connection();

        // run any internal event we have for page deletion
        $pe = new DeletePageEvent($this);
        Events::dispatch('on_page_delete', $pe);

        if (!$pe->proceed()) {
            return false;
        }
        Log::addEntry(t('Page "%s" at path "%s" deleted', $this->getCollectionName(), $this->getCollectionPath()), t('Page Action'));

        parent::delete();

        $cID = $this->getCollectionID();

        // Now that all versions are gone, we can delete the collection information
        $q = "delete from PagePaths where cID = '{$cID}'";
        $r = $db->query($q);

        // remove all pages where the pointer is this cID
        $r = $db->executeQuery('select cID from Pages where cPointerID = ?', [$cID]);
        while ($row = $r->fetchRow()) {
            PageStatistics::decrementParents($row['cID']);
            $db->executeQuery('DELETE FROM PagePaths WHERE cID=?', [$row['cID']]);
        }

        // Update cChildren for cParentID
        PageStatistics::decrementParents($cID);

        $db->executeQuery('delete from PagePermissionAssignments where cID = ?', [$cID]);

        $db->executeQuery('delete from Pages where cID = ?', [$cID]);

        $db->executeQuery('delete from MultilingualPageRelations where cID = ?', [$cID]);

        $db->executeQuery('delete from SiblingPageRelations where cID = ?', [$cID]);

        $db->executeQuery('delete from Pages where cPointerID = ?', [$cID]);

        $db->executeQuery('delete from Areas WHERE cID = ?', [$cID]);

        $db->executeQuery('delete from PageSearchIndex where cID = ?', [$cID]);

        $r = $db->executeQuery('select cID from Pages where cParentID = ?', [$cID]);
        if ($r) {
            while ($row = $r->fetchRow()) {
                if ($row['cID'] > 0) {
                    $nc = self::getByID($row['cID']);
                    $nc->delete();
                }
            }
        }

        if (\Core::make('multilingual/detector')->isEnabled()) {
            Section::unregisterPage($this);
        }

        $cache = PageCache::getLibrary();
        $cache->purge($this);
    }

    public function moveToTrash()
    {

        // run any internal event we have for page trashing
        $pe = new Event($this);
        Events::dispatch('on_page_move_to_trash', $pe);

        $trash = self::getByPath(Config::get('concrete.paths.trash'));
        Log::addEntry(t('Page "%s" at path "%s" Moved to trash', $this->getCollectionName(), $this->getCollectionPath()), t('Page Action'));
        $this->move($trash);
        $this->deactivate();

        // if this page has a custom canonical path we need to clear it
        $path = $this->getCollectionPathObject();
        if (!$path->isPagePathAutoGenerated()) {
            $path = $this->getAutoGeneratedPagePathObject();
            $this->setCanonicalPagePath($path->getPagePath(), true);
            $this->rescanCollectionPath();
        }
        $cID = ($this->getCollectionPointerOriginalID() > 0) ? $this->getCollectionPointerOriginalID() : $this->cID;
        $pages = [];
        $pages = $this->populateRecursivePages($pages, ['cID' => $cID], $this->getCollectionParentID(), 0, false);
        $db = Database::connection();
        foreach ($pages as $page) {
            $db->executeQuery('update Pages set cIsActive = 0 where cID = ?', [$page['cID']]);
        }
    }

    public function rescanChildrenDisplayOrder()
    {
        $db = Database::connection();
        // this should be re-run every time a new page is added, but i don't think it is yet - AE
        //$oneLevelOnly=1;
        //$children_array = $this->getCollectionChildrenArray( $oneLevelOnly );
        $q = 'SELECT cID FROM Pages WHERE cParentID = ? ORDER BY cDisplayOrder';
        $children_array = $db->getCol($q, [$this->getCollectionID()]);
        $current_count = 0;
        foreach ($children_array as $newcID) {
            $q = 'update Pages set cDisplayOrder = ? where cID = ?';
            $db->executeQuery($q, [$current_count, $newcID]);
            ++$current_count;
        }
    }

    public function isHomePage()
    {
        return $this->getSiteHomePageID() == $this->getCollectionID();
    }

    /**
     * Get the ID of the homepage for the site tree this page belongs to.
     *
     * @return int|null Returns NULL if there's no default locale
     */
    public function getSiteHomePageID()
    {
        return static::getHomePageID($this);
    }

    /**
     * Is this page the homepage of a site tree?
     *
     * @return bool
     */
    public function isLocaleHomePage()
    {
        return $this->getCollectionID() > 0 && $this->getSiteHomePageID() == $this->getCollectionID();
    }

    /**
     * Get the ID of the home page.
     *
     * @param Page|int $page The page (or its ID) for which you want the home (if not specified, we'll use the default locale site tree).
     *
     * @return int|null Returns NULL if $page is null (or it doesn't have a SiteTree associated) and if there's no default locale.
     */
    public static function getHomePageID($page = null)
    {
        if ($page) {
            if (!$page instanceof self) {
                $page = self::getByID($page);
            }
            if ($page instanceof Page) {
                $siteTree = $page->getSiteTreeObject();
                if ($siteTree !== null) {
                    return $siteTree->getSiteHomePageID();
                }
            }
        }
        $locale = Application::getFacadeApplication()->make(LocaleService::class)->getDefaultLocale();
        if ($locale !== null) {
            $siteTree = $locale->getSiteTreeObject();
            if ($siteTree != null) {
                return $siteTree->getSiteHomePageID();
            }
        }

        return null;
    }

    public function getAutoGeneratedPagePathObject()
    {
        $path = new PagePath();
        $path->setPagePathIsAutoGenerated(true);
        //if (!$this->isHomePage()) {
            $path->setPagePath($this->computeCanonicalPagePath());
        //}

        return $path;
    }

    public function getNextSubPageDisplayOrder()
    {
        $db = Database::connection();
        $max = $db->fetchColumn('select max(cDisplayOrder) from Pages where cParentID = ?', [$this->getCollectionID()]);

        return is_numeric($max) ? ($max + 1) : 0;
    }

    /**
     * Returns the URL-slug-based path to the current page (including any suffixes) in a string format. Does so in real time.
     */
    public function generatePagePath()
    {
        $newPath = '';
        //if ($this->cParentID > 0) {
            /**
             * @var Connection
             */
            $db = \Database::connection();
            /* @var $em \Doctrine\ORM\EntityManager */
            $pathObject = $this->getCollectionPathObject();
            if (is_object($pathObject) && !$pathObject->isPagePathAutoGenerated()) {
                $pathString = $pathObject->getPagePath();
            } else {
                $pathString = $this->computeCanonicalPagePath();
            }
            if (!$pathString) {
                return ''; // We are allowed to pass in a blank path in the event of the home page being scanned.
            }
            // ensure that the path is unique
            $suffix = 0;
            $cID = ($this->getCollectionPointerOriginalID() > 0) ? $this->getCollectionPointerOriginalID() : $this->cID;
            $pagePathSeparator = Config::get('concrete.seo.page_path_separator');
            while (true) {
                $newPath = ($suffix === 0) ? $pathString : $pathString.$pagePathSeparator.$suffix;
                $result = $db->fetchColumn('select p.cID from PagePaths pp inner join Pages p on pp.cID = p.cID where pp.cPath = ? and pp.cID <> ? and p.siteTreeID = ?',
                    [
                        $newPath,
                        $cID,
                        $this->getSiteTreeID(),
                    ]
                );
                if (empty($result)) {
                    break;
                }
                ++$suffix;
            }
        //}

        return $newPath;
    }

    /**
     * Recalculates the canonical page path for the current page, based on its current version, URL slug, etc..
     */
    public function rescanCollectionPath()
    {
        //if ($this->cParentID > 0) {
            $newPath = $this->generatePagePath();

            $pathObject = $this->getCollectionPathObject();
            $ppIsAutoGenerated = true;
            if (is_object($pathObject) && !$pathObject->isPagePathAutoGenerated()) {
                $ppIsAutoGenerated = false;
            }
            $this->setCanonicalPagePath($newPath, $ppIsAutoGenerated);
            $this->rescanSystemPageStatus();
            $this->cPath = $newPath;
            $this->refreshCache();

            $children = $this->getCollectionChildren();
            if (count($children) > 0) {
                foreach ($children as $child) {
                    $child->rescanCollectionPath();
                }
            }
        //}
    }

    /**
     * For the curret page, return the text that will be used for that pages canonical path. This happens before
     * any uniqueness checks get run.
     *
     * @return string
     */
    protected function computeCanonicalPagePath()
    {
        $parent = self::getByID($this->cParentID);
        $parentPath = $parent->getCollectionPathObject();
        $path = '';
        if ($parentPath instanceof PagePath) {
            $path = $parentPath->getPagePath();
        }
        $path .= '/';
        $cID = ($this->getCollectionPointerOriginalID() > 0) ? $this->getCollectionPointerOriginalID() : $this->cID;
        /** @var \Concrete\Core\Utility\Service\Validation\Strings $stringValidator */
        $stringValidator = Core::make('helper/validation/strings');
        if ($stringValidator->notempty($this->getCollectionHandle())) {
            $path .= $this->getCollectionHandle();
        } else if (!$this->isHomePage()) {
            $path .= $cID;
        } else {
            $path = ''; // This is computing the path for the home page, which has no handle, and so shouldn't have a segment.
        }

        $event = new PagePathEvent($this);
        $event->setPagePath($path);
        $event = Events::dispatch('on_compute_canonical_page_path', $event);

        return $event->getPagePath();
    }

    public function updateDisplayOrder($do, $cID = 0)
    {
        //this line was added to allow changing the display order of aliases
        if (!intval($cID)) {
            $cID = ($this->getCollectionPointerOriginalID() > 0) ? $this->getCollectionPointerOriginalID() : $this->cID;
        }
        $db = Database::connection();
        $db->executeQuery('update Pages set cDisplayOrder = ? where cID = ?', [$do, $cID]);
    }

    public function movePageDisplayOrderToTop()
    {
        // first, we take the current collection, stick it at the beginning of an array, then get all other items from the current level that aren't that cID, order by display order, and then update
        $db = Database::connection();
        $nodes = [];
        $nodes[] = $this->getCollectionID();
        $r = $db->GetCol('select cID from Pages where cParentID = ? and cID <> ? order by cDisplayOrder asc', [$this->getCollectionParentID(), $this->getCollectionID()]);
        $nodes = array_merge($nodes, $r);
        $displayOrder = 0;
        foreach ($nodes as $do) {
            $co = self::getByID($do);
            $co->updateDisplayOrder($displayOrder);
            ++$displayOrder;
        }
    }

    public function movePageDisplayOrderToBottom()
    {
        // find the highest cDisplayOrder and increment by 1
        $db = Database::connection();
        $mx = $db->fetchAssoc('select max(cDisplayOrder) as m from Pages where cParentID = ?', [$this->getCollectionParentID()]);
        $max = $mx ? $mx['m'] : 0;
        ++$max;
        $this->updateDisplayOrder($max);
    }

    public function movePageDisplayOrderToSibling(Page $c, $position = 'before')
    {
        // first, we get a list of IDs.
        $pageIDs = [];
        $db = Database::connection();
        $r = $db->executeQuery('select cID from Pages where cParentID = ? and cID <> ? order by cDisplayOrder asc', [$this->getCollectionParentID(), $this->getCollectionID()]);
        while ($row = $r->FetchRow()) {
            if ($row['cID'] == $c->getCollectionID() && $position == 'before') {
                $pageIDs[] = $this->cID;
            }
            $pageIDs[] = $row['cID'];
            if ($row['cID'] == $c->getCollectionID() && $position == 'after') {
                $pageIDs[] = $this->cID;
            }
        }
        $displayOrder = 0;
        foreach ($pageIDs as $cID) {
            $co = self::getByID($cID);
            $co->updateDisplayOrder($displayOrder);
            ++$displayOrder;
        }
    }

    /**
     * Looks at the current page. If the site tree ID is 0, sets system page to true.
     * If the site tree is not user, looks at where the page falls in the hierarchy. If it's inside a page
     * at the top level that has 0 as its parent, then it is considered a system page.
     */
    public function rescanSystemPageStatus()
    {
        $systemPage = false;
        $db = Database::connection();
        $cID = $this->getCollectionID();
        if (!$this->isHomePage()) {
            if ($this->getSiteTreeID() == 0) {
                $systemPage = true;
            } else {
                $cID = ($this->getCollectionPointerOriginalID() > 0) ? $this->getCollectionPointerOriginalID() : $this->getCollectionID();
                $db = Database::connection();
                $path = $db->fetchColumn('select cPath from PagePaths where cID = ? and ppIsCanonical = 1', array($cID));
                if ($path) {
                    // Grab the top level parent
                    $fragments = explode('/', $path);
                    $topPath = '/' . $fragments[1];
                    $c = \Page::getByPath($topPath);
                    if (is_object($c) && !$c->isError()) {
                        if ($c->getCollectionParentID() == 0 && !$c->isHomePage()) {
                            $systemPage = true;
                        }
                    }
                }
            }
        }

        if ($systemPage) {
            $db->executeQuery('update Pages set cIsSystemPage = 1 where cID = ?', array($cID));
            $this->cIsSystemPage = true;
        } else {
            $db->executeQuery('update Pages set cIsSystemPage = 0 where cID = ?', array($cID));
            $this->cIsSystemPage = false;
        }
    }

    public function isInTrash()
    {
        return $this->getCollectionPath() != Config::get('concrete.paths.trash') && strpos($this->getCollectionPath(), Config::get('concrete.paths.trash')) === 0;
    }

    public function moveToRoot()
    {
        $db = Database::connection();
        $db->executeQuery('update Pages set cParentID = 0 where cID = ?', [$this->getCollectionID()]);
        $this->cParentID = 0;
        $this->rescanSystemPageStatus();
    }

    public function deactivate()
    {
        $db = Database::connection();
        $db->executeQuery('update Pages set cIsActive = 0 where cID = ?', [$this->getCollectionID()]);
    }

    public function setPageToDraft()
    {
        $db = Database::connection();
        $db->executeQuery('update Pages set cIsDraft = 1 where cID = ?', [$this->getCollectionID()]);
        $this->cIsDraft = true;
    }

    public function activate()
    {
        $db = Database::connection();
        $db->executeQuery('update Pages set cIsActive = 1 where cID = ?', [$this->getCollectionID()]);
    }

    public function isActive()
    {
        return (bool) $this->cIsActive;
    }

    public function setPageIndexScore($score)
    {
        $this->cIndexScore = $score;
    }

    public function getPageIndexScore()
    {
        return round($this->cIndexScore, 2);
    }

    public function getPageIndexContent()
    {
        $db = Database::connection();

        return $db->fetchColumn('select content from PageSearchIndex where cID = ?', [$this->cID]);
    }

    protected function _associateMasterCollectionBlocks($newCID, $masterCID, $cAcquireComposerOutputControls)
    {
        $mc = self::getByID($masterCID, 'ACTIVE');
        $nc = self::getByID($newCID, 'RECENT');
        $db = Database::connection();

        $mcID = $mc->getCollectionID();
        $mcvID = $mc->getVersionID();

        $q = "select CollectionVersionBlocks.arHandle, BlockTypes.btCopyWhenPropagate, CollectionVersionBlocks.cbOverrideAreaPermissions, CollectionVersionBlocks.bID from CollectionVersionBlocks inner join Blocks on Blocks.bID = CollectionVersionBlocks.bID inner join BlockTypes on Blocks.btID = BlockTypes.btID where CollectionVersionBlocks.cID = '$mcID' and CollectionVersionBlocks.cvID = '{$mcvID}' order by CollectionVersionBlocks.cbDisplayOrder asc";

        // ok. This function takes two IDs, the ID of the newly created virgin collection, and the ID of the crusty master collection
        // who will impart his wisdom to the his young learner, by duplicating his various blocks, as well as their permissions, for the
        // new collection

        //$q = "select CollectionBlocks.cbAreaName, Blocks.bID, Blocks.bName, Blocks.bFilename, Blocks.btID, Blocks.uID, BlockTypes.btClassname, BlockTypes.btTablename from CollectionBlocks left join BlockTypes on (Blocks.btID = BlockTypes.btID) inner join Blocks on (CollectionBlocks.bID = Blocks.bID) where CollectionBlocks.cID = '$masterCID' order by CollectionBlocks.cbDisplayOrder asc";
        //$q = "select CollectionVersionBlocks.cbAreaName, Blocks.bID, Blocks.bName, Blocks.bFilename, Blocks.btID, Blocks.uID, BlockTypes.btClassname, BlockTypes.btTablename from CollectionBlocks left join BlockTypes on (Blocks.btID = BlockTypes.btID) inner join Blocks on (CollectionBlocks.bID = Blocks.bID) where CollectionBlocks.cID = '$masterCID' order by CollectionBlocks.cbDisplayOrder asc";

        $r = $db->query($q);

        if ($r) {
            while ($row = $r->fetchRow()) {
                $b = Block::getByID($row['bID'], $mc, $row['arHandle']);
                if ($cAcquireComposerOutputControls || !in_array($b->getBlockTypeHandle(), ['core_page_type_composer_control_output'])) {
                    if ($row['btCopyWhenPropagate']) {
                        $b->duplicate($nc, true);
                    } else {
                        $b->alias($nc);
                    }
                }
            }
            $r->free();
        }
    }

    protected function _associateMasterCollectionAttributes($newCID, $masterCID)
    {
        $mc = self::getByID($masterCID, 'ACTIVE');
        $nc = self::getByID($newCID, 'RECENT');
        $attributes = CollectionKey::getAttributeValues($mc);
        foreach($attributes as $attribute) {
            $value = $attribute->getValueObject();
            if ($value) {
                $value = clone $value;
                $nc->setAttribute($attribute->getAttributeKey(), $value);
            }
        }
    }

    /**
     * Adds the home page to the system. Typically used only by the installation program.
     *
     * @return page
     **/
    public static function addHomePage(TreeInterface $siteTree = null)
    {
        // creates the home page of the site
        $db = Database::connection();

        $cParentID = 0;
        $uID = HOME_UID;

        $data = [
            'name' => HOME_NAME,
            'uID' => $uID,
        ];
        $cobj = parent::createCollection($data);
        $cID = $cobj->getCollectionID();

        if (!is_object($siteTree)) {
            $site = \Core::make('site')->getSite();
            $siteTree = $site->getSiteTreeObject();
        }
        $siteTreeID = $siteTree->getSiteTreeID();

        $v = [$cID, $siteTreeID, $cParentID, $uID, 'OVERRIDE', 1, (int) $cID, 0];
        $q = 'insert into Pages (cID, siteTreeID, cParentID, uID, cInheritPermissionsFrom, cOverrideTemplatePermissions, cInheritPermissionsFromCID, cDisplayOrder) values (?, ?, ?, ?, ?, ?, ?, ?)';
        $r = $db->prepare($q);
        $r->execute($v);
        $pc = self::getByID($cID, 'RECENT');

        return $pc;
    }

    /**
     * Adds a new page of a certain type, using a passed associate array to setup value. $data may contain any or all of the following:
     * "uID": User ID of the page's owner
     * "pkgID": Package ID the page belongs to
     * "cName": The name of the page
     * "cHandle": The handle of the page as used in the path
     * "cDatePublic": The date assigned to the page.
     *
     * @param \Concrete\Core\Page\Type\Type $pt
     * @param array $data
     *
     * @return page
     **/
    public function add($pt, $data, $template = false)
    {
        $data += [
            'cHandle' => null,
        ];
        $db = Database::connection();
        $txt = Core::make('helper/text');

        // the passed collection is the parent collection
        $cParentID = $this->getCollectionID();

        $u = new User();
        if (isset($data['uID'])) {
            $uID = $data['uID'];
        } else {
            $uID = $u->getUserID();
            $data['uID'] = $uID;
        }

        if (isset($data['pkgID'])) {
            $pkgID = $data['pkgID'];
        } else {
            $pkgID = 0;
        }

        $cIsActive = 1;
        if (isset($data['cIsActive']) && !$data['cIsActive']) {
            $cIsActive = 0;
        }

        $cIsDraft = 0;
        if (isset($data['cIsDraft']) && $data['cIsDraft']) {
            $cIsDraft = 1;
        }

        if (isset($data['cName'])) {
            $data['name'] = $data['cName'];
        } elseif (!isset($data['name'])) {
            $data['name'] = '';
        }

        if (!$data['cHandle']) {
            // make the handle out of the title
            $handle = $txt->urlify($data['name']);
        } else {
            $handle = $txt->slugSafeString($data['cHandle']); // we take it as it comes.
        }

        $handle = str_replace('-', Config::get('concrete.seo.page_path_separator'), $handle);
        $data['handle'] = $handle;

        $ptID = 0;
        $masterCIDBlocks = null;
        $masterCID = null;
        if ($pt instanceof \Concrete\Core\Page\Type\Type) {
            if ($pt->getPageTypeHandle() == STACKS_PAGE_TYPE) {
                $data['cvIsNew'] = 0;
            }
            if ($pt->getPackageID() > 0) {
                $pkgID = $pt->getPackageID();
            }

            // if we have a page type and we don't have a template,
            // then we use the page type's default template
            if ($pt->getPageTypeDefaultPageTemplateID() > 0 && !$template) {
                $template = $pt->getPageTypeDefaultPageTemplateObject();
            }

            $ptID = $pt->getPageTypeID();
            if ($template) {
                $mc1 = $pt->getPageTypePageTemplateDefaultPageObject($template);
                $mc2 = $pt->getPageTypePageTemplateDefaultPageObject();
                $masterCIDBlocks = $mc1->getCollectionID();
                $masterCID = $mc2->getCollectionID();
            }
        }

        if ($template instanceof TemplateEntity) {
            $data['pTemplateID'] = $template->getPageTemplateID();
        }

        $cobj = parent::addCollection($data);
        $cID = $cobj->getCollectionID();

        //$this->rescanChildrenDisplayOrder();
        $cDisplayOrder = $this->getNextSubPageDisplayOrder();

        $siteTreeID = $this->getSiteTreeID();

        $cInheritPermissionsFromCID = ($this->overrideTemplatePermissions()) ? $this->getPermissionsCollectionID() : $masterCID;
        $cInheritPermissionsFrom = ($this->overrideTemplatePermissions()) ? 'PARENT' : 'TEMPLATE';
        $v = [$cID, $siteTreeID, $ptID, $cParentID, $uID, $cInheritPermissionsFrom, $this->overrideTemplatePermissions(), (int) $cInheritPermissionsFromCID, $cDisplayOrder, $pkgID, $cIsActive, $cIsDraft];
        $q = 'insert into Pages (cID, siteTreeID, ptID, cParentID, uID, cInheritPermissionsFrom, cOverrideTemplatePermissions, cInheritPermissionsFromCID, cDisplayOrder, pkgID, cIsActive, cIsDraft) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $r = $db->prepare($q);
        $res = $r->execute($v);

        $newCID = $cID;

        if ($res) {
            // Collection added with no problem -- update cChildren on parrent
            PageStatistics::incrementParents($newCID);

            if ($r) {
                $cAcquireComposerOutputControls = false;
                if (isset($data['cAcquireComposerOutputControls']) && $data['cAcquireComposerOutputControls']) {
                    $cAcquireComposerOutputControls = true;
                }
                // now that we know the insert operation was a success, we need to see if the collection type we're adding has a master collection associated with it
                if ($masterCIDBlocks) {
                    $this->_associateMasterCollectionBlocks($newCID, $masterCIDBlocks, $cAcquireComposerOutputControls);
                }
                if ($masterCID) {
                    $this->_associateMasterCollectionAttributes($newCID, $masterCID);
                }
            }

            $pc = self::getByID($newCID, 'RECENT');
            // if we are in the drafts area of the site, then we don't check multilingual status. Otherwise
            // we do
            if ($this->getCollectionPath() != Config::get('concrete.paths.drafts')) {
                Section::registerPage($pc);
            }

            if ($template) {
                $pc->acquireAreaStylesFromDefaults($template);
            }

            // run any internal event we have for page addition
            $pe = new Event($pc);
            Events::dispatch('on_page_add', $pe);

            $pc->rescanCollectionPath();
        }

        $entities = $u->getUserAccessEntityObjects();
        $hasAuthor = false;
        foreach ($entities as $obj) {
            if ($obj instanceof PageOwnerEntity) {
                $hasAuthor = true;
            }
        }
        if (!$hasAuthor) {
            $u->refreshUserGroups();
        }

        return $pc;
    }

    protected function acquireAreaStylesFromDefaults(\Concrete\Core\Entity\Page\Template $template)
    {
        $pt = $this->getPageTypeObject();
        if (is_object($pt)) {
            $mc = $pt->getPageTypePageTemplateDefaultPageObject($template);
            $db = Database::connection();

            // first, we delete any styles we currently have
            $db->delete('CollectionVersionAreaStyles', ['cID' => $this->getCollectionID(), 'cvID' => $this->getVersionID()]);

            // now we acquire
            $q = 'select issID, arHandle from CollectionVersionAreaStyles where cID = ?';
            $r = $db->executeQuery($q, [$mc->getCollectionID()]);
            while ($row = $r->FetchRow()) {
                $db->executeQuery(
                    'insert into CollectionVersionAreaStyles (cID, cvID, arHandle, issID) values (?, ?, ?, ?)',
                    [
                        $this->getCollectionID(),
                        $this->getVersionID(),
                        $row['arHandle'],
                        $row['issID'],
                    ]
                );
            }
        }
    }

    public function getCustomStyleObject()
    {
        $db = Database::connection();
        $row = $db->FetchAssoc('select * from CollectionVersionThemeCustomStyles where cID = ? and cvID = ?', [$this->getCollectionID(), $this->getVersionID()]);
        if (isset($row['cID'])) {
            $o = new \Concrete\Core\Page\CustomStyle();
            $o->setThemeID($row['pThemeID']);
            $o->setValueListID($row['scvlID']);
            $o->setPresetHandle($row['preset']);
            $o->setCustomCssRecordID($row['sccRecordID']);

            return $o;
        }
    }

    public function getCollectionFullPageCaching()
    {
        return $this->cCacheFullPageContent;
    }

    public function getCollectionFullPageCachingLifetime()
    {
        return $this->cCacheFullPageContentOverrideLifetime;
    }

    public function getCollectionFullPageCachingLifetimeCustomValue()
    {
        return $this->cCacheFullPageContentLifetimeCustom;
    }

    public function getCollectionFullPageCachingLifetimeValue()
    {
        if ($this->cCacheFullPageContentOverrideLifetime == 'default') {
            $lifetime = Config::get('concrete.cache.lifetime');
        } elseif ($this->cCacheFullPageContentOverrideLifetime == 'custom') {
            $lifetime = $this->cCacheFullPageContentLifetimeCustom * 60;
        } elseif ($this->cCacheFullPageContentOverrideLifetime == 'forever') {
            $lifetime = 31536000; // 1 year
        } else {
            if (Config::get('concrete.cache.full_page_lifetime') == 'custom') {
                $lifetime = Config::get('concrete.cache.full_page_lifetime_value') * 60;
            } elseif (Config::get('concrete.cache.full_page_lifetime') == 'forever') {
                $lifetime = 31536000; // 1 year
            } else {
                $lifetime = Config::get('concrete.cache.lifetime');
            }
        }

        if (!$lifetime) {
            // we have no value, which means forever, but we need a numerical value for page caching
            $lifetime = 31536000;
        }

        return $lifetime;
    }

    public static function addStatic($data, TreeInterface $parent = null)
    {
        $db = Database::connection();
        if ($parent instanceof Page) {
            $cParentID = $parent->getCollectionID();
            $parent->rescanChildrenDisplayOrder();
            $cDisplayOrder = $parent->getNextSubPageDisplayOrder();
            $cInheritPermissionsFromCID = $parent->getPermissionsCollectionID();
            $cOverrideTemplatePermissions = $parent->overrideTemplatePermissions();
        } else {
            $cParentID = static::getHomePageID();
            $cDisplayOrder = 0;
            $cInheritPermissionsFromCID = $cParentID;
            $cOverrideTemplatePermissions = 1;
        }

        if (isset($data['pkgID'])) {
            $pkgID = $data['pkgID'];
        } else {
            $pkgID = 0;
        }

        $cFilename = $data['filename'];

        $uID = USER_SUPER_ID;
        $data['uID'] = $uID;
        $cobj = parent::createCollection($data);
        $cID = $cobj->getCollectionID();

        // These get set to parent by default here, but they can be overridden later
        $cInheritPermissionsFrom = 'PARENT';

        $siteTreeID = 0;
        if (is_object($parent)) {
            $siteTreeID = $parent->getSiteTreeID();
        }

        $v = [$cID, $siteTreeID, $cFilename, $cParentID, $cInheritPermissionsFrom, $cOverrideTemplatePermissions, (int) $cInheritPermissionsFromCID, $cDisplayOrder, $uID, $pkgID];
        $q = 'insert into Pages (cID, siteTreeID, cFilename, cParentID, cInheritPermissionsFrom, cOverrideTemplatePermissions, cInheritPermissionsFromCID, cDisplayOrder, uID, pkgID) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $r = $db->prepare($q);
        $res = $r->execute($v);

        if ($res) {
            // Collection added with no problem -- update cChildren on parrent
            PageStatistics::incrementParents($cID);
        }

        $pc = self::getByID($cID);
        $pc->rescanCollectionPath();

        return $pc;
    }

    /*
     * returns an instance of the current page object
     *
    */
    public static function getCurrentPage()
    {
        $req = Request::getInstance();
        $current = $req->getCurrentPage();

        return $current;
    }

    public function getPageDraftTargetParentPageID()
    {
        $db = Database::connection();

        return $db->fetchColumn('select cDraftTargetParentPageID from Pages where cID = ?', [$this->cID]);
    }

    public function setPageDraftTargetParentPageID($cParentID)
    {
        if ($cParentID != $this->getPageDraftTargetParentPageID()) {
            Section::unregisterPage($this);
        }
        $db = Database::connection();
        $cParentID = intval($cParentID);
        $db->executeQuery('update Pages set cDraftTargetParentPageID = ? where cID = ?', [$cParentID, $this->cID]);
        $this->cDraftTargetParentPageID = $cParentID;

        Section::registerPage($this);
    }
}
