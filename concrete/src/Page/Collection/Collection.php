<?php

namespace Concrete\Core\Page\Collection;

use Area;
use Block;
use CacheLocal;
use CollectionVersion;
use Concrete\Core\Area\CustomStyle as AreaCustomStyle;
use Concrete\Core\Area\GlobalArea;
use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Block\CustomStyle as BlockCustomStyle;
use Concrete\Core\Entity\Attribute\Value\PageValue;
use Concrete\Core\Feature\Assignment\CollectionVersionAssignment as CollectionVersionFeatureAssignment;
use Concrete\Core\Feature\Feature;
use Concrete\Core\Foundation\ConcreteObject;
use Concrete\Core\Gathering\Item\Page as PageGatheringItem;
use Concrete\Core\Page\Collection\Version\VersionList;
use Concrete\Core\Page\Search\IndexedSearch;
use Concrete\Core\Search\Index\IndexManagerInterface;
use Concrete\Core\Statistics\UsageTracker\TrackableInterface;
use Concrete\Core\StyleCustomizer\Inline\StyleSet;
use Concrete\Core\Support\Facade\Application;
use Config;
use Loader;
use Page;
use PageCache;
use Permissions;
use Stack;
use User;

class Collection extends ConcreteObject implements TrackableInterface
{
    /**
     * The collection ID.
     *
     * @deprecated Use getCollectionID (what's deprecated is the public part)
     *
     * @var int|null
     */
    public $cID;

    /**
     * The collection version object.
     *
     * @var \Concrete\Core\Page\Collection\Version\Version|null
     */
    protected $vObj;

    /**
     * The collection handle.
     *
     * @var string|null
     */
    protected $cHandle;

    /**
     * The date/time when the collection has been created.
     *
     * @var string|null
     *
     * @example 2017-12-31 23:59:59
     */
    protected $cDateAdded;

    /**
     * The date/time when the collection was last modified.
     *
     * @var string|null
     *
     * @example 2017-12-31 23:59:59
     */
    protected $cDateModified;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * Destruct the class instance.
     */
    public function __destruct()
    {
        unset($this->attributes);
        unset($this->vObj);
    }

    /**
     * Get a collection by ID.
     *
     * @param int $cID The collection ID
     * @param string|int|false $version the collection version ('RECENT' for the most recent version, 'ACTIVE' for the currently published version, a falsy value to not load the collection version, or an integer to retrieve a specific version ID)
     *
     * @return Collection If the collection is not found, you'll get an empty Collection instance
     */
    public static function getByID($cID, $version = 'RECENT')
    {
        $db = Loader::db();
        $q = 'select Collections.cDateAdded, Collections.cDateModified, Collections.cID from Collections where cID = ?';
        $row = $db->getRow($q, [$cID]);

        $c = new self();
        $c->setPropertiesFromArray($row);

        if ($version != false) {
            // we don't do this on the front page
            $c->loadVersionObject($version);
        }

        return $c;
    }

    /**
     * Get a Collection by handle (provided that it's not a Page handle).
     * If there's there's no collection with the specified handle, a new Collection will be created.
     *
     * @param string $handle the collection handle
     *
     * @return \Concrete\Core\Page\Collection\Collection|null return NULL if $handle is the handle of an existing page
     */
    public static function getByHandle($handle)
    {
        $db = Loader::db();

        // first we ensure that this does NOT appear in the Pages table. This is not a page. It is more basic than that
        $r = $db->query(
            'select Collections.cID, Pages.cID as pcID from Collections left join Pages on Collections.cID = Pages.cID where Collections.cHandle = ?',
            [$handle]
            );
        if ($r->numRows() == 0) {
            // there is nothing in the collections table for this page, so we create and grab

            $data = [
                'handle' => $handle,
            ];
            $cObj = self::createCollection($data);
        } else {
            $row = $r->fetchRow();
            if ($row['cID'] > 0 && $row['pcID'] == null) {
                // there is a collection, but it is not a page. so we grab it
                $cObj = self::getByID($row['cID']);
            }
        }

        if (isset($cObj)) {
            return $cObj;
        }
    }

    /**
     * (Re)Index all the collections that are marked as to be (re)indexed.
     *
     * @return int returns the number of reindexed pages
     */
    public static function reindexPendingPages()
    {
        $app = Application::getFacadeApplication();

        $indexStack = $app->make(IndexManagerInterface::class);

        $num = 0;
        $db = $app['database']->connection();
        $r = $db->execute('select cID from PageSearchIndex where cRequiresReindex = 1');
        while ($id = $r->fetchColumn()) {
            $indexStack->index(\Concrete\Core\Page\Page::class, $id);
        }
        Config::save('concrete.misc.do_page_reindex_check', false);

        return $num;
    }

