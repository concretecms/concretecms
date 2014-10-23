<?php
namespace Concrete\Core\Area;

use Loader;
use \Concrete\Core\Foundation\Object;
use CacheLocal;
use Block;
use PermissionKey;
use View;
use Permissions;
use Page;
use User;
use \Concrete\Core\Block\View\BlockView;

class Area extends Object implements \Concrete\Core\Permission\ObjectInterface
{

    public $cID, $arID, $arHandle;
    public $c;

    /* area-specific attributes */

    /**
     * limits the number of blocks in the area
     * @var int
     */
    public $maximumBlocks = -1; //

    /**
     * @var boolean
     */
    protected $showControls = -1;

    /**
     * @var string
     */
    public $enclosingStart = '';

    /**
     * @var string
     */
    public $enclosingEnd = '';

    /**
     * Array of Blocks within the current area
     * @see Area::getAreaBlocksArray()
     * @var Block[]
     */
    public $areaBlocksArray = array();

    protected $arIsLoaded = false;
    protected $arUseGridContainer = false;
    protected $arDisplayName;
    protected $arGridMaximumColumns;

    public function setAreaDisplayName($arDisplayName)
    {
        $this->arDisplayName = $arDisplayName;
    }

    /**
     * Returns whether or not controls are to be displayed
     * @return bool
     */
    public function showControls()
    {
        if ($this->showControls === true || $this->showControls === false) {
            return $this->showControls;
        } else {
            $c = $this->getAreaCollectionObject();
            return $c->isEditMode();
        }
    }

    public function forceControlsToDisplay()
    {
        $this->showControls = true;
    }

    public function setAreaGridMaximumColumns($cspan)
    {
        $this->arGridMaximumColumns = $cspan;
    }

    public function getAreaGridMaximumColumns()
    {
        if (!isset($this->arGridMaximumColumns)) {
            $this->arGridMaximumColumns = null;
            if ($this->isGridContainerEnabled()) {
                $c = $this->getAreaCollectionObject();
                if (is_object($c)) {
                    $pt = $c->getCollectionThemeObject();
                    if (is_object($pt)) {
                        $gf = $pt->getThemeGridFrameworkObject();
                        if (!is_object($gf)) {
                            throw new \Exception(t('No grid framework found. Grid area methods require a valid grid framework defined in a PageTheme class.'));
                        }
                        $this->arGridMaximumColumns = $gf->getPageThemeGridFrameworkNumColumns();
                    }
                }
            }
        }
        return $this->arGridMaximumColumns;
    }

    final public function enableGridContainer()
    {
        $this->arUseGridContainer = true;
    }

    public function isGridContainerEnabled()
    {
        return $this->arUseGridContainer;
    }

	public function getAreaDisplayName()
    {
        if (isset($this->arDisplayName)) {
            return $this->arDisplayName;
        } else {
            return tc('AreaName', $this->arHandle);
        }
    }

	/**
     * The constructor is used primarily on page templates to create areas of content that are editable within the cms.
     * ex: $a = new Area('Main'); $a->display($c)
     * We actually use Collection::getArea() when we want to interact with a fully
     * qualified Area object when dealing with a Page/Collection object
     *
     * @param string
     * @return void
     */
	public function __construct($arHandle)
    {
        $this->arHandle = $arHandle;
    }

	public function getPermissionObjectIdentifier()
    {
        return $this->getCollectionID() . ':' . $this->getAreaHandle();
    }

	public function getPermissionResponseClassName()
    {
        return '\\Concrete\\Core\\Permission\\Response\\AreaResponse';
    }

	public function getPermissionAssignmentClassName()
    {
        return '\\Concrete\\Core\\Permission\\Assignment\\AreaAssignment';
    }
	public function getPermissionObjectKeyCategoryHandle()
    {
        return 'area';
    }



	/**
     * returns the Collection's cID
     * @return int
     */
	public function getCollectionID()
    {
        if (is_object($this->c)) {
            return $this->c->getCollectionID();
        }
    }

	/**
     * returns the Collection object for the current Area
     * @return Collection
     */
	public function getAreaCollectionObject()
    {
        return $this->c;
    }

	/**
     * whether or not it's a global area
     * @return bool
     */
	public function isGlobalArea()
    {
        return false;
    }

