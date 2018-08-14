<?php

namespace Concrete\Core\Page;

use Collection;
use Concrete\Core\Area\Area;
use Concrete\Core\Attribute\Category\PageCategory;
use Concrete\Core\Attribute\ObjectInterface as AttributeObjectInterface;
use Concrete\Core\Block\Block;
use Concrete\Core\Cache\Page\PageCache;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Page\PagePath;
use Concrete\Core\Entity\Page\Relation\SiblingRelation;
use Concrete\Core\Entity\Page\Template as PageTemplateEntity;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Entity\Site\SiteTree;
use Concrete\Core\Entity\Site\Tree;
use Concrete\Core\Entity\StyleCustomizer\CustomCssRecord;
use Concrete\Core\Entity\User\User as UserEntity;
use Concrete\Core\Export\ExportableInterface;
use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Foundation\Queue\QueueService;
use Concrete\Core\Http\Request;
use Concrete\Core\Localization\Locale\Service as LocaleService;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Multilingual\Service\UserInterface\Flag;
use Concrete\Core\Package\PackageList;
use Concrete\Core\Page\Controller\PageController;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Page\Statistics as PageStatistics;
use Concrete\Core\Page\Template as PageTemplate;
use Concrete\Core\Page\Theme\Theme as PageTheme;
use Concrete\Core\Page\Type\Composer\Control\BlockControl;
use Concrete\Core\Page\Type\Composer\FormLayoutSetControl;
use Concrete\Core\Page\Type\Type as PageType;
use Concrete\Core\Permission\Access\Entity\GroupCombinationEntity as GroupCombinationPermissionAccessEntity;
use Concrete\Core\Permission\Access\Entity\GroupEntity as GroupPermissionAccessEntity;
use Concrete\Core\Permission\Access\Entity\PageOwnerEntity;
use Concrete\Core\Permission\Access\Entity\UserEntity as UserPermissionAccessEntity;
use Concrete\Core\Permission\Access\PageAccess;
use Concrete\Core\Permission\AssignableObjectInterface;
use Concrete\Core\Permission\AssignableObjectTrait;
use Concrete\Core\Permission\Key\PageKey as PagePermissionKey;
use Concrete\Core\Permission\ObjectInterface as PermissionObjectInterface;
use Concrete\Core\Routing\RouterInterface;
use Concrete\Core\Session\SessionValidatorInterface;
use Concrete\Core\Site\SiteAggregateInterface;
use Concrete\Core\Site\Tree\TreeInterface;
use Concrete\Core\StyleCustomizer\Style\ValueList as StyleValueList;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfo;
use Concrete\Core\User\UserInfoRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use PDO;
use stdClass;

/**
 * The page object in Concrete encapsulates all the functionality used by a typical page and their contents including blocks, page metadata, page permissions.
 */
class Page extends Collection implements PermissionObjectInterface, AttributeObjectInterface, AssignableObjectInterface, TreeInterface, SiteAggregateInterface, ExportableInterface
{
    use AssignableObjectTrait;

    public $siteTree;

    /**
     * The page controller.
     *
     * @var \Concrete\Core\Page\Controller\PageController|null
     */
    protected $controller;

    /**
     * The list of block IDs that are alias.
     *
     * @var int[]|null
     */
    protected $blocksAliasedFromMasterCollection;

    /**
     * The original cID of a page (if it's a page alias).
     *
     * @var int|null
     */
    protected $cPointerOriginalID;

    /**
     * The link for the aliased page.
     *
     * @var string|null
     */
    protected $cPointerExternalLink;

    /**
     * Should the alias link to be opened in a new window?
     *
     * @var bool|int|null
     */
    protected $cPointerExternalLinkNewWindow;

    /**
     * Is this page a page default?
     *
     * @var bool|int|null
     */
    protected $isMasterCollection;

    /**
     * The ID of the page from which this page inherits permissions from.
     *
     * @var int|null
     */
    protected $cInheritPermissionsFromCID;

    /**
     * Is this a system page?
     *
     * @var bool
     */
    protected $cIsSystemPage = false;

    /**
     * The site tree ID.
     *
     * @var int|null
     */
    protected $siteTreeID;

    /**
     * Initialize collection until we populate it.
     */
    public function __construct()
    {
        $this->loadError(COLLECTION_INIT);
    }

    /**
     * * Get a page given its ID.
     *
     * @param int $cID the ID of the page
     * @param string $version the page version ('RECENT' for the most recent version, 'ACTIVE' for the currently published version, 'SCHEDULED' for the currently scheduled version, or an integer to retrieve a specific version ID)
     *
     * @return \Concrete\Core\Page\Page
     */
    public static function getByID($cID, $version = 'RECENT')
    {
        $class = get_called_class();
        if ($cID && $version) {
            $app = Application::getFacadeApplication();
            $cacheItem = $app->make('cache/request')->getItem("page/{$cID}/{$version}/{$class}");
            $c = $cacheItem->get();
            if ($c instanceof $class) {
                return $c;
            }
        } else {
            $cacheItem = null;
        }
        $c = new $class();
        $c->populatePage($cID, 'where Pages.cID = ?', $version);
        if ($cacheItem !== null) {
            $cacheItem->set($c)->save();
        }

        return $c;
    }

    /**
     * Get a page given its path.
     *
     * @param string $path the page path (example: /path/to/page)
     * @param string $version the page version ('RECENT' for the most recent version, 'ACTIVE' for the currently published version, 'SCHEDULED' for the currently scheduled version, or an integer to retrieve a specific version ID)
     * @param \Concrete\Core\Site\Tree\TreeInterface|null $tree
     *
     * @return \Concrete\Core\Page\Page
     */
    public static function getByPath($path, $version = 'RECENT', TreeInterface $tree = null)
    {
        $app = Application::getFacadeApplication();
        $cache = $app->make('cache/request');

        $path = rtrim($path, '/');

        if ($tree) {
            $item = $cache->getItem(sprintf('site/page/path/%s/%s', $tree->getSiteTreeID(), ltrim($path, '/')));
            if ($item->isMiss()) {
                $db = $app->make(Connection::class);
                $cID = $db->fetchColumn('select Pages.cID from PagePaths inner join Pages on Pages.cID = PagePaths.cID where cPath = ? and siteTreeID = ?', [$path, $tree->getSiteTreeID()]);
                $cache->save($item->set($cID));
            } else {
                $cID = $item->get();
            }
        } else {
            $item = $cache->getItem(sprintf('page/path/%s', ltrim($path, '/')));
            if ($item->isMiss()) {
                $db = $app->make(Connection::class);
                $cID = $db->fetchColumn('select cID from PagePaths where cPath = ?', [$path]);
                $cache->save($item->set($cID));
            } else {
                $cID = $item->get();
            }
        }

        return self::getByID($cID, $version);
    }

    /**
     * Get the ID of the home page.
     *
     * @param Page|int $page the page (or its ID) for which you want the home (if not specified, we'll use the default locale site tree)
     *
     * @return int|null returns NULL if $page is null (or it doesn't have a SiteTree associated) and if there's no default locale
     */
    public static function getHomePageID($page = null)
    {
        if ($page) {
            if (!$page instanceof self) {
                $page = self::getByID($page);
            }
            if ($page instanceof self) {
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

    /**
     * Add the home page to the system. Typically used only by the installation program.
     *
     * @param \Concrete\Core\Site\Tree\TreeInterface|null $siteTree
     *
     * @return \Concrete\Core\Page\Page
     **/
    public static function addHomePage(TreeInterface $siteTree = null)
    {
        // creates the home page of the site
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);

        $cParentID = 0;
        $uID = HOME_UID;

        $homeCollection = parent::createCollection([
            'name' => HOME_NAME,
            'uID' => $uID,
        ]);
        $cID = $homeCollection->getCollectionID();

        if (!$siteTree) {
            $site = $app->make('site')->getSite();
            $siteTree = $site->getSiteTreeObject();
        }
        $siteTreeID = $siteTree->getSiteTreeID();

        $db->insert('Pages', [
            'cID' => $cID,
            'siteTreeID' => $siteTreeID,
            'cParentID' => $cParentID,
            'uID' => $uID,
            'cInheritPermissionsFrom' => 'OVERRIDE',
            'cOverrideTemplatePermissions' => 1,
            'cInheritPermissionsFromCID' => $cID,
            'cDisplayOrder' => 0,
        ]);
        $homePage = self::getByID($cID, 'RECENT');

        return $homePage;
    }

    /**
     * Create a new page.
     *
     * @param array $data The data to be used to create the page. See Collection::createCollection() for the supported keys, plus 'pkgID' and 'filename'.
     * @param \Concrete\Core\Site\Tree\TreeInterface|null $parent the parent page (or the site) that will contain the new page
     *
     * @return \Concrete\Core\Page\Page
     *
     * @see \Concrete\Core\Page\Collection\Collection::createCollection()
     */
    public static function addStatic($data, TreeInterface $parent = null)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        if ($parent instanceof self) {
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
        // These get set to parent by default here, but they can be overridden later
        $data['uID'] = USER_SUPER_ID;
        $newCollection = parent::createCollection($data);
        $cID = $newCollection->getCollectionID();

        $db->insert('Pages', [
            'cID' => $cID,
            'siteTreeID' => $parent ? $parent->getSiteTreeID() : 0,
            'cFilename' => isset($data['filename']) ? $data['filename'] : null,
            'cParentID' => $cParentID,
            'cInheritPermissionsFrom' => 'PARENT',
            'cOverrideTemplatePermissions' => $cOverrideTemplatePermissions,
            'cInheritPermissionsFromCID' => (int) $cInheritPermissionsFromCID,
            'cDisplayOrder' => $cDisplayOrder,
            'uID' => $data['uID'],
            'pkgID' => isset($data['pkgID']) ? $data['pkgID'] : 0,
        ]);

        PageStatistics::incrementParents($cID);

        $pc = self::getByID($cID);
        $pc->rescanCollectionPath();

        return $pc;
    }

    /**
     * Uses a Request object to determine which page to load. Queries by path and then by cID.
     *
     * @param \Concrete\Core\Http\Request $request
     */
    public static function getFromRequest(Request $request)
    {
        // if something has already set a page object, we return it
        $c = $request->getCurrentPage();
        if ($c) {
            return $c;
        }
        $app = Application::getFacadeApplication();
        $path = $request->getPath();
        if ($path !== '') {
            $db = $app->make(Connection::class);
            $site = $app->make('site')->getSite();
            $treeIDs = [0];
            foreach ($site->getLocales() as $locale) {
                $tree = $locale->getSiteTree();
                if ($tree) {
                    $treeIDs[] = $tree->getSiteTreeID();
                }
            }
            $treeIDs = implode(',', $treeIDs);
            $cID = false;
            $ppIsCanonical = false;
            while (!$cID && $path) {
                $row = $db->fetchAssoc('select pp.cID, ppIsCanonical from PagePaths pp inner join Pages p on pp.cID = p.cID where cPath = ? and siteTreeID in (' . $treeIDs . ')', [$path]);
                if ($row !== false) {
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
        }
        $cID = $request->query->get('cID');
        if (!$cID) {
            $cID = $request->request->get('cID');
        }
        $cID = $app->make('helper/security')->sanitizeInt($cID);
        if ($cID) {
            $c = self::getByID($cID, 'ACTIVE');
        } else {
            $site = $app->make('site')->getSite();
            $c = $site->getSiteHomePageObject('ACTIVE');
        }
        $c->cPathFetchIsCanonical = true;

        return $c;
    }

    /**
     * Get the currently requested page.
     *
     * @return \Concrete\Core\Page\Page|null
     */
    public static function getCurrentPage()
    {
        $req = Request::getInstance();
        $current = $req->getCurrentPage();

        return $current;
    }

    /**
     * Get the path for a page from its cID.
     *
     * @param int $cID
     *
     * @return string|false
     */
    public static function getCollectionPathFromID($cID)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $path = $db->fetchColumn(
            'select cPath from PagePaths inner join CollectionVersions on PagePaths.cID = CollectionVersions.cID and CollectionVersions.cvIsApproved = 1 where PagePaths.cID = ? order by PagePaths.ppIsCanonical desc',
            [$cID]
            );

        return $path;
    }

    /**
     * Get the parent cID of a page given its cID.
     *
     * @param int $cID
     *
     * @return int|null
     */
    public static function getCollectionParentIDFromChildID($cID)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $cParentID = $db->fetchColumn('select cParentID from Pages where cID = ?', [(int) $cID]);

        return $cParentID ?: null;
    }

    /**
     * @private
     * Forces all pages to be checked in and edit mode to be reset.
     * @TODO – move this into a command in version 9.
     */
    public static function forceCheckInForAllPages()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->query('update Pages set cIsCheckedOut = 0, cCheckedOutUID = null, cCheckedOutDatetime = null, cCheckedOutDatetimeLastEdit = null');
    }

    /**
     * Get the drafts parent page for a specific site.
     *
     * @param \Concrete\Core\Entity\Site\Site|null $site
     *
     * @return \Concrete\Core\Page\Page
     */
    public static function getDraftsParentPage(Site $site = null)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        if ($site === null) {
            $site = $app->make('site')->getSite();
        }
        $config = $app->make('config');
        $cParentID = $db->fetchColumn(
            'select p.cID from PagePaths pp inner join Pages p on pp.cID = p.cID inner join SiteLocales sl on p.siteTreeID = sl.siteTreeID where cPath = ? and sl.siteID = ?',
            [$config->get('concrete.paths.drafts'), $site->getSiteID()]
            );

        return self::getByID($cParentID);
    }

    /**
     * Get the list of draft pages in a specific site.
     *
     * @param \Concrete\Core\Entity\Site\Site $site
     *
     * @return \Concrete\Core\Page\Page[]
     */
    public static function getDrafts(Site $site)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $parentPage = self::getDraftsParentPage($site);
        $pages = [];
        $r = $db->executeQuery('select Pages.cID from Pages inner join Collections c on Pages.cID = c.cID where cParentID = ? order by cDateAdded desc', [$parentPage->getCollectionID()]);
        while (($cID = $r->fetchColumn()) !== false) {
            $entry = self::getByID($cID);
            if ($entry && !$entry->isError()) {
                $pages[] = $entry;
            }
        }