    /**
     * Create a new Collection instance.
     *
     * @param array $data {
     *
     *     @var int|null $cID The ID of the collection to create (if unspecified or NULL: database autoincrement value)
     *     @var string $handle The collection handle (default: NULL)
     *     @var string $name The collection name (default: empty string)
     *     @var string $cDescription The collection description (default: NULL)
     *     @var string $cDatePublic The collection publish date/time in format 'YYYY-MM-DD hh:mm:ss' (default: now)
     *     @var bool $cvIsApproved Is the collection version approved (default: true)
     *     @var bool $cvIsNew Is the collection to be considered "new"? (default: true if $cvIsApproved is false, false if $cvIsApproved is true)
     *     @var int|null $pThemeID The collection theme ID (default: NULL)
     *     @var int|null $pTemplateID The collection template ID (default: NULL)
     *     @var int|null $uID The ID of the collection author (default: NULL)
     * }
     *
     * @return \Concrete\Core\Page\Collection\Collection
     */
    public static function createCollection($data)
    {
        $db = Loader::db();
        $dh = Loader::helper('date');
        $cDate = $dh->getOverridableNow();

        $data = array_merge(
            [
                'name' => '',
                'pTemplateID' => 0,
                'handle' => null,
                'uID' => null,
                'cDatePublic' => $cDate,
                'cDescription' => null,
            ],
            $data
            );

        $cDatePublic = ($data['cDatePublic']) ? $data['cDatePublic'] : $cDate;

        if (isset($data['cID'])) {
            $res = $db->query(
                'insert into Collections (cID, cHandle, cDateAdded, cDateModified) values (?, ?, ?, ?)',
                [$data['cID'], $data['handle'], $cDate, $cDate]
                );
            $newCID = $data['cID'];
        } else {
            $res = $db->query(
                'insert into Collections (cHandle, cDateAdded, cDateModified) values (?, ?, ?)',
                [$data['handle'], $cDate, $cDate]
                );
            $newCID = $db->Insert_ID();
        }

        $cvIsApproved = (isset($data['cvIsApproved']) && $data['cvIsApproved'] == 0) ? 0 : 1;
        $cvIsNew = 1;
        if ($cvIsApproved) {
            $cvIsNew = 0;
        }
        if (isset($data['cvIsNew'])) {
            $cvIsNew = $data['cvIsNew'];
        }
        $data['name'] = Loader::helper('text')->sanitize($data['name']);
        $pThemeID = 0;
        if (isset($data['pThemeID']) && $data['pThemeID']) {
            $pThemeID = $data['pThemeID'];
        }

        $pTemplateID = 0;
        if ($data['pTemplateID']) {
            $pTemplateID = $data['pTemplateID'];
        }

        if ($res) {
            // now we add a pending version to the collectionversions table
            $v2 = [
                $newCID,
                1,
                $pTemplateID,
                $data['name'],
                $data['handle'],
                $data['cDescription'],
                $cDatePublic,
                $cDate,
                t(VERSION_INITIAL_COMMENT),
                $data['uID'],
                $cvIsApproved,
                $cvIsNew,
                $pThemeID,
            ];
            $q2 = 'insert into CollectionVersions (cID, cvID, pTemplateID, cvName, cvHandle, cvDescription, cvDatePublic, cvDateCreated, cvComments, cvAuthorUID, cvIsApproved, cvIsNew, pThemeID) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $r2 = $db->prepare($q2);
            $res2 = $db->execute($r2, $v2);
        }

        $nc = self::getByID($newCID);

        return $nc;
    }

    /**
     * Get the collection ID.
     *
     * @return int|null
     */
    public function getCollectionID()
    {
        return $this->cID;
    }

    /**
     * Get the collection handle.
     *
     * @return string|null
     */
    public function getCollectionHandle()
    {
        return $this->cHandle;
    }

    /**
     * Get the date/time when the collection was last modified.
     *
     * @return string|null
     *
     * @example 2017-12-31 23:59:59
     */
    public function getCollectionDateLastModified()
    {
        return $this->cDateModified;
    }

    /**
     * Get the date/time when the collection has been created.
     *
     * @return string|null
     *
     * @example 2017-12-31 23:59:59
     */
    public function getCollectionDateAdded()
    {
        return $this->cDateAdded;
    }

    /**
     * Get the ID of the currently loaded version.
     *
     * @return int
     */
    public function getVersionID()
    {
        return $this->vObj->cvID;
    }

    /**
     * Get the currently loaded version object.
     *
     * @return \Concrete\Core\Page\Collection\Version\Version|null
     */
    public function getVersionObject()
    {
        return $this->vObj;
    }

    /**
     * Create a new Collection instance, using the same theme as this instance (if it's a Page instance).
     *
     * @param array $data {
     *
     *     @var int|null $cID The ID of the collection to create (if unspecified or NULL: database autoincrement value)
     *     @var string $handle The collection handle (default: NULL)
     *     @var string $name The collection name (default: empty string)
     *     @var string $cDescription The collection description (default: NULL)
     *     @var string $cDatePublic The collection publish date/time in format 'YYYY-MM-DD hh:mm:ss' (default: now)
     *     @var bool $cvIsApproved Is the collection version approved (default: true)
     *     @var bool $cvIsNew Is the collection to be considered "new"? (default: true if $cvIsApproved is false, false if $cvIsApproved is true)
     *     @var int|null $pTemplateID The collection template ID (default: NULL)
     *     @var int|null $uID The ID of the collection author (default: NULL)
     * }
     *
     * @return \Concrete\Core\Page\Collection\Collection
     */
    public function addCollection($data)
    {
        $data['pThemeID'] = 0;
        if (isset($this) && $this instanceof Page) {
            $data['pThemeID'] = $this->getCollectionThemeID();
        }

        return static::createCollection($data);
    }

    /**
     * Load a specific collection version (you can retrieve it with the getVersionObject() method).
     *
     * @param string|int $cvID the collection version ('RECENT' for the most recent version, 'ACTIVE' for the currently published version, or an integer to retrieve a specific version ID)
     */
    public function loadVersionObject($cvID = 'ACTIVE')
    {
        $this->vObj = CollectionVersion::get($this, $cvID);
    }

    /**
     * Get the Collection instance to be modified (this instance if it's a new or master Collection, a clone otherwise).
     *
     * @return $this|\Concrete\Core\Page\Page
     */
    public function getVersionToModify()
    {
        $u = new User();
        $vObj = $this->getVersionObject();
        if ($this->isMasterCollection() || ($vObj->isNew())) {
            return $this;
        } else {
            $nc = $this->cloneVersion(null);

            return $nc;
        }
    }

    /**
     * Get the automatic comment for the next collection version.
     *
     * @return string Example: 'Version 2'
     */
    public function getNextVersionComments()
    {
        $c = Page::getByID($this->getCollectionID(), 'ACTIVE');
        $cvID = $c->getVersionID();

        return t('Version %d', $cvID + 1);
    }