	/**
     * returns the arID of the current area
     * @return int
     */
	public function getAreaID()
    {
        return $this->arID;
    }

	/**
     * returns the handle for the current area
     * @return string
     */
	public function getAreaHandle()
    {
        return $this->arHandle;
    }

	/**
     * Returns the total number of blocks in an area.
     * @param Page $c must be passed if the display() method has not been run on the area object yet.
     */
	public function getTotalBlocksInArea($c = false)
    {
        if (!$c) {
            $c = $this->c;
        }

        // exclude the area layout proxy block from counting.
        $this->load($c);
        $db = Loader::db();
        $r = $db->GetOne(
            'select count(b.bID) from CollectionVersionBlocks cvb inner join Blocks b on cvb.bID = b.bID inner join BlockTypes bt on b.btID = bt.btID where cID = ? and cvID = ? and arHandle = ? and btHandle <> ?',
            array($c->getCollectionID(), $c->getVersionID(), $this->arHandle, BLOCK_HANDLE_LAYOUT_PROXY)
        );

        // now grab sub-blocks.
        // NOTE: this will only traverse one level. Deal with it.
        $arHandles = $db->GetCol('select arHandle from Areas where arParentID = ?', array($this->arID));
        if (is_array($arHandles) && count($arHandles) > 0) {
            $v = array($c->getCollectionID(), $c->getVersionID());
            $q = 'select count(bID) from CollectionVersionBlocks where cID = ? and cvID = ? and arHandle in (';
            for ($i = 0; $i < count($arHandles); $i++) {
                $arHandle = $arHandles[$i];
                $v[] = $arHandle;
                $q .= '?';
                if (($i + 1) < count($arHandles)) {
                    $q .= ',';
                }
            }
            $q .= ')';
            $sr = $db->GetOne($q, $v);
            $r += $sr;
        }
        return $r;
    }

	/**
     * Returns the amount of actual blocks in the area, does not exclude core blocks or layouts, does not recurse.
     */
	public function getTotalBlocksInAreaEditMode()
    {
        $db = Loader::db();
        $r = $db->GetOne(
            'select count(b.bID) from CollectionVersionBlocks cvb inner join Blocks b on cvb.bID = b.bID inner join BlockTypes bt on b.btID = bt.btID where cID = ? and cvID = ? and arHandle = ?',
            array($this->c->getCollectionID(), $this->c->getVersionID(), $this->arHandle)
        );
        return $r;
    }

	/**
     * check if the area has permissions that override the page's permissions
     * @return boolean
     */
	public function overrideCollectionPermissions()
    {
        return $this->arOverrideCollectionPermissions;
    }

	/**
     * @return int
     */
	public function getAreaCollectionInheritID()
    {
        return $this->arInheritPermissionsFromAreaOnCID;
    }

	/**
     * Sets the total number of blocks an area allows. Does not limit by type.
     * @param int $num
     * @return void
     */
	public function setBlockLimit($num)
    {
        $this->maximumBlocks = $num;
    }

	/**
     * disables controls for the current area
     * @return void
     */
	public function disableControls()
    {
        $this->showControls = false;
    }

	/**
     * gets the maximum allowed number of blocks, -1 if unlimited
     * @return int
     */
	public function getMaximumBlocks()
    {
        return $this->maximumBlocks;
    }

	/**
     *
     * @return string
     */
	function getAreaUpdateAction($task = 'update', $alternateHandler = null)
    {
        $valt = Loader::helper('validation/token');
        $token = '&' . $valt->getParameter();
        $step = ($_REQUEST['step']) ? '&step=' . $_REQUEST['step'] : '';
        $c = $this->getAreaCollectionObject();
        if ($alternateHandler) {
            $str = $alternateHandler . "?atask={$task}&cID=" . $c->getCollectionID(
                ) . "&arHandle=" . $this->getAreaHandle() . $step . $token;
        } else {
            $str = DIR_REL . "/" . DISPATCHER_FILENAME . "?atask=" . $task . "&cID=" . $c->getCollectionID(
                ) . "&arHandle=" . $this->getAreaHandle() . $step . $token;
        }
        return $str;
    }

    public function refreshCache($c)
    {
        $identifier = sprintf('/page/area/%s', $c->getCollectionID());
        $cache = \Core::make('cache/request');
        $cache->delete($identifier);
    }

