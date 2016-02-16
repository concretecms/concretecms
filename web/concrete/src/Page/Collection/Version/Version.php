<?php
namespace Concrete\Core\Page\Collection\Version;

use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Attribute\ObjectTrait;
use Concrete\Core\Entity\Attribute\Value\PageValue;
use Loader;
use Concrete\Core\Foundation\Object;
use Block;
use Page;
use PageType;
use PageTemplate;
use Permissions;
use User;
use Events;
use CacheLocal;
use Concrete\Core\Feature\Assignment\CollectionVersionAssignment as CollectionVersionFeatureAssignment;

class Version extends Object implements \Concrete\Core\Permission\ObjectInterface
{
    use ObjectTrait;

    public $cvIsApproved;

    public $cID;

    protected $attributes = array();

    public $layoutStyles = array();

    public function getPermissionObjectIdentifier()
    {
        return $this->getCollectionID() . ':' . $this->getVersionID();
    }

    public function getPermissionResponseClassName()
    {
        return '\\Concrete\\Core\\Permission\\Response\\CollectionVersionResponse';
    }

    public function getPermissionAssignmentClassName()
    {
        return '\\Concrete\\Core\\Permission\\Assignment\\PageAssignment';
    }

    public function getPermissionObjectKeyCategoryHandle()
    {
        return 'page';
    }

    public function refreshCache()
    {
        CacheLocal::delete('page', $this->getCollectionID());
    }

    public static function get(&$c, $cvID)
    {
        $db = Loader::db();

        if (($c instanceof Page) && $c->getCollectionPointerID()) {
            $v = array(
                $c->getCollectionPointerID(),
            );
        } else {
            $v = array(
                $c->getCollectionID(),
            );
        }

        $q = "select cvID, cvIsApproved, cvIsNew, cvHandle, cvName, cvDescription, cvDateCreated, cvDatePublic, " .
             "pTemplateID, cvAuthorUID, cvApproverUID, cvComments, pThemeID, cvPublishDate from CollectionVersions " .
             "where cID = ?";

        if ($cvID == 'ACTIVE') {
            $q .= ' and cvIsApproved = 1';
        } elseif ($cvID == 'RECENT') {
            $q .= ' order by cvID desc';
        } else {
            $v[] = $cvID;
            $q .= ' and cvID = ?';
        }

        $row = $db->GetRow($q, $v);
        $cv = new static();

        if (is_array($row) && $row['cvID']) {
            $cv->setPropertiesFromArray($row);
        }

        // load the attributes for a particular version object
        $cv->cID = $c->getCollectionID();

        return $cv;
    }

    public function getObjectAttributeCategory()
    {
        return \Core::make('\Concrete\Core\Attribute\Category\PageCategory');
    }

    public function getAttributeValueObject($ak, $createIfNotExists = false)
    {
        if (!is_object($ak)) {
            $ak = CollectionKey::getByHandle($ak);
        }
        $value = false;
        if (is_object($ak)) {
            $value = $this->getObjectAttributeCategory()->getAttributeValue($ak, $this);
        }

        if ($value) {
            return $value;
        } elseif ($createIfNotExists) {
            $attributeValue = new PageValue();
            $attributeValue->setPageID($this->getCollectionID());
            $attributeValue->setVersionID($this->getVersionID());
            $attributeValue->setAttributeKey($ak);

            return $attributeValue;
        }
    }

    public function isApproved()
    {
        return $this->cvIsApproved;
    }

    public function getPublishDate()
    {
        return $this->cvPublishDate;
    }

    public function isMostRecent()
    {
        if (!isset($this->isMostRecent)) {
            $cID = $this->cID;
            $db = Loader::db();
            $q = "select cvID from CollectionVersions where cID = '{$cID}' order by cvID desc";
            $cvID = $db->getOne($q);
            $this->isMostRecent = ($cvID == $this->cvID);
        }

        return $this->isMostRecent;
    }

    public function isNew()
    {
        return $this->cvIsNew;
    }

    public function getVersionID()
    {
        return $this->cvID;
    }

    public function getCollectionID()
    {
        return $this->cID;
    }

    public function getVersionName()
    {
        return $this->cvName;
    }

    public function getVersionComments()
    {
        return $this->cvComments;
    }