    /**
     * (Re)Index the contents of this collection (or mark the collection as to be reindexed if $actuallyDoReindex is falsy and the concrete.page.search.always_reindex configuration key is falsy).
     *
     * @param \Concrete\Core\Page\Search\IndexedSearch|false $index the IndexedSearch instance that indexes the collection content (if falsy: we'll create a new instance of it)
     * @param bool $actuallyDoReindex Set to true to always reindex the collection immediately (otherwise we'll look at the concrete.page.search.always_reindex configuration key)
     *
     * @return bool|null Return false if the collection can't be indexed, NULL otherwise
     */
    public function reindex($index = false, $actuallyDoReindex = true)
    {
        if ($this->isAlias() && !$this->isExternalLink()) {
            return false;
        }
        if ($actuallyDoReindex || Config::get('concrete.page.search.always_reindex') == true) {
            // Retrieve the attribute values for the current page
            $category = \Core::make('Concrete\Core\Attribute\Category\PageCategory');
            $indexer = $category->getSearchIndexer();
            $values = $category->getAttributeValues($this);
            foreach ($values as $value) {
                $indexer->indexEntry($category, $value, $this);
            }

            if ($index == false) {
                $index = new IndexedSearch();
            }

            $index->reindexPage($this);

            $db = \Database::connection();
            $db->Replace(
               'PageSearchIndex',
               ['cID' => $this->getCollectionID(), 'cRequiresReindex' => 0],
               ['cID'],
               false
            );

            $cache = PageCache::getLibrary();
            $cache->purge($this);

            // we check to see if this page is referenced in any gatherings
            $c = Page::getByID($this->getCollectionID(), $this->getVersionID());
            $items = PageGatheringItem::getListByItem($c);
            foreach ($items as $it) {
                $it->deleteFeatureAssignments();
                $it->assignFeatureAssignments($c);
            }
        } else {
            $db = Loader::db();
            Config::save('concrete.misc.do_page_reindex_check', true);
            $db->Replace(
               'PageSearchIndex',
               ['cID' => $this->getCollectionID(), 'cRequiresReindex' => 1],
               ['cID'],
               false
            );
        }
    }

    /**
     * Set the attribute value for the currently loaded collection version.
     *
     * @param string|\Concrete\Core\Attribute\Key\CollectionKey $ak the attribute key (or its handle)
     * @param \Concrete\Core\Entity\Attribute\Value\Value\AbstractValue|mixed $value an attribute value object, or the data needed by the attribute controller to create the attribute value object
     *
     * @return \Concrete\Core\Entity\Attribute\Value\PageValue
     */
    public function setAttribute($ak, $value)
    {
        return $this->vObj->setAttribute($ak, $value);
    }

    /**
     * Return the value of the attribute with the handle $akHandle of the currently loaded version (if it's loaded).
     *
     * @param string|\Concrete\Core\Attribute\Key\CollectionKey $akHandle the attribute key (or its handle)
     * @param string|false $displayMode makes The format of the attribute value
     *
     * @return mixed|null
     *
     * @example When you need the raw attribute value or object, use this:
     * <code>
     * $c = Page::getCurrentPage();
     * $attributeValue = $c->getAttribute('attribute_handle');
     * </code>
     * @example If you need the formatted output supported by some attribute, use this:
     * <code>
     * $c = Page::getCurrentPage();
     * $attributeValue = $c->getAttribute('attribute_handle', 'display');
     * </code>
     * @example An attribute type like "date" will then return the date in the correct format just like other attributes will show you a nicely formatted output and not just a simple value or object.
     */
    public function getAttribute($akHandle, $displayMode = false)
    {
        if (is_object($this->vObj)) {
            return $this->vObj->getAttribute($akHandle, $displayMode);
        }
    }

    /**
     * Return the attribute value object with the handle $akHandle of the currently loaded version (if it's loaded).
     *
     * @param string|\Concrete\Core\Attribute\Key\CollectionKey $akHandle the attribute key (or its handle)
     *
     * @return \Concrete\Core\Entity\Attribute\Value\PageValue|null
     */
    public function getAttributeValueObject($akHandle, $createIfNotExists = false)
    {
        if (is_object($this->vObj)) {
            return $this->vObj->getAttributeValue($akHandle);
        }
    }

    /**
     * Delete the values of the attributes associated to the currently loaded collection version.
     *
     * @param int[] $retainAKIDs a list of attribute key IDs to keep (their values won't be deleted)
     */
    public function clearCollectionAttributes($retainAKIDs = [])
    {
        $db = Loader::db();
        if (count($retainAKIDs) > 0) {
            $cleanAKIDs = [];
            foreach ($retainAKIDs as $akID) {
                $cleanAKIDs[] = (int) $akID;
            }
            $akIDStr = implode(',', $cleanAKIDs);
            $v2 = [$this->getCollectionID(), $this->getVersionID()];
            $db->query(
                "delete from CollectionAttributeValues where cID = ? and cvID = ? and akID not in ({$akIDStr})",
                $v2
            );
        } else {
            $v2 = [$this->getCollectionID(), $this->getVersionID()];
            $db->query('delete from CollectionAttributeValues where cID = ? and cvID = ?', $v2);
        }
        $this->reindex();
    }

    /**
     * Delete the value of a specific attribute key associated to the currently loaded collection version.
     *
     * @param string|\Concrete\Core\Attribute\Key\CollectionKey $ak the attribute key (or its handle)
     */
    public function clearAttribute($ak)
    {
        $this->vObj->clearAttribute($ak);
    }

    /**
     * Get the list of attribute keys for which the currently loaded collection version has values.
     *
     * @return \Concrete\Core\Entity\Attribute\Key\PageKey[]
     */
    public function getSetCollectionAttributes()
    {
        $category = $this->vObj->getObjectAttributeCategory();
        $values = $category->getAttributeValues($this->vObj);
        $attribs = [];
        foreach ($values as $value) {
            $attribs[] = $value->getAttributeKey();
        }

        return $attribs;
    }