	/**
     * Gets the Area object for the given page and area handle
     * @param Page|Collection $c
     * @param string $arHandle
     * @param int|null $arIsGlobal
     * @return Area
     */

	final public static function get(&$c, $arHandle)
    {
        if (!is_object($c)) {
            return false;
        }

        $identifier = sprintf('/page/area/%s', $c->getCollectionID());
        $cache = \Core::make('cache/request');
        $item = $cache->getItem($identifier);
        if (!$item->isMiss()) {
            $areas = $item->get();
            return $areas[$arHandle];
        }
        $areas = array();
        $db = Loader::db();
        // First, we verify that this is a legitimate area
        $v = array($c->getCollectionID());
        $q = "select arID, arHandle, cID, arOverrideCollectionPermissions, arInheritPermissionsFromAreaOnCID, arIsGlobal, arParentID from Areas where cID = ?";
        $r = $db->Execute($q, $v);
        while ($arRow = $r->FetchRow()) {
            if ($arRow['arID'] > 0) {
                if ($arRow['arIsGlobal']) {
                    $obj = new GlobalArea($arHandle);
                } else {
                    if ($arRow['arParentID']) {
                        $arParentHandle = self::getAreaHandleFromID($arRow['arParentID']);
                        $obj = new SubArea($arHandle, $arParentHandle, $arRow['arParentID']);
                    } else {
                        $obj = new Area($arHandle);
                    }
                }
                $obj->setPropertiesFromArray($arRow);
                $obj->c = $c;
                $arRowHandle = $arRow['arHandle'];
                $areas[$arRowHandle] = $obj;
            }
        }

        $item->set($areas);
        return $areas[$arHandle];
    }

	/**
     * Creates an area in the database. I would like to make this static but PHP pre 5.3 sucks at this stuff.
     */
	public function create($c, $arHandle)
    {
        $db = Loader::db();
        $db->Replace(
            'Areas',
            array('cID' => $c->getCollectionID(), 'arHandle' => $arHandle),
            array('arHandle', 'cID'),
            true
        );
        $this->refreshCache($c);
        $area = self::get($c, $arHandle);
        $area->rescanAreaPermissionsChain();


        return $area;
    }

	public static function getAreaHandleFromID($arID)
    {
        $identifier = sprintf('/page/area/handle/%s', $arID);
        $cache = \Core::make('cache/request');
        $item = $cache->getItem($identifier);
        if (!$item->isMiss()) {
            return $item->get();
        } else {
            $item->lock();
            $db = Loader::db();
            $arHandle = $db->GetOne('select arHandle from Areas where arID = ?', array($arID));
            $item->set($arHandle);
            return $arHandle;
        }
    }

	/**
     * Get all of the blocks within the current area for a given page
     * @param Page|Collection $c
     * @return Block[]
     */
	public function getAreaBlocksArray($c = false)
    {
        if (!$c) {
            $c = $this->c;
        }
        if (!$this->arIsLoaded) {
            $this->load($c);
        }
        return $this->areaBlocksArray;
    }

	/**
     * gets a list of all areas - no relation to the current page or area object
     * possibly could be set as a static method??
     * @return array
     */
	public function getHandleList()
    {
        $db = Loader::db();
        $r = $db->Execute('select distinct arHandle from Areas where arParentID = 0 and arIsGlobal = 0 order by arHandle asc');
        $handles = array();
        while ($row = $r->FetchRow()) {
            $handles[] = $row['arHandle'];
        }
        $r->Free();
        unset($r);
        unset($db);
        return $handles;
    }

	public function getListOnPage(Page $c)
    {
        $identifier = sprintf('/page/area/list/%s', $c->getCollectionID());
        $cache = \Core::make('cache/request');
        $item = $cache->getItem($identifier);
        if (!$item->isMiss()) {
            return $item->get();
        } else {
            $item->lock();
            $db = Loader::db();
            $r = $db->Execute('select arHandle from Areas where cID = ?', array($c->getCollectionID()));
            $areas = array();
            while ($row = $r->FetchRow()) {
                $area = Area::get($c, $row['arHandle']);
                if (is_object($area)) {
                    $areas[] = $area;
                }
            }
            $item->set($areas);
            return $areas;
        }
    }

