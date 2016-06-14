<?php

namespace Concrete\Core\Page;

use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Page\Type\Composer\Control\BlockControl;
use Concrete\Core\Page\Type\Composer\FormLayoutSetControl;
use Concrete\Core\Page\Type\Type;
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
use Concrete\Core\Permission\Key\PageKey as PagePermissionKey;
use PermissionAccess;
use Concrete\Core\Package\PackageList;
use Concrete\Core\Permission\Access\Entity\Entity as PermissionAccessEntity;
use Concrete\Core\Permission\Access\Entity\GroupEntity as GroupPermissionAccessEntity;
use Concrete\Core\Permission\Access\Entity\GroupCombinationEntity as GroupCombinationPermissionAccessEntity;
use Concrete\Core\Permission\Access\Entity\UserEntity as UserPermissionAccessEntity;
use Concrete\Core\StyleCustomizer\CustomCssRecord;
use Area;
use Queue;
use Log;
use Environment;
use Group;
use Session;

/**
 * The page object in Concrete encapsulates all the functionality used by a typical page and their contents
 * including blocks, page metadata, page permissions.
 */
class Page extends Collection implements \Concrete\Core\Permission\ObjectInterface
{
    protected $controller;
    protected $blocksAliasedFromMasterCollection = null;
    protected $cIsSystemPage = false;
    protected $cPointerOriginalID = null;
    /**
     * @param string $path /path/to/page
     * @param string $version ACTIVE or RECENT
     *
     * @return Page
     */
    public static function getByPath($path, $version = 'RECENT')
    {
        $path = rtrim($path, '/'); // if the path ends in a / remove it.

        $cID = CacheLocal::getEntry('page_id_from_path', $path);
        if ($cID == false) {
            $db = Database::connection();
            $cID = $db->fetchColumn('select cID from PagePaths where cPath = ?', array($path));
            CacheLocal::set('page_id_from_path', $path, $cID);
        }

        return Page::getByID($cID, $version);
    }

    /**
     * @param int $cID Collection ID of a page
     * @param string $version ACTIVE or RECENT
     *
     * @return Page
     */
    public static function getByID($cID, $version = 'RECENT')
    {
        $class = get_called_class();
        $c = CacheLocal::getEntry('page', $cID.'/'.$version.'/'.$class);
        if ($c instanceof $class) {
            return $c;
        }

        $where = 'where Pages.cID = ?';
        $c = new $class();
        $c->populatePage($cID, $where, $version);

        // must use cID instead of c->getCollectionID() because cID may be the pointer to another page
        CacheLocal::set('page', $cID.'/'.$version.'/'.$class, $c);

        return $c;
    }

    public function __construct()
    {
        $this->loadError(COLLECTION_INIT); // init collection until we populate.
    }

    /**
     * @access private
     */
    protected function populatePage($cInfo, $where, $cvID)
    {
        $db = Database::connection();

        $this->loadError(false);

        $q0 = 'select Pages.cID, Pages.pkgID, Pages.cPointerID, Pages.cPointerExternalLink, Pages.cIsActive, Pages.cIsSystemPage, Pages.cPointerExternalLinkNewWindow, Pages.cFilename, Pages.ptID, Collections.cDateAdded, Pages.cDisplayOrder, Collections.cDateModified, cInheritPermissionsFromCID, cInheritPermissionsFrom, cOverrideTemplatePermissions, cCheckedOutUID, cIsTemplate, uID, cPath, cParentID, cChildren, cCacheFullPageContent, cCacheFullPageContentOverrideLifetime, cCacheFullPageContentLifetimeCustom from Pages inner join Collections on Pages.cID = Collections.cID left join PagePaths on (Pages.cID = PagePaths.cID and PagePaths.ppIsCanonical = 1) ';
        //$q2 = "select cParentID, cPointerID, cPath, Pages.cID from Pages left join PagePaths on (Pages.cID = PagePaths.cID and PagePaths.ppIsCanonical = 1) ";

        $v = array($cInfo);
        $r = $db->executeQuery($q0.$where, $v);
        $row = $r->fetchRow();
        if ($row['cPointerID'] > 0) {
            $q1 = $q0.'where Pages.cID = ?';
            $cPointerOriginalID = $row['cID'];
            $v = array($row['cPointerID']);
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
        $r->cID = $this->getCollectionID();

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
                $this->controller = Core::make($class, array($this));
            } else {
                $this->controller = Core::make('\PageController', array($this));
            }
        }