    /**
     * Get an existing area associated to this collection.
     *
     * @param string $arHandle the handle of the area
     *
     * @return Area|null
     */
    public function getArea($arHandle)
    {
        return Area::get($this, $arHandle);
    }

    /**
     * Does this collection contain blocks that are aliased in other pages?
     *
     * @return bool
     */
    public function hasAliasedContent()
    {
        $db = Loader::db();
        // aliased content is content on the particular page that is being
        // used elsewhere - but the content on the PAGE is the original version
        $v = [$this->cID];
        $q = 'select bID from CollectionVersionBlocks where cID = ? and isOriginal = 1';
        $r = $db->query($q, $v);
        $bIDArray = [];
        if ($r) {
            while ($row = $r->fetchRow()) {
                $bIDArray[] = $row['bID'];
            }
            if (count($bIDArray) > 0) {
                $bIDList = implode(',', $bIDArray);
                $v2 = [$bIDList, $this->cID];
                $q2 = 'select cID from CollectionVersionBlocks where bID in (?) and cID <> ? limit 1';
                $aliasedCID = $db->getOne($q2, $v2);
                if ($aliasedCID > 0) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get the custom style of an area in the currently loaded collection version.
     *
     * @param \Concrete\Core\Area\Area $area the area for which you want the custom styles
     * @param bool $force Set to true to retrieve a CustomStyle even if the area does not define any custom style
     *
     * @return \Concrete\Core\Area\CustomStyle|null return NULL if the area does not have any custom style and $force is false, a CustomStyle instance otherwise
     */
    public function getAreaCustomStyle($area, $force = false)
    {
        $areac = $area->getAreaCollectionObject();
        if ($areac instanceof Stack) {
            // this fixes the problem of users applying design to the main area on the page, and then that trickling into any
            // stacks that have been added to other areas of the page.
            return null;
        }
        $result = null;
        $styles = $this->vObj->getCustomAreaStyles();
        $areaHandle = $area->getAreaHandle();
        if ($force || isset($styles[$areaHandle])) {
            $pss = isset($styles[$areaHandle]) ? StyleSet::getByID($styles[$areaHandle]) : null;
            $result = new AreaCustomStyle($pss, $area, $this->getCollectionThemeObject());
        }

        return $result;
    }

    /**
     * Set the custom style of an area in the currently loaded collection version.
     *
     * @param \Concrete\Core\Area\Area $area
     * @param \Concrete\Core\Entity\StyleCustomizer\Inline\StyleSet $set
     */
    public function setCustomStyleSet($area, $set)
    {
        $db = Loader::db();
        $db->Replace(
            'CollectionVersionAreaStyles',
            [
                'cID' => $this->getCollectionID(),
                'cvID' => $this->getVersionID(),
                'arHandle' => $area->getAreaHandle(),
                'issID' => $set->getID(),
            ],
            ['cID', 'cvID', 'arHandle'],
            true
            );
    }

    /**
     * Delete all the custom styles of an area of the currently loaded collection version.
     *
     * @param \Concrete\Core\Area\Area $area
     */
    public function resetAreaCustomStyle($area)
    {
        $db = Loader::db();
        $db->Execute(
            'delete from CollectionVersionAreaStyles where cID = ? and cvID = ? and arHandle = ?',
            [
                $this->getCollectionID(),
                $this->getVersionID(),
                $area->getAreaHandle(),
            ]
            );
    }

    /**
     * Retrieve all custom style rules that should be inserted into the header on a page, whether they are defined in areas or blocks.
     *
     * @param bool $return set to true to return the HTML that defines the styles, false to add it to the current View instance
     *
     * @return string|null
     */
    public function outputCustomStyleHeaderItems($return = false)
    {
        if (!Config::get('concrete.design.enable_custom')) {
            return '';
        }

        $db = Loader::db();
        $psss = [];
        $txt = Loader::helper('text');
        CacheLocal::set('pssCheck', $this->getCollectionID() . ':' . $this->getVersionID(), true);

        $r1 = $db->GetAll(
                 'select bID, arHandle, issID from CollectionVersionBlockStyles where cID = ? and cvID = ? and issID > 0',
                 [$this->getCollectionID(), $this->getVersionID()]
        );
        $r2 = $db->GetAll(
                 'select arHandle, issID from CollectionVersionAreaStyles where cID = ? and cvID = ? and issID > 0',
                 [$this->getCollectionID(), $this->getVersionID()]
        );
        foreach ($r1 as $r) {
            $issID = $r['issID'];
            $arHandle = $txt->filterNonAlphaNum($r['arHandle']);
            $bID = $r['bID'];
            $obj = StyleSet::getByID($issID);
            if (is_object($obj)) {
                $b = new Block();
                $b->bID = $bID;
                $a = new Area($arHandle);
                $b->setBlockAreaObject($a);
                $obj = new BlockCustomStyle($obj, $b, $this->getCollectionThemeObject());
                $psss[] = $obj;
                CacheLocal::set(
                          'pssObject',
                          $this->getCollectionID() . ':' . $this->getVersionID() . ':' . $r['arHandle'] . ':' . $r['bID'],
                          $obj
                );
            }
        }

        foreach ($r2 as $r) {
            $issID = $r['issID'];
            $obj = StyleSet::getByID($issID);
            if (is_object($obj)) {
                $a = new Area($r['arHandle']);
                $obj = new AreaCustomStyle($obj, $a, $this->getCollectionThemeObject());
                $psss[] = $obj;
                CacheLocal::set(
                          'pssObject',
                          $this->getCollectionID() . ':' . $this->getVersionID() . ':' . $r['arHandle'],
                          $obj
                );
            }
        }

        // grab all the header block style rules for items in global areas on this page
        $rs = $db->GetCol(
                 'select arHandle from Areas where arIsGlobal = 1 and cID = ?',
                 [$this->getCollectionID()]
        );
        if (count($rs) > 0) {
            $pcp = new Permissions($this);
            foreach ($rs as $garHandle) {
                if ($pcp->canViewPageVersions()) {
                    $s = Stack::getByName($garHandle, 'RECENT');
                } else {
                    $s = Stack::getByName($garHandle, 'ACTIVE');
                }
                if (is_object($s)) {
                    CacheLocal::set('pssCheck', $s->getCollectionID() . ':' . $s->getVersionID(), true);
                    $rs1 = $db->GetAll(
                              'select bID, issID, arHandle from CollectionVersionBlockStyles where cID = ? and cvID = ? and issID > 0',
                              [$s->getCollectionID(), $s->getVersionID()]
                    );
                    foreach ($rs1 as $r) {
                        $issID = $r['issID'];
                        $obj = StyleSet::getByID($issID);
                        if (is_object($obj)) {
                            $b = new Block();
                            $b->bID = $r['bID'];
                            $a = new GlobalArea($garHandle);
                            $b->setBlockAreaObject($a);
                            $obj = new BlockCustomStyle($obj, $b, $this->getCollectionThemeObject());
                            $psss[] = $obj;
                            CacheLocal::set(
                                      'pssObject',
                                      $s->getCollectionID() . ':' . $s->getVersionID() . ':' . $r['arHandle'] . ':' . $r['bID'],
                                      $obj
                            );
                        }
                    }
                }
            }
        }

        $styleHeader = '';
        foreach ($psss as $st) {
            $css = $st->getCSS();
            if ($css !== '') {
                $styleHeader .= $st->getStyleWrapper($css);
            }
        }

        if (strlen(trim($styleHeader))) {
            if ($return == true) {
                return $styleHeader;
            } else {
                $v = \View::getInstance();
                $v->addHeaderItem($styleHeader);
            }
        }
    }

    /**
     * Associate the edits of another collection to this collection.
     *
     * @param \Concrete\Core\Page\Collection\Collection $oc the collection that has been modified
     *
     * @return null|false return false if the other collection is already associated to this collection, NULL otherwise
     *
     * @example If a global area is modified inside this collection, you need to call $page->relateVersionEdits($globalArea)
     */
    public function relateVersionEdits($oc)
    {
        $db = Loader::db();
        $v = [
            $this->getCollectionID(),
            $this->getVersionID(),
            $oc->getCollectionID(),
            $oc->getVersionID(),
        ];
        $r = $db->GetOne(
                'select count(*) from CollectionVersionRelatedEdits where cID = ? and cvID = ? and cRelationID = ? and cvRelationID = ?',
                $v
        );
        if ($r > 0) {
            return false;
        } else {
            $db->Execute(
               'insert into CollectionVersionRelatedEdits (cID, cvID, cRelationID, cvRelationID) values (?, ?, ?, ?)',
               $v
            );
        }
    }

    /**
     * Returns the ID of the page/collection type.
     *
     * @return int|false
     */
    public function getPageTypeID()
    {
        return false;
    }

    /**
     * Empty the collection-related cache.
     */
    public function refreshCache()
    {
        CacheLocal::flush();
    }

    /**
     * Get the blocks contained in the all the global areas.
     *
     * @return \Concrete\Core\Block\Block[]
     */
    public function getGlobalBlocks()
    {
        $db = Loader::db();
        $v = [Stack::ST_TYPE_GLOBAL_AREA];
        $rs = $db->GetCol('select stName from Stacks where Stacks.stType = ?', $v);
        $blocks = [];
        if (count($rs) > 0) {
            $pcp = new Permissions($this);
            foreach ($rs as $garHandle) {
                if ($pcp->canViewPageVersions()) {
                    $s = Stack::getByName($garHandle, 'RECENT');
                } else {
                    $s = Stack::getByName($garHandle, 'ACTIVE');
                }
                if (is_object($s)) {
                    $blocksTmp = $s->getBlocks(STACKS_AREA_NAME);
                    $blocks = array_merge($blocks, $blocksTmp);
                }
            }
        }

        return $blocks;
    }

    /**
     * List the blocks in the currently loaded collection version (or in a specific area within it).
     *
     * @param string|false $arHandle The handle if the area (or falsy to get all the blocks in the collection)
     *
     * @return \Concrete\Core\Block\Block[]
     */
    public function getBlocks($arHandle = false)
    {
        $blockIDs = $this->getBlockIDs($arHandle);

        $blocks = [];
        if (is_array($blockIDs)) {
            foreach ($blockIDs as $row) {
                $ab = Block::getByID($row['bID'], $this, $row['arHandle']);
                if (is_object($ab)) {
                    $blocks[] = $ab;
                }
            }
        }

        return $blocks;
    }

    /**
     * List the block IDs and the associated area handles in the currently loaded collection version (or in a specific area within it).
     *
     * @param string|false $arHandle The handle if the area (or falsy to get all the blocks in the collection version)
     *
     * @return array Return a list of arrays, each one is a dictionary like ['bID' => <block ID>, 'arHandle' => <area handle>]
     */
    public function getBlockIDs($arHandle = false)
    {
        $blockIDs = CacheLocal::getEntry(
                              'collection_block_ids',
                              $this->getCollectionID() . ':' . $this->getVersionID()
        );

        if (!is_array($blockIDs)) {
            $v = [$this->getCollectionID(), $this->getVersionID()];
            $db = Loader::db();
            $q = 'select Blocks.bID, CollectionVersionBlocks.arHandle from CollectionVersionBlocks inner join Blocks on (CollectionVersionBlocks.bID = Blocks.bID) inner join BlockTypes on (Blocks.btID = BlockTypes.btID) where CollectionVersionBlocks.cID = ? and (CollectionVersionBlocks.cvID = ? or CollectionVersionBlocks.cbIncludeAll=1) order by CollectionVersionBlocks.cbDisplayOrder asc';
            $r = $db->GetAll($q, $v);
            $blockIDs = [];
            if (is_array($r)) {
                foreach ($r as $bl) {
                    $blockIDs[strtolower($bl['arHandle'])][] = $bl;
                }
            }
            CacheLocal::set('collection_block_ids', $this->getCollectionID() . ':' . $this->getVersionID(), $blockIDs);
        }

        $result = [];
        if ($arHandle != false) {
            $key = strtolower($arHandle);
            if (isset($blockIDs[$key])) {
                $result = $blockIDs[$key];
            }
        } else {
            foreach ($blockIDs as $arHandle => $row) {
                foreach ($row as $brow) {
                    if (!in_array($brow, $blockIDs)) {
                        $result[] = $brow;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Add a new block to a specific area of the currently loaded collection version.
     *
     * @param \Concrete\Core\Entity\Block\BlockType\BlockType $bt the type of block to be added
     * @param string|\Concrete\Core\Area\Area $a the area instance (or its handle) to which the block should be added to
     * @param array $data The data of the block. This data depends on the specific block type. Common values are: 'uID' to specify the ID of the author (if not specified: we'll use the current user), 'bName' to specify the block name.
     *
     * @return \Concrete\Core\Block\Block
     */
    public function addBlock($bt, $a, $data)
    {
        $db = Loader::db();

        // first we add the block to the system
        $nb = $bt->add($data, $this, $a);

        // now that we have a block, we add it to the collectionversions table

        $arHandle = (is_object($a)) ? $a->getAreaHandle() : $a;
        $cID = $this->getCollectionID();
        $vObj = $this->getVersionObject();

        if ($bt->includeAll()) {
            // normally, display order is dependant on a per area, per version basis. However, since this block
            // is not aliased across versions, then we want to get display order simply based on area, NOT based
            // on area + version
            $newBlockDisplayOrder = $this->getCollectionAreaDisplayOrder(
                                         $arHandle,
                                         true
            ); // second argument is "ignoreVersions"
        } else {
            $newBlockDisplayOrder = $this->getCollectionAreaDisplayOrder($arHandle);
        }

        $cbRelationID = $db->GetOne('select max(cbRelationID) as cbRelationID from CollectionVersionBlocks');
        if (!$cbRelationID) {
            $cbRelationID = 1;
        } else {
            ++$cbRelationID;
        }

        $v = [
            $cID,
            $vObj->getVersionID(),
            $nb->getBlockID(),
            $arHandle,
            $cbRelationID,
            $newBlockDisplayOrder,
            1,
            (int) ($bt->includeAll()),
        ];
        $q = 'insert into CollectionVersionBlocks (cID, cvID, bID, arHandle, cbRelationID, cbDisplayOrder, isOriginal, cbIncludeAll) values (?, ?, ?, ?, ?, ?, ?, ?)';

        $res = $db->Execute($q, $v);

        $controller = $nb->getController();
        $features = $controller->getBlockTypeFeatureObjects();
        if (count($features) > 0) {
            foreach ($features as $fe) {
                $fd = $fe->getFeatureDetailObject($controller);
                $fa = CollectionVersionFeatureAssignment::add($fe, $fd, $this);
                $db->Execute(
                   'insert into BlockFeatureAssignments (cID, cvID, bID, faID) values (?, ?, ?, ?)',
                   [
                       $this->getCollectionID(),
                       $this->getVersionID(),
                       $nb->getBlockID(),
                       $fa->getFeatureAssignmentID(),
                   ]
                );
            }
        }

        return Block::getByID($nb->getBlockID(), $this, $a);
    }

    /**
     * Get the next value of the display order (to be used when adding new blocks to an area).
     *
     * @param string $arHandle The handle of the area
     * @param bool $ignoreVersions Set to true to ignore the collection version
     *
     * @return int
     */
    public function getCollectionAreaDisplayOrder($arHandle, $ignoreVersions = false)
    {
        $db = Loader::db();
        $cID = $this->cID;
        $cvID = $this->vObj->cvID;
        if ($ignoreVersions) {
            $q = 'select max(cbDisplayOrder) as cbdis from CollectionVersionBlocks where cID = ? and arHandle = ?';
            $v = [$cID, $arHandle];
        } else {
            $q = 'select max(cbDisplayOrder) as cbdis from CollectionVersionBlocks where cID = ? and cvID = ? and arHandle = ?';
            $v = [$cID, $cvID, $arHandle];
        }
        $r = $db->query($q, $v);
        if ($r) {
            if ($r->numRows() > 0) {
                // then we know we got a value; we increment it and return
                $res = $r->fetchRow();
                $displayOrder = $res['cbdis'];
                if ($displayOrder === null) {
                    return 0;
                }
                ++$displayOrder;

                return $displayOrder;
            } else {
                // we didn't get anything, so we return a zero
                return 0;
            }
        }
    }

    /**
     * Fix the display order properties for all the blocks within the collection/area.
     *
     * @param string $arHandle the handle of the area to be processed
     */
    public function rescanDisplayOrder($arHandle)
    {
        // this collection function f

        $db = Loader::db();
        $cID = $this->cID;
        $cvID = $this->vObj->cvID;
        $args = [$cID, $cvID, $arHandle];
        $q = 'select bID from CollectionVersionBlocks where cID = ? and cvID = ? and arHandle=? order by cbDisplayOrder asc';
        $r = $db->query($q, $args);

        if ($r) {
            $displayOrder = 0;
            while ($row = $r->fetchRow()) {
                $args = [$displayOrder, $cID, $cvID, $arHandle, $row['bID']];
                $q = 'update CollectionVersionBlocks set cbDisplayOrder = ? where cID = ? and cvID = ? and arHandle = ? and bID = ?';
                $db->query($q, $args);
                ++$displayOrder;
            }
            $r->free();
        }
    }

    /**
     * Associate a feature to the currently loaded collection version.
     *
     * @param Feature $fe
     */
    public function addFeature(Feature $fe)
    {
        $db = Loader::db();
        $db->Replace(
           'CollectionVersionFeatures',
           ['cID' => $this->getCollectionID(), 'cvID' => $this->getVersionID(), 'feID' => $fe->getFeatureID()],
           ['cID', 'cvID', 'feID'],
           true
        );
    }

    /**
     * Get the list of assigned features.
     *
     * @return \Concrete\Core\Feature\Assignment\CollectionVersionAssignment[]
     */
    public function getFeatureAssignments()
    {
        if (is_object($this->vObj)) {
            return CollectionVersionFeatureAssignment::getList($this);
        }

        return [];
    }

    /**
     * Update the last edit date/time.
     */
    public function markModified()
    {
        // marks this collection as newly modified
        $db = Loader::db();
        $dh = Loader::helper('date');
        $cDateModified = $dh->getOverridableNow();

        $v = [$cDateModified, $this->cID];
        $q = 'update Collections set cDateModified = ? where cID = ?';
        $r = $db->prepare($q);
        $res = $db->execute($r, $v);
    }

    /**
     * Delete this collection, and all its versions, contents and attributes.
     */
    public function delete()
    {
        if ($this->cID > 0) {
            $db = Loader::db();

            // First we delete all versions
            $vl = new VersionList($this);
            $vl->setItemsPerPage(-1);
            $vlArray = $vl->getPage();

            foreach ($vlArray as $v) {
                $v->delete();
            }

            $cID = $this->getCollectionID();

            $q = "delete from CollectionAttributeValues where cID = {$cID}";
            $db->query($q);

            $q = "delete from Collections where cID = '{$cID}'";
            $r = $db->query($q);

            try {
                $q = "delete from CollectionSearchIndexAttributes where cID = {$cID}";
                $db->query($q);
            } catch (\Exception $e) {
            }
        }
    }

    /**
     * Create a clone of this collection, and all its versions, contents and attributes.
     *
     * @return \Concrete\Core\Page\Collection\Collection
     */
    public function duplicateCollection()
    {
        $db = Loader::db();
        $dh = Loader::helper('date');
        $cDate = $dh->getOverridableNow();

        $v = [$cDate, $cDate, $this->cHandle];
        $r = $db->query('insert into Collections (cDateAdded, cDateModified, cHandle) values (?, ?, ?)', $v);
        $newCID = $db->Insert_ID();

        if ($r) {
            // first, we get the creation date of the active version in this collection
            //$q = "select cvDateCreated from CollectionVersions where cvIsApproved = 1 and cID = {$this->cID}";
            //$dcOriginal = $db->getOne($q);
            // now we create the query that will grab the versions we're going to copy

            $qv = "select * from CollectionVersions where cID = '{$this->cID}' order by cvDateCreated asc";

            // now we grab all of the current versions
            $rv = $db->query($qv);
            $cvList = [];
            while ($row = $rv->fetchRow()) {
                // insert
                $cvList[] = $row['cvID'];
                $cDate = date('Y-m-d H:i:s', strtotime($cDate) + 1);
                $vv = [
                    $newCID,
                    $row['cvID'],
                    $row['cvName'],
                    $row['cvHandle'],
                    $row['cvDescription'],
                    $row['cvDatePublic'],
                    $cDate,
                    $row['cvComments'],
                    $row['cvAuthorUID'],
                    $row['cvIsApproved'],
                    $row['pThemeID'],
                    $row['pTemplateID'],
                ];
                $qv = 'insert into CollectionVersions (cID, cvID, cvName, cvHandle, cvDescription, cvDatePublic, cvDateCreated, cvComments, cvAuthorUID, cvIsApproved, pThemeID, pTemplateID) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
                $db->query($qv, $vv);
            }

            $ql = "select * from CollectionVersionBlockStyles where cID = '{$this->cID}'";
            $rl = $db->query($ql);
            while ($row = $rl->fetchRow()) {
                $vl = [$newCID, $row['cvID'], $row['bID'], $row['arHandle'], $row['issID']];
                $ql = 'insert into CollectionVersionBlockStyles (cID, cvID, bID, arHandle, issID) values (?, ?, ?, ?, ?)';
                $db->query($ql, $vl);
            }
            $ql = "select * from CollectionVersionAreaStyles where cID = '{$this->cID}'";
            $rl = $db->query($ql);
            while ($row = $rl->fetchRow()) {
                $vl = [$newCID, $row['cvID'], $row['arHandle'], $row['issID']];
                $ql = 'insert into CollectionVersionAreaStyles (cID, cvID, arHandle, issID) values (?, ?, ?, ?)';
                $db->query($ql, $vl);
            }

            $ql = "select * from CollectionVersionThemeCustomStyles where cID = '{$this->cID}'";
            $rl = $db->query($ql);
            while ($row = $rl->fetchRow()) {
                $vl = [$newCID, $row['cvID'], $row['pThemeID'], $row['scvlID'], $row['preset'], $row['sccRecordID']];
                $ql = 'insert into CollectionVersionThemeCustomStyles (cID, cvID, pThemeID, scvlID, preset, sccRecordID) values (?, ?, ?, ?, ?, ?)';
                $db->query($ql, $vl);
            }

            $ql = "select * from CollectionVersionBlocksCacheSettings where cID = '{$this->cID}'";
            $rl = $db->query($ql);
            while ($row = $rl->fetchRow()) {
                $vl = [$newCID, $row['cvID'], $row['bID'], $row['arHandle'], $row['btCacheBlockOutput'], $row['btCacheBlockOutputOnPost'], $row['btCacheBlockOutputForRegisteredUsers'], $row['btCacheBlockOutputLifetime']];
                $ql = 'insert into CollectionVersionBlocksCacheSettings (cID, cvID, bID, arHandle, btCacheBlockOutput, btCacheBlockOutputOnPost, btCacheBlockOutputForRegisteredUsers, btCacheBlockOutputLifetime) values (?, ?, ?, ?, ?, ?, ?, ?)';
                $db->query($ql, $vl);
            }

            // now we grab all the blocks we're going to need
            $cvList = implode(',', $cvList);
            $q = "select bID, cvID, arHandle, cbDisplayOrder, cbOverrideAreaPermissions, cbIncludeAll, cbRelationID, cbOverrideBlockTypeCacheSettings, cbOverrideBlockTypeContainerSettings, cbEnableBlockContainer from CollectionVersionBlocks where cID = '{$this->cID}' and cvID in ({$cvList})";
            $r = $db->query($q);
            while ($row = $r->fetchRow()) {
                $v = [
                    $newCID,
                    $row['cvID'],
                    $row['bID'],
                    $row['arHandle'],
                    $row['cbDisplayOrder'],
                    $row['cbRelationID'],
                    0,
                    $row['cbOverrideAreaPermissions'],
                    $row['cbIncludeAll'],
                    $row['cbOverrideBlockTypeCacheSettings'],
                    $row['cbOverrideBlockTypeContainerSettings'],
                    $row['cbEnableBlockContainer']
                ];
                $q = 'insert into CollectionVersionBlocks (cID, cvID, bID, arHandle, cbDisplayOrder, cbRelationID, isOriginal, cbOverrideAreaPermissions, cbIncludeAll, cbOverrideBlockTypeCacheSettings, cbOverrideBlockTypeContainerSettings, cbEnableBlockContainer) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
                $db->query($q, $v);
                if ($row['cbOverrideAreaPermissions'] != 0) {
                    $q2 = "select paID, pkID from BlockPermissionAssignments where cID = '{$this->cID}' and bID = '{$row['bID']}' and cvID = '{$row['cvID']}'";
                    $r2 = $db->query($q2);
                    while ($row2 = $r2->fetchRow()) {
                        $db->Replace(
                           'BlockPermissionAssignments',
                           [
                               'cID' => $newCID,
                               'cvID' => $row['cvID'],
                               'bID' => $row['bID'],
                               'paID' => $row2['paID'],
                               'pkID' => $row2['pkID'],
                           ],
                           ['cID', 'cvID', 'bID', 'paID', 'pkID'],
                           true
                        );
                    }
                }
            }

            // duplicate any attributes belonging to the collection
            $list = CollectionKey::getAttributeValues($this->vObj);
            $em = \Database::connection()->getEntityManager();
            foreach ($list as $av) {
                /**
                 * @var PageValue
                 */
                $cav = new PageValue();
                $cav->setPageID($newCID);
                $cav->setGenericValue($av->getGenericValue());
                $cav->setVersionID($this->vObj->getVersionID());
                $cav->setAttributeKey($av->getAttributeKey());
                $em->persist($cav);
            }
            $em->flush();

            return self::getByID($newCID);
        }
    }

    /**
     * Clone the currently loaded version and returns a Page instance containing the new version.
     *
     * @param string|null $versionComments the comments to be associated to the new Version
     * @param bool $createEmpty set to true to create a Version without any blocks/area styles, false to clone them too
     *
     * @return \Concrete\Core\Page\Page
     */
    public function cloneVersion($versionComments, $createEmpty = false)
    {
        // first, we run the version object's createNew() command, which returns a new
        // version object, which we can combine with our collection object, so we'll have
        // our original collection object ($this), and a new collection object, consisting
        // of our collection + the new version
        $vObj = $this->getVersionObject();
        $nvObj = $vObj->createNew($versionComments);
        $nc = Page::getByID($this->getCollectionID());
        $nc->vObj = $nvObj;
        // now that we have the original version object and the cloned version object,
        // we're going to select all the blocks that exist for this page, and we're going
        // to copy them to the next version
        // unless btIncludeAll is set -- as that gets included no matter what

        $db = Loader::db();
        $cID = $this->getCollectionID();
        $cvID = $vObj->getVersionID();
        if (!$createEmpty) {
            $q = "select bID, arHandle from CollectionVersionBlocks where cID = '$cID' and cvID = '$cvID' and cbIncludeAll=0 order by cbDisplayOrder asc";
            $r = $db->query($q);
            if ($r) {
                while ($row = $r->fetchRow()) {
                    // now we loop through these, create block objects for all of them, and
                    // duplicate them to our collection object (which is actually the same collection,
                    // but different version)
                    $b = Block::getByID($row['bID'], $this, $row['arHandle']);
                    if (is_object($b)) {
                        $b->alias($nc);
                    }
                }
            }
            // duplicate any area styles
            $q = "select issID, arHandle from CollectionVersionAreaStyles where cID = '$cID' and cvID = '$cvID'";
            $r = $db->query($q);
            while ($row = $r->FetchRow()) {
                $db->Execute(
                    'insert into CollectionVersionAreaStyles (cID, cvID, arHandle, issID) values (?, ?, ?, ?)',
                    [
                        $this->getCollectionID(),
                        $nvObj->getVersionID(),
                        $row['arHandle'],
                        $row['issID'],
                    ]
                    );
            }
        }

        return $nc;
    }

    /**
     * @deprecated Use of getAttributeValueObject()
     *
     * @param string|\Concrete\Core\Attribute\Key\CollectionKey $ak
     *
     * @return \Concrete\Core\Entity\Attribute\Value\PageValue|null
     */
    public function getAttributeValue($ak)
    {
        return $this->getAttributeValueObject($ak);
    }

    /**
     * @deprecated use the getAttribute() method
     *
     * @param string|\Concrete\Core\Attribute\Key\CollectionKey $akHandle
     *
     * @return mixed|null
     */
    public function getCollectionAttributeValue($akHandle)
    {
        return $this->getAttribute($akHandle);
    }

    /**
     * @deprecated use the getPageTypeID() method
     */
    public function getCollectionTypeID()
    {
        return $this->getPageTypeID();
    }
}
