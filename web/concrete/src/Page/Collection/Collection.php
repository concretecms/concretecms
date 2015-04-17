<?php

namespace Concrete\Core\Page\Collection;

use Area;
use Block;
use CacheLocal;
use CollectionAttributeKey;
use CollectionVersion;
use Concrete\Core\Attribute\Key\Key;
use Concrete\Core\Attribute\Value\CollectionValue as CollectionAttributeValue;
use Concrete\Core\Feature\Assignment\CollectionVersionAssignment as CollectionVersionFeatureAssignment;
use Concrete\Core\Feature\Feature;
use Concrete\Core\Foundation\Object as Object;
use Concrete\Core\Gathering\Item\Page as PageGatheringItem;
use Concrete\Core\Page\Collection\Version\VersionList;
use Concrete\Core\Page\Search\IndexedSearch;
use Concrete\Core\StyleCustomizer\Inline\StyleSet;
use Concrete\Core\Block\CustomStyle as BlockCustomStyle;
use Concrete\Core\Area\CustomStyle as AreaCustomStyle;
use Config;
use Loader;
use Page;
use PageCache;
use Permissions;
use Stack;
use User;

class Collection extends Object
{
    public $cID;
    protected $attributes = array();

    /* version specific stuff */

    public static function reindexPendingPages()
    {
        $num = 0;
        $db = Loader::db();
        $r = $db->Execute('select cID from PageSearchIndex where cRequiresReindex = 1');
        while ($row = $r->FetchRow()) {
            $pc = Page::getByID($row['cID']);
            $pc->reindex(false, true);
            $num++;
        }
        Config::save('concrete.misc.do_page_reindex_check', false);

        return $num;
    }

    public static function getByHandle($handle)
    {
        $db = Loader::db();

        // first we ensure that this does NOT appear in the Pages table. This is not a page. It is more basic than that

        $r = $db->query(
                'select Collections.cID, Pages.cID as pcID from Collections left join Pages on Collections.cID = Pages.cID where Collections.cHandle = ?',
                array($handle)
        );
        if ($r->numRows() == 0) {

            // there is nothing in the collections table for this page, so we create and grab

            $data['handle'] = $handle;
            $cObj = self::addCollection($data);
        } else {
            $row = $r->fetchRow();
            if ($row['cID'] > 0 && $row['pcID'] == null) {

                // there is a collection, but it is not a page. so we grab it
                $cObj = Collection::getByID($row['cID']);
            }
        }

        if (isset($cObj)) {
            return $cObj;
        }
    }

