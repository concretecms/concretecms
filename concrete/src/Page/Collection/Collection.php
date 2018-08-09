<?php

namespace Concrete\Core\Page\Collection;

use Concrete\Core\Area\Area;
use Concrete\Core\Area\CustomStyle as AreaCustomStyle;
use Concrete\Core\Area\GlobalArea;
use Concrete\Core\Attribute\Category\PageCategory as PageAttributeCategory;
use Concrete\Core\Block\Block;
use Concrete\Core\Block\CustomStyle as BlockCustomStyle;
use Concrete\Core\Cache\Page\PageCache;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Attribute\Value\PageValue as PageAttributeValue;
use Concrete\Core\Feature\Assignment\CollectionVersionAssignment as CollectionVersionFeatureAssignment;
use Concrete\Core\Feature\Feature;
use Concrete\Core\Foundation\ConcreteObject;
use Concrete\Core\Gathering\Item\Page as PageGatheringItem;
use Concrete\Core\Page\Collection\Version\Version as CollectionVersion;
use Concrete\Core\Page\Collection\Version\VersionList;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Search\IndexedSearch;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Search\Index\IndexManagerInterface;
use Concrete\Core\Statistics\UsageTracker\TrackableInterface;
use Concrete\Core\StyleCustomizer\Inline\StyleSet;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\View\View;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PDO;

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
     * @return \Concrete\Core\Page\Collection\Collection If the collection is not found, you'll get an empty Collection instance
     */
    public static function getByID($cID, $version = 'RECENT')
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $row = $db->fetchAssoc('select Collections.cDateAdded, Collections.cDateModified, Collections.cID from Collections where cID = ?', [$cID]);

        $c = new self();
        if ($row !== false) {
            $c->setPropertiesFromArray($row);
        }
        if ($version) {
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
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        // first we ensure that this does NOT appear in the Pages table. This is not a page. It is more basic than that
        $row = $db->fetchAssoc(
            'select Collections.cID, Pages.cID as pcID from Collections left join Pages on Collections.cID = Pages.cID where Collections.cHandle = ?',
            [$handle]
        );
        if ($row === false) {
            // there is nothing in the collections table for this page, so we create and grab
            $cObj = self::createCollection([
                'handle' => $handle,
            ]);
        } elseif ($row['pcID'] == null) {
            // there is a collection, but it is not a page. so we grab it
            $cObj = self::getByID($row['cID']);
        } else {
            $cObj = null;
        }

        return $cObj;
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
        $db = $app->make(Connection::class);

        $num = 0;
        $r = $db->executeQuery('select cID from PageSearchIndex where cRequiresReindex = 1');
        while ($id = $r->fetchColumn()) {
            $indexStack->index(Page::class, $id);
            ++$num;
        }
        $app->make('config')->save('concrete.misc.do_page_reindex_check', false);

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
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $dh = $app->make('date');
        $now = $dh->getOverridableNow();

        if (!is_array($data)) {
            $data = [];
        }
        $data += [
            'name' => '',
            'pTemplateID' => 0,
            'pThemeID' => 0,
            'handle' => null,
            'uID' => null,
            'cDatePublic' => $now,
            'cDescription' => null,
        ];

        $insert = [
            'cHandle' => $data['handle'],
            'cDateAdded' => $now,
            'cDateModified' => $now,
        ];
        if (empty($data['cID'])) {
            $db->insert('Collections', $insert);
            $newCID = $db->lastInsertId();
        } else {
            $insert['cID'] = $data['cID'];
            $db->insert('Collections', $insert);
            $newCID = $data['cID'];
        }

        $insert = [
            'cID' => $newCID,
            'cvID' => 1,
            'pTemplateID' => $data['pTemplateID'] ?: 0,
            'cvName' => $app->make('helper/text')->sanitize($data['name']),
            'cvHandle' => $data['handle'],
            'cvDescription' => $data['cDescription'],
            'cvDatePublic' => $data['cDatePublic'] ?: $now,
            'cvDateCreated' => $now,
            'cvComments' => t(VERSION_INITIAL_COMMENT),
            'cvAuthorUID' => $data['uID'],
            'cvIsApproved' => isset($data['cvIsApproved']) && empty($data['cvIsApproved']) ? 0 : 1,
            'pThemeID' => $data['pThemeID'] ?: 0,
        ];
        if (isset($data['cvIsNew'])) {
            $insert['cvIsNew'] = $data['cvIsNew'] ? 1 : 0;
        } else {
            $insert['cvIsNew'] = $cvIsApproved ? 0 : 1;
        }
        $db->insert('CollectionVersions', $insert);

        $newCollection = self::getByID($newCID);

        return $newCollection;
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
     * @return int|null
     */
    public function getVersionID()
    {
        $versionObject = $this->getVersionObject();

        return $versionObject ? $versionObject->getVersionID() : null;
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
        if (!is_array($data)) {
            $data = [];
        }
        $data['pThemeID'] = $this instanceof Page ? (int) $this->getCollectionThemeID() : 0;

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
        if ($this->isMasterCollection()) {
            return $this;
        }
        $vObj = $this->getVersionObject();
        if ($vObj->isNew()) {
            return $this;
        }

        return $this->cloneVersion(null);
    }

    /**
     * Get the automatic comment for the next collection version.
     *
     * @return string Example: 'Version 2'
     */
    public function getNextVersionComments()
    {
        $c = Page::getByID($this->getCollectionID(), 'ACTIVE');
        $cvID = (int) $c->getVersionID();

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
        $app = Application::getFacadeApplication();
        $config = $app->make('config');
        $db = $app->make(Connection::class);
        if ($actuallyDoReindex || $config->get('concrete.page.search.always_reindex')) {
            // Retrieve the attribute values for the current page
            $category = $app->make(PageAttributeCategory::class);
            $indexer = $category->getSearchIndexer();
            $values = $category->getAttributeValues($this);
            foreach ($values as $value) {
                $indexer->indexEntry($category, $value, $this);
            }

            if (!$index) {
                $index = new IndexedSearch();
            }

            $index->reindexPage($this);

            $db->replace(
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
            $config->save('concrete.misc.do_page_reindex_check', true);
            $db->replace(
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
        return $this->getVersionObject()->setAttribute($ak, $value);
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
        $vObj = $this->getVersionObject();
        if ($vObj) {
            return $vObj->getAttribute($akHandle, $displayMode);
        }
    }

    /**
     * Return the attribute value object with the handle $akHandle of the currently loaded version (if it's loaded).
     *
     * @param string|\Concrete\Core\Attribute\Key\CollectionKey $akHandle the attribute key (or its handle)
     * @param mixed $createIfNotExists
     *
     * @return \Concrete\Core\Entity\Attribute\Value\PageValue|null
     */
    public function getAttributeValueObject($akHandle, $createIfNotExists = false)
    {
        $vObj = $this->getVersionObject();
        if ($vObj) {
            return $vObj->getAttributeValue($akHandle);
        }
    }

    /**
     * Delete the values of the attributes associated to the currently loaded collection version.
     *
     * @param int[] $retainAKIDs a list of attribute key IDs to keep (their values won't be deleted)
     */
    public function clearCollectionAttributes($retainAKIDs = [])
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        if (!empty($retainAKIDs)) {
            $cleanAKIDs = [];
            foreach ($retainAKIDs as $akID) {
                $cleanAKIDs[] = (int) $akID;
            }
            $akIDStr = implode(',', $cleanAKIDs);
            $db->executeQuery(
                "delete from CollectionAttributeValues where cID = ? and cvID = ? and akID not in ({$akIDStr})",
                [$this->getCollectionID(), $this->getVersionID()]
            );
        } else {
            $db->executeQuery(
                'delete from CollectionAttributeValues where cID = ? and cvID = ?',
                [$this->getCollectionID(), $this->getVersionID()]
            );
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
        $vObj = $this->getVersionObject();
        if ($vObj) {
            $vObj->clearAttribute($ak);
        }
    }

    /**
     * Get the list of attribute keys for which the currently loaded collection version has values.
     *
     * @return \Concrete\Core\Entity\Attribute\Key\PageKey[]
     */
    public function getSetCollectionAttributes()
    {
        $vObj = $this->getVersionObject();
        $category = $vObj->getObjectAttributeCategory();
        $values = $category->getAttributeValues($vObj);
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
     * @return \Concrete\Core\Area\Area|null
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
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        // aliased content is content on the particular page that is being
        // used elsewhere - but the content on the PAGE is the original version
        $r = $db->executeQuery(
            'select bID from CollectionVersionBlocks where cID = ? and isOriginal = 1',
            [$this->getCollectionID()]
        );
        $bIDArray = [];
        while (($bID = $r->fetchColumn()) !== false) {
            $bIDArray[] = $bID;
        }
        if (empty($bIDArray)) {
            return false;
        }
        $bIDList = implode(',', $bIDArray);
        $aliasedCID = $db->fetchColumn(
            'select cID from CollectionVersionBlocks where bID in (?) and cID <> ? limit 1',
            [$bIDList, $this->getCollectionID()]
        );

        return $aliasedCID ? true : false;
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
        $styles = $this->getVersionObject()->getCustomAreaStyles();
        $areaHandle = $area->getAreaHandle();
        if ($force || isset($styles[$areaHandle])) {
            $pss = isset($styles[$areaHandle]) ? StyleSet::getByID($styles[$areaHandle]) : null;
            $result = new AreaCustomStyle($pss, $area, $this->getCollectionThemeObject());
        } else {
            $result = null;
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
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->replace(
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
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->delete('CollectionVersionAreaStyles', [
            'cID' => $this->getCollectionID(),
            'cvID' => $this->getVersionID(),
            'arHandle' => $area->getAreaHandle(),
        ]);
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
        $app = Application::getFacadeApplication();
        $config = $app->make('config');
        if (!$config->get('concrete.design.enable_custom')) {
            return '';
        }
        $db = $app->make(Connection::class);
        $txt = $app->make('helper/text');

        $psss = [];

        $blockStyles = $db->fetchAll(
            'select bID, arHandle, issID from CollectionVersionBlockStyles where cID = ? and cvID = ? and issID > 0',
            [$this->getCollectionID(), $this->getVersionID()]
        );
        foreach ($blockStyles as $blockStyle) {
            $styleSet = StyleSet::getByID($blockStyle['issID']);
            if ($styleSet !== null) {
                $b = new Block();
                $b->bID = $blockStyle['bID'];
                $a = new Area($txt->filterNonAlphaNum($blockStyle['arHandle']));
                $b->setBlockAreaObject($a);
                $psss[] = new BlockCustomStyle($styleSet, $b, $this->getCollectionThemeObject());
            }
        }

        $areaStyles = $db->fetchAll(
            'select arHandle, issID from CollectionVersionAreaStyles where cID = ? and cvID = ? and issID > 0',
            [$this->getCollectionID(), $this->getVersionID()]
        );
        foreach ($areaStyles as $areaStyle) {
            $styleSet = StyleSet::getByID($areaStyle['issID']);
            if ($styleSet !== null) {
                $a = new Area($areaStyle['arHandle']);
                $psss[] = new AreaCustomStyle($styleSet, $a, $this->getCollectionThemeObject());
            }
        }

        // grab all the header block style rules for items in global areas on this page
        $pcp = null;
        $rs = $db->executeQuery('select arHandle from Areas where arIsGlobal = 1 and cID = ?', [$this->getCollectionID()]);
        while (($garHandle = $rs->fetchColumn()) !== false) {
            if ($pcp === null) {
                $pcp = new Checker($this);
            }
            if ($pcp->canViewPageVersions()) {
                $s = Stack::getByName($garHandle, 'RECENT');
            } else {
                $s = Stack::getByName($garHandle, 'ACTIVE');
            }
            if ($s) {
                $blockStyles = $db->fetchAll(
                  'select bID, issID, arHandle from CollectionVersionBlockStyles where cID = ? and cvID = ? and issID > 0',
                  [$s->getCollectionID(), $s->getVersionID()]
                );
                foreach ($blockStyles as $blockStyle) {
                    $styleSet = StyleSet::getByID($blockStyle['issID']);
                    if ($styleSet !== null) {
                        $b = new Block();
                        $b->bID = $blockStyle['bID'];
                        $a = new GlobalArea($garHandle);
                        $b->setBlockAreaObject($a);
                        $psss[] = new BlockCustomStyle($styleSet, $b, $this->getCollectionThemeObject());
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

        if (trim($styleHeader) !== '') {
            if ($return) {
                return $styleHeader;
            } else {
                $v = View::getInstance();
                $v->addHeaderAsset($styleHeader);
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
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $params = [
            $this->getCollectionID(),
            $this->getVersionID(),
            $oc->getCollectionID(),
            $oc->getVersionID(),
        ];
        $count = $db->fetchColumn(
            'select count(*) from CollectionVersionRelatedEdits where cID = ? and cvID = ? and cRelationID = ? and cvRelationID = ?',
            $params
        );
        if ($count) {
            return false;
        }
        $db->executeQuery(
            'insert into CollectionVersionRelatedEdits (cID, cvID, cRelationID, cvRelationID) values (?, ?, ?, ?)',
            $params
        );
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
        $app = Application::getFacadeApplication();
        $cache = $app->make('cache/request');
        $cache->flush();
    }

    /**
     * Get the blocks contained in the all the global areas.
     *
     * @return \Concrete\Core\Block\Block[]
     */
    public function getGlobalBlocks()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $blocks = [];
        $pcp = null;
        $rs = $db->executeQuery('select stName from Stacks where Stacks.stType = ?', [Stack::ST_TYPE_GLOBAL_AREA]);
        while (($garHandle = $rs->fetchColumn()) !== false) {
            if ($pcp === null) {
                $pcp = new Checker($this);
            }
            if ($pcp->canViewPageVersions()) {
                $s = Stack::getByName($garHandle, 'RECENT');
            } else {
                $s = Stack::getByName($garHandle, 'ACTIVE');
            }
            if ($s) {
                $blocks = array_merge($blocks, $s->getBlocks(STACKS_AREA_NAME));
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
        $blocks = [];
        foreach ($this->getBlockIDs($arHandle) as $row) {
            $block = Block::getByID($row['bID'], $this, $row['arHandle']);
            if ($block) {
                $blocks[] = $block;
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
        $app = Application::getFacadeApplication();
        $cache = $app->make('cache/request');
        $cacheItem = $cache->getItem('collection_block_ids/' . $this->getCollectionID() . ':' . $this->getVersionID());
        if ($cacheItem->isMiss()) {
            $db = $app->make(Connection::class);
            $blockIDs = [];
            $rs = $db->executeQuery(
                'select Blocks.bID, CollectionVersionBlocks.arHandle from CollectionVersionBlocks inner join Blocks on (CollectionVersionBlocks.bID = Blocks.bID) inner join BlockTypes on (Blocks.btID = BlockTypes.btID) where CollectionVersionBlocks.cID = ? and (CollectionVersionBlocks.cvID = ? or CollectionVersionBlocks.cbIncludeAll=1) order by CollectionVersionBlocks.cbDisplayOrder asc',
                [$this->getCollectionID(), $this->getVersionID()]
            );
            while (($row = $rs->fetch(PDO::FETCH_ASSOC)) !== false) {
                $blockIDs[strtolower($row['arHandle'])][] = $row;
            }
            $cacheItem->set($blockIDs)->save();
        } else {
            $blockIDs = $cacheItem->get();
        }

        $result = [];
        if ($arHandle) {
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
     * @param \Concrete\Core\Entity\Block\BlockType\BlockType $blockType the type of block to be added
     * @param string|\Concrete\Core\Area\Area $a the area instance (or its handle) to which the block should be added to
     * @param array $data The data of the block. This data depends on the specific block type. Common values are: 'uID' to specify the ID of the author (if not specified: we'll use the current user), 'bName' to specify the block name.
     * @param mixed $bt
     *
     * @return \Concrete\Core\Block\Block
     */
    public function addBlock($bt, $a, $data)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);

        // first we add the block to the system
        $newBlock = $bt->add($data, $this, $a);

        // now that we have a block, we add it to the collectionversions table

        $arHandle = is_object($a) ? $a->getAreaHandle() : $a;
        $cID = $this->getCollectionID();
        $vObj = $this->getVersionObject();

        if ($bt->includeAll()) {
            // normally, display order is dependant on a per area, per version basis. However, since this block
            // is not aliased across versions, then we want to get display order simply based on area, NOT based
            // on area + version
            $newBlockDisplayOrder = $this->getCollectionAreaDisplayOrder($arHandle, true);
        } else {
            $newBlockDisplayOrder = $this->getCollectionAreaDisplayOrder($arHandle);
        }

        $cbRelationID = 1 + (int) $db->fetchColumn('select max(cbRelationID) from CollectionVersionBlocks');

        $db->insert('CollectionVersionBlocks', [
            'cID' => $cID,
            'cvID' => $vObj->getVersionID(),
            'bID' => $newBlock->getBlockID(),
            'arHandle' => $arHandle,
            'cbRelationID' => $cbRelationID,
            'cbDisplayOrder' => $newBlockDisplayOrder,
            'isOriginal' => 1,
            'cbIncludeAll' => (int) $bt->includeAll(),
        ]);
        $controller = $newBlock->getController();
        $features = $controller->getBlockTypeFeatureObjects();
        foreach ($features as $fe) {
            $fd = $fe->getFeatureDetailObject($controller);
            $fa = CollectionVersionFeatureAssignment::add($fe, $fd, $this);
            $db->insert('BlockFeatureAssignments', [
                'cID' => $this->getCollectionID(),
                'cvID' => $this->getVersionID(),
                'bID' => $newBlock->getBlockID(),
                'faID' => $fa->getFeatureAssignmentID(),
            ]);
        }

        return Block::getByID($newBlock->getBlockID(), $this, $a);
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
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        if ($ignoreVersions) {
            $q = 'select max(cbDisplayOrder) from CollectionVersionBlocks where cID = ? and arHandle = ?';
            $v = [$this->getCollectionID(), $arHandle];
        } else {
            $q = 'select max(cbDisplayOrder) from CollectionVersionBlocks where cID = ? and cvID = ? and arHandle = ?';
            $v = [$this->getCollectionID(), $this->getVersionID(), $arHandle];
        }
        $maxDisplayOrder = $db->fetchColumn($q, $v);

        return $maxDisplayOrder === null || $maxDisplayOrder === false ? 0 : 1 + $maxDisplayOrder;
    }

    /**
     * Fix the display order properties for all the blocks within the collection/area.
     *
     * @param string $arHandle the handle of the area to be processed
     */
    public function rescanDisplayOrder($arHandle)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $cID = $this->getCollectionID();
        $cvID = $this->getVersionID();
        $displayOrder = 0;
        $rs = $db->executeQuery(
            'select bID from CollectionVersionBlocks where cID = ? and cvID = ? and arHandle = ? order by cbDisplayOrder asc',
            [$cID, $cvID, $arHandle]
        );
        while (($bID = $rs->fetchColumn()) !== false) {
            $db->executeQuery(
                'update CollectionVersionBlocks set cbDisplayOrder = ? where cID = ? and cvID = ? and arHandle = ? and bID = ?',
                [$displayOrder, $cID, $cvID, $arHandle, $bID]
            );
            ++$displayOrder;
        }
    }

    /**
     * Associate a feature to the currently loaded collection version.
     *
     * @param Feature $fe
     */
    public function addFeature(Feature $fe)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->replace(
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
        $version = $this->getVersionObject();

        return $version === null ? [] : CollectionVersionFeatureAssignment::getList($this);
    }

    /**
     * Mark this collection as newly modified.
     */
    public function markModified()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $dh = $app->make('date');
        $db->executeQuery(
            'update Collections set cDateModified = ? where cID = ?',
            [$dh->getOverridableNow(), $this->getCollectionID()]
        );
    }

    /**
     * Delete this collection, and all its versions, contents and attributes.
     */
    public function delete()
    {
        $cID = $this->getCollectionID();
        if ($cID) {
            $app = Application::getFacadeApplication();
            $db = $app->make(Connection::class);
            // First we delete all versions
            $versionList = new VersionList($this);
            $versionList->setItemsPerPage(-1);
            foreach ($versionList->getPage() as $v) {
                $v->delete();
            }
            $db->executeQuery('delete from CollectionAttributeValues where cID = ?', [$cID]);
            $db->executeQuery('delete from Collections where cID = ?', [$cID]);
            try {
                $db->executeQuery('delete from CollectionSearchIndexAttributes where cID = ?', [$cID]);
            } catch (Exception $e) {
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
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $em = $app->make(EntityManagerInterface::class);
        $dh = $app->make('date');
        $cDate = $dh->getOverridableNow();
        $cID = $this->getCollectionID();

        $db->insert('Collections', [
            'cDateAdded' => $cDate,
            'cDateModified' => $cDate,
            'cHandle' => $this->getCollectionHandle(),
        ]);
        $newCID = $db->lastInsertId();

        $rs = $db->executeQuery('select * from CollectionVersions where cID = ? order by cvDateCreated asc', [$cID]);
        while (($row = $rs->fetch(PDO::FETCH_ASSOC)) !== false) {
            // insert
            $cDate = date('Y-m-d H:i:s', strtotime($cDate) + 1);
            $db->insert('CollectionVersions', [
                'cID' => $newCID,
                'cvID' => $row['cvID'],
                'cvName' => $row['cvName'],
                'cvHandle' => $row['cvHandle'],
                'cvDescription' => $row['cvDescription'],
                'cvDatePublic' => $row['cvDatePublic'],
                'cvDateCreated' => $cDate,
                'cvComments' => $row['cvComments'],
                'cvAuthorUID' => $row['cvAuthorUID'],
                'cvIsApproved' => $row['cvIsApproved'],
                'pThemeID' => $row['pThemeID'],
                'pTemplateID' => $row['pTemplateID'],
            ]);
        }

        $copyFields = 'cvID, bID, arHandle, issID';
        $db->executeQuery(
            "insert into CollectionVersionBlockStyles (cID, {$copyFields}) select ?, {$copyFields} from CollectionVersionBlockStyles where cID = ?",
            [$newCID, $cID]
        );

        $copyFields = 'cvID, arHandle, issID';
        $db->executeQuery(
            "insert into CollectionVersionAreaStyles (cID, {$copyFields}) select ?, {$copyFields} from CollectionVersionAreaStyles where cID = ?",
            [$newCID, $cID]
        );

        $copyFields = 'cvID, pThemeID, scvlID, preset, sccRecordID';
        $db->executeQuery(
            "insert into CollectionVersionThemeCustomStyles (cID, {$copyFields}) select ?, {$copyFields} from CollectionVersionThemeCustomStyles where cID = ?",
            [$newCID, $cID]
        );

        $copyFields = 'cvID, bID, arHandle, btCacheBlockOutput, btCacheBlockOutputOnPost, btCacheBlockOutputForRegisteredUsers, btCacheBlockOutputLifetime';
        $db->executeQuery(
            "insert into CollectionVersionBlocksCacheSettings (cID, {$copyFields}) select ?, {$copyFields} from CollectionVersionBlocksCacheSettings where cID = ?",
            [$newCID, $cID]
        );

        $copyFields = 'cvID, bID, arHandle, cbDisplayOrder, cbRelationID, cbOverrideAreaPermissions, cbIncludeAll, cbOverrideBlockTypeCacheSettings, cbOverrideBlockTypeContainerSettings, cbEnableBlockContainer';
        $db->executeQuery(
            "insert into CollectionVersionBlocks (cID, isOriginal, {$copyFields}) select ?, 0, {$copyFields} from CollectionVersionBlocks where cID = ?",
            [$newCID, $cID]
        );

        $copyFields = 'cvID, bID, pkID, paID';
        $db->executeQuery(
            "insert into BlockPermissionAssignments (cID, {$copyFields}) select ?, {$copyFields} from BlockPermissionAssignments where cID = ?",
            [$newCID, $cID]
        );

        $versionID = $this->getVersionID();
        $list = $app->make(PageAttributeCategory::class)->getAttributeValues($this->getVersionObject());
        foreach ($list as $av) {
            $cav = new PageAttributeValue();
            $cav->setPageID($newCID);
            $cav->setGenericValue($av->getGenericValue());
            $cav->setVersionID($versionID);
            $cav->setAttributeKey($av->getAttributeKey());
            $em->persist($cav);
        }
        $em->flush();

        return self::getByID($newCID);
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
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);

        // first, we run the version object's createNew() command, which returns a new
        // version object, which we can combine with our collection object, so we'll have
        // our original collection object ($this), and a new collection object, consisting
        // of our collection + the new version
        $vObj = $this->getVersionObject();
        $nvObj = $vObj->createNew($versionComments);
        $nc = Page::getByID($this->getCollectionID());
        $nc->vObj = $nvObj;

        if (!$createEmpty) {
            // now that we have the original version object and the cloned version object,
            // we're going to select all the blocks that exist for this page, and we're going
            // to copy them to the next version
            // unless btIncludeAll is set -- as that gets included no matter what
            $cID = $this->getCollectionID();
            $oldVersionID = $vObj->getVersionID();
            $newVersionID = $nvObj->getVersionID();
            $rs = $db->executeQuery('select bID, arHandle from CollectionVersionBlocks where cID = ? and cvID = ? and cbIncludeAll = 0 order by cbDisplayOrder asc', [$cID, $oldVersionID]);
            while (($row = $rs->fetch(PDO::FETCH_ASSOC)) !== false) {
                // now we loop through these, create block objects for all of them, and
                // duplicate them to our collection object (which is actually the same collection,
                // but different version)
                $b = Block::getByID($row['bID'], $this, $row['arHandle']);
                if ($b) {
                    $b->alias($nc);
                }
            }
            // duplicate any area styles
            $copyFields = 'arHandle, issID';
            $db->executeQuery(
                "insert into CollectionVersionAreaStyles (cID, cvID, {$copyFields}) select ?, ?, {$copyFields} from CollectionVersionAreaStyles where cID = ? and cvID = ?",
                [$cID, $newVersionID, $cID, $oldVersionID]
            );
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