	/**
     * This function removes all permissions records for the current Area
     * and sets it to inherit from the Page permissions
     * @return void
     */
	function revertToPagePermissions()
    {
        // this function removes all permissions records for a particular area on this page
        // and sets it to inherit from the page above
        // this function will also need to ensure that pages below it do the same

        $db = Loader::db();
        $v = array($this->getAreaHandle(), $this->getCollectionID());
        $db->query("delete from AreaPermissionAssignments where arHandle = ? and cID = ?", $v);
        $db->query("update Areas set arOverrideCollectionPermissions = 0 where arID = ?", array($this->getAreaID()));

        // now we set rescan this area to determine where it -should- be inheriting from
        $this->arOverrideCollectionPermissions = false;
        $this->rescanAreaPermissionsChain();

        $areac = $this->getAreaCollectionObject();
        if ($areac->isMasterCollection()) {
            $this->rescanSubAreaPermissionsMasterCollection($areac);
        } else {
            if ($areac->overrideTemplatePermissions()) {
                // now we scan sub areas
                $this->rescanSubAreaPermissions();
            }
        }
    }

	public function __destruct()
    {
        unset($this->c);
    }


	/**
     * Rescans the current Area's permissions ensuring that it's enheriting permissions properly up the chain
     * @return void
     */
	public function rescanAreaPermissionsChain()
    {
        $db = Loader::db();
        if ($this->overrideCollectionPermissions()) {
            return false;
        }
        // first, we obtain the inheritance of permissions for this particular collection
        $areac = $this->getAreaCollectionObject();
        if (is_a($areac, 'Page')) {
            if ($areac->getCollectionInheritance() == 'PARENT') {

                $cIDToCheck = $areac->getCollectionParentID();
                // first, we temporarily set the arInheritPermissionsFromAreaOnCID to whatever the arInheritPermissionsFromAreaOnCID is set to
                // in the immediate parent collection
                $arInheritPermissionsFromAreaOnCID = $db->getOne(
                    "select a.arInheritPermissionsFromAreaOnCID from Pages c inner join Areas a on (c.cID = a.cID) where c.cID = ? and a.arHandle = ?",
                    array($cIDToCheck, $this->getAreaHandle())
                );
                if ($arInheritPermissionsFromAreaOnCID > 0) {
                    $db->query(
                        "update Areas set arInheritPermissionsFromAreaOnCID = ? where arID = ?",
                        array($arInheritPermissionsFromAreaOnCID, $this->getAreaID())
                    );
                }

                // now we do the recursive rescan to see if any areas themselves override collection permissions

                while ($cIDToCheck > 0) {
                    $row = $db->getRow(
                        "select c.cParentID, c.cID, a.arHandle, a.arOverrideCollectionPermissions, a.arID from Pages c inner join Areas a on (c.cID = a.cID) where c.cID = ? and a.arHandle = ?",
                        array($cIDToCheck, $this->getAreaHandle())
                    );
                    if ($row['arOverrideCollectionPermissions'] == 1) {
                        break;
                    } else {
                        $cIDToCheck = $row['cParentID'];
                    }
                }

                if (is_array($row)) {
                    if ($row['arOverrideCollectionPermissions'] && $row['cID'] > 0) {
                        // then that means we have successfully found a parent area record that we can inherit from. So we set
                        // out current area to inherit from that COLLECTION ID (not area ID - from the collection ID)
                        $db->query(
                            "update Areas set arInheritPermissionsFromAreaOnCID = ? where arID = ?",
                            array($row['cID'], $this->getAreaID())
                        );
                        $this->arInheritPermissionsFromAreaOnCID = $row['cID'];
                    }
                }
            } else {
                if ($areac->getCollectionInheritance() == 'TEMPLATE') {
                    // we grab an area on the master collection (if it exists)
                    $doOverride = $db->getOne(
                        "select arOverrideCollectionPermissions from Pages c inner join Areas a on (c.cID = a.cID) where c.cID = ? and a.arHandle = ?",
                        array($areac->getPermissionsCollectionID(), $this->getAreaHandle())
                    );
                    if ($doOverride && $areac->getPermissionsCollectionID() > 0) {
                        $db->query(
                            "update Areas set arInheritPermissionsFromAreaOnCID = ? where arID = ?",
                            array($areac->getPermissionsCollectionID(), $this->getAreaID())
                        );
                        $this->arInheritPermissionsFromAreaOnCID = $areac->getPermissionsCollectionID();
                    }
                }
            }
        }
    }