        return $pages;
    }

    /**
     * Sort a list of pages, so that the order is correct for the deletion.
     *
     * @param array $a
     * @param array $b
     *
     * @return int
     */
    public static function queueForDeletionSort($a, $b)
    {
        return $b['level'] - $a['level'];
    }

    /**
     * Sort a list of pages, so that the order is correct for the duplication.
     *
     * @param array $a
     * @param array $b
     *
     * @return int
     */
    public static function queueForDuplicationSort($a, $b)
    {
        $cmp = $a['level'] - $b['level'];
        if ($cmp === 0) {
            $cmp = $a['cDisplayOrder'] - $b['cDisplayOrder'];
            if ($cmp === 0) {
                $cmp = $a['cID'] - $b['cID'];
            }
        }

        return $cmp;
    }

    /**
     * Clears the custom theme styles for every page.
     */
    public static function resetAllCustomStyles()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->delete('CollectionVersionThemeCustomStyles', ['1' => 1]);
        $app->clearCaches();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\ObjectInterface::getObjectAttributeCategory()
     *
     * @return \Concrete\Core\Attribute\Category\PageCategory
     */
    public function getObjectAttributeCategory()
    {
        $app = Application::getFacadeApplication();

        return $app->make(PageCategory::class);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Export\ExportableInterface::getExporter()
     *
     * @return \Concrete\Core\Page\Exporter
     */
    public function getExporter()
    {
        return new Exporter();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\ObjectInterface::getPermissionResponseClassName()
     */
    public function getPermissionResponseClassName()
    {
        return '\\Concrete\\Core\\Permission\\Response\\PageResponse';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\ObjectInterface::getPermissionAssignmentClassName()
     */
    public function getPermissionAssignmentClassName()
    {
        return '\\Concrete\\Core\\Permission\\Assignment\\PageAssignment';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\ObjectInterface::getPermissionObjectKeyCategoryHandle()
     */
    public function getPermissionObjectKeyCategoryHandle()
    {
        return 'page';
    }

    /**
     * Return a representation of the Page object as something easily serializable.
     *
     * @return \stdClass
     */
    public function getJSONObject()
    {
        $r = new stdClass();
        $r->name = $this->getCollectionName();
        if ($this->isAliasPage()) {
            $r->cID = $this->getCollectionPointerOriginalID();
        } else {
            $r->cID = $this->getCollectionID();
        }

        return $r;
    }

    /**
     * Get the page controller.
     *
     * @return \Concrete\Core\Page\Controller\PageController
     */
    public function getPageController()
    {
        if ($this->controller === null) {
            $app = Application::getFacadeApplication();
            if ($this->getPageTypeID() > 0) {
                $pt = $this->getPageTypeObject();
                $ptHandle = $pt->getPageTypeHandle();
                $fileLocator = $app->make(FileLocator::class);
                $pkgHandle = $pt->getPackageHandle();
                if ($pkgHandle) {
                    $fileLocator->addLocation(new FileLocator\PackageLocation($pkgHandle));
                }
                $r = $fileLocator->getRecord(DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_TYPES . '/' . $ptHandle . '.php');
                $prefix = $r->override ? true : $pt->getPackageHandle();
                $class = core_class('Controller\\PageType\\' . camelcase($ptHandle), $prefix);
            } elseif ($this->isGeneratedCollection()) {
                $file = $this->getCollectionFilename();
                if (strpos($file, '/' . FILENAME_COLLECTION_VIEW) !== false) {
                    $path = substr($file, 0, strpos($file, '/' . FILENAME_COLLECTION_VIEW));
                } else {
                    $path = substr($file, 0, strpos($file, '.php'));
                }
                $fileLocator = $app->make(FileLocator::class);
                $pkgHandle = $this->getPackageHandle();
                if ($pkgHandle) {
                    $fileLocator->addLocation(new FileLocator\PackageLocation($pkgHandle));
                }
                $r = $fileLocator->getRecord(DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS . $path . '.php');
                $prefix = $r->override ? true : $this->getPackageHandle();
                $class = core_class('Controller\\SinglePage\\' . str_replace('/', '\\', camelcase($path, true)), $prefix);
            } else {
                $class = null;
            }

            if (!$class || !class_exists($class)) {
                $class = PageController::class;
            }

            $this->controller = $app->make($class, [$this]);
        }

        return $this->controller;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\ObjectInterface::getPermissionObjectIdentifier()
     */
    public function getPermissionObjectIdentifier()
    {
        // this is a hack but it's a really good one for performance
        // if the permission access entity for page owner exists in the database, then we return the collection ID. Otherwise, we just return the permission collection id
        // this is because page owner is the ONLY thing that makes it so we can't use getPermissionsCollectionID, and for most sites that will DRAMATICALLY reduce the number of queries.
        // Drafts are exceptions to this rule because some permission keys of these pages are inherited from "Edit Page Type Draft" permission.
        if (PageAccess::usePermissionCollectionIDForIdentifier() && !$this->isPageDraft()) {
            return $this->getPermissionsCollectionID();
        } else {
            return $this->getCollectionID();
        }
    }

    /**
     * Is the page in edit mode?
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
     * Get the handle the the package that added this page.
     *
     * @return string|null
     */
    public function getPackageHandle()
    {
        if (!isset($this->pkgHandle)) {
            if ($this->pkgID) {
                $this->pkgHandle = PackageList::getHandle($this->pkgID) ?: null;
            } else {
                $this->pkgHandle = null;
            }
        }

        return $this->pkgHandle;
    }

    /**
     * Forces the page to be checked in if its checked out.
     */
    public function forceCheckIn()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->executeQuery(
            'update Pages set cIsCheckedOut = 0, cCheckedOutUID = null, cCheckedOutDatetime = null, cCheckedOutDatetimeLastEdit = null where cID = ?',
            [$this->getCollectionID()]
        );
    }

    /**
     * Is this a dashboard page?
     *
     * @return bool
     */
    public function isAdminArea()
    {
        if (!$this->isGeneratedCollection()) {
            return false;
        }

        return strpos($this->getCollectionFilename(), '/' . DIRNAME_DASHBOARD) !== false;
    }

    /**
     * Persist the data associated to a block when it has been moved around in the page.
     *
     * @param int $area_id The ID of the area where the block resides after the arrangment
     * @param int $moved_block_id The ID of the moved block
     * @param int[] $block_order The IDs of all the blocks in the area, ordered by their display order
     */
    public function processArrangement($area_id, $moved_block_id, $block_order)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);

        $area_handle = Area::getAreaHandleFromID($area_id);

        $db->executeQuery(
            'UPDATE CollectionVersionBlockStyles SET arHandle = ? WHERE cID = ? and cvID = ? and bID = ?',
            [$area_handle, $this->getCollectionID(), $this->getVersionID(), $moved_block_id]
        );
        $db->executeQuery(
            'UPDATE CollectionVersionBlocks SET arHandle = ? WHERE cID = ? and cvID = ? and bID = ?',
            [$area_handle, $this->getCollectionID(), $this->getVersionID(), $moved_block_id]
        );

        $when_statements = [];
        $update_values = [];
        $block_order = array_map('intval', $block_order);
        foreach ($block_order as $key => $block_id) {
            $when_statements[] = 'WHEN ? THEN ?';
            $update_values[] = $block_id;
            $update_values[] = $key;
        }
        $update_query = 'UPDATE CollectionVersionBlocks SET cbDisplayOrder = CASE bID ' . implode(' ', $when_statements) . ' END';
        $update_query .= ' WHERE bID in (' . implode(',', array_pad([], count($block_order), '?')) . ') AND cID = ? AND cvID = ?';
        $values = array_merge($update_values, $block_order, [$this->getCollectionID(), $this->getVersionID()]);
        $db->executeQuery($update_query, $values);
    }

    /**
     * Is the page checked out?
     *
     * @return bool|null returns NULL if the page does not exist, a boolean otherwise
     */
    public function isCheckedOut()
    {
        if (isset($this->isCheckedOutCache)) {
            return $this->isCheckedOutCache;
        }
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $row = $db->fetchAssoc('select cIsCheckedOut, cCheckedOutDatetimeLastEdit from Pages where cID = ?', [$this->getCollectionID()]);
        if ($row === false) {
            return null;
        }
        if (empty($row['cIsCheckedOut'])) {
            return false;
        }
        // If cCheckedOutDatetimeLastEdit is present, get the time span in seconds since it's last edit.
        if (empty($row['cCheckedOutDatetimeLastEdit'])) {
            $this->isCheckedOutCache = true;
        } else {
            $dh = $app->make('date');
            $timeSinceCheckout = $dh->getOverridableNow(true) - strtotime($row['cCheckedOutDatetimeLastEdit']);
            if ($timeSinceCheckout > CHECKOUT_TIMEOUT) {
                $this->forceCheckIn();
                $this->isCheckedOutCache = false;
            } else {
                $this->isCheckedOutCache = true;
            }
        }

        return $this->isCheckedOutCache;
    }

    /**
     * Gets the user that is editing the current page.
     * $return string $name.
     */
    public function getCollectionCheckedOutUserName()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $checkedOutId = $db->fetchColumn('select cCheckedOutUID from Pages where cID = ?', [$this->getCollectionID()]);
        if ($checkedOutId) {
            $userInfo = $app->make(UserInfoRepository::class)->getByID($checkedOutId);
        } else {
            $userInfo = null;
        }

        return $userInfo === null ? t('Unknown User') : $userInfo->getUserName();
    }

    /**
     * Checks if the page is checked out by the current user.
     *
     * @return bool
     */
    public function isCheckedOutByMe()
    {
        $result = false;
        $checkedOutUserID = $this->getCollectionCheckedOutUserID();
        if ($checkedOutUserID > 0) {
            $u = new User();
            $result = $checkedOutUserID == $u->getUserID();
        }

        return $result;
    }

    /**
     * Checks if the page is a single page.
     *
     * Generated collections are collections without templates, that have special cFilename attributes
     *.
     *
     * @return bool
     */
    public function isGeneratedCollection()
    {
        return $this->getCollectionFilename() && !$this->getPageTemplateID();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\AssignableObjectInterface::setPermissionsToOverride()
     */
    public function setPermissionsToOverride()
    {
        if ($this->getCollectionInheritance() !== 'OVERRIDE') {
            $this->setPermissionsToManualOverride();
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\AssignableObjectInterface::setChildPermissionsToOverride()
     */
    public function setChildPermissionsToOverride()
    {
        foreach ($this->getCollectionChildren() as $child) {
            $child->setPermissionsToManualOverride();
        }
    }

    /**
     * Remove specific permission keys for a specific access entity (user, group, group combination).
     *
     * @param \Concrete\Core\User\Group\Group|\Concrete\Core\User\Group\Group[]|\Concrete\Core\User\User|\Concrete\Core\User\UserInfo|\Concrete\Core\Entity\User\User $userOrGroup A list of groups for a group combination, or a group or a user
     * @param string[] $permissions the handles of page permission keys to be removed
     */
    public function removePermissions($userOrGroup, $permissions = [])
    {
        if ($this->getCollectionInheritance() !== 'OVERRIDE') {
            return;
        }

        if (is_array($userOrGroup)) {
            // group combination
            $pe = GroupCombinationPermissionAccessEntity::getOrCreate($userOrGroup);
        } elseif ($userOrGroup instanceof UserInfo) {
            // user (from UserInfo)
            $pe = UserPermissionAccessEntity::getOrCreate($userOrGroup);
        } elseif ($userOrGroup instanceof User || $userOrGroup instanceof UserEntity) {
            // user (from User object / entity)
            $pe = UserPermissionAccessEntity::getOrCreate($userOrGroup->getUserInfoObject());
        } else {
            // group
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

    /**
     * Set the page controller.
     *
     * @param \Concrete\Core\Page\Controller\PageController|null $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * This is the legacy function that is called just by xml. We pass these values in as though they were the old ones.
     *
     * @private
     *
     * @param \SimpleXMLElement $px
     */
    public function assignPermissionSet($px)
    {
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
            $app = Application::getFacadeApplication();
            $userInfoRepository = $app->make(UserInfoRepository::class);
            foreach ($px->user as $u) {
                $pkHandles = self::translatePermissionsXMLToKeys($px->administrators);
                $this->assignPermissions($userInfoRepository->getByID($u['uID']), $pkHandles);
            }
        }
    }

    /**
     * Make an alias to a page.
     *
     * @param \Concrete\Core\Page\Page $parentPage The parent page
     *
     * @return int $newCID
     */
    public function addCollectionAlias($parentPage)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);

        $cParentID = $parentPage->getCollectionID();

        $q = 'select PagePaths.cPath from PagePaths where cID = ?';
        $v = [$cParentID];
        if ($cParentID != static::getHomePageID($parentPage)) {
            $q .= ' and ppIsCanonical = ?';
            $v[] = 1;
        }
        $cPath = $db->fetchColumn($q, $v);
        $handle = $this->getCollectionHandle();
        $u = new User();
        $uID = $u->getUserID();
        $cDisplayOrder = $parentPage->getNextSubPageDisplayOrder();

        $cobj = parent::addCollection([
            'handle' => $handle,
            'name' => $this->getCollectionName(),
        ]);
        $newCID = $cobj->getCollectionID();

        $db->insert('Pages', [
            'cID' => $newCID,
            'siteTreeID' => $parentPage->getSiteTreeID(),
            'cParentID' => $cParentID,
            'uID' => $uID,
            'cPointerID' => $this->getCollectionID(),
            'cDisplayOrder' => $cDisplayOrder,
        ]);
        PageStatistics::incrementParents($newCID);

        $db->insert('PagePaths', [
            'cID' => $newCID,
            'cPath' => $cPath . '/' . $handle,
            'ppIsCanonical' => 1,
            'ppGeneratedFromURLSlugs' => 1,
        ]);

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
        if ($this->isExternalLink()) {
            $app = Application::getFacadeApplication();
            $db = $app->make(Connection::class);
            $this->markModified();
            $db->executeQuery('update CollectionVersions set cvName = ? where cID = ?', [$cName, $this->getCollectionID()]);
            $db->executeQuery('update Pages set cPointerExternalLink = ?, cPointerExternalLinkNewWindow = ? where cID = ?', [$cLink, $newWindow ? 1 : 0, $this->getCollectionID()]);
        }
    }

    /**
     * Add a new external link as a child of this page.
     *
     * @param string $cName
     * @param string $cLink
     * @param bool $newWindow
     *
     * @return int $newCID
     */
    public function addCollectionAliasExternal($cName, $cLink, $newWindow = 0)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $u = new User();

        $cLink = $app->make('helper/security')->sanitizeURL($cLink);
        $handle = $app->make('helper/text')->urlify($cLink);

        $cobj = parent::addCollection([
            'handle' => $handle,
            'name' => $cName,
        ]);
        $newCID = $cobj->getCollectionID();

        $db->insert('Pages', [
            'cID' => $newCID,
            'siteTreeID' => $this->getSiteTreeID() ?: $app->make('site')->getSite()->getSiteTreeID(),
            'cParentID' => $this->getCollectionID(),
            'uID' => $u->getUserID(),
            'cInheritPermissionsFrom' => 'PARENT',
            'cInheritPermissionsFromCID' => (int) $this->getPermissionsCollectionID(),
            'cPointerExternalLink' => $cLink,
            'cPointerExternalLinkNewWindow' => $newWindow ? 1 : 0,
            'cDisplayOrder' => $this->getNextSubPageDisplayOrder(),
        ]);
        PageStatistics::incrementParents($newCID);

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
        $app = Application::getFacadeApplication();

        $pe = new Event($this);
        $pe->setArgument('icon', '');
        $app->make('director')->dispatch('on_page_get_icon', $pe);
        $icon = $pe->getArgument('icon');
        if (!$icon && $this->isGeneratedCollection()) {
            $pkgHandle = $this->getPackageHandle();
            $cPath = $this->getCollectionPath();
            if ($pkgHandle) {
                if (is_dir(DIR_PACKAGES . '/' . $pkgHandle)) {
                    $dirp = DIR_PACKAGES;
                    $url = Application::getApplicationURL();
                } else {
                    $dirp = DIR_PACKAGES_CORE;
                    $url = ASSETS_URL;
                }
                $file = $dirp . '/' . $pkgHandle . '/' . DIRNAME_PAGES . $cPath . '/' . FILENAME_PAGE_ICON;
                if (file_exists($file)) {
                    $icon = $url . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_PAGES . $cPath . '/' . FILENAME_PAGE_ICON;
                }
            } elseif (file_exists(DIR_FILES_CONTENT . $cPath . '/' . FILENAME_PAGE_ICON)) {
                $icon = $app->getApplicationURL() . '/' . DIRNAME_PAGES . $cPath . '/' . FILENAME_PAGE_ICON;
            } elseif (file_exists(DIR_FILES_CONTENT_REQUIRED . $cPath . '/' . FILENAME_PAGE_ICON)) {
                $icon = ASSETS_URL . '/' . DIRNAME_PAGES . $cPath . '/' . FILENAME_PAGE_ICON;
            }
        }
        if (!$icon && $app->make('multilingual/detector')->isEnabled()) {
            $icon = Flag::getDashboardSitemapIconSRC($this);
        }

        return $icon;
    }

    /**
     * Remove an external link/alias.
     *
     * @return int cID for the original page if the page was an alias
     */
    public function removeThisAlias()
    {
        $cPointerExternalLink = $this->getCollectionPointerExternalLink();
        if ($cPointerExternalLink != '') {
            $this->delete();
        } else {
            $cIDRedir = $this->getCollectionPointerID();
            if ($cIDRedir > 0) {
                $app = Application::getFacadeApplication();
                $db = $app->make(Connection::class);

                $cID = $this->getCollectionPointerOriginalID();

                PageStatistics::decrementParents($cID);

                $db->executeQuery('delete from Pages where cID = ?', [$cID]);
                $db->executeQuery('delete from Collections where cID = ?', [$cID]);
                $db->executeQuery('delete from CollectionVersions where cID = ?', [$cID]);
                $db->executeQuery('delete from PagePaths where cID = ?', [$cID]);

                return $cIDRedir;
            }
        }
    }

    /**
     * Create an array containing data about child pages.
     *
     * @param array $pages the previously loaded data
     * @param array $pageRow The data of current parent page (it must contain cID and optionally cDisplayOrder)
     * @param int $cParentID The parent page ID
     * @param int $level The current depth level
     * @param bool $includeThisPage Should $pageRow itself be added to the resulting array?
     *
     * @return array Every array item contains the following keys: {
     *
     *    @var int $cID
     *    @var int $cDisplayOrder
     *    @var int $cParentID
     *    @var int $level
     *    @var int $total
     * }
     */
    public function populateRecursivePages($pages, $pageRow, $cParentID, $level, $includeThisPage = true)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $children = $db->fetchAll('select cID, cDisplayOrder from Pages where cParentID = ? order by cDisplayOrder asc', [$pageRow['cID']]);
        if ($includeThisPage) {
            $pages[] = [
                'cID' => $pageRow['cID'],
                'cDisplayOrder' => isset($pageRow['cDisplayOrder']) ? $pageRow['cDisplayOrder'] : null,
                'cParentID' => $cParentID,
                'level' => $level,
                'total' => count($children),
            ];
        }
        ++$level;
        $cParentID = $pageRow['cID'];
        foreach ($children as $pageRow) {
            $pages = $this->populateRecursivePages($pages, $pageRow, $cParentID, $level);
        }

        return $pages;
    }

    /**
     * Add this page and its subpages to the Delete Page queue.
     */
    public function queueForDeletion()
    {
        $app = Application::getFacadeApplication();
        if ($this->getCollectionPath() == $app->make('config')->get('concrete.paths.trash')) {
            // we're in the trash. we can't delete the trash. we're skipping over the trash node.
            $includeThisPage = false;
        } else {
            $includeThisPage = true;
        }
        $pages = $this->populateRecursivePages([], ['cID' => $this->getCollectionID()], $this->getCollectionParentID(), 0, $includeThisPage);
        // now, since this is deletion, we want to order the pages by level, which
        // should get us no funny business if the queue dies.
        usort($pages, ['Page', 'queueForDeletionSort']);
        $queue = $app->make(QueueService::class)->get('delete_page');
        foreach ($pages as $page) {
            $queue->send(serialize($page));
        }
    }

    /**
     * Add this page and its subpages to the Delete Page Requests queue (or to a custom queue).
     *
     * @param \ZendQueue\Queue|null $queue the custom queue to add the pages to
     * @param bool $includeThisPage Include this page itself in the page to be added to the queue?
     */
    public function queueForDeletionRequest($queue = null, $includeThisPage = true)
    {
        $pages = $this->populateRecursivePages([], ['cID' => $this->getCollectionID()], $this->getCollectionParentID(), 0, $includeThisPage);
        // now, since this is deletion, we want to order the pages by level, which
        // should get us no funny business if the queue dies.
        usort($pages, ['Page', 'queueForDeletionSort']);
        if (!$queue) {
            $app = Application::getFacadeApplication();
            $queue = $app->make(QueueService::class)->get('delete_page_request');
        }
        foreach ($pages as $page) {
            $queue->send(serialize($page));
        }
    }

    /**
     * Add this page and its subpages to the Copy Page queue.
     *
     * @param \Concrete\Core\Page\Page $destination the destination parent page where the pages will be copied to
     * @param bool $includeParent Include this page itself in the page to be added to the queue?
     */
    public function queueForDuplication($destination, $includeParent = true)
    {
        $app = Application::getFacadeApplication();
        $pages = $this->populateRecursivePages([], ['cID' => $this->getCollectionID()], $this->getCollectionParentID(), 0, $includeParent);
        // we want to order the pages by level, which should get us no funny
        // business if the queue dies.
        usort($pages, ['Page', 'queueForDuplicationSort']);
        $queue = $app->make(QueueService::class)->get('copy_page');
        foreach ($pages as $page) {
            $page['destination'] = $destination->getCollectionID();
            $queue->send(serialize($page));
        }
    }

    /**
     * Get the uID for a page that is checked out (if any).
     *
     * @return int|null
     */
    public function getCollectionCheckedOutUserID()
    {
        return $this->cCheckedOutUID;
    }

    /**
     * Get the path of this page.
     *
     * @return string
     */
    public function getCollectionPath()
    {
        return isset($this->cPath) ? $this->cPath : null;
    }

    /**
     * Returns the PagePath object for the current page.
     *
     * @return \Concrete\Core\Entity\Page\PagePath|null
     */
    public function getCollectionPathObject()
    {
        $app = Application::getFacadeApplication();
        $em = $app->make(EntityManagerInterface::class);
        $cID = $this->getCollectionPointerOriginalID() ?: $this->getCollectionID();
        $path = $em->getRepository(PagePath::class)->findOneBy(['cID' => $cID, 'ppIsCanonical' => true]);

        return $path;
    }

    /**
     * Add a non-canonical page path to the current page.
     *
     * @param string $cPath
     * @param bool $commit Should the new PagePath instance be persisted?
     *
     * @return \Concrete\Core\Entity\Page\PagePath
     */
    public function addAdditionalPagePath($cPath, $commit = true)
    {
        $app = Application::getFacadeApplication();
        $em = $app->make(EntityManagerInterface::class);
        $path = new PagePath();
        $path->setPagePath('/' . trim($cPath, '/'));
        $path->setPageObject($this);
        $em->persist($path);
        if ($commit) {
            $em->flush();
        }

        return $path;
    }

    /**
     * Set the canonical page path for a page.
     *
     * @param string $cPath
     * @param bool $isAutoGenerated is the page path generated from URL slugs?
     */
    public function setCanonicalPagePath($cPath, $isAutoGenerated = false)
    {
        $app = Application::getFacadeApplication();
        $em = $app->make(EntityManagerInterface::class);
        $path = $this->getCollectionPathObject();
        if ($path !== null) {
            $path->setPagePath($cPath);
        } else {
            $path = new PagePath();
            $path->setPagePath($cPath);
            $path->setPageObject($this);
        }
        $path->setPagePathIsAutoGenerated($isAutoGenerated);
        $path->setPagePathIsCanonical(true);
        $em->persist($path);
        $em->flush();
    }

    /**
     * Get all the page paths of this page.
     *
     * @return \Concrete\Core\Entity\Page\PagePath[]
     */
    public function getPagePaths()
    {
        $app = Application::getFacadeApplication();
        $em = $app->make(EntityManagerInterface::class);

        return $em->getRepository(PagePath::class)->findBy(['cID' => $this->getCollectionID()], ['ppID' => 'asc']);
    }

    /**
     * Get all the non-canonical page paths of this page.
     *
     * @return \Concrete\Core\Entity\Page\PagePath[]
     */
    public function getAdditionalPagePaths()
    {
        $app = Application::getFacadeApplication();
        $em = $app->make(EntityManagerInterface::class);

        return $em->getRepository(PagePath::class)->findBy(['cID' => $this->getCollectionID(), 'ppIsCanonical' => false]);
    }

    /**
     * Clear all page paths for a page.
     */
    public function clearPagePaths()
    {
        $app = Application::getFacadeApplication();
        $em = $app->make(EntityManagerInterface::class);
        $paths = $this->getPagePaths();
        foreach ($paths as $path) {
            $em->remove($path);
        }
        $em->flush();
    }

    /**
     * Returns full url for the current page.
     *
     * @param bool $appendBaseURL UNUSED
     *
     * @return string
     */
    public function getCollectionLink($appendBaseURL = false)
    {
        $app = Application::getFacadeApplication();

        return (string) $app->make(ResolverManagerInterface::class)->resolve([$this]);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Site\Tree\TreeInterface::getSiteTreeID()
     */
    public function getSiteTreeID()
    {
        return $this->siteTreeID;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Site\SiteAggregateInterface::getSite()
     */
    public function getSite()
    {
        $tree = $this->getSiteTreeObject();
        if ($tree instanceof SiteTree) {
            return $tree->getSite();
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Site\Tree\TreeInterface::getSiteTreeObject()
     */
    public function getSiteTreeObject()
    {
        if (!isset($this->siteTree) && $this->getSiteTreeID()) {
            $app = Application::getFacadeApplication();
            $em = $app->make(EntityManagerInterface::class);
            $this->siteTree = $em->find(Tree::class, $this->getSiteTreeID());
        }

        return $this->siteTree;
    }

    /**
     * Get the uID of the page author (if any).
     *
     * @return int|null
     */
    public function getCollectionUserID()
    {
        return isset($this->uID) ? $this->uID : null;
    }

    /**
     * Get the page handle.
     *
     * @return string
     */
    public function getCollectionHandle()
    {
        return $this->getVersionObject()->cvHandle;
    }

    /**
     * Get the display name of the page type (if available).
     *
     * @return string|null
     */
    public function getPageTypeName()
    {
        if (!isset($this->pageType)) {
            $this->pageType = $this->getPageTypeObject();
        }
        if ($this->pageType) {
            return $this->pageType->getPageTypeDisplayName();
        }
    }

    /**
     * Get the Collection Type ID.
     *
     * @return int|null
     */
    public function getPageTypeID()
    {
        return isset($this->ptID) ? $this->ptID : null;
    }

    /**
     * Get the page type object.
     *
     * @return \Concrete\Core\Page\Type\Type|null
     */
    public function getPageTypeObject()
    {
        $ptID = $this->getPageTypeID();

        return $ptID ? PageType::getByID($ptID) : null;
    }

    /**
     * Get the Page Template ID.
     *
     * @return int
     */
    public function getPageTemplateID()
    {
        return $this->getVersionObject()->pTemplateID;
    }

    /**
     * Get the Page Template Object.
     *
     * @return \Concrete\Core\Entity\Page\Template|null
     */
    public function getPageTemplateObject()
    {
        $ptID = $this->getPageTemplateID();

        return $ptID ? PageTemplate::getByID($ptID) : null;
    }

    /**
     * Get the handle of the Page Template.
     *
     * @return string|false
     */
    public function getPageTemplateHandle()
    {
        $pt = $this->getPageTemplateObject();

        return $pt ? $pt->getPageTemplateHandle() : false;
    }

    /**
     * Get the handle of the Page Type.
     *
     * @return string|false
     */
    public function getPageTypeHandle()
    {
        if (!isset($this->ptHandle)) {
            $this->ptHandle = false;
            $ptID = $this->getPageTypeID();
            if ($ptID) {
                $pt = PageType::getByID($ptID);
                if ($pt) {
                    $this->ptHandle = $pt->getPageTypeHandle();
                }
            }
        }

        return $this->ptHandle;
    }

    /**
     * Get the theme ID for the collection.
     *
     * @return int|null
     */
    public function getCollectionThemeID()
    {
        $theme = $this->getCollectionThemeObject();

        return $theme ? $theme->getThemeID() : null;
    }

    /**
     * Check if a block is an alias from a page default.
     *
     * @param \Concrete\Core\Block\Block $b
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
        if ($this->blocksAliasedFromMasterCollection === null) {
            $app = Application::getFacadeApplication();
            $db = $app->make(Connection::class);
            $rs = $db->executeQuery(<<<EOT
SELECT cvb.bID
FROM CollectionVersionBlocks AS cvb
INNER JOIN CollectionVersionBlocks AS cvb2 ON cvb.bID = cvb2.bID AND cvb2.cID = ?
WHERE cvb.cID = ? AND cvb.isOriginal = 0 AND cvb.cvID = ?
GROUP BY cvb.bID
EOT
                ,
                [$this->getMasterCollectionID(), $this->getCollectionID(), $this->getVersionObject()->getVersionID()]
            );
            $this->blocksAliasedFromMasterCollection = [];
            while (($bID = $rs->fetchColumn()) !== false) {
                $this->blocksAliasedFromMasterCollection[] = $bID;
            }
        }

        return in_array($b->getBlockID(), $this->blocksAliasedFromMasterCollection);
    }

    /**
     * Get the collection's theme object.
     *
     * @return \Concrete\Core\Page\Theme\Theme
     */
    public function getCollectionThemeObject()
    {
        if (!isset($this->themeObject)) {
            $app = Application::getFacadeApplication();
            $tmpTheme = $app->make(RouterInterface::class)->getThemeByRoute($this->getCollectionPath());
            if ($tmpTheme) {
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
            } else {
                $themeID = (int) $this->getVersionObject()->pThemeID;
                if ($themeID < 1) {
                    $this->themeObject = PageTheme::getSiteTheme();
                } else {
                    $this->themeObject = PageTheme::getByID($themeID);
                }
            }
        }
        if (!$this->themeObject) {
            $this->themeObject = PageTheme::getSiteTheme();
        }

        return $this->themeObject;
    }

    /**
     * Get the page name.
     *
     * @return string
     */
    public function getCollectionName()
    {
        $vo = $this->getVersionObject();
        if ($vo) {
            return $vo->cvName;
        }

        return isset($this->cvName) ? $this->cvName : null;
    }

    /**
     * Gett the collection ID for the aliased page (returns 0 unless used on an actual alias).
     *
     * @return int
     */
    public function getCollectionPointerID()
    {
        return isset($this->cPointerID) ? (int) $this->cPointerID : 0;
    }

    /**
     * Get the link for the aliased page.
     *
     * @return string|null
     */
    public function getCollectionPointerExternalLink()
    {
        return $this->cPointerExternalLink;
    }

    /**
     * Should the alias link to be opened in a new window?
     *
     * @return bool|int|null
     */
    public function openCollectionPointerExternalLinkInNewWindow()
    {
        return $this->cPointerExternalLinkNewWindow;
    }

    /**
     * Is this page an alias page or an external link?
     *
     * @return bool
     */
    public function isAliasPageOrExternalLink()
    {
        return $this->isExternalLink() || $this->isAliasPage();
    }

    /**
     * Is this page an alias page?
     *
     * @return bool
     */
    public function isAliasPage()
    {
        return $this->getCollectionPointerID() > 0;
    }

    /**
     * Is this page an external link?
     *
     * @return bool
     */
    public function isExternalLink()
    {
        return (string) $this->cPointerExternalLink !== '';
    }

    /**
     * Get the original cID of a page (if it's a page alias).
     *
     * @return int|null
     */
    public function getCollectionPointerOriginalID()
    {
        return $this->cPointerOriginalID;
    }

    /**
     * Get the file name of a page (single pages).
     *
     * @return string|null
     */
    public function getCollectionFilename()
    {
        return $this->cFilename;
    }

    /**
     * Get the date/time when the current version was made public (or a falsy value if the current version doesn't have public date).
     *
     * @return string
     *
     * @example 2009-01-01 00:00:00
     */
    public function getCollectionDatePublic()
    {
        return $this->getVersionObject()->cvDatePublic;
    }

    /**
     * Get the date/time when the current version was made public (or NULL value if the current version doesn't have public date).
     *
     * @return \DateTime|null
     */
    public function getCollectionDatePublicObject()
    {
        $cvDatePublic = $this->getCollectionDatePublic();

        return $cvDatePublic ? new DateTime($cvDatePublic) : null;
    }

    /**
     * Get the description of a page.
     *
     * @return string
     */
    public function getCollectionDescription()
    {
        return (string) $this->getVersionObject()->cvDescription;
    }

    /**
     * Gets the cID of the parent page.
     *
     * @return int|null
     */
    public function getCollectionParentID()
    {
        if (isset($this->cParentID)) {
            return $this->cParentID;
        }
    }

    /**
     * Get an array containint this cParentID and aliased parentIDs.
     *
     * @return int[]
     */
    public function getCollectionParentIDs()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $cIDs = [$this->getCollectionParentID()];
        $rs = $db->executeQuery('SELECT cParentID FROM Pages WHERE cPointerID = ?', [$this->getCollectionID()]);
        while (($cParentID = $rs->fetchColumn()) !== false) {
            $cIDs[] = $cParentID;
        }

        return $cIDs;
    }

    /**
     * Is this page a page default?
     *
     * @return bool|int|null
     */
    public function isMasterCollection()
    {
        return $this->isMasterCollection;
    }

    /**
     * Are template permissions overriden?
     *
     * @return bool|int|null
     */
    public function overrideTemplatePermissions()
    {
        return isset($this->cOverrideTemplatePermissions) ? $this->cOverrideTemplatePermissions : null;
    }

    /**
     * Get the position of the page in the sitemap, relative to its parent page.
     *
     * @return int|null
     */
    public function getCollectionDisplayOrder()
    {
        return isset($this->cDisplayOrder) ? $this->cDisplayOrder : null;
    }

    /**
     * Set the theme of this page.
     *
     * @param \Concrete\Core\Page\Theme\Theme $pl
     */
    public function setTheme($pl)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->executeQuery('update CollectionVersions set pThemeID = ? where cID = ? and cvID = ?', [$pl->getThemeID(), $this->getCollectionID(), $this->getVersionObject()->getVersionID()]);
    }

    /**
     * Set the theme for a page using the page object.
     *
     * @param \Concrete\Core\Page\Type\Type|null $type
     */
    public function setPageType(PageType $type = null)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $ptID = $type ? $type->getPageTypeID() : 0;
        $db->executeQuery('update Pages set ptID = ? where cID = ?', [$ptID, $this->getCollectionID()]);
        $this->ptID = $ptID;
    }

    /**
     * Set the permissions of sub-collections added beneath this permissions to inherit from the template.
     */
    public function setPermissionsInheritanceToTemplate()
    {
        $cID = $this->getCollectionID();
        if ($cID) {
            $app = Application::getFacadeApplication();
            $db = $app->make(Connection::class);
            $db->executeQuery('update Pages set cOverrideTemplatePermissions = 0 where cID = ?', [$cID]);
        }
    }

    /**
     * Set the permissions of sub-collections added beneath this permissions to inherit from the parent.
     */
    public function setPermissionsInheritanceToOverride()
    {
        $cID = $this->getCollectionID();
        if ($cID) {
            $app = Application::getFacadeApplication();
            $db = $app->make(Connection::class);
            $db->executeQuery('update Pages set cOverrideTemplatePermissions = 1 where cID = ?', [$cID]);
        }
    }

    /**
     * Get the ID of the page from which this page inherits permissions from.
     *
     * @return int|null
     */
    public function getPermissionsCollectionID()
    {
        return $this->cInheritPermissionsFromCID;
    }

    /**
     * Where permissions should be inherited from? 'PARENT' or 'TEMPLATE' or 'OVERRIDE'.
     *
     * @return string|null
     */
    public function getCollectionInheritance()
    {
        return isset($this->cInheritPermissionsFrom) ? $this->cInheritPermissionsFrom : null;
    }

    /**
     * Get the ID of the page from which the parent page page inherits permissions from.
     *
     * @return int|null
     */
    public function getParentPermissionsCollectionID()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $cParentID = $this->getCollectionParentID() ?: $this->getSiteHomePageID();
        $ppID = $db->fetchColumn('select cInheritPermissionsFromCID from Pages where cID = ?', [$cParentID]);

        return $ppID ?: null;
    }

    /**
     * Get the page from which this page inherits permissions from.
     *
     * @return \Concrete\Core\Page\Page
     */
    public function getPermissionsCollectionObject()
    {
        return self::getByID($this->getPermissionsCollectionID(), 'RECENT');
    }

    /**
     * Get the master page of this page, given its page template and page type.
     *
     * @return int returns 0 if not found
     */
    public function getMasterCollectionID()
    {
        $type = $this->getPageTypeObject();
        if ($type === null) {
            return 0;
        }
        $template = $this->getPageTemplateObject();
        if ($template === null) {
            return 0;
        }
        $c = $type->getPageTypePageTemplateDefaultPageObject($template);

        return (int) $c->getCollectionID();
    }

    /**
     * Get the ID of the original collection.
     *
     * @return int|null
     */
    public function getOriginalCollectionID()
    {
        // this is a bit weird...basically, when editing a master collection, we store the
        // master collection ID in session, along with the collection ID we were looking at before
        // moving to the master collection. This allows us to get back to that original collection
        $result = null;
        $app = Application::getFacadeApplication();
        $sessionValidator = $app->make(SessionValidatorInterface::class);
        if ($sessionValidator->hasActiveSession()) {
            $session = $app->make('session');
            $result = $session->get('ocID');
        }

        return $result;
    }

    /**
     * Get the number of child pages.
     *
     * @return int|null
     */
    public function getNumChildren()
    {
        return isset($this->cChildren) ? $this->cChildren : null;
    }

    /**
     * Get the number of child pages (direct children only).
     *
     * @return int
     */
    public function getNumChildrenDirect()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $v = [$this->getCollectionID()];

        return (int) $db->fetchColumn('select count(cID) as total from Pages where cParentID = ?', $v);
    }

    /**
     * Get the first child of the current page, or null if there is no child.
     *
     * @param string $sortColumn the ORDER BY clause
     *
     * @return \Concrete\Core\Page\Page|null
     */
    public function getFirstChild($sortColumn = 'cDisplayOrder asc')
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $cID = $db->fetchColumn(
            "select Pages.cID from Pages inner join CollectionVersions on Pages.cID = CollectionVersions.cID where cvIsApproved = 1 and cParentID = ? order by {$sortColumn}",
            [$this->getCollectionID()]
        );
        if ($cID && $cID != $this->getSiteHomePageID()) {
            return self::getByID($cID, 'ACTIVE');
        }

        return null;
    }

    /**
     * Get the list of child page IDs, sorted by their display order.
     *
     * @param bool $oneLevelOnly set to true to return only the direct children, false for all the child pages
     *
     * @return int[]
     */
    public function getCollectionChildrenArray($oneLevelOnly = 0)
    {
        $this->childrenCIDArray = [];
        $this->_getNumChildren($this->getCollectionID(), $oneLevelOnly);

        return $this->childrenCIDArray;
    }

    /**
     * Get the immediate children of the this page.
     *
     * @return \Concrete\Core\Page\Page[]
     */
    public function getCollectionChildren()
    {
        $children = [];
        foreach ($this->getCollectionChildrenArray(true) as $cID) {
            $children[] = self::getByID($cID);
        }

        return $children;
    }

    /**
     * Check if a collection is this page itself or one of its sub-pages.
     *
     * @param \Concrete\Core\Page\Collection\Collection|int $otherPage
     * @param mixed $otherCollection
     *
     * @return bool
     */
    public function isParentOf($otherCollection)
    {
        $otherCollectionID = (int) (is_object($otherCollection) ? $otherCollection->getCollectionID() : $otherCollection);
        if ($otherCollectionID === 0) {
            return false;
        }
        if ($otherCollectionID == $this->getCollectionID()) {
            return false;
        }
        $childIDs = $this->getCollectionChildrenArray();

        return !in_array($otherCollectionID, $childIDs);
    }

    /**
     * Update the collection name.
     *
     * @param string $name
     */
    public function updateCollectionName($name)
    {
        $vo = $this->getVersionObject();
        if (!$vo) {
            return;
        }
        $app = Application::getFacadeApplication();
        $config = $app->make('config');
        $db = $app->make(Connection::class);

        $cHandle = $app->make('helper/text')->urlify($name);
        $cHandle = str_replace('-', $config->get('concrete.seo.page_path_separator'), $cHandle);

        $this->markModified();
        $db->executeQuery('update CollectionVersions set cvName = ?, cvHandle = ? where cID = ? and cvID = ?', [$name, $cHandle, $this->getCollectionID(), $vo->getVersionID()]);
        $vo->cvName = $name;

        $cache = PageCache::getLibrary();
        $cache->purge($this);

        $pe = new Event($this);
        $app->make('director')->dispatch('on_page_update', $pe);
    }

    /**
     * Does this page have theme customizations?
     *
     * @return bool
     */
    public function hasPageThemeCustomizations()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);

        return (bool) $db->fetchColumn(
            'select count(cID) from CollectionVersionThemeCustomStyles where cID = ? and cvID = ?',
            [$this->getCollectionID(), $this->getVersionID()]
        );
    }

    /**
     * Clears the custom theme styles for this page.
     */
    public function resetCustomThemeStyles()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->executeQuery('delete from CollectionVersionThemeCustomStyles where cID = ? and cvID = ?', [$this->getCollectionID(), $this->getVersionID()]);
        $this->writePageThemeCustomizations();
    }

    /**
     * Set the custom style for this page for a specific theme.
     *
     * @param \Concrete\Core\Page\Theme\Theme $theme
     * @param \Concrete\Core\StyleCustomizer\Style\ValueList $valueList
     * @param \Concrete\Core\StyleCustomizer\Preset|null|false $selectedPreset
     * @param \Concrete\Core\Entity\StyleCustomizer\CustomCssRecord $customCssRecord
     *
     * @return \Concrete\Core\Page\CustomStyle
     */
    public function setCustomStyleObject(PageTheme $theme, StyleValueList $valueList, $selectedPreset = false, CustomCssRecord $customCssRecord = null)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->delete('CollectionVersionThemeCustomStyles', ['cID' => $this->getCollectionID(), 'cvID' => $this->getVersionID()]);
        $preset = $selectedPreset ? $selectedPreset->getPresetHandle() : false;
        $sccRecordID = $customCssRecord ? $customCssRecord->getRecordID() : 0;
        $db->insert(
            'CollectionVersionThemeCustomStyles',
            [
                'cID' => $this->getCollectionID(),
                'cvID' => $this->getVersionID(),
                'pThemeID' => $theme->getThemeID(),
                'sccRecordID' => $sccRecordID,
                'preset' => $preset,
                'scvlID' => $valueList->getValueListID(),
            ]
        );

        $scc = new CustomStyle();
        $scc->setThemeID($theme->getThemeID());
        $scc->setValueListID($valueList->getValueListID());
        $scc->setPresetHandle($preset);
        $scc->setCustomCssRecordID($sccRecordID);

        return $scc;
    }

    /**
     * Get the CSS class to be used to wrap the whole page contents.
     *
     * @return string
     */
    public function getPageWrapperClass()
    {
        $classes = ['ccm-page'];
        $type = $this->getPageTypeObject();
        if ($type) {
            $classes[] = 'page-type-' . str_replace('_', '-', $type->getPageTypeHandle());
        }
        $view = $this->getPageController()->getViewObject();
        $template = $view ? $view->getPageTemplate() : $this->getPageTemplateObject();
        if ($template) {
            $classes[] = 'page-template-' . str_replace('_', '-', $template->getPageTemplateHandle());
        }

        return implode(' ', $classes);
    }

    /**
     * Write the page theme customization CSS files to the cache directory.
     */
    public function writePageThemeCustomizations()
    {
        $theme = $this->getCollectionThemeObject();
        if ($theme && $theme->isThemeCustomizable()) {
            $app = Application::getFacadeApplication();
            $config = $app->make('config');
            $style = $this->getCustomStyleObject();
            $styleValueList = $style ? $style->getValueList() : null;
            $theme->setStylesheetCachePath($config->get('concrete.cache.directory') . '/pages/' . $this->getCollectionID());
            $theme->setStylesheetCacheRelativePath(REL_DIR_FILES_CACHE . '/pages/' . $this->getCollectionID());
            $sheets = $theme->getThemeCustomizableStyleSheets();
            foreach ($sheets as $sheet) {
                if ($styleValueList !== null) {
                    $sheet->setValueList($styleValueList);
                    $sheet->output();
                } else {
                    $sheet->clearOutputFile();
                }
            }
        }
    }

    /**
     * Update the data of this page.
     *
     * @param array $data Allowed keys {
     *
     *     @var string $cHandle
     *     @var string $cName
     *     @var string $cDescription
     *     @var string $cDatePublic
     *     @var int $ptID
     *     @var int $pTemplateID
     *     @var int $uID
     *     @var string $$cFilename
     *     @var int $cCacheFullPageContent -1: use the default settings; 0: no; 1: yes
     *     @var int $cCacheFullPageContentLifetimeCustom
     *     @var string $cCacheFullPageContentOverrideLifetime
     * }
     */
    public function update($data)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $config = $app->make('config');
        $txt = $app->make('helper/text');

        $vo = $this->getVersionObject();
        $cvID = $vo->getVersionID();
        $pkgID = $this->getPackageID();
        $existingPageTemplateID = $this->getPageTemplateID();
        $isHomePage = $this->isHomePage();

        $cFilename = isset($data['cFilename']) ? $data['cFilename'] : $this->getCollectionFilename();
        $cName = isset($data['cName']) ? $data['cName'] : $this->getCollectionName();
        $cCacheFullPageContent = isset($data['cCacheFullPageContent']) ? $data['cCacheFullPageContent'] : $this->getCollectionFullPageCaching();
        $cCacheFullPageContentLifetimeCustom = isset($data['cCacheFullPageContentLifetimeCustom']) ? $data['cCacheFullPageContentLifetimeCustom'] : $this->getCollectionFullPageCachingLifetimeCustomValue();
        $cCacheFullPageContentOverrideLifetime = isset($data['cCacheFullPageContentOverrideLifetime']) ? $data['cCacheFullPageContentOverrideLifetime'] : $this->getCollectionFullPageCachingLifetime();
        $cDescription = isset($data['cDescription']) ? $data['cDescription'] : $this->getCollectionDescription();
        $uID = isset($data['uID']) ? $data['uID'] : $this->getCollectionUserID();
        $pTemplateID = isset($data['pTemplateID']) ? $data['pTemplateID'] : $existingPageTemplateID;
        $ptID = isset($data['ptID']) ? $data['ptID'] : $this->getPageTypeID();
        $cDatePublic = isset($data['cDatePublic']) ? $data['cDatePublic'] : $this->getCollectionDatePublic();
        if (!$cDatePublic) {
            $cDatePublic = $app->make('date')->getOverridableNow();
        }
        if (!isset($data['cHandle']) && $this->getCollectionHandle()) {
            // No passed cHandle, and there is an existing handle: use it.
            $cHandle = $this->getCollectionHandle();
        } elseif (!$isHomePage && !$app->make('helper/validation/strings')->notempty($data['cHandle'])) {
            // no passed cHandle, and no existing handle: make the handle out of the title
            $cHandle = $txt->urlify($cName);
            $cHandle = str_replace('-', $config->get('concrete.seo.page_path_separator'), $cHandle);
        } else {
            // passed cHandle, no existing handle
            $cHandle = isset($data['cHandle']) ? $txt->slugSafeString($data['cHandle']) : ''; // we DON'T run urlify
            $cHandle = str_replace('-', $config->get('concrete.seo.page_path_separator'), $cHandle);
        }
        $cName = $txt->sanitize($cName);

        $this->markModified();

        if ($this->isGeneratedCollection()) {
            // we only update a subset
            $db->update(
                'CollectionVersions',
                [
                    'cvName' => $cName,
                    'cvHandle' => $cHandle,
                    'cvDescription' => $cDescription,
                    'cvDatePublic' => $cDatePublic,
                ],
                [
                    'cID' => $this->getCollectionID(),
                    'cvID' => $cvID,
                ]
            );
        } else {
            if ($existingPageTemplateID && $pTemplateID && ($existingPageTemplateID != $pTemplateID) && $this->getPageTypeID() > 0 && $this->isPageDraft()) {
                // we are changing a page template in this operation.
                // when that happens, we need to get the new defaults for this page, remove the other blocks
                // on this page that were set by the old defaults master page
                $pt = $this->getPageTypeObject();
                if ($pt) {
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
                            $db->insert('CollectionVersionBlocks', [
                                'cID' => $this->getCollectionID(),
                                'cvID' => $cvID,
                                'bID' => $b->getBlockID(),
                                'arHandle' => $b->getAreaHandle(),
                                'cbDisplayOrder' => $newBlockDisplayOrder,
                                'isOriginal' => (int) $b->isAlias(),
                                'cbOverrideAreaPermissions' => $b->overrideAreaPermissions(),
                                'cbIncludeAll' => $b->disableBlockVersioning(),
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

            $db->update(
                'CollectionVersions',
                [
                    'cvName' => $cName,
                    'cvHandle' => $cHandle,
                    'pTemplateID' => $pTemplateID,
                    'cvDescription' => $cDescription,
                    'cvDatePublic' => $cDatePublic,
                ],
                [
                    'cID' => $this->getCollectionID(),
                    'cvID' => $cvID,
                ]
            );
        }

        // load new version object
        $this->loadVersionObject($cvID);

        $db->update(
            'Pages',
            [
                'ptID' => $ptID,
                'uID' => $uID,
                'pkgID' => $pkgID,
                'cFilename' => $cFilename,
                'cCacheFullPageContent' => $cCacheFullPageContent,
                'cCacheFullPageContentLifetimeCustom' => $cCacheFullPageContentLifetimeCustom,
                'cCacheFullPageContentOverrideLifetime' => $cCacheFullPageContentOverrideLifetime,
            ],
            ['cID' => $this->getCollectionID()]
        );

        $cache = PageCache::getLibrary();
        $cache->purge($this);

        $this->refreshCache();

        $pe = new Event($this);
        $app->make('director')->dispatch('on_page_update', $pe);
    }

    /**
     * Clear all the page permissions.
     */
    public function clearPagePermissions()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->executeQuery('delete from PagePermissionAssignments where cID = ?', [$this->getCollectionID()]);
    }

    /**
     * Set this page permissions to be inherited from its parent page.
     */
    public function inheritPermissionsFromParent()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $cpID = (int) $this->getParentPermissionsCollectionID();
        $this->updatePermissionsCollectionID($this->getCollectionID(), $cpID);
        $db->executeQuery(
            'update Pages set cInheritPermissionsFrom = ?, cInheritPermissionsFromCID = ? where cID = ?',
            ['PARENT', $cpID, $this->getCollectionID()]
        );
        $this->cInheritPermissionsFrom = 'PARENT';
        $this->cInheritPermissionsFromCID = $cpID;
        $this->clearPagePermissions();
        $this->rescanAreaPermissions();
    }

    /**
     * Set this page permissions to be inherited from its parent type defaults.
     */
    public function inheritPermissionsFromDefaults()
    {
        $type = $this->getPageTypeObject();
        if ($type) {
            $master = $type->getPageTypePageTemplateDefaultPageObject();
            if ($master && !$master->isError()) {
                $app = Application::getFacadeApplication();
                $db = $app->make(Connection::class);
                $cpID = $master->getCollectionID();
                $this->updatePermissionsCollectionID($this->getCollectionID(), $cpID);
                $db->executeQuery(
                    'update Pages set cInheritPermissionsFrom = ?, cInheritPermissionsFromCID = ? where cID = ?',
                    ['TEMPLATE', (int) $cpID, $this->getCollectionID()]
                );
                $this->cInheritPermissionsFrom = 'TEMPLATE';
                $this->cInheritPermissionsFromCID = $cpID;
                $this->clearPagePermissions();
                $this->rescanAreaPermissions();
            }
        }
    }

    /**
     * Set this page permissions to be manually specified.
     */
    public function setPermissionsToManualOverride()
    {
        if ($this->getCollectionInheritance() !== 'OVERRIDE') {
            $app = Application::getFacadeApplication();
            $db = $app->make(Connection::class);
            $this->acquirePagePermissions($this->getPermissionsCollectionID());
            $this->acquireAreaPermissions($this->getPermissionsCollectionID());

            $cpID = (int) $this->getCollectionID();
            $this->updatePermissionsCollectionID($this->getCollectionID(), $cpID);
            $db->executeQuery(
                'update Pages set cInheritPermissionsFrom = ?, cInheritPermissionsFromCID = ? where cID = ?',
                ['OVERRIDE', (int) $cpID, $this->getCollectionID()]
            );
            $this->cInheritPermissionsFrom = 'OVERRIDE';
            $this->cInheritPermissionsFromCID = $cpID;
            $this->rescanAreaPermissions();
        }
    }

    /**
     * Rescan the page areas ensuring that they are inheriting permissions properly.
     */
    public function rescanAreaPermissions()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $rs = $db->executeQuery('select arHandle, arIsGlobal from Areas where cID = ?', [$this->getCollectionID()]);
        while (($row = $rs->fetch(PDO::FETCH_ASSOC)) !== false) {
            $a = Area::getOrCreate($this, $row['arHandle'], $row['arIsGlobal']);
            $a->rescanAreaPermissionsChain();
        }
    }

    /**
     * Are template permissions overriden?
     *
     * @param bool|int $cOverrideTemplatePermissions
     */
    public function setOverrideTemplatePermissions($cOverrideTemplatePermissions)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->executeQuery(
            'update Pages set cOverrideTemplatePermissions = ? where cID = ?',
            [$cOverrideTemplatePermissions, $this->getCollectionID()]
        );
        $this->cOverrideTemplatePermissions = $cOverrideTemplatePermissions;
    }

    /**
     * Set the child pages of a list of parent pages to inherit permissions from the specified page (proided that thay previouly had the same inheritance page as this page).
     *
     * @param int|string $cParentIDString A comma-separeted list of parent page IDs
     * @param int $newInheritPermissionsFromCID the new page the child pages should inherit permissions from
     */
    public function updatePermissionsCollectionID($cParentIDString, $newInheritPermissionsFromCID)
    {
        // now we iterate through
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $oldInheritPermissionsFromCID = $this->getPermissionsCollectionID();
        $r = $db->executeQuery("select cID from Pages where cParentID in ({$cParentIDString}) and cInheritPermissionsFromCID = ?", [$oldInheritPermissionsFromCID]);
        $childIDs = [];
        while (($cID = $r->fetchColumn()) !== false) {
            $childIDs[] = $cID;
        }
        if (count($childIDs) > 0) {
            $childIDs = implode(',', $childIDs);
            $db->executeQuery("update Pages set cInheritPermissionsFromCID = ? where cID in ({$childIDs})", [$newInheritPermissionsFromCID]);
            $this->updatePermissionsCollectionID($childIDs, $newInheritPermissionsFromCID);
        }
    }

    /**
     * Acquire the area permissions, copying them from the inherited ones.
     *
     * @param int $permissionsCollectionID the ID of the collection from which the page previously inherited permissions from
     */
    public function acquireAreaPermissions($permissionsCollectionID)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->executeQuery('delete from AreaPermissionAssignments where cID = ?', [$this->getCollectionID()]);

        // we need to copy area permissions from that page as well
        $copyFields = 'arHandle, paID, pkID';
        $db->executeQuery(
            "insert into AreaPermissionAssignments (cID, {$copyFields}) select ?, {$copyFields} from AreaPermissionAssignments where cID = ?",
            [$this->getCollectionID(), $permissionsCollectionID]
        );

        // any areas that were overriding permissions on the current page need to be overriding permissions on the NEW page as well.
        $copyFields = 'arHandle, arOverrideCollectionPermissions, arInheritPermissionsFromAreaOnCID, arIsGlobal';
        $db->executeQuery(
            "insert into Areas (cID, {$copyFields}) select ?, {$copyFields} from Areas where cID = ? and arOverrideCollectionPermissions != 0",
            [$this->getCollectionID(), $permissionsCollectionID]
        );
    }

    /**
     * Acquire the page permissions, copying them from the inherited ones.
     *
     * @param int $permissionsCollectionID the ID of the collection from which the page previously inherited permissions from
     */
    public function acquirePagePermissions($permissionsCollectionID)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->executeQuery('delete from PagePermissionAssignments where cID = ?', [$this->getCollectionID()]);

        $copyFields = 'paID, pkID';
        $db->executeQuery(
            "insert into PagePermissionAssignments (cID, {$copyFields}) select ?, {$copyFields} from PagePermissionAssignments where cID = ?",
            [$this->getCollectionID(), $permissionsCollectionID]
        );
    }

    /**
     * Add a new block to a specific area of the page.
     *
     * @param \Concrete\Core\Entity\Block\BlockType\BlockType $bt the type of block to be added
     * @param \Concrete\Core\Area\Area $a the area instance (or its handle) to which the block should be added to
     * @param array $data The data of the block. This data depends on the specific block type
     *
     * @return \Concrete\Core\Block\Block
     */
    public function addBlock($bt, $a, $data)
    {
        $b = parent::addBlock($bt, $a, $data);
        $btHandle = $bt->getBlockTypeHandle();
        if ($b->getBlockTypeHandle() == BLOCK_HANDLE_PAGE_TYPE_OUTPUT_PROXY) {
            $bi = $b->getController();
            $output = $bi->getComposerOutputControlObject();
            $control = FormLayoutSetControl::getByID($output->getPageTypeComposerFormLayoutSetControlID());
            $object = $control->getPageTypeComposerControlObject();
            if ($object instanceof BlockControl) {
                $btHandle = $object->getBlockTypeObject()->getBlockTypeHandle();
            }
        }
        $theme = $this->getCollectionThemeObject();
        if ($btHandle && $theme) {
            $pageTypeTemplates = [];
            $areaTemplates = is_object($a) ? $a->getAreaCustomTemplates() : [];
            $themeTemplates = $theme->getThemeDefaultBlockTemplates();
            if (!is_array($themeTemplates)) {
                $themeTemplates = [];
            } else {
                $pt = $this->getPageTemplateHandle() ?: $this->getPageTemplateHandle();
                foreach ($themeTemplates as $key => $template) {
                    if (is_array($template) && $key == $pt) {
                        $pageTypeTemplates = $template;
                        unset($themeTemplates[$key]);
                    }
                }
            }
            $templates = array_merge($pageTypeTemplates, $themeTemplates, $areaTemplates);
            if (isset($templates[$btHandle])) {
                $template = $templates[$btHandle];
                $b->updateBlockInformation(['bFilename' => $template]);
            }
        }

        return $b;
    }

    /**
     * Get the relations of this page.
     *
     * @return \Concrete\Core\Entity\Page\Relation\SiblingRelation[]
     */
    public function getPageRelations()
    {
        $app = Application::getFacadeApplication();
        $em = $app->make(EntityManagerInterface::class);
        $repo = $em->getRepository(SiblingRelation::class);
        $relations = [];
        $relation = $repo->findOneBy(['cID' => $this->getCollectionID()]);
        if ($relation !== null) {
            $allRelations = $repo->findBy(['mpRelationID' => $relation->getPageRelationID()]);
            foreach ($allRelations as $relation) {
                if ($relation->getPageID() != $this->getCollectionID() && $relation->getPageObject()->getSiteTreeObject() instanceof SiteTree) {
                    $relations[] = $relation;
                }
            }
        }

        return $relations;
    }

    /**
     * Move this page under a new parent page.
     *
     * @param \Concrete\Core\Page\Page $newParentPage
     */
    public function move($newParentPage)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $now = $app->make('date')->getOverridableNow();

        $newCParentID = $newParentPage->getCollectionID();
        $oldParentPage = self::getByID($this->getCollectionParentID(), 'RECENT');

        $cID = $this->getCollectionPointerOriginalID() ?: $this->getCollectionID();

        if ($this->getPermissionsCollectionID() != $cID) {
            // implicitly, we're set to inherit the permissions of wherever we are in the site.
            // as such, we'll change to inherit whatever permissions our new parent has
            $npID = (int) $newParentPage->getPermissionsCollectionID();
            if ($npID != $this->getPermissionsCollectionID()) {
                //we have to update the existing collection with the info for the new
                //as well as all collections beneath it that are set to inherit from this parent
                // first we do this one
                $db->executeQuery('update Pages set cInheritPermissionsFromCID = ? where cID = ?', [$npID, $cID]);
                $this->updatePermissionsCollectionID($cID, $npID);
            }
        }
        PageStatistics::decrementParents($cID);
        $db->executeQuery('update Collections set cDateModified = ? where cID = ?', [$now, $cID]);
        $db->executeQuery('update Pages set cParentID = ? where cID = ?', [$newCParentID, $cID]);
        PageStatistics::incrementParents($cID);

        $childPages = null;

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

        if ($newParentPage->getSiteTreeID() != $this->getSiteTreeID()) {
            $db->executeQuery('update Pages set siteTreeID = ? where cID = ?', [$newParentPage->getSiteTreeID(), $cID]);
            if ($childPages === null) {
                $childPages = $this->populateRecursivePages([], ['cID' => $cID], $this->getCollectionParentID(), 0, false);
            }
            foreach ($childPages as $page) {
                $db->executeQuery('update Pages set siteTreeID = ? where cID = ?', [$newParentPage->getSiteTreeID(), $page['cID']]);
            }
        }

        $this->siteTreeID = $newParentPage->getSiteTreeID();
        $this->siteTree = null; // in case we need to get the updated one
        $this->cParentID = $newCParentID;
        $this->movePageDisplayOrderToBottom();
        // run any event we have for page move. Arguments are
        // 1. current page being moved
        // 2. former parent
        // 3. new parent

        $newParentPage = self::getByID($newCParentID, 'RECENT');

        $pe = new MovePageEvent($this);
        $pe->setOldParentPageObject($oldParentPage);
        $pe->setNewParentPageObject($newParentPage);
        $app->make('director')->dispatch('on_page_move', $pe);

        $multilingual = $app->make('multilingual/detector');
        if ($multilingual->isEnabled()) {
            Section::registerMove($this, $oldParentPage, $newParentPage);
        }

        // now that we've moved the collection, we rescan its path
        $this->rescanCollectionPath();
    }

    /**
     * Duplicate this page and all its child pages and return the new Page created.
     *
     * @param \Concrete\Core\Page\Page|null $toParentPage The page under which this page should be copied to
     * @param bool $preserveUserID Set to true to preserve the original page author IDs
     * @param \Concrete\Core\Entity\Site\Site|null $site the destination site (used if $toParentPage is NULL)
     *
     * @return \Concrete\Core\Page\Page
     */
    public function duplicateAll($toParentPage = null, $preserveUserID = false, Site $site = null)
    {
        $nc2 = $this->duplicate($toParentPage, $preserveUserID, $site);
        $this->_duplicateAll($this, $nc2, $preserveUserID, $site);

        return $nc2;
    }

    /**
     * Duplicate this page and return the new Page created.
     *
     * @param \Concrete\Core\Page\Page|null $toParentPage The page under which this page should be copied to
     * @param bool $preserveUserID Set to true to preserve the original page author IDs
     * @param \Concrete\Core\Site\Tree\TreeInterface|null $site the destination site (used if $toParentPage is NULL)
     *
     * @return \Concrete\Core\Page\Page
     */
    public function duplicate($toParentPage = null, $preserveUserID = false, TreeInterface $site = null)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);

        $cParentID = $toParentPage ? $toParentPage->getCollectionID() : 0;

        if ($preserveUserID) {
            $uID = $this->getCollectionUserID();
        } else {
            $u = new User();
            $uID = $u->getUserID();
        }

        // create new name
        $originalCollectionName = $this->getCollectionName();
        $newCollectionName = $originalCollectionName;
        $nameIndex = 1;
        $nameCount = 1;
        while ($nameCount > 0) {
            $nameCount = $db->fetchColumn(
                'select count(Pages.cID) from CollectionVersions inner join Pages on (CollectionVersions.cID = Pages.cID and CollectionVersions.cvIsApproved = 1) where Pages.cParentID = ? and CollectionVersions.cvName = ?',
                [$cParentID, $newCollectionName]
            );
            if ($nameCount) {
                ++$nameIndex;
                $newCollectionName = $originalCollectionName . ' ' . $nameIndex;
            }
        }

        $oldCollection = parent::getByID($this->getCollectionID());
        $newCollection = $oldCollection->duplicateCollection();
        $newCID = $newCollection->getCollectionID();

        if ($toParentPage) {
            $siteTreeID = $toParentPage->getSiteTreeID();
        } else {
            $siteTreeID = is_object($site) ? $site->getSiteTreeID() : $app->make('site')->getSite()->getSiteTreeID();
        }

        $db->insert('Pages', [
            'cID' => $newCID,
            'siteTreeID' => $siteTreeID,
            'ptID' => $this->getPageTypeID(),
            'cParentID' => $cParentID,
            'uID' => $uID,
            'cOverrideTemplatePermissions' => $this->overrideTemplatePermissions(),
            'cInheritPermissionsFromCID' => (int) $this->getPermissionsCollectionID(),
            'cInheritPermissionsFrom' => $this->getCollectionInheritance(),
            'cFilename' => $this->cFilename,
            'cPointerID' => $this->getCollectionPointerID(),
            'cPointerExternalLink' => $this->cPointerExternalLink,
            'cPointerExternalLinkNewWindow' => $this->cPointerExternalLinkNewWindow,
            'cDisplayOrder' => $this->cDisplayOrder,
            'pkgID' => $this->pkgID,
        ]);

        // Composer specific
        $copyFields = 'cvID, arHandle, cbDisplayOrder, ptComposerFormLayoutSetControlID, bID';
        $db->executeQuery(
            "insert into PageTypeComposerOutputBlocks (cID, {$copyFields}) select ?, {$copyFields} from PageTypeComposerOutputBlocks where cID = ?",
            [$newCID, $this->getCollectionID()]
        );

        PageStatistics::incrementParents($newCID);

        $newPage = self::getByID($newCID);

        // now with any specific permissions - but only if this collection is set to override
        switch ($this->getCollectionInheritance()) {
            case 'OVERRIDE':
                $newPage->acquirePagePermissions($this->getPermissionsCollectionID());
                $newPage->acquireAreaPermissions($this->getPermissionsCollectionID());
                // make sure we update the proper permissions pointer to the new page ID
                $db->executeQuery('update Pages set cInheritPermissionsFromCID = ? where cID = ?', [$newCID, $newCID]);
                $newPage->cInheritPermissionsFromCID = $newCID;
                break;
            case 'PARENT':
                // we need to clear out any lingering permissions groups (just in case), and set this collection to inherit from the parent
                $npID = $toParentPage->getPermissionsCollectionID();
                $db->executeQuery('update Pages set cInheritPermissionsFromCID = ? where cID = ?', [(int) $npID, $newCID]);
                $newPage->cInheritPermissionsFromCID = $npID;
                break;
        }

        $args = [];
        if ($nameIndex > 1) {
            $args['cName'] = $newCollectionName;
            if ($newPage->getCollectionHandle()) {
                $args['cHandle'] = $newPage->getCollectionHandle() . '-' . $nameIndex;
            }
        }
        $newPage->update($args);

        Section::registerDuplicate($newPage, $this);

        $pe = new DuplicatePageEvent($this);
        $pe->setNewPageObject($newPage);
        $app->make('director')->dispatch('on_page_duplicate', $pe);

        $newPage->rescanCollectionPath();
        $newPage->movePageDisplayOrderToBottom();

        return $newPage;
    }

    /**
     * Delete this page and all its child pages.
     *
     * @return null|false return false if it's not possible to delete this page (for instance because it's the main homepage)
     */
    public function delete()
    {
        if ($this->isAliasPage()) {
            $this->removeThisAlias();

            return;
        }
        $cID = $this->getCollectionID();
        if ($cID < 1 || $cID == static::getHomePageID()) {
            return false;
        }

        $app = Application::getFacadeApplication();

        // run any internal event we have for page deletion
        $pe = new DeletePageEvent($this);
        $app->make('director')->dispatch('on_page_delete', $pe);

        if (!$pe->proceed()) {
            return false;
        }
        $app->make('log')->debug(t('Page "%s" at path "%s" deleted', $this->getCollectionName(), $this->getCollectionPath()));

        parent::delete();

        $db = $app->make(Connection::class);

        // Now that all versions are gone, we can delete the collection information

        $db->executeQuery('delete from PagePaths where cID = ?', [$cID]);

        // remove all pages where the pointer is this cID
        $rs = $db->executeQuery('select cID from Pages where cPointerID = ?', [$cID]);
        while (($cPointerID = $rs->fetchColumn()) !== false) {
            PageStatistics::decrementParents($cPointerID);
            $db->executeQuery('DELETE FROM PagePaths WHERE cID = ?', [$cPointerID]);
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

        $rs = $db->executeQuery('select cID from Pages where cParentID = ?', [$cID]);
        while (($childPageID = $rs->fetchColumn()) !== false) {
            $childPage = self::getByID($childPageID);
            $childPage->delete();
        }

        if ($app->make('multilingual/detector')->isEnabled()) {
            Section::unregisterPage($this);
        }

        $cache = PageCache::getLibrary();
        $cache->purge($this);
    }

    /**
     * Move this page and all its child pages to the trash.
     */
    public function moveToTrash()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $config = $app->make('config');

        // run any internal event we have for page trashing
        $pe = new Event($this);
        $app->make('director')->dispatch('on_page_move_to_trash', $pe);

        $trash = self::getByPath($config->get('concrete.paths.trash'));
        $app->make('log')->debug(t('Page "%s" at path "%s" Moved to trash', $this->getCollectionName(), $this->getCollectionPath()));
        $this->move($trash);
        $this->deactivate();

        // if this page has a custom canonical path we need to clear it
        $path = $this->getCollectionPathObject();
        if (!$path->isPagePathAutoGenerated()) {
            $path = $this->getAutoGeneratedPagePathObject();
            $this->setCanonicalPagePath($path->getPagePath(), true);
            $this->rescanCollectionPath();
        }
        $cID = $this->getCollectionPointerOriginalID() ?: $this->getCollectionID();
        $childPages = $this->populateRecursivePages([], ['cID' => $cID], $this->getCollectionParentID(), 0, false);
        foreach ($childPages as $childPage) {
            $db->executeQuery('update Pages set cIsActive = 0 where cID = ?', [$childPage['cID']]);
        }
    }

    /**
     * Regenerate the display order of the child pages.
     */
    public function rescanChildrenDisplayOrder()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $rs = $db->executeQuery('SELECT cID FROM Pages WHERE cParentID = ? ORDER BY cDisplayOrder', [$this->getCollectionID()]);
        $displayOrder = 0;
        while (($childID = $rs->fetchColumn()) !== false) {
            $db->executeQuery('update Pages set cDisplayOrder = ? where cID = ?', [$displayOrder, $childID]);
            ++$displayOrder;
        }
    }

    /**
     * Is this the homepage for the site tree this page belongs to?
     *
     * @return bool
     */
    public function isHomePage()
    {
        return $this->getCollectionID() && $this->getSiteHomePageID() == $this->getCollectionID();
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
     * Get a new PagePath object with the computed canonical page path.
     *
     * @return \Concrete\Core\Entity\Page\PagePath
     */
    public function getAutoGeneratedPagePathObject()
    {
        $path = new PagePath();
        $path->setPagePathIsAutoGenerated(true);
        $path->setPagePath($this->computeCanonicalPagePath());

        return $path;
    }

    /**
     * Get the next available display order of child pages.
     *
     * @return int
     */
    public function getNextSubPageDisplayOrder()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $max = $db->fetchColumn('select max(cDisplayOrder) from Pages where cParentID = ?', [$this->getCollectionID()]);

        return $max === false || $max === null ? 0 : 1 + (int) $max;
    }

    /**
     * Get the URL-slug-based path to the current page (including any suffixes) in a string format. Does so in real time.
     *
     * @return string
     */
    public function generatePagePath()
    {
        $pathObject = $this->getCollectionPathObject();
        if ($pathObject && !$pathObject->isPagePathAutoGenerated()) {
            $pathString = $pathObject->getPagePath();
        } else {
            $pathString = $this->computeCanonicalPagePath();
        }
        if (!$pathString) {
            // We are allowed to pass in a blank path in the event of the home page being scanned.
            return '';
        }
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $cID = ($this->getCollectionPointerOriginalID() > 0) ? $this->getCollectionPointerOriginalID() : $this->getCollectionID();
        $config = $app->make('config');
        $pagePathSeparator = $config->get('concrete.seo.page_path_separator');
        // ensure that the path is unique
        $suffix = 0;
        while (true) {
            $newPath = $suffix === 0 ? $pathString : $pathString . $pagePathSeparator . $suffix;
            $result = $db->fetchColumn(
                'select p.cID from PagePaths pp inner join Pages p on pp.cID = p.cID where pp.cPath = ? and pp.cID <> ? and p.siteTreeID = ?',
                [$newPath, $cID, $this->getSiteTreeID()]
            );
            if (empty($result)) {
                break;
            }
            ++$suffix;
        }

        return $newPath;
    }

    /**
     * Recalculate the canonical page path for the current page and its sub-pages, based on its current version, URL slug, etc.
     */
    public function rescanCollectionPath()
    {
        $newPath = $this->generatePagePath();
        $pathObject = $this->getCollectionPathObject();
        $ppIsAutoGenerated = $pathObject && !$pathObject->isPagePathAutoGenerated() ? false : true;
        $this->setCanonicalPagePath($newPath, $ppIsAutoGenerated);
        $this->rescanSystemPageStatus();
        $this->cPath = $newPath;
        $this->refreshCache();

        $children = $this->getCollectionChildren();
        foreach ($children as $child) {
            $child->rescanCollectionPath();
        }
    }

    /**
     * Set a new display order for this page (or for another page given its ID).
     *
     * @param int $displayOrder
     * @param int|null $cID The page ID to set the display order for (if empty, we'll use this page)
     */
    public function updateDisplayOrder($displayOrder, $cID = 0)
    {
        //this line was added to allow changing the display order of aliases
        if (!$cID) {
            $cID = $this->getCollectionPointerOriginalID() ?: $this->getCollectionID();
        }
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->executeQuery('update Pages set cDisplayOrder = ? where cID = ?', [$displayOrder, $cID]);
    }

    /**
     * Make this page the first child of its parent.
     */
    public function movePageDisplayOrderToTop()
    {
        // first, we take the current collection, stick it at the beginning of an array, then get all other items from the current level that aren't that cID, order by display order, and then update
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $nodes = [$this->getCollectionID()];
        $rs = $db->executeQuery('select cID from Pages where cParentID = ? and cID <> ? order by cDisplayOrder asc', [$this->getCollectionParentID(), $this->getCollectionID()]);
        while (($cID = $rs->fetchColumn()) !== false) {
            $nodes[] = $cID;
        }
        $displayOrder = 0;
        foreach ($nodes as $displayOrder => $cID) {
            $page = self::getByID($cID);
            $page->updateDisplayOrder($displayOrder);
        }
    }

    /**
     * Make this page the first child of its parent.
     */
    public function movePageDisplayOrderToBottom()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $max = $db->fetchColumn('select max(cDisplayOrder) as m from Pages where cParentID = ?', [$this->getCollectionParentID()]);
        $this->updateDisplayOrder(1 + (int) $max);
    }

    /**
     * Move this page before of after another page.
     *
     * @param Page $referencePage The reference page
     * @param string $position 'before' or 'after'
     */
    public function movePageDisplayOrderToSibling(Page $referencePage, $position = 'before')
    {
        if ($position !== 'after') {
            $position = 'before';
        }
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $myCID = $this->getCollectionPointerOriginalID() ?: $this->getCollectionID();
        $relatedCID = $referencePage->getCollectionPointerOriginalID() ?: $referencePage->getCollectionID();
        // first, we get a list of IDs.
        $pageIDs = [];
        $rs = $db->executeQuery('select cID from Pages where cParentID = ? and cID <> ? order by cDisplayOrder asc', [$this->getCollectionParentID(), $myCID]);
        while (($cID = $rs->fetchColumn()) !== false) {
            if ($cID == $relatedCID && $position == 'before') {
                $pageIDs[] = $myCID;
            }
            $pageIDs[] = $cID;
            if ($cID == $relatedCID && $position == 'after') {
                $pageIDs[] = $myCID;
            }
        }
        $displayOrder = 0;
        foreach ($pageIDs as $cID) {
            $page = self::getByID($cID);
            $page->updateDisplayOrder($displayOrder);
            ++$displayOrder;
        }
    }

    /**
     * Recalculate the "is a system page" state.
     * Looks at the current page. If the site tree ID is 0, sets system page to true.
     * If the site tree is not user, looks at where the page falls in the hierarchy. If it's inside a page
     * at the top level that has 0 as its parent, then it is considered a system page.
     */
    public function rescanSystemPageStatus()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $systemPage = false;
        $cID = $this->getCollectionID();
        if (!$this->isHomePage()) {
            if (!$this->getSiteTreeID()) {
                $systemPage = true;
            } else {
                $cID = $this->getCollectionPointerOriginalID() ?: $this->getCollectionID();
                $path = $db->fetchColumn('select cPath from PagePaths where cID = ? and ppIsCanonical = 1', [$cID]);
                if ($path) {
                    // Grab the top level parent
                    $fragments = explode('/', $path);
                    $topPath = '/' . $fragments[1];
                    $c = self::getByPath($topPath);
                    if ($c && !$c->isError()) {
                        if (!$c->getCollectionParentID() && !$c->isHomePage()) {
                            $systemPage = true;
                        }
                    }
                }
            }
        }

        $db->executeQuery('update Pages set cIsSystemPage = ? where cID = ?', [$systemPage ? 1 : 0, $cID]);
        $this->cIsSystemPage = $systemPage;
    }

    /**
     * Is this page in the trash?
     *
     * @return bool
     */
    public function isInTrash()
    {
        $app = Application::getFacadeApplication();
        $config = $app->make('config');
        $trashPath = $config->get('concrete.paths.trash');

        return $this->getCollectionPath() != $trashPath && strpos($this->getCollectionPath(), $trashPath) === 0;
    }

    /**
     * Make this page child of nothing, thus moving it to the root level.
     */
    public function moveToRoot()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->executeQuery('update Pages set cParentID = 0 where cID = ?', [$this->getCollectionID()]);
        $this->cParentID = 0;
        $this->rescanSystemPageStatus();
    }

    /**
     * Mark this page as non active.
     */
    public function deactivate()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->executeQuery('update Pages set cIsActive = 0 where cID = ?', [$this->getCollectionID()]);
        $this->cIsActive = 0;
    }

    /**
     * Mark this page as active.
     */
    public function activate()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->executeQuery('update Pages set cIsActive = 1 where cID = ?', [$this->getCollectionID()]);
        $this->cIsActive = 1;
    }

    /**
     * Is this page marked as active?
     *
     * @return bool
     */
    public function isActive()
    {
        return !empty($this->cIsActive);
    }

    /**
     * Set the page index score (used by a PageList for instance).
     *
     * @param float $score
     */
    public function setPageIndexScore($score)
    {
        $this->cIndexScore = $score;
    }

    /**
     * Get the page index score (as set by a PageList for instance).
     *
     * @return float
     */
    public function getPageIndexScore()
    {
        return empty($this->cIndexScore) ? 0.0 : round($this->cIndexScore, 2);
    }

    /**
     * Get the indexed content of this page.
     *
     * @return string
     */
    public function getPageIndexContent()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);

        return (string) $db->fetchColumn('select content from PageSearchIndex where cID = ?', [$this->getCollectionID()]);
    }

    /**
     * Add a new page, child of this page.
     *
     * @param \Concrete\Core\Page\Type\Type|null $pageType
     * @param array $data Supported keys: {
     *
     *     @var int|null $uID The ID of the page author (if unspecified or NULL: current user)
     *     @var int|null $pkgID the ID of the package that creates this page
     *     @var string $cName The page name
     *     @var string $name (used if cName is not specified)
     *     @var int|null $cID The ID of the page to create (if unspecified or NULL: database autoincrement value)
     *     @var int|bool $cIsActive Is the page to be considered as active?
     *     @var int|bool $cIsDraft Is the page to be considered as draft?
     *     @var string $cHandle The page handle
     *     @var string $cDescription The page description (default: NULL)
     *     @var string $cDatePublic The page publish date/time in format 'YYYY-MM-DD hh:mm:ss' (default: now)
     *     @var bool $cvIsApproved Is the page version approved (default: true)
     *     @var bool $cvIsNew Is the page to be considered "new"? (default: true if $cvIsApproved is false, false if $cvIsApproved is true)
     *     @var bool $cAcquireComposerOutputControls
     * }
     *
     * @param \Concrete\Core\Entity\Page\Template|null $pageTemplate
     *
     * @return page

     **/
    public function add($pageType, $data, $pageTemplate = false)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $config = $app->make('config');
        $txt = $app->make('helper/text');
        $u = new User();

        $data['uID'] = isset($data['uID']) ? $data['uID'] : $u->getUserID();
        $data['pkgID'] = isset($data['pkgID']) ? $data['pkgID'] : 0;
        $data['cIsActive'] = isset($data['cIsActive']) && !$data['cIsActive'] ? 0 : 1;
        $data['cIsDraft'] = isset($data['cIsDraft']) && $data['cIsDraft'] ? 1 : 0;
        $data['name'] = isset($data['cName']) ? $data['cName'] : (isset($data['name']) ? $data['name'] : '');
        if (empty($data['cHandle'])) {
            // make the handle out of the title
            $handle = $txt->urlify($data['name']);
        } else {
            // we take it as it comes.
            $handle = $txt->slugSafeString($data['cHandle']);
        }
        $handle = str_replace('-', $config->get('concrete.seo.page_path_separator'), $handle);
        $data['handle'] = $handle;

        $masterCIDBlocks = null;
        $masterCID = null;
        if ($pageTemplate instanceof PageTemplateEntity) {
            $data['pTemplateID'] = $pageTemplate->getPageTemplateID();
        } else {
            $pageTemplate = null;
        }
        if ($pageType instanceof PageType) {
            if ($pageType->getPageTypeHandle() == STACKS_PAGE_TYPE) {
                $data['cvIsNew'] = 0;
            }
            if ($pageType->getPackageID() > 0) {
                $data['pkgID'] = $pageType->getPackageID();
            }

            // if we have a page type and we don't have a template, then we use the page type's default template
            if (!$pageTemplate && $pageType->getPageTypeDefaultPageTemplateID() > 0) {
                $pageTemplate = $pageType->getPageTypeDefaultPageTemplateObject();
            }
            if ($pageTemplate) {
                $mc1 = $pageType->getPageTypePageTemplateDefaultPageObject($pageTemplate);
                $mc2 = $pageType->getPageTypePageTemplateDefaultPageObject();
                $masterCIDBlocks = $mc1->getCollectionID();
                $masterCID = $mc2->getCollectionID();
            }
        } else {
            $pageType = null;
        }

        $newCollection = parent::addCollection($data);
        $cID = $newCollection->getCollectionID();

        $db->insert('Pages', [
            'cID' => $cID,
            'siteTreeID' => $this->getSiteTreeID(),
            'ptID' => $pageType ? $pageType->getPageTypeID() : 0,
            'cParentID' => $this->getCollectionID(),
            'uID' => $data['uID'],
            'cInheritPermissionsFrom' => $this->overrideTemplatePermissions() ? 'PARENT' : 'TEMPLATE',
            'cOverrideTemplatePermissions' => $this->overrideTemplatePermissions() ? 1 : 0,
            'cInheritPermissionsFromCID' => $this->overrideTemplatePermissions() ? (int) $this->getPermissionsCollectionID() : (int) $masterCID,
            'cDisplayOrder' => $this->getNextSubPageDisplayOrder(),
            'pkgID' => $data['pkgID'],
            'cIsActive' => $data['cIsActive'],
            'cIsDraft' => $data['cIsDraft'],
        ]);

        // Collection added with no problem -- update cChildren on parrent
        PageStatistics::incrementParents($cID);

        // We need to see if the collection type we're adding has a master collection associated with it
        if ($masterCIDBlocks) {
            $this->_associateMasterCollectionBlocks($cID, $masterCIDBlocks, !empty($data['cAcquireComposerOutputControls']));
        }
        if ($masterCID) {
            $this->_associateMasterCollectionAttributes($cID, $masterCID);
        }

        $newPage = self::getByID($cID, 'RECENT');

        // Check the multilingual status unless we are in the drafts area of the site
        if ($this->getCollectionPath() != $config->get('concrete.paths.drafts')) {
            Section::registerPage($newPage);
        }

        if ($pageTemplate) {
            $newPage->acquireAreaStylesFromDefaults($pageTemplate);
        }

        // run any internal event we have for page addition
        $pe = new Event($newPage);
        $app->make('director')->dispatch('on_page_add', $pe);

        $newPage->rescanCollectionPath();

        $hasAuthor = false;
        foreach ($u->getUserAccessEntityObjects() as $obj) {
            if ($obj instanceof PageOwnerEntity) {
                $hasAuthor = true;
                break;
            }
        }
        if (!$hasAuthor) {
            $u->refreshUserGroups();
        }

        return $newPage;
    }

    /**
     * Get the custom style for the currently loaded page version (if any).
     *
     * @return \Concrete\Core\Page\CustomStyle|null
     */
    public function getCustomStyleObject()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $row = $db->fetchAssoc('select * from CollectionVersionThemeCustomStyles where cID = ? and cvID = ?', [$this->getCollectionID(), $this->getVersionID()]);
        if ($row) {
            $o = new CustomStyle();
            $o->setThemeID($row['pThemeID']);
            $o->setValueListID($row['scvlID']);
            $o->setPresetHandle($row['preset']);
            $o->setCustomCssRecordID($row['sccRecordID']);

            return $o;
        }
    }

    /**
     * Get the full-page cache flag (-1: use global setting; 0: no; 1: yes - NULL if page is not loaded).
     *
     * @return int|null
     */
    public function getCollectionFullPageCaching()
    {
        return isset($this->cCacheFullPageContent) ? $this->cCacheFullPageContent : null;
    }

    /**
     * Get the full-page cache lifetime criteria ('default': use default lifetime; 'forever': no expiration; 'custom': custom lifetime value - see getCollectionFullPageCachingLifetimeCustomValue(); other: use the default lifetime - NULL if page is not loaded).
     *
     * @return string|null
     */
    public function getCollectionFullPageCachingLifetime()
    {
        return isset($this->cCacheFullPageContentOverrideLifetime) ? $this->cCacheFullPageContentOverrideLifetime : null;
    }

    /**
     * Get the full-page cache custom lifetime in minutes (to be used if getCollectionFullPageCachingLifetime() is 'custom').
     *
     * @return int|null returns NULL if page is not loaded
     */
    public function getCollectionFullPageCachingLifetimeCustomValue()
    {
        return isset($this->cCacheFullPageContentLifetimeCustom) ? $this->cCacheFullPageContentLifetimeCustom : null;
    }

    /**
     * Get the actual full-page cache lifespan (in seconds).
     *
     * @return int
     */
    public function getCollectionFullPageCachingLifetimeValue()
    {
        $app = Application::getFacadeApplication();
        $config = $app->make('config');
        switch ($this->getCollectionFullPageCachingLifetime()) {
            case 'default':
                $lifetime = $config->get('concrete.cache.lifetime');
                break;
            case 'custom':
                $lifetime = $this->getCollectionFullPageCachingLifetimeCustomValue() * 60;
                break;
            case 'forever':
                $lifetime = 31536000; // 1 year
                break;
            default:
                switch ($config->get('concrete.cache.full_page_lifetime')) {
                    case 'custom':
                        $lifetime = $config->get('concrete.cache.full_page_lifetime_value') * 60;
                        break;
                    case 'forever':
                        $lifetime = 31536000; // 1 year
                        break;
                    default:
                        $lifetime = $config->get('concrete.cache.lifetime');
                        break;
                }
                break;
        }

        if (!$lifetime) {
            // we have no value, which means forever, but we need a numerical value for page caching
            $lifetime = 31536000;
        }

        return $lifetime;
    }

    /**
     * Is this a draft?
     *
     * @return bool
     */
    public function isPageDraft()
    {
        return !empty($this->cIsDraft);
    }

    /**
     * Mark this page as non draft.
     */
    public function setPageToDraft()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->executeQuery('update Pages set cIsDraft = 1 where cID = ?', [$this->getCollectionID()]);
        $this->cIsDraft = true;
    }

    /**
     * Get the ID of the draft parent page ID.
     *
     * @return int
     */
    public function getPageDraftTargetParentPageID()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);

        return (int) $db->fetchColumn('select cDraftTargetParentPageID from Pages where cID = ?', [$this->getCollectionID()]);
    }

    /**
     * Set the ID of the draft parent page ID.
     *
     * @param int $cParentID
     */
    public function setPageDraftTargetParentPageID($cParentID)
    {
        if ($cParentID != $this->getPageDraftTargetParentPageID()) {
            Section::unregisterPage($this);
        }
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $cParentID = (int) $cParentID;
        $db->executeQuery('update Pages set cDraftTargetParentPageID = ? where cID = ?', [$cParentID, $this->getCollectionID()]);
        $this->cDraftTargetParentPageID = $cParentID;
        Section::registerPage($this);
    }

    /**
     * @deprecated This method is an alias of isParentOf()
     *
     * @param \Concrete\Core\Page\Page $cobj
     *
     * @return bool
     */
    public function canMoveCopyTo($cobj)
    {
        return $this->isParentOf($cobj);
    }

    /**
     * @deprecated use the isAliasPageOrExternalLink() method
     *
     * @return bool
     */
    public function isAlias()
    {
        return $this->isAliasPageOrExternalLink();
    }

    /**
     * @deprecated use the isHomePage() method
     *
     * @return bool
     */
    public function isLocaleHomePage()
    {
        return $this->isHomePage();
    }

    /**
     * @deprecated use the getPageController() method
     */
    public function getController()
    {
        return $this->getPageController();
    }

    /**
     * @deprecated use the getPageTypeID() method
     */
    public function getCollectionTypeID()
    {
        return $this->getPageTypeID();
    }

    /**
     * @deprecated use the getPageTypeHandle() method
     *
     * @return string|false
     */
    public function getCollectionTypeHandle()
    {
        return $this->getPageTypeHandle();
    }

    /**
     * @deprecated use the getPageTypeName() method
     */
    public function getCollectionTypeName()
    {
        return $this->getPageTypeName();
    }

    /**
     * @deprecated There's no more an "Arrange Mode"
     *
     * @return false
     */
    public function isArrangeMode()
    {
        return false;
    }

    /**
     * @deprecated use the \Concrete\Core\Page\Exporter class
     *
     * @param \SimpleXMLElement $pageNode
     *
     * @see \Concrete\Core\Page\Page::getExporter()
     */
    public function export($pageNode)
    {
        $exporter = $this->getExporter();
        $exporter->export($this, $pageNode);
    }

    /**
     * @deprecated is this function still useful? There's no reference to it in the core
     *
     * @param int|string $cParentIDString
     */
    public function updateGroupsSubCollection($cParentIDString)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $rs = $db->executeQuery("select cID from Pages where cParentID in ({$cParentIDString}) and cInheritPermissionsFrom = 'PARENT'");
        $cList = [];
        while (($cID = $rs->fetchColumn()) !== false) {
            $cList[] = $cID;
        }
        if (count($cList) > 0) {
            $cParentIDString = implode(',', $cList);
            $db->executeQuery("update Pages set cInheritPermissionsFromCID = ? where cID in ({$cParentIDString})", [$this->getCollectionID()]);
            $this->updateGroupsSubCollection($cParentIDString);
        }
    }

    /**
     * Read the data from the database.
     *
     * @param mixed $cInfo The argument of the $where condition
     * @param string $where The SQL 'WHERE' part
     * @param string|int $cvID
     */
    protected function populatePage($cInfo, $where, $cvID)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);

        $this->loadError(false);

        $sqlBase = 'select Pages.cID, Pages.pkgID, Pages.siteTreeID, Pages.cPointerID, Pages.cPointerExternalLink, Pages.cIsDraft, Pages.cIsActive, Pages.cIsSystemPage, Pages.cPointerExternalLinkNewWindow, Pages.cFilename, Pages.ptID, Collections.cDateAdded, Pages.cDisplayOrder, Collections.cDateModified, cInheritPermissionsFromCID, cInheritPermissionsFrom, cOverrideTemplatePermissions, cCheckedOutUID, cIsTemplate, uID, cPath, cParentID, cChildren, cCacheFullPageContent, cCacheFullPageContentOverrideLifetime, cCacheFullPageContentLifetimeCustom from Pages inner join Collections on Pages.cID = Collections.cID left join PagePaths on (Pages.cID = PagePaths.cID and PagePaths.ppIsCanonical = 1) ';
        //$q2 = "select cParentID, cPointerID, cPath, Pages.cID from Pages left join PagePaths on (Pages.cID = PagePaths.cID and PagePaths.ppIsCanonical = 1) ";

        $row = $db->fetchAssoc($sqlBase . $where, [$cInfo]);
        if ($row) {
            if ($row['cPointerID'] > 0) {
                $originalRow = $row;
                $row = $db->fetchAssoc($sqlBase . 'where Pages.cID = ?', [$row['cPointerID']]);
            } else {
                $originalRow = null;
            }
            if ($row) {
                foreach ($row as $key => $value) {
                    $this->{$key} = $value;
                }
                $this->isMasterCollection = $row['cIsTemplate'];
                if ($originalRow !== null) {
                    $this->cPointerID = $originalRow['cPointerID'];
                    $this->cIsActive = $originalRow['cIsActive'];
                    $this->cPointerOriginalID = $originalRow['cID'];
                    $this->cPath = $originalRow['cPath'];
                    $this->cParentID = $originalRow['cParentID'];
                    $this->cDisplayOrder = $originalRow['cDisplayOrder'];
                }
                if ($cvID) {
                    $this->loadVersionObject($cvID);
                }
            }
        } else {
            // there was no record of this particular collection in the database
            $this->loadError(COLLECTION_NOT_FOUND);
        }
    }

    /**
     * Populate the childrenCIDArray property (called by the getCollectionChildrenArray() method).
     *
     * @param int $cID
     * @param bool $oneLevelOnly
     * @param string $sortColumn
     */
    protected function _getNumChildren($cID, $oneLevelOnly = 0, $sortColumn = 'cDisplayOrder asc')
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $rs = $db->executeQuery("select cID from Pages where cParentID = ? and cIsTemplate = 0 order by {$sortColumn}", [$cID]);
        while (($childID = $rs->fetchColumn()) !== false) {
            $this->childrenCIDArray[] = $childID;
            if (!$oneLevelOnly) {
                $this->_getNumChildren($childID, false, $sortColumn);
            }
        }
    }

    /**
     * Duplicate all the child pages of a specific page which has already have been duplicated.
     *
     * @param \Concrete\Core\Page\Page $originalParentPage The original parent page
     * @param \Concrete\Core\Page\Page $newParentPage The duplicated parent page
     * @param bool $preserveUserID Set to true to preserve the original page author IDs
     * @param \Concrete\Core\Entity\Site\Site|null $site unused
     */
    protected function _duplicateAll($originalParentPage, $newParentPage, $preserveUserID = false, Site $site = null)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $cID = $originalParentPage->getCollectionID();
        $rs = $db->executeQuery('select cID, ptHandle from Pages p left join PageTypes pt on p.ptID = pt.ptID where cParentID = ? order by cDisplayOrder asc', [$cID]);
        while (($row = $rs->fetch(PDO::FETCH_ASSOC)) !== false) {
            // This is a terrible hack.
            if ($row['ptHandle'] === STACKS_PAGE_TYPE) {
                $originalChildPage = Stack::getByID($row['cID']);
            } else {
                $originalChildPage = self::getByID($row['cID']);
            }
            $originalChildPage->duplicateAll($newParentPage, $preserveUserID, $site);
        }
    }

    /**
     * Get the canonical path string of this page .
     * This happens before any uniqueness checks get run.
     *
     * @return string
     */
    protected function computeCanonicalPagePath()
    {
        $app = Application::getFacadeApplication();
        $stringValidator = $app->make('helper/validation/strings');
        $parent = self::getByID($this->cParentID);
        $parentPath = $parent->getCollectionPathObject();
        $path = $parentPath ? $parentPath->getPagePath() : '';
        $path .= '/';
        $cID = $this->getCollectionPointerOriginalID() ?: $this->getCollectionID();
        if ($stringValidator->notempty($this->getCollectionHandle())) {
            $path .= $this->getCollectionHandle();
        } elseif ($this->isHomePage()) {
            // This is computing the path for the home page, which has no handle, and so shouldn't have a segment.
            $path = '';
        } else {
            $path .= $cID;
        }

        $event = new PagePathEvent($this);
        $event->setPagePath($path);
        $event = $app->make('director')->dispatch('on_compute_canonical_page_path', $event);

        return $event->getPagePath();
    }

    /**
     * Duplicate the master collection blocks/permissions to a newly created page.
     *
     * @param int $newCID the ID of the newly created page
     * @param int $mcID the ID of the master collection
     * @param bool $cAcquireComposerOutputControls
     */
    protected function _associateMasterCollectionBlocks($newCID, $mcID, $cAcquireComposerOutputControls)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);

        $masterCollection = self::getByID($mcID, 'ACTIVE');
        $newPage = self::getByID($newCID, 'RECENT');
        $mcID = $masterCollection->getCollectionID();
        $mcvID = $masterCollection->getVersionID();

        $rs = $db->executeQuery(<<<EOT
select
    CollectionVersionBlocks.arHandle, BlockTypes.btCopyWhenPropagate, CollectionVersionBlocks.cbOverrideAreaPermissions, CollectionVersionBlocks.bID
from
    CollectionVersionBlocks
    inner join Blocks on Blocks.bID = CollectionVersionBlocks.bID
    inner join BlockTypes on Blocks.btID = BlockTypes.btID
where
    CollectionVersionBlocks.cID = ? and CollectionVersionBlocks.cvID = ?
order by
    CollectionVersionBlocks.cbDisplayOrder asc
EOT
            ,
            [$mcID, $mcvID]
        );
        while (($row = $rs->fetch(PDO::FETCH_ASSOC)) !== false) {
            $b = Block::getByID($row['bID'], $masterCollection, $row['arHandle']);
            if ($cAcquireComposerOutputControls || !in_array($b->getBlockTypeHandle(), ['core_page_type_composer_control_output'])) {
                if ($row['btCopyWhenPropagate']) {
                    $b->duplicate($newPage, true);
                } else {
                    $b->alias($newPage);
                }
            }
        }
    }

    /**
     * Duplicate the master collection attributes to a newly created page.
     *
     * @param int $newCID the ID of the newly created page
     * @param int $mcID the ID of the master collection
     */
    protected function _associateMasterCollectionAttributes($newCID, $mcID)
    {
        $masterCollection = self::getByID($mcID, 'ACTIVE');
        $newPage = self::getByID($newCID, 'RECENT');
        $attributes = $masterCollection->getObjectAttributeCategory()->getAttributeValues($masterCollection);
        foreach ($attributes as $attribute) {
            $value = $attribute->getValueObject();
            if ($value) {
                $value = clone $value;
                $newPage->setAttribute($attribute->getAttributeKey(), $value);
            }
        }
    }

    /**
     * Copy the area styles from a page template.
     *
     * @param \Concrete\Core\Entity\Page\Template $pageTemplate
     */
    protected function acquireAreaStylesFromDefaults(PageTemplateEntity $pageTemplate)
    {
        $pageType = $this->getPageTypeObject();
        if ($pageType) {
            $app = Application::getFacadeApplication();
            $db = $app->make(Connection::class);

            $mc = $pageType->getPageTypePageTemplateDefaultPageObject($pageTemplate);

            // first, we delete any styles we currently have
            $db->delete('CollectionVersionAreaStyles', ['cID' => $this->getCollectionID(), 'cvID' => $this->getVersionID()]);

            // now we acquire the styles
            $copyFields = 'arHandle, issID';
            $db->executeQuery(
                "insert into CollectionVersionAreaStyles (cID, cvID, {$copyFields}) select ?, ?, {$copyFields} from CollectionVersionAreaStyles where cID = ?",
                [$this->getCollectionID(), $this->getVersionID(), $mc->getCollectionID()]
            );
        }
    }

    /**
     * @param \SimpleXMLElement $node
     *
     * @return string[]
     */
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
}