    public function getVersionAuthorUserID()
    {
        return $this->cvAuthorUID;
    }

    public function getVersionApproverUserID()
    {
        return $this->cvApproverUID;
    }

    public function getVersionAuthorUserName()
    {
        if ($this->cvAuthorUID > 0) {
            $db = Loader::db();

            return $db->GetOne('select uName from Users where uID = ?', array(
                $this->cvAuthorUID,
            ));
        }
    }

    public function getVersionApproverUserName()
    {
        if ($this->cvApproverUID > 0) {
            $db = Loader::db();

            return $db->GetOne('select uName from Users where uID = ?', array(
                $this->cvApproverUID,
            ));
        }
    }

    public function getCustomAreaStyles()
    {
        if (!isset($this->customAreaStyles)) {
            $db = Loader::db();
            $r = $db->GetAll('select issID, arHandle from CollectionVersionAreaStyles where cID = ? and cvID = ?', array(
                $this->getCollectionID(),
                $this->cvID,
            ));
            $this->customAreaStyles = array();
            foreach ($r as $styles) {
                $this->customAreaStyles[$styles['arHandle']] = $styles['issID'];
            }
        }

        return $this->customAreaStyles;
    }

    /**
     * Gets the date the collection version was created.
     *
     * @return string date formated like: 2009-01-01 00:00:00
     */
    public function getVersionDateCreated()
    {
        return $this->cvDateCreated;
    }

    public function canWrite()
    {
        return $this->cvCanWrite;
    }

    public function setComment($comment)
    {
        $thisCVID = $this->getVersionID();
        $comment = ($comment != null) ? $comment : "Version {$thisCVID}";
        $v = array(
            $comment,
            $thisCVID,
            $this->cID,
        );
        $db = Loader::db();
        $q = "update CollectionVersions set cvComments = ? where cvID = ? and cID = ?";
        $r = $db->query($q, $v);

        $this->versionComments = $comment;
    }

    public function setPublishDate($publishDate)
    {
        $thisCVID = $this->getVersionID();
        $v = array(
            $publishDate,
            $thisCVID,
            $this->cID,
        );

        $db = Loader::db();
        $q = "update CollectionVersions set cvPublishDate = ? where cvID = ? and cID = ?";

        $db->query($q, $v);
    }

    public function createNew($versionComments)
    {
        $db = Loader::db();
        $highestVID = $db->GetOne('select max(cvID) from CollectionVersions where cID = ?', array(
            $this->cID,
        ));
        $newVID = $highestVID + 1;
        $c = Page::getByID($this->cID, $this->cvID);

        $u = new User();
        $versionComments = (!$versionComments) ? t("New Version %s", $newVID) : $versionComments;
        $cvIsNew = 1;
        if ($c->getPageTypeHandle() == STACKS_PAGE_TYPE) {
            $cvIsNew = 0;
        }
        $dh = Loader::helper('date');
        $v = array(
            $this->cID,
            $newVID,
            $c->getCollectionName(),
            $c->getCollectionHandle(),
            $c->getCollectionDescription(),
            $c->getCollectionDatePublic(),
            $dh->getOverridableNow(),
            $versionComments,
            $u->getUserID(),
            $cvIsNew,
            $this->pThemeID,
            $this->pTemplateID,
            $this->cvPublishDate,
        );
        $q = "insert into CollectionVersions (cID, cvID, cvName, cvHandle, cvDescription, cvDatePublic, " .
             "cvDateCreated, cvComments, cvAuthorUID, cvIsNew, pThemeID, pTemplateID, cvPublishDate) " .
			 "values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $r = $db->prepare($q);
        $res = $db->execute($r, $v);

        $category = $this->getObjectAttributeCategory();
        $values = $category->getAttributeValues($this);
        $em = $db->getEntityManager();

        foreach ($values as $value) {
            $value = clone $value;
            /*
             * @var $value PageValue
             */
            $value->setVersionID($this->getVersionID());
            $em->persist($value);
        }

        $q3 = "select faID from CollectionVersionFeatureAssignments where cID = ? and cvID = ?";
        $v3 = array(
            $c->getCollectionID(),
            $this->getVersionID(),
        );
        $r3 = $db->query($q3, $v3);
        while ($row3 = $r3->fetchRow()) {
            $v3 = array(
                intval($c->getCollectionID()),
                $newVID,
                $row3['faID'],
            );
            $db->query("insert into CollectionVersionFeatureAssignments (cID, cvID, faID) values (?, ?, ?)", $v3);
        }

        $q4 = "select pThemeID, scvlID, preset, sccRecordID from CollectionVersionThemeCustomStyles where cID = ? and cvID = ?";
        $v4 = array(
            $c->getCollectionID(),
            $this->getVersionID(),
        );
        $r4 = $db->query($q4, $v4);
        while ($row4 = $r4->fetchRow()) {
            $v4 = array(
                intval($c->getCollectionID()),
                $newVID,
                $row4['pThemeID'],
                $row4['scvlID'],
                $row4['preset'],
                $row4['sccRecordID'],
            );
            $db->query("insert into CollectionVersionThemeCustomStyles (cID, cvID, pThemeID, scvlID, preset, sccRecordID) values (?, ?, ?, ?, ?, ?)", $v4);
        }

        $nv = static::get($c, $newVID);

        $ev = new Event($c);
        $ev->setCollectionVersionObject($nv);
        Events::dispatch('on_page_version_add', $ev);

        $nv->refreshCache();
        // now we return it
        return $nv;
    }