	/**
     * works a lot like rescanAreaPermissionsChain() but it works down. This is typically only
     * called when we update an area to have specific permissions, and all areas that are on pagesbelow it with the same
     * handle, etc... should now inherit from it.
     * @return void
     */
	function rescanSubAreaPermissions($cIDToCheck = null)
    {
        // works a lot like rescanAreaPermissionsChain() but it works down. This is typically only
        // called when we update an area to have specific permissions, and all areas that are on pagesbelow it with the same
        // handle, etc... should now inherit from it.
        $db = Loader::db();
        if (!$cIDToCheck) {
            $cIDToCheck = $this->getCollectionID();
        }

        $v = array($this->getAreaHandle(), 'PARENT', $cIDToCheck);
        $r = $db->query(
            "select Areas.arID, Areas.cID from Areas inner join Pages on (Areas.cID = Pages.cID) where Areas.arHandle = ? and cInheritPermissionsFrom = ? and arOverrideCollectionPermissions = 0 and cParentID = ?",
            $v
        );
        while ($row = $r->fetchRow()) {
            // these are all the areas we need to update.
            if ($this->getAreaCollectionInheritID() > 0) {
                $db->query(
                    "update Areas set arInheritPermissionsFromAreaOnCID = ? where arID = ?",
                    array($this->getAreaCollectionInheritID(), $row['arID'])
                );
                $this->rescanSubAreaPermissions($row['cID']);
            }
        }
    }

	/**
     * similar to rescanSubAreaPermissions, but for those who have setup their pages to inherit master collection permissions
     * @see Area::rescanSubAreaPermissions()
     * @return void
     */
	function rescanSubAreaPermissionsMasterCollection($masterCollection)
    {
        // like above, but for those who have setup their pages to inherit master collection permissions
        // this might make more sense in the collection class, but I'm putting it here
        if (!$masterCollection->isMasterCollection()) {
            return false;
        }

        // if we're not overriding permissions on the master collection then we set the ID to zero. If we are, then we set it to our own ID
        $toSetCID = ($this->overrideCollectionPermissions()) ? $masterCollection->getCollectionID() : 0;

        $db = Loader::db();
        $v = array($this->getAreaHandle(), 'TEMPLATE', $masterCollection->getCollectionID());
        $db->query(
            "update Areas, Pages set Areas.arInheritPermissionsFromAreaOnCID = " . $toSetCID . " where Areas.cID = Pages.cID and Areas.arHandle = ? and cInheritPermissionsFrom = ? and arOverrideCollectionPermissions = 0 and cInheritPermissionsFromCID = ?",
            $v
        );
    }

	public static function getOrCreate($c, $arHandle)
    {
        $area = Area::get($c, $arHandle);
        if (!is_object($area)) {
            $a = new Area($arHandle);
            $area = $a->create($c, $arHandle);
        }
        return $area;
    }

	public function load($c)
    {
        if (!$this->arIsLoaded) {
            // replaces the current empty object with the passed object.
            $area = self::get($c, $this->arHandle);
            if (!is_object($area)) {
                $area = $this->create($c, $this->arHandle);
            }
            $this->c = $c;
            $this->areaBlocksArray = $this->getAreaBlocks();
            $this->arIsLoaded = true;
            $this->arOverrideCollectionPermissions = $area->overrideCollectionPermissions();
            $this->arInheritPermissionsFromAreaOnCID = $area->getAreaCollectionInheritID();
            $this->arID = $area->getAreaID();
        }
    }

	protected function getAreaBlocks()
    {
        $blocksTmp = $this->c->getBlocks($this->arHandle);
        $currentPage = Page::getCurrentPage();
        $blocks = array();
        foreach ($blocksTmp as $ab) {
            $ab->setBlockAreaObject($this);
            if (is_object($currentPage) && $currentPage->getCollectionID() != $this->c->getCollectionID()) {
                // this is useful for rendering areas from one page
                // onto the next and including interactive elements
                $ab->setBlockActionCollectionID($this->c->getCollectionID());
            }
            $blocks[] = $ab;
        }
        return $blocks;
    }