    public function addCollection($data)
    {
        $db = Loader::db();
        $dh = Loader::helper('date');
        $cDate = $dh->getOverridableNow();

        $data = array_merge(
            array(
                'name' => '',
                'pTemplateID' => 0,
                'handle' => null,
                'uID' => null,
                'cDatePublic' => $cDate,
                'cDescription' => null,
            ),
            $data
        );

        $cDatePublic = ($data['cDatePublic']) ? $data['cDatePublic'] : $cDate;

        if (isset($data['cID'])) {
            $res = $db->query(
                      'insert into Collections (cID, cHandle, cDateAdded, cDateModified) values (?, ?, ?, ?)',
                      array($data['cID'], $data['handle'], $cDate, $cDate)
            );
            $newCID = $data['cID'];
        } else {
            $res = $db->query(
                      'insert into Collections (cHandle, cDateAdded, cDateModified) values (?, ?, ?)',
                      array($data['handle'], $cDate, $cDate)
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
        if (isset($this) && $this instanceof Page) {
            $pThemeID = $this->getCollectionThemeID();
        } else {
            $pThemeID = 0;
        }

        $pTemplateID = 0;
        if ($data['pTemplateID']) {
            $pTemplateID = $data['pTemplateID'];
        }

        if ($res) {
            // now we add a pending version to the collectionversions table
            $v2 = array(
                $newCID,
                1,
                $pTemplateID,
                $data['name'],
                $data['handle'],
                $data['cDescription'],
                $cDatePublic,
                $cDate,
                VERSION_INITIAL_COMMENT,
                $data['uID'],
                $cvIsApproved,
                $cvIsNew,
                $pThemeID,
            );
            $q2 = 'insert into CollectionVersions (cID, cvID, pTemplateID, cvName, cvHandle, cvDescription, cvDatePublic, cvDateCreated, cvComments, cvAuthorUID, cvIsApproved, cvIsNew, pThemeID) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $r2 = $db->prepare($q2);
            $res2 = $db->execute($r2, $v2);
        }

        $nc = Collection::getByID($newCID);

        return $nc;
    }

    /**
     * @param int   $cID
     * @param mixed $version 'RECENT'|'ACTIVE'|version id
     *
     * @return Collection
     */
    public static function getByID($cID, $version = 'RECENT')
    {
        $db = Loader::db();
        $q = 'select Collections.cDateAdded, Collections.cDateModified, Collections.cID from Collections where cID = ?';
        $row = $db->getRow($q, array($cID));

        $c = new Collection();
        $c->setPropertiesFromArray($row);

        if ($version != false) {
            // we don't do this on the front page
            $c->loadVersionObject($version);
        }

        return $c;
    }

    public function loadVersionObject($cvID = 'ACTIVE')
    {
        $this->vObj = CollectionVersion::get($this, $cvID);
    }

    /* attribute stuff */

    public function getVersionToModify()
    {
        // first, we check to see if the version we're modifying has the same
        // author uID associated with it as we currently have, and if it's inactive
        // If that's the case, then we just return the current collection + version object.

        $u = new User();
        $vObj = $this->getVersionObject();
        if ($this->isMasterCollection() || ($vObj->isNew())) {
            return $this;
        } else {
            // otherwise, we have to clone this version of the collection entirely,
            // and return that collection.

            $nc = $this->cloneVersion($versionComments);

            return $nc;
        }
    }

    public function getVersionObject()
    {
        return $this->vObj;
    }

    // remove the collection attributes for this version of a page

    public function cloneVersion($versionComments)
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
               array(
                   $this->getCollectionID(),
                   $nvObj->getVersionID(),
                   $row['arHandle'],
                   $row['issID'],
               )
            );
        }