    public function approve($doReindexImmediately = true)
    {
        $db = Loader::db();
        $u = new User();
        $uID = $u->getUserID();
        $cvID = $this->cvID;
        $cID = $this->cID;
        $c = Page::getByID($cID, $this->cvID);

        $ov = Page::getByID($cID, 'ACTIVE');

        $oldHandle = $ov->getCollectionHandle();
        $newHandle = $this->cvHandle;

        // update a collection updated record
        $dh = Loader::helper('date');
        $db->query('update Collections set cDateModified = ? where cID = ?', array(
            $dh->getOverridableNow(),
            $cID,
        ));

        // first we remove approval for the other version of this collection
        $v = array(
            $cID,
        );
        $q = "update CollectionVersions set cvIsApproved = 0 where cID = ?";
        $r = $db->query($q, $v);
        $ov->refreshCache();

        // now we approve our version
        $v2 = array(
            $uID,
            $cID,
            $cvID,
        );
        $q2 = "update CollectionVersions set cvIsNew = 0, cvIsApproved = 1, cvApproverUID = ? where cID = ? and cvID = ?";
        $r = $db->query($q2, $v2);

        // next, we rescan our collection paths for the particular collection, but only if this isn't a generated collection
        // I don't know why but this just isn't reliable. It might be a race condition with the cached page objects?
        /*
         * if ((($oldHandle != $newHandle) || $oldHandle == '') && (!$c->isGeneratedCollection())) {
         */

        $c->rescanCollectionPath();

        // }

        // check for related version edits. This only gets applied when we edit global areas.
        $r = $db->Execute('select cRelationID, cvRelationID from CollectionVersionRelatedEdits where cID = ? and cvID = ?', array(
            $cID,
            $cvID,
        ));
        while ($row = $r->FetchRow()) {
            $cn = Page::getByID($row['cRelationID'], $row['cvRelationID']);
            $cnp = new Permissions($cn);
            if ($cnp->canApprovePageVersions()) {
                $v = $cn->getVersionObject();
                $v->approve();
                $db->Execute('delete from CollectionVersionRelatedEdits where cID = ? and cvID = ? and cRelationID = ? and cvRelationID = ?', array(
                    $cID,
                    $cvID,
                    $row['cRelationID'],
                    $row['cvRelationID'],
                ));
            }
        }

        if ($c->getCollectionInheritance() == 'TEMPLATE') {
            // we make sure to update the cInheritPermissionsFromCID value
            $pType = PageType::getByID($c->getPageTypeID());
            $pTemplate = PageTemplate::getByID($c->getPageTemplateID());
            $masterC = $pType->getPageTypePageTemplateDefaultPageObject($pTemplate);
            $db->Execute('update Pages set cInheritPermissionsFromCID = ? where cID = ?', array(
                $masterC->getCollectionID(),
                $c->getCollectioniD(),
            ));
        }

        $ev = new Event($c);
        $ev->setCollectionVersionObject($this);
        Events::dispatch('on_page_version_approve', $ev);

        $c->reindex(false, $doReindexImmediately);
        $c->writePageThemeCustomizations();
        $this->refreshCache();
    }