	/**
     * displays the Area in the page
     * ex: $a = new Area('Main'); $a->display($c);
     * @param Page|Collection $c
     * @param Block[] $alternateBlockArray optional array of blocks to render instead of default behavior
     * @return void
     */
	function display($c, $alternateBlockArray = null)
    {

        $v = View::getRequestInstance();

        if (!is_object($c) || $c->isError()) {
            return false;
        }

        $this->load($c);
        $ap = new Permissions($this);
        if (!$ap->canViewArea()) {
            return false;
        }

        $blocksToDisplay = ($alternateBlockArray) ? $alternateBlockArray : $this->getAreaBlocksArray();


        $u = new User();

        // now, we iterate through these block groups (which are actually arrays of block objects), and display them on the page
        if ($this->showControls && $c->isEditMode() && $ap->canViewAreaControls()) {
            Loader::element('block_area_header', array('a' => $this));
        } else {
            Loader::element('block_area_header_view', array('a' => $this));
        }

        foreach ($blocksToDisplay as $b) {
            $bv = new BlockView($b);
            $bv->setAreaObject($this);
            $p = new Permissions($b);
            if ($p->canViewBlock()) {
                if (!$c->isEditMode()) {
                    echo $this->enclosingStart;
                }
                $bv->render('view');
                if (!$c->isEditMode()) {
                    echo $this->enclosingEnd;
                }
            }
        }

        if ($this->showControls && $c->isEditMode() && $ap->canViewAreaControls()) {
            Loader::element('block_area_footer', array('a' => $this));
        } else {
            Loader::element('block_area_footer_view', array('a' => $this));
        }
    }

	/**
     * Exports the area to content format
     * @todo need more documentation export?
     */
	public function export($p, $page)
    {
        $area = $p->addChild('area');
        $area->addAttribute('name', $this->getAreaHandle());
        $blocks = $page->getBlocks($this->getAreaHandle());
        $c = $this->getAreaCollectionObject();
        $style = $c->getAreaCustomStyle($this);
        if (is_object($style)) {
            $set = $style->getStyleSet();
            $set->export($area);
        }
        $wrapper = $area->addChild('blocks');
        foreach ($blocks as $bl) {
            $bl->export($wrapper);
        }
    }


	/**
     * Specify HTML to automatically print before blocks contained within the area
     * @param string $html
     * @return void
     */
	function setBlockWrapperStart($html)
    {
        $this->enclosingStart = $html;
    }

	/**
     * Set HTML that automatically prints after any blocks contained within the area
     * @param string $html
     * @return void
     */
	function setBlockWrapperEnd($html)
    {
        $this->enclosingEnd = $html;
    }

	public function overridePagePermissions()
    {
        $db = Loader::db();
        $cID = $this->getCollectionID();
        $v = array($cID, $this->getAreaHandle());
        // update the Area record itself. Hopefully it's been created.
        $db->query(
            "update Areas set arOverrideCollectionPermissions = 1, arInheritPermissionsFromAreaOnCID = 0 where arID = ?",
            array($this->getAreaID())
        );

        // copy permissions from the page to the area
        $permissions = PermissionKey::getList('area');
        foreach ($permissions as $pk) {
            $pk->setPermissionObject($this);
            $pk->copyFromPageToArea();
        }

        // finally, we rescan subareas so that, if they are inheriting up the tree, they inherit from this place
        $this->arInheritPermissionsFromAreaOnCID = $this->getCollectionID(
        ); // we don't need to actually save this on the area, but we need it for the rescan function
        $this->arOverrideCollectionPermissions = 1; // to match what we did above - useful for the rescan functions below

        $acobj = $this->getAreaCollectionObject();
        if ($acobj->isMasterCollection()) {
            // if we're updating the area on a master collection we need to go through to all areas set on subpages that aren't set to override to change them to inherit from this area
            $this->rescanSubAreaPermissionsMasterCollection($acobj);
        } else {
            $this->rescanSubAreaPermissions();
        }
    }

    /**
     * @deprecated
     * This no longer functions. This functionality is now located at the theme level.
     * sets a custom block template for blocks of a type specified by the btHandle
     * @param string $btHandle handle for the block type
     * @param string $view string identifying the block template ex: 'templates/breadcrumb.php'
     */
    public function setCustomTemplate($btHandle, $view)
    {
        $this->customTemplateArray[$btHandle] = $view;
    }



}