        return $this->controller;
    }

    public function getPermissionObjectIdentifier()
    {
        // this is a hack but it's a really good one for performance
        // if the permission access entity for page owner exists in the database, then we return the collection ID. Otherwise, we just return the permission collection id
        // this is because page owner is the ONLY thing that makes it so we can't use getPermissionsCollectionID, and for most sites that will DRAMATICALLY reduce the number of queries.
        if (\Concrete\Core\Permission\Access\PageAccess::usePermissionCollectionIDForIdentifier()) {
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
        $db->executeQuery($q, array($this->cID));
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

            return ($pos > -1);
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
            while ((!$cID) && $path) {
                $row = $db->fetchAssoc('select cID, ppIsCanonical from PagePaths where cPath = ?', array($path));
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
                $c = Page::getByID($cID, 'ACTIVE');
                $c->cPathFetchIsCanonical = $ppIsCanonical;
            } else {
                $c = new Page();
                $c->loadError(COLLECTION_NOT_FOUND);
            }

            return $c;
        } else {
            $cID = $request->query->get('cID');
            if (!$cID) {
                $cID = $request->request->get('cID');
            }
            $cID = Core::make('helper/security')->sanitizeInt($cID);
            if (!$cID) {
                $cID = 1;
            }
            $c = Page::getByID($cID, 'ACTIVE');
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
                     array($area_handle, $this->getCollectionID(), $this->getVersionID(), $moved_block_id));
        $db->executeQuery('UPDATE CollectionVersionBlocks SET arHandle = ?  WHERE cID = ? and cvID = ? and bID = ?',
                     array($area_handle, $this->getCollectionID(), $this->getVersionID(), $moved_block_id));

        $update_query = 'UPDATE CollectionVersionBlocks SET cbDisplayOrder = CASE bID';
        $when_statements = array();
        $update_values = array();
        foreach ($block_order as $key => $block_id) {
            $when_statements[] = 'WHEN ? THEN ?';
            $update_values[] = $block_id;
            $update_values[] = $key;
        }

        $update_query .= ' '.implode(' ', $when_statements).' END WHERE bID in ('.
            implode(',', array_pad(array(), count($block_order), '?')).') AND cID = ? AND cvID = ?';
        $values = array_merge($update_values, $block_order);
        $values = array_merge($values, array($this->getCollectionID(), $this->getVersionID()));

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

        $dh = Core::make('helper/date');

        $q = 'select cIsCheckedOut, '.$dh->getOverridableNow(true)." - UNIX_TIMESTAMP(cCheckedOutDatetimeLastEdit) as timeout from Pages where cID = '{$this->cID}'";
        $r = $db->executeQuery($q);
        if ($r) {
            $row = $r->fetchRow();
            if ($row['cIsCheckedOut'] == 0) {
                return false;
            } else {
                if ($row['timeout'] > CHECKOUT_TIMEOUT) {
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
        $vals = array($this->cID);
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

        return ($this->getCollectionCheckedOutUserID() > 0 && $this->getCollectionCheckedOutUserID() == $u->getUserID());
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

    public function assignPermissions($userOrGroup, $permissions = array(), $accessType = PagePermissionKey::ACCESS_TYPE_INCLUDE)
    {
        if ($this->cInheritPermissionsFrom != 'OVERRIDE') {
            $this->setPermissionsToManualOverride();
            $this->clearPagePermissions();
        }

        if (is_array($userOrGroup)) {
            $pe = GroupCombinationPermissionAccessEntity::getOrCreate($userOrGroup);
            // group combination
        } elseif ($userOrGroup instanceof User || $userOrGroup instanceof \Concrete\Core\User\UserInfo) {
            $pe = UserPermissionAccessEntity::getOrCreate($userOrGroup);
        } elseif ($userOrGroup instanceof PermissionAccessEntity) {
            $pe = $userOrGroup;
        } else {
            // group;
            $pe = GroupPermissionAccessEntity::getOrCreate($userOrGroup);
        }

        foreach ($permissions as $pkHandle) {
            $pk = PagePermissionKey::getByHandle($pkHandle);
            $pk->setPermissionObject($this);
            $pa = $pk->getPermissionAccessObject();
            if (!is_object($pa)) {
                $pa = PermissionAccess::create($pk);
            } elseif ($pa->isPermissionAccessInUse()) {
                $pa = $pa->duplicate();
            }
            $pa->addListItem($pe, false, $accessType);
            $pt = $pk->getPermissionAssignmentObject();
            $pt->assignPermissionAccess($pa);
        }
    }

    public function removePermissions($userOrGroup, $permissions = array())
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

    public static function getDrafts()
    {
        $db = Database::connection();
        $u = new User();
        $nc = Page::getByPath(Config::get('concrete.paths.drafts'));
        $r = $db->executeQuery('select Pages.cID from Pages inner join Collections c on Pages.cID = c.cID where cParentID = ? order by cDateAdded desc', array($nc->getCollectionID()));
        $pages = array();
        while ($row = $r->FetchRow()) {
            $entry = Page::getByID($row['cID']);
            if (is_object($entry)) {
                $pages[] = $entry;
            }
        }

        return $pages;
    }

    public function isPageDraft()
    {
        $nc = Page::getByPath(Config::get('concrete.paths.drafts'));

        return $this->getCollectionParentID() == $nc->getCollectionID();
    }

    private static function translatePermissionsXMLToKeys($node)
    {
        $pkHandles = array();
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
        $v = array($_cParentID);
        if ($_cParentID > 1) {
            $q .=  ' and ppIsCanonical = ?';
            $v[] = 1;
        }
        $cPath = $db->fetchColumn($q, $v);

        $data = array(
            'handle' => $this->getCollectionHandle(),
            'name' => $this->getCollectionName(),
        );
        $cobj = parent::addCollection($data);
        $newCID = $cobj->getCollectionID();

        $v = array($newCID, $cParentID, $uID, $this->getCollectionID(), $cDisplayOrder);
        $q = "insert into Pages (cID, cParentID, uID, cPointerID, cDisplayOrder) values (?, ?, ?, ?, ?)";
        $r = $db->prepare($q);

        $r->execute($v);

        PageStatistics::incrementParents($newCID);

        $q2 = 'insert into PagePaths (cID, cPath, ppIsCanonical, ppGeneratedFromURLSlugs) values (?, ?, ?, ?)';
        $v2 = array($newCID, $cPath.'/'.$handle, 1, 1);
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
            $db->executeQuery('update CollectionVersions set cvName = ? where cID = ?', array($cName, $this->cID));
            $db->executeQuery('update Pages set cPointerExternalLink = ?, cPointerExternalLinkNewWindow = ? where cID = ?', array($cLink, $newWindow, $this->cID));
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
        $data = array(
            'handle' => $handle,
            'name' => $cName,
        );
        $cobj = parent::addCollection($data);
        $newCID = $cobj->getCollectionID();

        if ($newWindow) {
            $newWindow = 1;
        } else {
            $newWindow = 0;
        }

        $cInheritPermissionsFromCID = $this->getPermissionsCollectionID();
        $cInheritPermissionsFrom = 'PARENT';

        $v = array($newCID, $cParentID, $uID, $cInheritPermissionsFrom, $cInheritPermissionsFromCID, $cLink, $newWindow);
        $q = 'insert into Pages (cID, cParentID, uID, cInheritPermissionsFrom, cInheritPermissionsFromCID, cPointerExternalLink, cPointerExternalLinkNewWindow) values (?, ?, ?, ?, ?, ?, ?)';
        $r = $db->prepare($q);

        $r->execute($v);

        PageStatistics::incrementParents($newCID);

        Page::getByID($newCID)->movePageDisplayOrderToBottom();

        return $newCID;
    }

    /**
     * Check if a page is a single page that is in the core (/concrete directory).
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

            $args = array($this->getCollectionPointerOriginalID());
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
        $children = $db->GetAll('select cID, cDisplayOrder from Pages where cParentID = ? order by cDisplayOrder asc', array($pageRow['cID']));
        if ($includeThisPage) {
            $pages[] = array(
                'cID' => $pageRow['cID'],
                'cDisplayOrder' => $pageRow['cDisplayOrder'],
                'cParentID' => $cParentID,
                'level' => $level,
                'total' => count($children),
            );
        }
        $level++;
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
        $pages = array();
        $includeThisPage = true;
        if ($this->getCollectionPath() == Config::get('concrete.paths.trash')) {
            // we're in the trash. we can't delete the trash. we're skipping over the trash node.
            $includeThisPage = false;
        }
        $pages = $this->populateRecursivePages($pages, array('cID' => $this->getCollectionID()), $this->getCollectionParentID(), 0, $includeThisPage);
        // now, since this is deletion, we want to order the pages by level, which
        // should get us no funny business if the queue dies.
        usort($pages, array('Page', 'queueForDeletionSort'));
        $q = Queue::get('delete_page');
        foreach ($pages as $page) {
            $q->send(serialize($page));
        }
    }

    public function queueForDeletionRequest($queue = null, $includeThisPage = true)
    {
        $pages = array();
        $pages = $this->populateRecursivePages($pages, array('cID' => $this->getCollectionID()), $this->getCollectionParentID(), 0, $includeThisPage);
        // now, since this is deletion, we want to order the pages by level, which
        // should get us no funny business if the queue dies.
        usort($pages, array('Page', 'queueForDeletionSort'));
        if (!$queue) {
            $queue = Queue::get('delete_page_request');
        }
        foreach ($pages as $page) {
            $queue->send(serialize($page));
        }
    }

    public function queueForDuplication($destination, $includeParent = true)
    {
        $pages = array();
        $pages = $this->populateRecursivePages($pages, array('cID' => $this->getCollectionID()), $this->getCollectionParentID(), 0, $includeParent);
        // we want to order the pages by level, which should get us no funny
        // business if the queue dies.
        usort($pages, array('Page', 'queueForDuplicationSort'));
        $q = Queue::get('copy_page');
        foreach ($pages as $page) {
            $page['destination'] = $destination->getCollectionID();
            $q->send(serialize($page));
        }
    }

    public function export($pageNode, $includePublicDate = true)
    {
        $p = $pageNode->addChild('page');
        $p->addAttribute('name', Core::make('helper/text')->entities($this->getCollectionName()));
        $p->addAttribute('path', $this->getCollectionPath());
        if ($includePublicDate) {
            $p->addAttribute('public-date', $this->getCollectionDatePUblic());
        }
        $p->addAttribute('filename', $this->getCollectionFilename());
        $p->addAttribute('pagetype', $this->getPageTypeHandle());
        $template = PageTemplate::getByID($this->getPageTemplateID());
        if (is_object($template)) {
            $p->addAttribute('template', $template->getPageTemplateHandle());
        }
        $ui = UserInfo::getByID($this->getCollectionUserID());
        if (!is_object($ui)) {
            $ui = UserInfo::getByID(USER_SUPER_ID);
        }
        $p->addAttribute('user', $ui->getUserName());
        $p->addAttribute('description', Core::make('helper/text')->entities($this->getCollectionDescription()));
        $p->addAttribute('package', $this->getPackageHandle());
        if ($this->getCollectionParentID() == 0 && $this->isSystemPage()) {
            $p->addAttribute('root', 'true');
        }

        $attribs = $this->getSetCollectionAttributes();
        if (count($attribs) > 0) {
            $attributes = $p->addChild('attributes');
            foreach ($attribs as $ak) {
                $av = $this->getAttributeValueObject($ak);
                $cnt = $ak->getController();
                $cnt->setAttributeValue($av);
                $akx = $attributes->addChild('attributekey');
                $akx->addAttribute('handle', $ak->getAttributeKeyHandle());
                $cnt->exportValue($akx);
            }
        }

        $db = Database::connection();
        $r = $db->executeQuery('select arHandle from Areas where cID = ? and arIsGlobal = 0 and arParentID = 0', array($this->getCollectionID()));
        while ($row = $r->FetchRow()) {
            $ax = Area::get($this, $row['arHandle']);
            $ax->export($p, $this);
        }
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
        return $this->cPath;
    }

    /**
     * Returns the PagePath object for the current page.
     */
    public function getCollectionPathObject()
    {
        $em = \ORM::entityManager('core');
        $cID = ($this->getCollectionPointerOriginalID() > 0) ? $this->getCollectionPointerOriginalID() : $this->cID;
        $path = $em->getRepository('\Concrete\Core\Page\PagePath')->findOneBy(
            array('cID' => $cID, 'ppIsCanonical' => true,
        ));

        return $path;
    }

    /**
     * Adds a non-canonical page path to the current page.
     */
    public function addAdditionalPagePath($cPath, $commit = true)
    {
        $em = \ORM::entityManager('core');
        $path = new \Concrete\Core\Page\PagePath();
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
        $em = \ORM::entityManager('core');
        $path = $this->getCollectionPathObject();
        if (is_object($path)) {
            $path->setPagePath($cPath);
        } else {
            $path = new \Concrete\Core\Page\PagePath();
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
        $em = \ORM::entityManager('core');

        return $em->getRepository('\Concrete\Core\Page\PagePath')->findBy(
            array('cID' => $this->getCollectionID()), array('ppID' => 'asc')
        );
    }

    public function getAdditionalPagePaths()
    {
        $em = \ORM::entityManager('core');

        return $em->getRepository('\Concrete\Core\Page\PagePath')->findBy(
            array('cID' => $this->getCollectionID(), 'ppIsCanonical' => false,
        ));
    }

    /**
     * Clears all page paths for a page.
     */
    public function clearPagePaths()
    {
        $em = \ORM::entityManager('core');
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
        $path = $db->fetchColumn('select cPath from PagePaths inner join CollectionVersions on (PagePaths.cID = CollectionVersions.cID and CollectionVersions.cvIsApproved = 1) where PagePaths.cID = ? order by PagePaths.ppIsCanonical desc', array($cID));

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
        return $this->ptID;
    }

    public function getPageTypeObject()
    {
        return PageType::getByID($this->ptID);
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
        if ($pt instanceof PageTemplate) {
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
            if ($this->ptID) {
                $pt = Type::getByID($this->ptID);
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
        if ($this->vObj->pThemeID < 1 && $this->cID != HOME_CID) {
            $c = Page::getByID(HOME_CID);

            return $c->getCollectionThemeID();
        } else {
            return $this->vObj->pThemeID;
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
            $v = array($this->getMasterCollectionID(), $this->getCollectionID(), $this->getVersionObject()->getVersionID());
            $this->blocksAliasedFromMasterCollection = $db->GetCol($q, $v);
        }

        return in_array($b->getBlockID(), $this->blocksAliasedFromMasterCollection);
    }

    /**
     * Returns Collection's theme object.
     *
     * @return PageTheme
     */
    public function getCollectionThemeObject()
    {
        if (!isset($this->themeObject)) {
            if ($this->vObj->pThemeID < 1) {
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
            return $this->vObj->cvName;
        }

        return $this->cvName;
    }

    /**
     * Returns the collection ID for the aliased page (returns 0 unless used on an actual alias).
     *
     * @return int
     */
    public function getCollectionPointerID()
    {
        return $this->cPointerID;
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
        return $this->cPointerID > 0 || $this->cPointerExternalLink != null;
    }

    /**
     * Checks if a page is an external link.
     *
     * @return bool
     */
    public function isExternalLink()
    {
        return ($this->cPointerExternalLink != null);
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
        return $this->cParentID;
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
        $cParentID = $db->fetchColumn($q, array($cID));

        return $cParentID;
    }

    /**
     * Returns an array of this cParentID and aliased parentIDs.
     *
     * @return array $cID
     */
    public function getCollectionParentIDs()
    {
        $cIDs = array($this->cParentID);
        $db = Database::connection();
        $aliasedParents = $db->fetchAll('SELECT cParentID FROM Pages WHERE cPointerID = ?', array($this->cID));
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
        $db->executeQuery('update CollectionVersions set pThemeID = ? where cID = ? and cvID = ?', array($pl->getThemeID(), $this->cID, $this->vObj->getVersionID()));
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
        $db->executeQuery('update Pages set ptID = ? where cID = ?', array($ptID, $this->cID));
        $this->ptID = $ptID;
    }

    /**
     * Set the permissions of sub-collections added beneath this permissions to inherit from the template.
     */
    public function setPermissionsInheritanceToTemplate()
    {
        $db = Database::connection();
        if ($this->cID) {
            $db->executeQuery('update Pages set cOverrideTemplatePermissions = 0 where cID = ?', array($this->cID));
        }
    }

    /**
     * Set the permissions of sub-collections added beneath this permissions to inherit from the parent.
     */
    public function setPermissionsInheritanceToOverride()
    {
        $db = Database::connection();
        if ($this->cID) {
            $db->executeQuery('update Pages set cOverrideTemplatePermissions = 1 where cID = ?', array($this->cID));
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
        $v = array($this->cParentID);
        $q = 'select cInheritPermissionsFromCID from Pages where cID = ?';
        $ppID = $db->fetchColumn($q, $v);

        return $ppID;
    }

    public function getPermissionsCollectionObject()
    {
        return Page::getByID($this->cInheritPermissionsFromCID, 'RECENT');
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
        $v = array($this->cID);
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
    public function getFirstChild($sortColumn = 'cDisplayOrder asc', $excludeSystemPages = false)
    {
        if ($excludeSystemPages) {
            $systemPages = ' and cIsSystemPage = 0';
        } else {
            $systemPages = '';
        }

        $db = Database::connection();
        $cID = $db->fetchColumn('select Pages.cID from Pages inner join CollectionVersions on Pages.cID = CollectionVersions.cID where cvIsApproved = 1 and cParentID = ? '.$systemPages." order by {$sortColumn}", array($this->cID));
        if ($cID > 1) {
            return Page::getByID($cID, 'ACTIVE');
        }

        return false;
    }

    public function getCollectionChildrenArray($oneLevelOnly = 0)
    {
        $this->childrenCIDArray = array();
        $this->_getNumChildren($this->cID, $oneLevelOnly);

        return $this->childrenCIDArray;
    }

    /**
     * Returns the immediate children of the current page.
     */
    public function getCollectionChildren()
    {
        $children = array();
        $db = Database::connection();
        $q = 'select cID from Pages where cParentID = ? and cIsTemplate = 0 order by cDisplayOrder asc';
        $r = $db->executeQuery($q, array($this->getCollectionID()));
        if ($r) {
            while ($row = $r->fetchRow()) {
                if ($row['cID'] > 0) {
                    $c = Page::getByID($row['cID']);
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

        return (!in_array($cobj->getCollectionID(), $children));
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

            $db->executeQuery('update CollectionVersions set cvName = ?, cvHandle = ? where cID = ? and cvID = ?', array($name, $cHandle, $this->getCollectionID(), $cvID));

            $cache = PageCache::getLibrary();
            $cache->purge($this);

            $pe = new Event($this);
            Events::dispatch('on_page_update', $pe);
        }
    }

    public function hasPageThemeCustomizations()
    {
        $db = Database::connection();

        return ($db->fetchColumn('select count(cID) from CollectionVersionThemeCustomStyles where cID = ? and cvID = ?', array(
            $this->cID, $this->getVersionID(),
        )) > 0);
    }

    public function resetCustomThemeStyles()
    {
        $db = Database::connection();
        $db->executeQuery('delete from CollectionVersionThemeCustomStyles where cID = ? and cvID = ?', array($this->getCollectionID(), $this->getVersionID()));
        $this->writePageThemeCustomizations();
    }

    public function setCustomStyleObject(\Concrete\Core\Page\Theme\Theme $pt, \Concrete\Core\StyleCustomizer\Style\ValueList $valueList, $selectedPreset = false, $customCssRecord = false)
    {
        $db = Database::connection();
        $db->delete('CollectionVersionThemeCustomStyles', array('cID' => $this->getCollectionID(), 'cvID' => $this->getVersionID()));
        $sccRecordID = 0;
        if ($customCssRecord instanceof CustomCssRecord) {
            $sccRecordID = $customCssRecord->getRecordID();
        }
        $preset = false;
        if ($selectedPreset) {
            $preset = $selectedPreset->getPresetHandle();
        }
        if ($customCssRecord instanceof CustomCssRecord) {
            $sccRecordID = $customCssRecord->getRecordID();
        }
        $db->insert(
            'CollectionVersionThemeCustomStyles',
            array(
                'cID' => $this->getCollectionID(),
                'cvID' => $this->getVersionID(),
                'pThemeID' => $pt->getThemeID(),
                'sccRecordID' => $sccRecordID,
                'preset' => $preset,
                'scvlID' => $valueList->getValueListID(),
            )
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
        if($view) {
            $ptm = $view->getPageTemplate();
        } else {
            $ptm = $this->getPageTemplateObject();
        }

        $classes = array('ccm-page');
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
            if (is_object($style)) {
                $scl = $style->getValueList();
            }

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
        $db->delete('CollectionVersionThemeCustomStyles', array('1' => 1));
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

        if (!$cDatePublic) {
            $cDatePublic = Core::make('helper/date')->getOverridableNow();
        }
        $txt = Core::make('helper/text');
        if (!isset($data['cHandle']) && ($this->getCollectionHandle() != '')) {
            $cHandle = $this->getCollectionHandle();
        } elseif (!Core::make('helper/validation/strings')->notempty($data['cHandle'])) {
            // make the handle out of the title
            $cHandle = $txt->urlify($cName);
            $cHandle = str_replace('-', Config::get('concrete.seo.page_path_separator'), $cHandle);
        } else {
            $cHandle = $txt->slugSafeString($data['cHandle']); // we DON'T run urlify
            $cHandle = str_replace('-', Config::get('concrete.seo.page_path_separator'), $cHandle);
        }
        $cName = $txt->sanitize($cName);

        if ($this->isGeneratedCollection()) {
            if (isset($data['cFilename'])) {
                $cFilename = $data['cFilename'];
            }
            // we only update a subset
            $v = array($cName, $cHandle, $cDescription, $cDatePublic, $cvID, $this->cID);
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
                    $oldMCBlockIDs = array();
                    foreach ($oldMCBlocks as $ob) {
                        $oldMCBlockIDs[] = $ob->getBlockID();
                    }

                    // now, we default all blocks on the current version of the page.
                    $db->executeQuery('delete from CollectionVersionBlocks where cID = ? and cvID = ?', array($this->getCollectionID(), $cvID));

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
                            $db->executeQuery('insert into CollectionVersionBlocks (cID, cvID, bID, arHandle, cbDisplayOrder, isOriginal, cbOverrideAreaPermissions, cbIncludeAll) values (?, ?, ?, ?, ?, ?, ?, ?)', array(
                                $this->getCollectionID(), $cvID, $b->getBlockID(), $b->getAreaHandle(), $newBlockDisplayOrder, intval($b->isAlias()), $b->overrideAreaPermissions(), $b->disableBlockVersioning(),
                            ));
                        }
                    }

                    // Now, we need to change the default styles on the page, in case we are inheriting any from the
                    // defaults (for areas)
                    if ($template) {
                        $this->acquireAreaStylesFromDefaults($template);
                    }
                }
            }

            $v = array($cName, $cHandle, $pTemplateID, $cDescription, $cDatePublic, $cvID, $this->cID);
            $q = 'update CollectionVersions set cvName = ?, cvHandle = ?, pTemplateID = ?, cvDescription = ?, cvDatePublic = ? where cvID = ? and cID = ?';
            $r = $db->prepare($q);
            $r->execute($v);
        }

        // load new version object
        $this->loadVersionObject($cvID);

        $db->executeQuery('update Pages set uID = ?, pkgID = ?, cFilename = ?, cCacheFullPageContent = ?, cCacheFullPageContentLifetimeCustom = ?, cCacheFullPageContentOverrideLifetime = ? where cID = ?', array($uID, $pkgID, $cFilename, $cCacheFullPageContent, $cCacheFullPageContentLifetimeCustom, $cCacheFullPageContentOverrideLifetime, $this->cID));

        $cache = PageCache::getLibrary();
        $cache->purge($this);

        $this->refreshCache();

        $pe = new Event($this);
        Events::dispatch('on_page_update', $pe);
    }

    public function clearPagePermissions()
    {
        $db = Database::connection();
        $db->executeQuery('delete from PagePermissionAssignments where cID = ?', array($this->cID));
        $this->permissionAssignments = array();
    }

    public function inheritPermissionsFromParent()
    {
        $db = Database::connection();
        $cpID = $this->getParentPermissionsCollectionID();
        $this->updatePermissionsCollectionID($this->cID, $cpID);
        $v = array('PARENT', $cpID, $this->cID);
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
                $v = array('TEMPLATE', $cpID, $this->cID);
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
            $v = array('OVERRIDE', $cpID, $this->cID);
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
        $r = $db->executeQuery('select arHandle, arIsGlobal from Areas where cID = ?', array($this->getCollectionID()));
        while ($row = $r->FetchRow()) {
            $a = Area::getOrCreate($this, $row['arHandle'], $row['arIsGlobal']);
            $a->rescanAreaPermissionsChain();
        }
    }

    public function setOverrideTemplatePermissions($cOverrideTemplatePermissions)
    {
        $db = Database::connection();
        $v = array($cOverrideTemplatePermissions, $this->cID);
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
        $cList = array();
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
        $v = array($this->cID);
        $db = Database::connection();
        $q = 'delete from AreaPermissionAssignments where cID = ?';
        $db->executeQuery($q, $v);

        // ack - we need to copy area permissions from that page as well
        $v = array($permissionsCollectionID);
        $q = 'select cID, arHandle, paID, pkID from AreaPermissionAssignments where cID = ?';
        $r = $db->executeQuery($q, $v);
        while ($row = $r->fetchRow()) {
            $v = array($this->cID, $row['arHandle'], $row['paID'], $row['pkID']);
            $q = 'insert into AreaPermissionAssignments (cID, arHandle, paID, pkID) values (?, ?, ?, ?)';
            $db->executeQuery($q, $v);
        }

        // any areas that were overriding permissions on the current page need to be overriding permissions
        // on the NEW page as well.
        $v = array($permissionsCollectionID);
        $q = 'select * from Areas where cID = ? and arOverrideCollectionPermissions';
        $r = $db->executeQuery($q, $v);
        while ($row = $r->fetchRow()) {
            $v = array($this->cID, $row['arHandle'], $row['arOverrideCollectionPermissions'], $row['arInheritPermissionsFromAreaOnCID'], $row['arIsGlobal']);
            $q = 'insert into Areas (cID, arHandle, arOverrideCollectionPermissions, arInheritPermissionsFromAreaOnCID, arIsGlobal) values (?, ?, ?, ?, ?)';
            $db->executeQuery($q, $v);
        }
    }

    public function acquirePagePermissions($permissionsCollectionID)
    {
        $v = array($this->cID);
        $db = Database::connection();
        $q = 'delete from PagePermissionAssignments where cID = ?';
        $db->executeQuery($q, $v);

        $v = array($permissionsCollectionID);
        $q = 'select cID, paID, pkID from PagePermissionAssignments where cID = ?';
        $r = $db->executeQuery($q, $v);
        while ($row = $r->fetchRow()) {
            $v = array($this->cID, $row['paID'], $row['pkID']);
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
        $cList = array();
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
            $areaTemplates = array();
            if (is_object($a)) {
                $areaTemplates = $a->getAreaCustomTemplates();
            }
            $themeTemplates = $theme->getThemeDefaultBlockTemplates();
            if (!is_array($themeTemplates)) {
                $themeTemplates = array();
            }
            $templates = array_merge($themeTemplates, $areaTemplates);
            if (count($templates) && isset($templates[$btHandle])) {
                $template = $templates[$btHandle];
                $b->updateBlockInformation(array('bFilename' => $template));
            }
        }

        return $b;
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
        if ($this->getPermissionsCollectionID() != $this->getCollectionID()) {
            // implicitly, we're set to inherit the permissions of wherever we are in the site.
            // as such, we'll change to inherit whatever permissions our new parent has
            $npID = $nc->getPermissionsCollectionID();
            if ($npID != $this->getPermissionsCollectionID()) {
                //we have to update the existing collection with the info for the new
                //as well as all collections beneath it that are set to inherit from this parent
                // first we do this one
                $q = 'update Pages set cInheritPermissionsFromCID = ? where cID = ?';
                $r = $db->executeQuery($q, array($npID, $this->cID));
                $this->updatePermissionsCollectionID($this->getCollectionID(), $npID);
            }
        }

        $oldParent = Page::getByID($this->getCollectionParentID(), 'RECENT');

        $db->executeQuery('update Collections set cDateModified = ? where cID = ?', array($cDateModified, $cID));
        $v = array($newCParentID, $cID);
        $q = 'update Pages set cParentID = ? where cID = ?';
        $r = $db->prepare($q);
        $r->execute($v);

        PageStatistics::incrementParents($cID);
        if (!$this->isActive()) {
            $this->activate();
            // if we're moving from the trash, we have to activate recursively
            if ($this->isInTrash()) {
                $pages = array();
                $pages = $this->populateRecursivePages($pages, array('cID' => $this->getCollectionID()), $this->getCollectionParentID(), 0, false);
                foreach ($pages as $page) {
                    $db->executeQuery('update Pages set cIsActive = 1 where cID = ?', array($page['cID']));
                }
            }
        }

        $this->cParentID = $newCParentID;
        $this->movePageDisplayOrderToBottom();
        // run any event we have for page move. Arguments are
        // 1. current page being moved
        // 2. former parent
        // 3. new parent

        $newParent = Page::getByID($newCParentID, 'RECENT');

        $pe = new MovePageEvent($this);
        $pe->setOldParentPageObject($oldParent);
        $pe->setNewParentPageObject($newParent);
        Events::dispatch('on_page_move', $pe);

        Section::registerMove($this, $oldParent, $newParent);

        // now that we've moved the collection, we rescan its path
        $this->rescanCollectionPath();
    }

    public function duplicateAll($nc, $preserveUserID = false)
    {
        $nc2 = $this->duplicate($nc);
        Page::_duplicateAll($this, $nc2, $preserveUserID);

        return $nc2;
    }

    /**
     * @access private
     **/
    protected function _duplicateAll($cParent, $cNewParent, $preserveUserID = false)
    {
        $db = Database::connection();
        $cID = $cParent->getCollectionID();
        $q = 'select cID from Pages where cParentID = ? order by cDisplayOrder asc';
        $r = $db->executeQuery($q, array($cID));
        if ($r) {
            while ($row = $r->fetchRow()) {
                $tc = Page::getByID($row['cID']);
                $nc = $tc->duplicate($cNewParent, $preserveUserID);
                $tc->_duplicateAll($tc, $nc, $preserveUserID);
            }
        }
    }

    public function duplicate($nc, $preserveUserID = false)
    {
        $db = Database::connection();
        // the passed collection is the parent collection
        $cParentID = $nc->getCollectionID();

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
                array($cParentID, $newCollectionName)
            );
            if ($nameCount > 0) {
                $index++;
                $newCollectionName = $this->getCollectionName().' '.$index;
            }
        }

        $newC = $cobj->duplicateCollection();
        $newCID = $newC->getCollectionID();

        $v = array($newCID, $this->getPageTypeID(), $cParentID, $uID, $this->overrideTemplatePermissions(), $this->getPermissionsCollectionID(), $this->getCollectionInheritance(), $this->cFilename, $this->cPointerID, $this->cPointerExternalLink, $this->cPointerExternalLinkNewWindow, $this->cDisplayOrder, $this->pkgID);
        $q = 'insert into Pages (cID, ptID, cParentID, uID, cOverrideTemplatePermissions, cInheritPermissionsFromCID, cInheritPermissionsFrom, cFilename, cPointerID, cPointerExternalLink, cPointerExternalLinkNewWindow, cDisplayOrder, pkgID) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $res = $db->executeQuery($q, $v);

        // Composer specific
        $rows = $db->fetchAll('select cID, arHandle, cbDisplayOrder, ptComposerFormLayoutSetControlID, bID from PageTypeComposerOutputBlocks where cID = ?',
            array($this->cID));
        if ($rows && is_array($rows)) {
            foreach ($rows as $row) {
                if (is_array($row) && $row['cID']) {
                    $db->insert('PageTypeComposerOutputBlocks', array(
                        'cID' => $newCID,
                        'arHandle' => $row['arHandle'],
                        'cbDisplayOrder' => $row['cbDisplayOrder'],
                        'ptComposerFormLayoutSetControlID' => $row['ptComposerFormLayoutSetControlID'],
                        'bID' => $row['bID']
                        ));
                    }
                }
        }

        PageStatistics::incrementParents($newCID);

        if ($res) {
            // rescan the collection path
            $nc2 = Page::getByID($newCID);

            // now with any specific permissions - but only if this collection is set to override
            if ($this->getCollectionInheritance() == 'OVERRIDE') {
                $nc2->acquirePagePermissions($this->getPermissionsCollectionID());
                $nc2->acquireAreaPermissions($this->getPermissionsCollectionID());
                // make sure we update the proper permissions pointer to the new page ID
                $q = 'update Pages set cInheritPermissionsFromCID = ? where cID = ?';
                $v = array($newCID, $newCID);
                $db->executeQuery($q, $v);
            } elseif ($this->getCollectionInheritance() == 'PARENT') {
                // we need to clear out any lingering permissions groups (just in case), and set this collection to inherit from the parent
                $npID = $nc->getPermissionsCollectionID();
                $q = 'update Pages set cInheritPermissionsFromCID = ? where cID = ?';
                $db->executeQuery($q, array($npID, $newCID));
            }

            $args = array();
            if ($index > 1) {
                $args['cName'] = $newCollectionName;
                $args['cHandle'] = $nc2->getCollectionHandle().'-'.$index;
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

        if ($cID <= 1) {
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
        $r = $db->executeQuery('select cID from Pages where cPointerID = ?', array($cID));
        while ($row = $r->fetchRow()) {
            PageStatistics::decrementParents($row['cID']);
            $db->executeQuery('DELETE FROM PagePaths WHERE cID=?', array($row['cID']));
        }

        // Update cChildren for cParentID
        PageStatistics::decrementParents($cID);

        $db->executeQuery('delete from PagePermissionAssignments where cID = ?', array($cID));

        $db->executeQuery('delete from Pages where cID = ?', array($cID));

        $db->executeQuery('delete from Pages where cPointerID = ?', array($cID));

        $db->executeQuery('delete from Areas WHERE cID = ?', array($cID));

        $db->executeQuery('delete from PageSearchIndex where cID = ?', array($cID));

        $r = $db->executeQuery('select cID from Pages where cParentID = ?', array($cID));
        if ($r) {
            while ($row = $r->fetchRow()) {
                if ($row['cID'] > 0) {
                    $nc = Page::getByID($row['cID']);
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

        $trash = Page::getByPath(Config::get('concrete.paths.trash'));
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
        $pages = array();
        $pages = $this->populateRecursivePages($pages, array('cID' => $cID), $this->getCollectionParentID(), 0, false);
        $db = Database::connection();
        foreach ($pages as $page) {
            $db->executeQuery('update Pages set cIsActive = 0 where cID = ?', array($page['cID']));
        }
    }

    public function rescanChildrenDisplayOrder()
    {
        $db = Database::connection();
        // this should be re-run every time a new page is added, but i don't think it is yet - AE
        //$oneLevelOnly=1;
        //$children_array = $this->getCollectionChildrenArray( $oneLevelOnly );
        $q = 'SELECT cID FROM Pages WHERE cParentID = ? ORDER BY cDisplayOrder';
        $children_array = $db->getCol($q, array($this->getCollectionID()));
        $current_count = 0;
        foreach ($children_array as $newcID) {
            $q = 'update Pages set cDisplayOrder = ? where cID = ?';
            $db->executeQuery($q, array($current_count, $newcID));
            $current_count++;
        }
    }

    public function getAutoGeneratedPagePathObject()
    {
        $path = new PagePath();
        $path->setPagePath($this->computeCanonicalPagePath());
        $path->setPagePathIsAutoGenerated(true);
        return $path;
    }
    public function getNextSubPageDisplayOrder()
    {
        $db = Database::connection();
        $max = $db->fetchColumn('select max(cDisplayOrder) from Pages where cParentID = ?', array($this->getCollectionID()));

        return is_numeric($max) ? ($max + 1) : 0;
    }

    /**
     * Returns the URL-slug-based path to the current page (including any suffixes) in a string format. Does so in real time.
     */
    public function generatePagePath()
    {
        $newPath = '';
        if ($this->cParentID > 0) {
            $em = \ORM::entityManager('core');
            /* @var $em \Doctrine\ORM\EntityManager */
            $pathObject = $this->getCollectionPathObject();
            if (is_object($pathObject) && !$pathObject->isPagePathAutoGenerated()) {
                $pathString = $pathObject->getPagePath();
            } else {
                $pathString = $this->computeCanonicalPagePath();
            }
            // ensure that the path is unique
            $suffix = 0;
            $cID = ($this->getCollectionPointerOriginalID() > 0) ? $this->getCollectionPointerOriginalID() : $this->cID;
            $q = $em->createQuery("select p.cID from Concrete\Core\Page\PagePath p where p.cPath = ?0 and p.cID <> ?1");
            $q->setHydrationMode(\Doctrine\ORM\Query::HYDRATE_SCALAR);
            $pagePathSeparator = Config::get('concrete.seo.page_path_separator');
            while (true) {
                $newPath = ($suffix === 0) ? $pathString : $pathString.$pagePathSeparator.$suffix;
                $result = $q->execute(
                    array(
                        $newPath,
                        $cID,
                    )
                );
                if (empty($result)) {
                    break;
                }
                $suffix++;
            }
        }

        return $newPath;
    }

    /**
     * Recalculates the canonical page path for the current page, based on its current version, URL slug, etc..
     */
    public function rescanCollectionPath()
    {
        if ($this->cParentID > 0) {
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
        }
    }

    /**
     * For the curret page, return the text that will be used for that pages canonical path. This happens before
     * any uniqueness checks get run.
     *
     * @return string
     */
    protected function computeCanonicalPagePath()
    {
        $parent = Page::getByID($this->cParentID);
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
        } else {
            $path .= $cID;
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
            $cID = $this->getCollectionID();
        }
        $db = Database::connection();
        $db->executeQuery('update Pages set cDisplayOrder = ? where cID = ?', array($do, $cID));
    }

    public function movePageDisplayOrderToTop()
    {
        // first, we take the current collection, stick it at the beginning of an array, then get all other items from the current level that aren't that cID, order by display order, and then update
        $db = Database::connection();
        $nodes = array();
        $nodes[] = $this->getCollectionID();
        $r = $db->GetCol('select cID from Pages where cParentID = ? and cID <> ? order by cDisplayOrder asc', array($this->getCollectionParentID(), $this->getCollectionID()));
        $nodes = array_merge($nodes, $r);
        $displayOrder = 0;
        foreach ($nodes as $do) {
            $co = Page::getByID($do);
            $co->updateDisplayOrder($displayOrder);
            $displayOrder++;
        }
    }

    public function movePageDisplayOrderToBottom()
    {
        // find the highest cDisplayOrder and increment by 1
        $db = Database::connection();
        $mx = $db->fetchAssoc('select max(cDisplayOrder) as m from Pages where cParentID = ?', array($this->getCollectionParentID()));
        $max = $mx ? $mx['m'] : 0;
        $max++;
        $this->updateDisplayOrder($max);
    }

    public function movePageDisplayOrderToSibling(Page $c, $position = 'before')
    {
        // first, we get a list of IDs.
        $pageIDs = array();
        $db = Database::connection();
        $r = $db->executeQuery('select cID from Pages where cParentID = ? and cID <> ? order by cDisplayOrder asc', array($this->getCollectionParentID(), $this->getCollectionID()));
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
            $co = Page::getByID($cID);
            $co->updateDisplayOrder($displayOrder);
            $displayOrder++;
        }
    }

    public function rescanSystemPageStatus()
    {
        $cID = ($this->getCollectionPointerOriginalID() > 0) ? $this->getCollectionPointerOriginalID() : $this->getCollectionID();
        $db = Database::connection();
        $newPath = $db->fetchColumn('select cPath from PagePaths where cID = ? and ppIsCanonical = 1', array($cID));
        // now we mark the page as a system page based on this path:
        $systemPages = array('/login', '/register', Config::get('concrete.paths.trash'), STACKS_PAGE_PATH, Config::get('concrete.paths.drafts'), '/members', '/members/*', '/account', '/account/*', Config::get('concrete.paths.trash').'/*', STACKS_PAGE_PATH.'/*', Config::get('concrete.paths.drafts').'/*', '/download_file', '/dashboard', '/dashboard/*','/page_forbidden','/page_not_found');
        $th = Core::make('helper/text');
        $db->executeQuery('update Pages set cIsSystemPage = 0 where cID = ?', array($cID));
        if ($this->cParentID == 0) {
            $db->executeQuery('update Pages set cIsSystemPage = 1 where cID = ?', array($cID));
            $this->cIsSystemPage = true;
        } else {
            foreach ($systemPages as $sp) {
                if ($th->fnmatch($sp, $newPath)) {
                    $db->executeQuery('update Pages set cIsSystemPage = 1 where cID = ?', array($cID));
                    $this->cIsSystemPage = true;
                }
            }
        }
    }

    public function isInTrash()
    {
        return $this->getCollectionPath() != Config::get('concrete.paths.trash') && strpos($this->getCollectionPath(), Config::get('concrete.paths.trash')) === 0;
    }

    public function moveToRoot()
    {
        $db = Database::connection();
        $db->executeQuery('update Pages set cParentID = 0 where cID = ?', array($this->getCollectionID()));
        $this->cParentID = 0;
        $this->rescanSystemPageStatus();
    }

    public function deactivate()
    {
        $db = Database::connection();
        $db->executeQuery('update Pages set cIsActive = 0 where cID = ?', array($this->getCollectionID()));
    }

    public function activate()
    {
        $db = Database::connection();
        $db->executeQuery('update Pages set cIsActive = 1 where cID = ?', array($this->getCollectionID()));
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

        return $db->fetchColumn('select content from PageSearchIndex where cID = ?', array($this->cID));
    }

    protected function _associateMasterCollectionBlocks($newCID, $masterCID, $cAcquireComposerOutputControls)
    {
        $mc = Page::getByID($masterCID, 'ACTIVE');
        $nc = Page::getByID($newCID, 'RECENT');
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
                if ($cAcquireComposerOutputControls || !in_array($b->getBlockTypeHandle(), array('core_page_type_composer_control_output'))) {
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
        $mc = Page::getByID($masterCID, 'ACTIVE');
        $nc = Page::getByID($newCID, 'RECENT');
        $db = Database::connection();

        $mcID = $mc->getCollectionID();

        $q = 'select * from CollectionAttributeValues where cID = ?';
        $r = $db->executeQuery($q, array($mcID));

        if ($r) {
            while ($row = $r->fetchRow()) {
                $db->executeQuery('insert into CollectionAttributeValues (cID, cvID, akID, avID) values (?, ?, ?, ?)', array(
                    $nc->getCollectionID(), $nc->getVersionID(), $row['akID'], $row['avID'],
                ));
            }
            $r->free();
        }
    }

    /**
     * Adds the home page to the system. Typically used only by the installation program.
     *
     * @return page
     **/
    public static function addHomePage()
    {
        // creates the home page of the site
        $db = Database::connection();

        $cParentID = 0;
        $handle = HOME_HANDLE;
        $uID = HOME_UID;

        $data = array(
            'name' => HOME_NAME,
            'handle' => $handle,
            'uID' => $uID,
            'cID' => HOME_CID,
        );
        $cobj = parent::createCollection($data);
        $cID = $cobj->getCollectionID();

        $v = array($cID, $cParentID, $uID, 'OVERRIDE', 1, 1, 0);
        $q = 'insert into Pages (cID, cParentID, uID, cInheritPermissionsFrom, cOverrideTemplatePermissions, cInheritPermissionsFromCID, cDisplayOrder) values (?, ?, ?, ?, ?, ?, ?)';
        $r = $db->prepare($q);
        $r->execute($v);
        $pc = Page::getByID($cID, 'RECENT');

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
        $data += array(
            'cHandle' => null,
        );
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

        if (isset($data['cName'])) {
            $data['name'] = $data['cName'];
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

        if ($template instanceof PageTemplate) {
            $data['pTemplateID'] = $template->getPageTemplateID();
        }

        $cobj = parent::addCollection($data);
        $cID = $cobj->getCollectionID();

        //$this->rescanChildrenDisplayOrder();
        $cDisplayOrder = $this->getNextSubPageDisplayOrder();

        $cInheritPermissionsFromCID = ($this->overrideTemplatePermissions()) ? $this->getPermissionsCollectionID() : $masterCID;
        $cInheritPermissionsFrom = ($this->overrideTemplatePermissions()) ? 'PARENT' : 'TEMPLATE';
        $v = array($cID, $ptID, $cParentID, $uID, $cInheritPermissionsFrom, $this->overrideTemplatePermissions(), $cInheritPermissionsFromCID, $cDisplayOrder, $pkgID, $cIsActive);
        $q = 'insert into Pages (cID, ptID, cParentID, uID, cInheritPermissionsFrom, cOverrideTemplatePermissions, cInheritPermissionsFromCID, cDisplayOrder, pkgID, cIsActive) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
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

            $pc = Page::getByID($newCID, 'RECENT');
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

    protected function acquireAreaStylesFromDefaults(\Concrete\Core\Page\Template $template)
    {
        $pt = $this->getPageTypeObject();
        if (is_object($pt)) {
            $mc = $pt->getPageTypePageTemplateDefaultPageObject($template);
            $db = Database::connection();

            // first, we delete any styles we currently have
            $db->delete('CollectionVersionAreaStyles', array('cID' => $this->getCollectionID(), 'cvID' => $this->getVersionID()));

            // now we acquire
            $q = 'select issID, arHandle from CollectionVersionAreaStyles where cID = ?';
            $r = $db->executeQuery($q, array($mc->getCollectionID()));
            while ($row = $r->FetchRow()) {
                $db->executeQuery(
                    'insert into CollectionVersionAreaStyles (cID, cvID, arHandle, issID) values (?, ?, ?, ?)',
                    array(
                        $this->getCollectionID(),
                        $this->getVersionID(),
                        $row['arHandle'],
                        $row['issID'],
                    )
                );
            }
        }
    }

    public function getCustomStyleObject()
    {
        $db = Database::connection();
        $row = $db->FetchAssoc('select * from CollectionVersionThemeCustomStyles where cID = ? and cvID = ?', array($this->getCollectionID(), $this->getVersionID()));
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

    public function addStatic($data)
    {
        $db = Database::connection();
        $cParentID = $this->getCollectionID();

        if (isset($data['pkgID'])) {
            $pkgID = $data['pkgID'];
        } else {
            $pkgID = 0;
        }

        $cFilename = $data['filename'];

        $uID = USER_SUPER_ID;
        $data['uID'] = $uID;
        $cIsSystemPage = 0;
        $cobj = parent::addCollection($data);
        $cID = $cobj->getCollectionID();

        $this->rescanChildrenDisplayOrder();
        $cDisplayOrder = $this->getNextSubPageDisplayOrder();

        // These get set to parent by default here, but they can be overridden later
        $cInheritPermissionsFromCID = $this->getPermissionsCollectionID();
        $cInheritPermissionsFrom = 'PARENT';

        $v = array($cID, $cFilename, $cParentID, $cInheritPermissionsFrom, $this->overrideTemplatePermissions(), $cInheritPermissionsFromCID, $cDisplayOrder, $cIsSystemPage, $uID, $pkgID);
        $q = 'insert into Pages (cID, cFilename, cParentID, cInheritPermissionsFrom, cOverrideTemplatePermissions, cInheritPermissionsFromCID, cDisplayOrder, cIsSystemPage, uID, pkgID) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $r = $db->prepare($q);
        $res = $r->execute($v);

        if ($res) {
            // Collection added with no problem -- update cChildren on parrent
            PageStatistics::incrementParents($cID);
        }

        $pc = Page::getByID($cID);
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

    /**
     * Returns the total number of page views for a specific page.
     */
    public function getTotalPageViews($date = null)
    {
        $db = Database::connection();
        if ($date != null) {
            return $db->fetchColumn('select count(pstID) from PageStatistics where date = ? AND cID = ?', array($date, $this->getCollectionID()));
        } else {
            return $db->fetchColumn('select count(pstID) from PageStatistics where cID = ?', array($this->getCollectionID()));
        }
    }

    public function getPageDraftTargetParentPageID()
    {
        $db = Database::connection();

        return $db->fetchColumn('select cDraftTargetParentPageID from Pages where cID = ?', array($this->cID));
    }

    public function setPageDraftTargetParentPageID($cParentID)
    {
        $db = Database::connection();
        $cParentID = intval($cParentID);
        $db->executeQuery('update Pages set cDraftTargetParentPageID = ? where cID = ?', array($cParentID, $this->cID));
        $this->cDraftTargetParentPageID = $cParentID;
    }

    /**
     * Gets a pages statistics.
     */
    public function getPageStatistics($limit = 20)
    {
        $db = Database::connection();
        $limitString = '';
        if ($limit != false) {
            $limitString = 'limit '.$limit;
        }

        if (is_object($this) && $this instanceof Page) {
            return $db->fetchAll("SELECT * FROM PageStatistics WHERE cID = ? ORDER BY timestamp desc {$limitString}", array($this->getCollectionID()));
        } else {
            return $db->fetchAll("SELECT * FROM PageStatistics ORDER BY timestamp desc {$limitString}");
        }
    }
}
