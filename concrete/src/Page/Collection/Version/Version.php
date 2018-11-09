<?php
namespace Concrete\Core\Page\Collection\Version;

use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Attribute\ObjectTrait;
use Concrete\Core\Entity\Attribute\Value\PageValue;
use Concrete\Core\Foundation\ConcreteObject;
use Block;
use Page;
use PageType;
use Permissions;
use User;
use Concrete\Core\Attribute\ObjectInterface as AttributeObjectInterface;
use Concrete\Core\Permission\ObjectInterface as PermissionObjectInterface;
use Concrete\Core\Feature\Assignment\CollectionVersionAssignment as CollectionVersionFeatureAssignment;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Page\Cloner;
use Concrete\Core\Page\ClonerOptions;

class Version extends ConcreteObject implements PermissionObjectInterface, AttributeObjectInterface
{
    use ObjectTrait;

    // Properties from database record

    /**
     * @deprecated what's deprecated is the public part of this property: use the getVersionID() method instead
     *
     * @var int|string
     */
    public $cvID;

    /**
     * @deprecated what's deprecated is the public part of this property: use the isApproved() method instead
     *
     * @var bool|int|string
     */
    public $cvIsApproved;

    /**
     * @deprecated what's deprecated is the public part of this property: use the isNew() / removeNewStatus() method instead
     *
     * @var bool|int|string
     */
    public $cvIsNew;

    /**
     * The collection version handle.
     *
     * @var string|null
     */
    public $cvHandle;

    /**
     * @deprecated what's deprecated is the public part of this property: use the getVersionName() method instead
     *
     * @var string|null
     */
    public $cvName;

    /**
     * The collection version description.
     *
     * @var string|null
     */
    public $cvDescription;

    /**
     * @deprecated what's deprecated is the public part of this property: use the getVersionDateCreated() method instead
     *
     * @var string|null
     */
    public $cvDateCreated;

    /**
     * The date/time when this collection version was made public.
     *
     * @var string|null
     *
     * @example '2018-21-31 23:59:59'
     */
    public $cvDatePublic;

    /**
     * The ID of the page template.
     *
     * @var int|string
     */
    public $pTemplateID;

    /**
     * @deprecated what's deprecated is the public part of this property: use the getVersionAuthorUserID() method instead
     *
     * @var int|string|null
     */
    public $cvAuthorUID;

    /**
     * @deprecated what's deprecated is the public part of this property: use the getVersionApproverUserID() method instead
     *
     * @var int|string|null
     */
    public $cvApproverUID;

    /**
     * @deprecated what's deprecated is the public part of this property: use the getVersionComments() / setComment() methods instead
     *
     * @var string|null
     */
    public $cvComments;

    /**
     * The ID of the page theme.
     *
     * @var int|string
     */
    public $pThemeID;

    /**
     * @deprecated what's deprecated is the public part of this property: use the getPublishDate() / setPublishDate() methods instead
     *
     * @var string|null
     */
    public $cvPublishDate;

    /**
     * @deprecated what's deprecated is the public part of this property: use the getPublishEndDate() / setPublishEndDate() methods instead
     *
     * @var string|null
     */
    public $cvPublishEndDate;

    /**
     * @deprecated what's deprecated is the public part of this property: use the getVersionDateApproved() method instead
     *
     * @var string|null
     */
    public $cvDateApproved;

    // Other properties

    /**
     * @deprecated what's deprecated is the public part of this property: use the getCollectionID() method instead
     *
     * @var int
     */
    public $cID;

    protected $attributes = array();

    public $layoutStyles = array();

    /**
     * Is this the most recent version?
     *
     * @var bool|null
     *
     * @see \Concrete\Core\Page\Collection\Version\Version::isMostRecent()
     */
    protected $isMostRecent;