        return $nc;
    }

    public function getCollectionID()
    {
        return $this->cID;
    }

    public function getNextVersionComments()
    {
        $c = Page::getByID($this->getCollectionID(), 'ACTIVE');
        $cvID = $c->getVersionID();

        return t('Version %d', $cvID + 1);
    }

    public function getFeatureAssignments()
    {
        if (is_object($this->vObj)) {
            return CollectionVersionFeatureAssignment::getList($this);
        }

        return array();
    }

    /**
     * Returns the value of the attribute with the handle $ak
     * of the current object.
     *
     * $displayMode makes it possible to get the correct output
     * value. When you need the raw attribute value or object, use
     * this:
     * <code>
     * $c = Page::getCurrentPage();
     * $attributeValue = $c->getAttribute('attribute_handle');
     * </code>
     *
     * But if you need the formatted output supported by some
     * attribute, use this:
     * <code>
     * $c = Page::getCurrentPage();
     * $attributeValue = $c->getAttribute('attribute_handle', 'display');
     * </code>
     *
     * An attribute type like "date" will then return the date in
     * the correct format just like other attributes will show
     * you a nicely formatted output and not just a simple value
     * or object.
     *
     *
     * @param string|object $akHandle
     * @param bool       $displayMode
     *
     * @return type
     */
    public function getAttribute($akHandle, $displayMode = false)
    {
        if (is_object($this->vObj)) {
            return $this->vObj->getAttribute($akHandle, $this, $displayMode);
        }
    }

    public function getCollectionAttributeValue($ak)
    {
        if (is_object($this->vObj)) {
            return $this->vObj->getAttribute($ak, $this);
        }
    }

    // get's an array of collection attribute objects that are attached to this collection. Does not get values

    public function clearCollectionAttributes($retainAKIDs = array())
    {
        $db = Loader::db();
        if (count($retainAKIDs) > 0) {
            $cleanAKIDs = array();
            foreach ($retainAKIDs as $akID) {
                $cleanAKIDs[] = intval($akID);
            }
            $akIDStr = implode(',', $cleanAKIDs);
            $v2 = array($this->getCollectionID(), $this->getVersionID());
            $db->query(
               "delete from CollectionAttributeValues where cID = ? and cvID = ? and akID not in ({$akIDStr})",
               $v2
            );
        } else {
            $v2 = array($this->getCollectionID(), $this->getVersionID());
            $db->query('delete from CollectionAttributeValues where cID = ? and cvID = ?', $v2);
        }
        $this->reindex();
    }

    public function getVersionID()
    {
        // shortcut
        return $this->vObj->cvID;
    }

    /* area stuff */

    public function reindex($index = false, $actuallyDoReindex = true)
    {
        if ($this->isAlias()) {
            return false;
        }
        if ($actuallyDoReindex || Config::get('concrete.page.search.always_reindex') == true) {
            $db = Loader::db();
            $attribs = CollectionAttributeKey::getAttributes(
                $this->getCollectionID(),
                $this->getVersionID(),
                'getSearchIndexValue'
            );
            $db->Execute('delete from CollectionSearchIndexAttributes where cID = ?', array($this->getCollectionID()));
            $searchableAttributes = array('cID' => $this->getCollectionID());
            $key = new Key();
            $key->reindex('CollectionSearchIndexAttributes', $searchableAttributes, $attribs);

            if ($index == false) {
                $index = new IndexedSearch();
            }

            $index->reindexPage($this);
            $db->Replace(
               'PageSearchIndex',
               array('cID' => $this->getCollectionID(), 'cRequiresReindex' => 0),
               array('cID'),
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
               array('cID' => $this->getCollectionID(), 'cRequiresReindex' => 1),
               array('cID'),
               false
            );
        }
    }

    /* aliased content */

    public function clearAttribute($ak)
    {
        $db = Loader::db();
        $cav = $this->getAttributeValueObject($ak);
        if (is_object($cav)) {
            $cav->delete();
        }
        $this->reindex();
    }

    /* basic CRUD */

    public function getAttributeValueObject($ak, $createIfNotFound = false)
    {
        $db = Loader::db();
        $av = false;
        if (is_string($ak)) {
            $ak = CollectionAttributeKey::getByHandle($ak);
        }
        $v = array($this->getCollectionID(), $this->getVersionID(), $ak->getAttributeKeyID());
        $avID = $db->GetOne('select avID from CollectionAttributeValues where cID = ? and cvID = ? and akID = ?', $v);
        if ($avID > 0) {
            $av = CollectionAttributeValue::getByID($avID);
            if (is_object($av)) {
                $av->setCollection($this);
                $av->setAttributeKey($ak);
            }
        }

        if ($createIfNotFound) {
            $cnt = 0;

            // Is this avID in use ?
            if (is_object($av)) {
                $cnt = $db->GetOne(
                          'select count(avID) from CollectionAttributeValues where avID = ?',
                          $av->getAttributeValueID()
                );
            }

            if ((!is_object($av)) || ($cnt > 1)) {
                $newAV = $ak->addAttributeValue();
                $av = CollectionAttributeValue::getByID($newAV->getAttributeValueID());
                $av->setCollection($this);
            }
        }

        return $av;
    }

    public function getSetCollectionAttributes()
    {
        $db = Loader::db();
        $akIDs = $db->GetCol(
                    'select akID from CollectionAttributeValues where cID = ? and cvID = ?',
                    array($this->getCollectionID(), $this->getVersionID())
        );
        $attribs = array();
        foreach ($akIDs as $akID) {
            $attribs[] = CollectionAttributeKey::getByID($akID);
        }

        return $attribs;
    }

    public function addAttribute($ak, $value)
    {
        $this->setAttribute($ak, $value);
    }

    public function setAttribute($ak, $value)
    {
        if (!is_object($ak)) {
            $ak = CollectionAttributeKey::getByHandle($ak);
        }
        $ak->setAttribute($this, $value);
        unset($ak);
        $this->reindex();
    }

    /**
     * @param string $arHandle
     *
     * @return Area
     */
    public function getArea($arHandle)
    {
        return Area::get($this, $arHandle);
    }

    public function hasAliasedContent()
    {
        $db = Loader::db();
        // aliased content is content on the particular page that is being
        // used elsewhere - but the content on the PAGE is the original version
        $v = array($this->cID);
        $q = 'select bID from CollectionVersionBlocks where cID = ? and isOriginal = 1';
        $r = $db->query($q, $v);
        $bIDArray = array();
        if ($r) {
            while ($row = $r->fetchRow()) {
                $bIDArray[] = $row['bID'];
            }
            if (count($bIDArray) > 0) {
                $bIDList = implode(',', $bIDArray);
                $v2 = array($bIDList, $this->cID);
                $q2 = 'select cID from CollectionVersionBlocks where bID in (?) and cID <> ? limit 1';
                $aliasedCID = $db->getOne($q2, $v2);
                if ($aliasedCID > 0) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getCollectionDateLastModified()
    {
        return $this->cDateModified;
    }

    public function getCollectionHandle()
    {
        return $this->cHandle;
    }

    public function getCollectionDateAdded()
    {
        return $this->cDateAdded;
    }

    public function __destruct()
    {
        unset($this->vObj);
    }

    /**
     * Retrieves all custom style rules that should be inserted into the header on a page, whether they are defined in areas
     * or blocks.
     */
    public function outputCustomStyleHeaderItems($return = false)
    {
        $db = Loader::db();
        $psss = array();
        $txt = Loader::helper('text');
        CacheLocal::set('pssCheck', $this->getCollectionID().':'.$this->getVersionID(), true);

        $r1 = $db->GetAll(
                 'select bID, arHandle, issID from CollectionVersionBlockStyles where cID = ? and cvID = ? and issID > 0',
                 array($this->getCollectionID(), $this->getVersionID())
        );
        $r2 = $db->GetAll(
                 'select arHandle, issID from CollectionVersionAreaStyles where cID = ? and cvID = ? and issID > 0',
                 array($this->getCollectionID(), $this->getVersionID())
        );
        foreach ($r1 as $r) {
            $issID = $r['issID'];
            $arHandle = $txt->filterNonAlphaNum($r['arHandle']);
            $bID = $r['bID'];
            $obj = StyleSet::getByID($issID);
            if (is_object($obj)) {
                $obj = new BlockCustomStyle($obj, $bID, $arHandle);
                $psss[] = $obj;
                CacheLocal::set(
                          'pssObject',
                          $this->getCollectionID().':'.$this->getVersionID().':'.$r['arHandle'].':'.$r['bID'],
                          $obj
                );
            }
        }

        foreach ($r2 as $r) {
            $issID = $r['issID'];
            $obj = StyleSet::getByID($issID);
            if (is_object($obj)) {
                $obj = new AreaCustomStyle($obj, $r['arHandle']);
                $psss[] = $obj;
                CacheLocal::set(
                          'pssObject',
                          $this->getCollectionID().':'.$this->getVersionID().':'.$r['arHandle'],
                          $obj
                );
            }
        }

        // grab all the header block style rules for items in global areas on this page
        $rs = $db->GetCol(
                 'select arHandle from Areas where arIsGlobal = 1 and cID = ?',
                 array($this->getCollectionID())
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
                    CacheLocal::set('pssCheck', $s->getCollectionID().':'.$s->getVersionID(), true);
                    $rs1 = $db->GetAll(
                              'select bID, issID, arHandle from CollectionVersionBlockStyles where cID = ? and cvID = ? and issID > 0',
                              array($s->getCollectionID(), $s->getVersionID())
                    );
                    foreach ($rs1 as $r) {
                        $issID = $r['issID'];
                        $obj = StyleSet::getByID($issID);
                        if (is_object($obj)) {
                            $obj = new BlockCustomStyle($obj, $r['bID'], $r['arHandle']);
                            $psss[] = $obj;
                            CacheLocal::set(
                                      'pssObject',
                                      $s->getCollectionID().':'.$s->getVersionID().':'.$r['arHandle'].':'.$r['bID'],
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
                $styleHeader .= '<style type="text/css" data-style-set="'.$st->getStyleSet()->getID().'">'.$css.'</style>';
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
            $result = new AreaCustomStyle($pss, $areaHandle);
        }

        return $result;
    }

    public function resetAreaCustomStyle($area)
    {
        $db = Loader::db();
        $db->Execute(
           'delete from CollectionVersionAreaStyles where cID = ? and cvID = ? and arHandle = ?',
           array(
               $this->getCollectionID(),
               $this->getVersionID(),
               $area->getAreaHandle(),
           )
        );
    }

    public function setCustomStyleSet($area, $set)
    {
        $db = Loader::db();
        $db->Replace(
           'CollectionVersionAreaStyles',
           array(
               'cID'      => $this->getCollectionID(),
               'cvID'     => $this->getVersionID(),
               'arHandle' => $area->getAreaHandle(),
               'issID'    => $set->getID(),
           ),
           array('cID', 'cvID', 'arHandle'),
           true
        );
    }

    public function relateVersionEdits($oc)
    {
        $db = Loader::db();
        $v = array(
            $this->getCollectionID(),
            $this->getVersionID(),
            $oc->getCollectionID(),
            $oc->getVersionID(),
        );
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

    public function getCollectionTypeID()
    {
        return false;
    }

    /* new cleaned up API below */

    public function getPageTypeID()
    {
        return false;
    }

    /* This function is slightly misnamed: it should be getOrCreateByHandle($handle) but I wanted to keep it brief
     * @param string $handle
     * @return Collection
     */

    public function rescanDisplayOrder($areaName)
    {
        // this collection function fixes the display order properties for all the blocks within the collection/area. We select all the items
        // order by display order, and fix the sequence

        $db = Loader::db();
        $cID = $this->cID;
        $cvID = $this->vObj->cvID;
        $q = "select bID from CollectionVersionBlocks where cID = '$cID' and cvID = '{$cvID}' and arHandle='$arHandle' order by cbDisplayOrder asc";
        $r = $db->query($q);

        if ($r) {
            $displayOrder = 0;
            while ($row = $r->fetchRow()) {
                $q = "update CollectionVersionBlocks set cbDisplayOrder = '$displayOrder' where cID = '$cID' and cvID = '{$cvID}' and arHandle = '$arHandle' and bID = '{$row['bID']}'";
                $r2 = $db->query($q);
                $displayOrder++;
            }
            $r->free();
        }
    }

    public function refreshCache()
    {
        CacheLocal::flush();
    }

    public function getGlobalBlocks()
    {
        $db = Loader::db();
        $v = array(Stack::ST_TYPE_GLOBAL_AREA);
        $rs = $db->GetCol('select stName from Stacks where Stacks.stType = ?', $v);
        $blocks = array();
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
     * List the blocks in a collection or area within a collection.
     *
     * @param bool|string $arHandle . If specified, returns just the blocks in an area
     *
     * @return array
     */
    public function getBlocks($arHandle = false)
    {
        $blockIDs = $this->getBlockIDs($arHandle);

        $blocks = array();
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
     * List the block IDs in a collection or area within a collection.
     *
     * @param bool|string $arHandle . If specified, returns just the blocks in an area
     *
     * @return array
     */
    public function getBlockIDs($arHandle = false)
    {
        $blockIDs = CacheLocal::getEntry(
                              'collection_block_ids',
                              $this->getCollectionID().':'.$this->getVersionID()
        );

        if (!is_array($blockIDs)) {
            $v = array($this->getCollectionID(), $this->getVersionID());
            $db = Loader::db();
            $q = 'select Blocks.bID, CollectionVersionBlocks.arHandle from CollectionVersionBlocks inner join Blocks on (CollectionVersionBlocks.bID = Blocks.bID) inner join BlockTypes on (Blocks.btID = BlockTypes.btID) where CollectionVersionBlocks.cID = ? and (CollectionVersionBlocks.cvID = ? or CollectionVersionBlocks.cbIncludeAll=1) order by CollectionVersionBlocks.cbDisplayOrder asc';
            $r = $db->GetAll($q, $v);
            $blockIDs = array();
            if (is_array($r)) {
                foreach ($r as $bl) {
                    $blockIDs[strtolower($bl['arHandle'])][] = $bl;
                }
            }
            CacheLocal::set('collection_block_ids', $this->getCollectionID().':'.$this->getVersionID(), $blockIDs);
        }

        $result = array();
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

        $v = array(
            $cID,
            $vObj->getVersionID(),
            $nb->getBlockID(),
            $arHandle,
            $newBlockDisplayOrder,
            1,
            intval($bt->includeAll()),
        );
        $q = 'insert into CollectionVersionBlocks (cID, cvID, bID, arHandle, cbDisplayOrder, isOriginal, cbIncludeAll) values (?, ?, ?, ?, ?, ?, ?)';

        $res = $db->Execute($q, $v);

        $controller = $nb->getController();
        $features = $controller->getBlockTypeFeatureObjects();
        if (count($features) > 0) {
            foreach ($features as $fe) {
                $fd = $fe->getFeatureDetailObject($controller);
                $fa = CollectionVersionFeatureAssignment::add($fe, $fd, $this);
                $db->Execute(
                   'insert into BlockFeatureAssignments (cID, cvID, bID, faID) values (?, ?, ?, ?)',
                   array(
                       $this->getCollectionID(),
                       $this->getVersionID(),
                       $nb->getBlockID(),
                       $fa->getFeatureAssignmentID(),
                   )
                );
            }
        }

        return Block::getByID($nb->getBlockID(), $this, $a);
    }

    public function getCollectionAreaDisplayOrder($arHandle, $ignoreVersions = false)
    {
        // this function queries CollectionBlocks to grab the highest displayOrder value, then increments it, and returns
        // this is used to add new blocks to existing Pages/areas

        $db = Loader::db();
        $cID = $this->cID;
        $cvID = $this->vObj->cvID;
        if ($ignoreVersions) {
            $q = 'select max(cbDisplayOrder) as cbdis from CollectionVersionBlocks where cID = ? and arHandle = ?';
            $v = array($cID, $arHandle);
        } else {
            $q = 'select max(cbDisplayOrder) as cbdis from CollectionVersionBlocks where cID = ? and cvID = ? and arHandle = ?';
            $v = array($cID, $cvID, $arHandle);
        }
        $r = $db->query($q, $v);
        if ($r) {
            if ($r->numRows() > 0) {
                // then we know we got a value; we increment it and return
                $res = $r->fetchRow();
                $displayOrder = $res['cbdis'];
                if (is_null($displayOrder)) {
                    return 0;
                }
                $displayOrder++;

                return $displayOrder;
            } else {
                // we didn't get anything, so we return a zero
                return 0;
            }
        }
    }

    public function addFeature(Feature $fe)
    {
        $db = Loader::db();
        $db->Replace(
           'CollectionVersionFeatures',
           array('cID' => $this->getCollectionID(), 'cvID' => $this->getVersionID(), 'feID' => $fe->getFeatureID()),
           array('cID', 'cvID', 'feID'),
           true
        );
    }

    public function markModified()
    {
        // marks this collection as newly modified
        $db = Loader::db();
        $dh = Loader::helper('date');
        $cDateModified = $dh->getOverridableNow();

        $v = array($cDateModified, $this->cID);
        $q = 'update Collections set cDateModified = ? where cID = ?';
        $r = $db->prepare($q);
        $res = $db->execute($r, $v);
    }

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

    public function duplicateCollection()
    {
        $db = Loader::db();
        $dh = Loader::helper('date');
        $cDate = $dh->getOverridableNow();

        $v = array($cDate, $cDate, $this->cHandle);
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
            $cvList = array();
            while ($row = $rv->fetchRow()) {
                // insert
                $cvList[] = $row['cvID'];
                $cDate = date('Y-m-d H:i:s', strtotime($cDate) + 1);
                $vv = array(
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
                );
                $qv = 'insert into CollectionVersions (cID, cvID, cvName, cvHandle, cvDescription, cvDatePublic, cvDateCreated, cvComments, cvAuthorUID, cvIsApproved, pThemeID, pTemplateID) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
                $db->query($qv, $vv);
            }

            $ql = "select * from CollectionVersionBlockStyles where cID = '{$this->cID}'";
            $rl = $db->query($ql);
            while ($row = $rl->fetchRow()) {
                $vl = array($newCID, $row['cvID'], $row['bID'], $row['arHandle'], $row['issID']);
                $ql = 'insert into CollectionVersionBlockStyles (cID, cvID, bID, arHandle, issID) values (?, ?, ?, ?, ?)';
                $db->query($ql, $vl);
            }
            $ql = "select * from CollectionVersionAreaStyles where cID = '{$this->cID}'";
            $rl = $db->query($ql);
            while ($row = $rl->fetchRow()) {
                $vl = array($newCID, $row['cvID'], $row['arHandle'], $row['issID']);
                $ql = 'insert into CollectionVersionAreaStyles (cID, cvID, arHandle, issID) values (?, ?, ?, ?)';
                $db->query($ql, $vl);
            }

            $ql = "select * from CollectionVersionThemeCustomStyles where cID = '{$this->cID}'";
            $rl = $db->query($ql);
            while ($row = $rl->fetchRow()) {
                $vl = array($newCID, $row['cvID'], $row['pThemeID'], $row['scvlID'], $row['preset'], $row['sccRecordID']);
                $ql = 'insert into CollectionVersionThemeCustomStyles (cID, cvID, pThemeID, scvlID, preset, sccRecordID) values (?, ?, ?, ?, ?, ?)';
                $db->query($ql, $vl);
            }

            // now we grab all the blocks we're going to need
            $cvList = implode(',', $cvList);
            $q = "select bID, cvID, arHandle, cbDisplayOrder, cbOverrideAreaPermissions, cbIncludeAll from CollectionVersionBlocks where cID = '{$this->cID}' and cvID in ({$cvList})";
            $r = $db->query($q);
            while ($row = $r->fetchRow()) {
                $v = array(
                    $newCID,
                    $row['cvID'],
                    $row['bID'],
                    $row['arHandle'],
                    $row['cbDisplayOrder'],
                    0,
                    $row['cbOverrideAreaPermissions'],
                    $row['cbIncludeAll'],
                );
                $q = 'insert into CollectionVersionBlocks (cID, cvID, bID, arHandle, cbDisplayOrder, isOriginal, cbOverrideAreaPermissions, cbIncludeAll) values (?, ?, ?, ?, ?, ?, ?, ?)';
                $db->query($q, $v);
                if ($row['cbOverrideAreaPermissions'] != 0) {
                    $q2 = "select paID, pkID from BlockPermissionAssignments where cID = '{$this->cID}' and bID = '{$row['bID']}' and cvID = '{$row['cvID']}'";
                    $r2 = $db->query($q2);
                    while ($row2 = $r2->fetchRow()) {
                        $db->Replace(
                           'BlockPermissionAssignments',
                           array(
                               'cID'  => $newCID,
                               'cvID' => $row['cvID'],
                               'bID'  => $row['bID'],
                               'paID' => $row2['paID'],
                               'pkID' => $row2['pkID'],
                           ),
                           array('cID', 'cvID', 'bID', 'paID', 'pkID'),
                           true
                        );
                    }
                }
            }

            // duplicate any attributes belonging to the collection

            $v = array($this->getCollectionID());
            $q = 'select akID, cvID, avID from CollectionAttributeValues where cID = ?';
            $r = $db->query($q, $v);
            while ($row = $r->fetchRow()) {
                $v2 = array($row['akID'], $row['cvID'], $row['avID'], $newCID);
                $db->query('insert into CollectionAttributeValues (akID, cvID, avID, cID) values (?, ?, ?, ?)', $v2);
            }

            return Collection::getByID($newCID);
        }
    }
}