    public function discard()
    {
        // discard's my most recent edit that is pending
        $u = new User();
        if ($this->isNew()) {
            $db = Loader::db();
            // check for related version edits. This only gets applied when we edit global areas.
            $r = $db->Execute('select cRelationID, cvRelationID from CollectionVersionRelatedEdits where cID = ? and cvID = ?', array(
                $this->cID,
                $this->cvID,
            ));
            while ($row = $r->FetchRow()) {
                $cn = Page::getByID($row['cRelationID'], $row['cvRelationID']);
                $cnp = new Permissions($cn);
                if ($cnp->canApprovePageVersions()) {
                    $v = $cn->getVersionObject();
                    $v->delete();
                    $db->Execute('delete from CollectionVersionRelatedEdits where cID = ? and cvID = ? and cRelationID = ? and cvRelationID = ?', array(
                        $this->cID,
                        $this->cvID,
                        $row['cRelationID'],
                        $row['cvRelationID'],
                    ));
                }
            }
            $this->delete();
        }
        $this->refreshCache();
    }

    public function canDiscard()
    {
        $db = Loader::db();
        $total = $db->GetOne('select count(cvID) from CollectionVersions where cID = ?', array(
            $this->cID,
        ));

        return $this->isNew() && $total > 1;
    }

    public function removeNewStatus()
    {
        $db = Loader::db();
        $db->query("update CollectionVersions set cvIsNew = 0 where cID = ? and cvID = ?", array(
            $this->cID,
            $this->cvID,
        ));
        $this->refreshCache();
    }

    public function deny()
    {
        $db = Loader::db();
        $cvID = $this->cvID;
        $cID = $this->cID;

        // first we update a collection updated record
        $dh = Loader::helper('date');
        $db->query('update Collections set cDateModified = ? where cID = ?', array(
            $dh->getOverridableNow(),
            $cID,
        ));

        // first we remove approval for all versions of this collection
        $v = array(
            $cID,
        );
        $q = "update CollectionVersions set cvIsApproved = 0 where cID = ?";
        $r = $db->query($q, $v);

        // now we deny our version
        $v2 = array(
            $cID,
            $cvID,
        );
        $q2 = "update CollectionVersions set cvIsApproved = 0, cvApproverUID = 0 where cID = ? and cvID = ?";
        $r2 = $db->query($q2, $v2);
        $this->refreshCache();
    }

    public function delete()
    {
        $db = Loader::db();

        $cvID = $this->cvID;
        $c = Page::getByID($this->cID, $cvID);
        $cID = $c->getCollectionID();

        $q = "select bID, arHandle from CollectionVersionBlocks where cID = ? and cvID = ?";
        $r = $db->query($q, array(
            $cID,
            $cvID,
        ));
        if ($r) {
            while ($row = $r->fetchRow()) {
                if ($row['bID']) {
                    $b = Block::getByID($row['bID'], $c, $row['arHandle']);
                    if (is_object($b)) {
                        $b->deleteBlock();
                    }
                }
                unset($b);
            }
        }

        $features = CollectionVersionFeatureAssignment::getList($this);
        foreach ($features as $fa) {
            $fa->delete();
        }

        $category = \Core::make('Concrete\Core\Attribute\Category\PageCategory');
        $attributes = $category->getAttributeValues($this);
        foreach ($attributes as $attribute) {
            $category->deleteValue($attribute);
        }

        $db->Execute('delete from CollectionVersionBlockStyles where cID = ? and cvID = ?', array(
            $cID,
            $cvID,
        ));
        $db->Execute('delete from CollectionVersionThemeCustomStyles where cID = ? and cvID = ?', array(
            $cID,
            $cvID,
        ));
        $db->Execute('delete from CollectionVersionRelatedEdits where cID = ? and cvID = ?', array(
            $cID,
            $cvID,
        ));
        $db->Execute('delete from CollectionVersionAreaStyles where cID = ? and cvID = ?', array(
            $cID,
            $cvID,
        ));

        $q = "delete from CollectionVersions where cID = '{$cID}' and cvID='{$cvID}'";
        $r = $db->query($q);
        $this->refreshCache();
    }
}