    /**
     * The custom area style IDs.
     *
     * @var array|null
     *
     * @see \Concrete\Core\Page\Collection\Version\Version::getCustomAreaStyles()
     */
    protected $customAreaStyles;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\ObjectInterface::getPermissionObjectIdentifier()
     */
    public function getPermissionObjectIdentifier()
    {
        return $this->getCollectionID() . ':' . $this->getVersionID();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\ObjectInterface::getPermissionResponseClassName()
     */
    public function getPermissionResponseClassName()
    {
        return '\\Concrete\\Core\\Permission\\Response\\CollectionVersionResponse';
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
     * Clear the cache for this collection.
     */
    public function refreshCache()
    {
        $app = Facade::getFacadeApplication();
        $cache = $app->make('cache/request');
        if ($cache->isEnabled()) {
            $cache->delete('page/'.$this->getCollectionID());
        }
    }

    /**
     * Get a Version instance given the Collection and a version identifier.
     *
     * @param \Concrete\Core\Page\Collection\Collection $c the collection for which you want the version
     * @param int|string $cvID the specific version ID (or 'ACTIVE', 'SCHEDULED', 'RECENT')
     *
     * @return static
     */
    public static function get($c, $cvID)
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();

        $cID = false;
        if ($c instanceof \Concrete\Core\Page\Page) {
            $cID = $c->getCollectionPointerID();
        }
        if (!$cID) {
            $cID = $c->getCollectionID();
        }
        $v = array($cID);

        $q = "select * from CollectionVersions where cID = ?";

        $now = new \DateTime();

        switch ($cvID) {
            case 'ACTIVE':
                $q .= ' and cvIsApproved = 1 and (cvPublishDate <= ? or cvPublishDate is null) order by cvPublishDate desc limit 1';
                $v[] = $now->format('Y-m-d H:i:s');
                break;
            case 'SCHEDULED':
                $q .= ' and cvIsApproved = 1 and (cvPublishDate is not NULL or cvPublishEndDate is not null) limit 1';
                break;
            case 'RECENT':
                $q .= ' order by cvID desc limit 1';
                break;
            default:
                $v[] = $cvID;
                $q .= ' and cvID = ?';
                break;
        }

        $row = $db->fetchAssoc($q, $v);
        $cv = new static();

        if ($row !== false) {
            $cv->setPropertiesFromArray($row);
        }

        $cv->cID = $c->getCollectionID();

        return $cv;
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
        $app = Facade::getFacadeApplication();

        return $app->make('\Concrete\Core\Attribute\Category\PageCategory');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\ObjectInterface::getAttributeValueObject()
     *
     * @return \Concrete\Core\Entity\Attribute\Value\PageValue|null
     */
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

    /**
     * Is this version approved?
     *
     * @return bool|int|string
     */
    public function isApproved()
    {
        return $this->cvIsApproved;
    }

    /**
     * Get the scheduled date/time when the collection is published: start.
     *
     * @return string|null
     *
     * @example '2018-21-31 23:59:59'
     */
    public function getPublishDate()
    {
        return $this->cvPublishDate;
    }

    /**
     * Get the scheduled date/time when the collection is published: end.
     *
     * @return string|null
     *
     * @example '2018-21-31 23:59:59'
     */
    public function getPublishEndDate()
    {
        return $this->cvPublishEndDate;
    }

    /**
     * Is this the most recent version?
     *
     * @return bool
     */
    public function isMostRecent()
    {
        if (!isset($this->isMostRecent)) {
            $app = Facade::getFacadeApplication();
            $db = $app->make('database')->connection();
            $cvID = $db->fetchColumn('select cvID from CollectionVersions where cID = ? order by cvID desc', array($this->cID));
            $this->isMostRecent = ($cvID == $this->cvID);
        }

        return $this->isMostRecent;
    }

    /**
     * Is this a new version?
     *
     * @return bool|number|string
     */
    public function isNew()
    {
        return $this->cvIsNew;
    }

    /**
     * Get the collection version ID.
     *
     * @return int|string
     */
    public function getVersionID()
    {
        return $this->cvID;
    }

    /**
     * Get the collection ID.
     *
     * @return int
     */
    public function getCollectionID()
    {
        return $this->cID;
    }

    /**
     * The collection version name.
     *
     * @return string|null
     */
    public function getVersionName()
    {
        return $this->cvName;
    }

    /**
     * Get the collection version comments.
     *
     * @return string|null
     */
    public function getVersionComments()
    {
        return $this->cvComments;
    }

    /**
     * Get the ID of the user that created this collection version.
     *
     * @return int|string|null
     */
    public function getVersionAuthorUserID()
    {
        return $this->cvAuthorUID;
    }

    /**
     * Get the ID of the user that approved this collection version.
     *
     * @return int|string|null
     */
    public function getVersionApproverUserID()
    {
        return $this->cvApproverUID;
    }

    /**
     * Get the name of the user that approved this collection version.
     *
     * @return string|null|false return NULL if there's no author, false if it has been deleted, or a string otherwise
     */
    public function getVersionAuthorUserName()
    {
        if ($this->cvAuthorUID > 0) {
            $app = Facade::getFacadeApplication();
            $db = $app->make('database')->connection();

            return $db->fetchColumn('select uName from Users where uID = ?', array(
                $this->cvAuthorUID,
            ));
        }
    }

    /**
     * Get the name of the user that approved this collection version.
     *
     * @return string|null|false return NULL if there's no author, false if it has been deleted, or a string otherwise
     */
    public function getVersionApproverUserName()
    {
        if ($this->cvApproverUID > 0) {
            $app = Facade::getFacadeApplication();
            $db = $app->make('database')->connection();

            return $db->fetchColumn('select uName from Users where uID = ?', array(
                $this->cvApproverUID,
            ));
        }
    }

    /**
     * Get the custom area style IDs.
     *
     * @return array key: area handle, value: the inline stle set ID
     */
    public function getCustomAreaStyles()
    {
        if (!isset($this->customAreaStyles)) {
            $app = Facade::getFacadeApplication();
            $db = $app->make('database')->connection();
            $r = $db->fetchAll('select issID, arHandle from CollectionVersionAreaStyles where cID = ? and cvID = ?', array(
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
     * Get the date/time when the collection version was created.
     *
     * @return string|null
     *
     * @example '2018-21-31 23:59:59'
     */
    public function getVersionDateCreated()
    {
        return $this->cvDateCreated;
    }

    /**
     * Get the date the collection version was approved.
     *
     * @return string|null
     *
     * @example '2018-21-31 23:59:59'
     */
    public function getVersionDateApproved()
    {
        return $this->cvDateApproved;
    }

    /**
     * Set the collection version comments.
     *
     * @param string $comment
     */
    public function setComment($comment)
    {
        $thisCVID = $this->getVersionID();
        $comment = ($comment != null) ? $comment : "Version {$thisCVID}";
        $v = array(
            $comment,
            $thisCVID,
            $this->cID,
        );
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $q = "update CollectionVersions set cvComments = ? where cvID = ? and cID = ?";
        $db->executeQuery($q, $v);
        $this->cvComments = $comment;
    }

    /**
     * Set the scheduled date/time when the collection is published: start.
     *
     * @param string|null $publishDate Date/time in format like '2018-21-31 23:59:59'
     */
    public function setPublishDate($publishDate)
    {
        $thisCVID = $this->getVersionID();
        $v = array(
            $publishDate,
            $thisCVID,
            $this->cID,
        );

        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $q = "update CollectionVersions set cvPublishDate = ? where cvID = ? and cID = ?";
        $db->executeQuery($q, $v);
        $this->cvPublishDate = $publishDate;
    }

    /**
     * Set the scheduled date/time when the collection is published: end.
     *
     * @param string|null $publishEndDate Date/time in format like '2018-21-31 23:59:59'
     */
    public function setPublishEndDate($publishEndDate)
    {
        $thisCVID = $this->getVersionID();
        $v = array(
            $publishEndDate,
            $thisCVID,
            $this->cID,
        );

        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $q = "update CollectionVersions set cvPublishEndDate = ? where cvID = ? and cID = ?";
        $db->executeQuery($q, $v);
        $this->cvPublishEndDate = $publishEndDate;
    }

    /**
     * Create a new version for the same collection as this collection version.
     *
     * @param string $versionComments the new collection version comments
     *
     * @return \Concrete\Core\Page\Collection\Version\Version
     */
    public function createNew($versionComments)
    {
        $app = Facade::getFacadeApplication();
        $cloner = $app->make(Cloner::class);
        $clonerOptions = $app->build(ClonerOptions::class)
            ->setCopyContents(false)
            ->setVersionComments($versionComments)
        ;
        $myCollection = Page::getByID($this->getCollectionID());
        $newVersion = $cloner->cloneCollectionVersion($this, $myCollection, $clonerOptions);

        return $newVersion;
    }

    /**
     * Approve this collection version.
     *
     * @param bool $doReindexImmediately reindex the collection contents now? Otherwise it's reindexing will just be scheduled
     * @param string|null $cvPublishDate the scheduled date/time when the collection is published (start)
     * @param string|null $cvPublishEndDate the scheduled date/time when the collection is published (end)
     */
    public function approve($doReindexImmediately = true, $cvPublishDate = null, $cvPublishEndDate = null)
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $u = new User();
        $uID = $u->getUserID();
        $cvID = $this->cvID;
        $cID = $this->cID;
        $c = Page::getByID($cID, $this->cvID);

        // Current active
        $ov = Page::getByID($cID, 'ACTIVE');

        $oldHandle = $ov->getCollectionHandle();
        $newHandle = $this->cvHandle;

        // update a collection updated record
        $dh = $app->make('helper/date');
        $db->executeQuery('update Collections set cDateModified = ? where cID = ?', array(
            $dh->getOverridableNow(),
            $cID,
        ));

        // Remove all publish dates before setting the new ones, if any
        $this->clearPublishStartDate();

        if ($this->getPublishEndDate()) {
            $now = $dh->date('Y-m-d G:i:s');
            if (strtotime($now) >= strtotime($this->getPublishEndDate())) {
                $this->clearPublishEndDate();
            }
        }

        if ($cvPublishDate || $cvPublishEndDate) {
            // remove approval for all versions except the current one because a scheduled version is being processed
            $oldVersion = $ov->getVersionObject();
            $v = array($cID, $oldVersion->cvID);
            $q = "update CollectionVersions set cvIsApproved = 0 where cID = ? and cvID != ?";
            $this->setPublishDate($cvPublishDate);
            $this->setPublishEndDate($cvPublishEndDate);
        } else {
            // remove approval for the other version of this collection
            $v = array($cID);
            $q = "update CollectionVersions set cvIsApproved = 0 where cID = ?";
        }

        $r = $db->executeQuery($q, $v);
        $ov->refreshCache();

        // now we approve our version
        $v2 = array(
            $uID,
            $dh->getOverridableNow(),
            $cID,
            $cvID,
        );
        $q2 = "update CollectionVersions set cvIsNew = 0, cvIsApproved = 1, cvApproverUID = ?, cvDateApproved = ? where cID = ? and cvID = ?";
        $db->executeQuery($q2, $v2);

        // next, we rescan our collection paths for the particular collection, but only if this isn't a generated collection
        $shouldRescanCollectionPath = true;
        if ($c->isGeneratedCollection()) {
            $shouldRescanCollectionPath = false;
        } elseif ($oldHandle == $newHandle) {
            $shouldRescanCollectionPath = false;
        }
        if ($shouldRescanCollectionPath) {

            $c->rescanCollectionPath();

        }

        // check for related version edits. This only gets applied when we edit global areas.
        $r = $db->executeQuery('select cRelationID, cvRelationID from CollectionVersionRelatedEdits where cID = ? and cvID = ?', array(
            $cID,
            $cvID,
        ));
        while ($row = $r->fetch()) {
            $cn = Page::getByID($row['cRelationID'], $row['cvRelationID']);
            $cnp = new Permissions($cn);
            if ($cnp->canApprovePageVersions()) {
                $v = $cn->getVersionObject();
                $v->approve();
                $db->executeQuery('delete from CollectionVersionRelatedEdits where cID = ? and cvID = ? and cRelationID = ? and cvRelationID = ?', array(
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
            $masterC = $pType->getPageTypePageTemplateDefaultPageObject();
            $db->executeQuery('update Pages set cInheritPermissionsFromCID = ? where cID = ?', array(
                (int) $masterC->getCollectionID(),
                $c->getCollectioniD(),
            ));
        }

        $ev = new Event($c);
        $ev->setCollectionVersionObject($this);
        $app->make('director')->dispatch('on_page_version_approve', $ev);

        $c->reindex(false, $doReindexImmediately);
        $c->writePageThemeCustomizations();
        $this->refreshCache();
    }

    /**
     * Discard my most recent edit that is pending.
     */
    public function discard()
    {
        if ($this->isNew()) {
            $app = Facade::getFacadeApplication();
            $db = $app->make('database')->connection();
            // check for related version edits. This only gets applied when we edit global areas.
            $r = $db->executeQuery('select cRelationID, cvRelationID from CollectionVersionRelatedEdits where cID = ? and cvID = ?', array(
                $this->cID,
                $this->cvID,
            ));
            while ($row = $r->fetch()) {
                $cn = Page::getByID($row['cRelationID'], $row['cvRelationID']);
                $cnp = new Permissions($cn);
                if ($cnp->canApprovePageVersions()) {
                    $v = $cn->getVersionObject();
                    $v->delete();
                    $db->executeQuery('delete from CollectionVersionRelatedEdits where cID = ? and cvID = ? and cRelationID = ? and cvRelationID = ?', array(
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

    /**
     * Check if this collection version can be discarded.
     *
     * @return bool
     */
    public function canDiscard()
    {
        $result = false;
        if ($this->isNew()) {
            $app = Facade::getFacadeApplication();
            $db = $app->make('database')->connection();
            $total = $db->fetchColumn('select count(cvID) from CollectionVersions where cID = ?', array(
                $this->cID,
            ));
            if ($total > 1) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Mark this collection version as not new.
     */
    public function removeNewStatus()
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $db->executeQuery("update CollectionVersions set cvIsNew = 0 where cID = ? and cvID = ?", array(
            $this->cID,
            $this->cvID,
        ));
        $this->refreshCache();
    }

    /**
     * Mark this collection version as not approved.
     */
    public function deny()
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $cvID = $this->cvID;
        $cID = $this->cID;

        // first we update a collection updated record
        $dh = $app->make('helper/date');
        $db->executeQuery('update Collections set cDateModified = ? where cID = ?', array(
            $dh->getOverridableNow(),
            $cID,
        ));

        // first we remove approval for all versions of this collection
        $v = array(
            $cID,
        );
        $q = "update CollectionVersions set cvIsApproved = 0 where cID = ?";
        $db->executeQuery($q, $v);

        // now we deny our version
        $v2 = array(
            $cID,
            $cvID,
        );
        $q2 = "update CollectionVersions set cvIsApproved = 0, cvApproverUID = 0 where cID = ? and cvID = ?";
        $db->executeQuery($q2, $v2);
        $this->refreshCache();
    }

    /**
     * Delete this version and its related data (blocks, feature assignments, attributes, custom styles, ...).
     */
    public function delete()
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();

        $cvID = $this->cvID;
        $c = Page::getByID($this->cID, $cvID);
        $cID = $c->getCollectionID();

        $q = "select bID, arHandle from CollectionVersionBlocks where cID = ? and cvID = ?";
        $r = $db->executeQuery($q, array(
            $cID,
            $cvID,
        ));
        while ($row = $r->fetch()) {
            if ($row['bID']) {
                $b = Block::getByID($row['bID'], $c, $row['arHandle']);
                if (is_object($b)) {
                    $b->deleteBlock();
                }
                unset($b);
            }
        }

        $features = CollectionVersionFeatureAssignment::getList($this);
        foreach ($features as $fa) {
            $fa->delete();
        }

        $category = $app->make('Concrete\Core\Attribute\Category\PageCategory');
        $attributes = $category->getAttributeValues($this);
        foreach ($attributes as $attribute) {
            $category->deleteValue($attribute);
        }

        $db->executeQuery('delete from CollectionVersionBlockStyles where cID = ? and cvID = ?', array(
            $cID,
            $cvID,
        ));
        $db->executeQuery('delete from CollectionVersionThemeCustomStyles where cID = ? and cvID = ?', array(
            $cID,
            $cvID,
        ));
        $db->executeQuery('delete from CollectionVersionRelatedEdits where cID = ? and cvID = ?', array(
            $cID,
            $cvID,
        ));
        $db->executeQuery('delete from CollectionVersionAreaStyles where cID = ? and cvID = ?', array(
            $cID,
            $cvID,
        ));

        $db->executeQuery('delete from PageTypeComposerOutputBlocks where cID = ? and cvID = ?', array(
            $cID,
            $cvID,
        ));

        $q = "delete from CollectionVersions where cID = '{$cID}' and cvID='{$cvID}'";
        $db->executeQuery($q);
        $this->refreshCache();
    }

    /**
     * Clear the publish start date and mark this version as unapproved.
     */
    private function clearPublishStartDate()
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $q = "update CollectionVersions set cvPublishDate = NULL , cvIsApproved = 0 where cID = ? AND cvPublishDate IS NOT NULL";

        $db->executeQuery($q, array($this->cID));
    }

    /**
     * Clear the publish end date.
     */
    private function clearPublishEndDate()
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $q = "update CollectionVersions set cvPublishEndDate = NULL where cID = ?";

        $db->executeQuery($q, array($this->cID));
    }

}
