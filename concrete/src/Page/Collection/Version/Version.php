<?php

namespace Concrete\Core\Page\Collection\Version;

use Concrete\Core\Attribute\ObjectInterface as AttributeObjectInterface;
use Concrete\Core\Attribute\ObjectTrait;
use Concrete\Core\Block\Block;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Attribute\Value\PageValue;
use Concrete\Core\Feature\Assignment\CollectionVersionAssignment as CollectionVersionFeatureAssignment;
use Concrete\Core\Foundation\ConcreteObject;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\Type as PageType;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\ObjectInterface as PermissionObjectInterface;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManagerInterface;
use PDO;

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
     * Get a Version instance given the Collection and a version identifier.
     *
     * @param \Concrete\Core\Page\Collection\Collection $c the collection for which you want the version
     * @param int|string $cvID the specific version ID (or 'ACTIVE', 'SCHEDULED', 'RECENT')
     *
     * @return static
     */
    public static function get($c, $cvID)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);

        $cID = false;
        if ($c instanceof Page) {
            $cID = $c->getCollectionPointerID();
        }
        if (!$cID) {
            $cID = $c->getCollectionID();
        }

        $v = [$cID];
        $q = 'select * from CollectionVersions where cID = ?';

        switch ($cvID) {
            case 'ACTIVE':
                $q .= ' and cvIsApproved = 1 and (cvPublishDate <= ? or cvPublishDate is null) order by cvPublishDate desc limit 1';
                $v[] = $app->make('date')->getOverridableNow();
                break;
            case 'SCHEDULED':
                $q .= ' and cvIsApproved = 1 and (cvPublishDate is not null or cvPublishEndDate is not null) limit 1';
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
     * Get the collection ID.
     *
     * @return int
     */
    public function getCollectionID()
    {
        return $this->cID;
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
     * Is this version approved?
     *
     * @var bool|int|string
     */
    public function isApproved()
    {
        return $this->cvIsApproved;
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
     * Mark this collection version as not new.
     */
    public function removeNewStatus()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->executeQuery(
            'update CollectionVersions set cvIsNew = 0 where cID = ? and cvID = ?',
            [$this->getCollectionID(), $this->getVersionID()]
        );
        $this->cvIsNew = 0;
        $this->refreshCache();
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
     * Get the date/time when the collection version was created.
     *
     * @var string|null
     *
     * @example '2018-21-31 23:59:59'
     */
    public function getVersionDateCreated()
    {
        return $this->cvDateCreated;
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
     * Get the name of the user that approved this collection version.
     *
     * @var string|null|false return NULL if there's no author, false if it has been deleted, or a string otherwise
     */
    public function getVersionAuthorUserName()
    {
        $uID = $this->getVersionAuthorUserID();
        if ($uID > 0) {
            $app = Application::getFacadeApplication();
            $db = $app->make(Connection::class);

            return $db->fetchColumn('select uName from Users where uID = ?', [$uID]);
        }
    }

    /**
     * Get the ID of the user that approved this collection version.
     *
     * @var int|string|null
     */
    public function getVersionApproverUserID()
    {
        return $this->cvApproverUID;
    }

    /**
     * Get the name of the user that approved this collection version.
     *
     * @var string|null|false return NULL if there's no author, false if it has been deleted, or a string otherwise
     */
    public function getVersionApproverUserName()
    {
        $uID = $this->getVersionApproverUserID();
        if ($uID > 0) {
            $app = Application::getFacadeApplication();
            $db = $app->make(Connection::class);

            return $db->fetchColumn('select uName from Users where uID = ?', [$uID]);
        }
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
     * Set the collection version comments.
     *
     * @param string $comment
     */
    public function setComment($comment)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $thisCVID = $this->getVersionID();
        $comment = (string) $comment;
        if ($comment === '') {
            $comment = "Version {$thisCVID}";
        }
        $db->executeQuery(
            'update CollectionVersions set cvComments = ? where cvID = ? and cID = ?',
            [$comment, $thisCVID, $this->getCollectionID()]
        );
        $this->cvComments = $comment;
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
     * Set the scheduled date/time when the collection is published: start.
     *
     * @param string|null $publishDate Date/time in format like '2018-21-31 23:59:59'
     */
    public function setPublishDate($publishDate)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->executeQuery(
            'update CollectionVersions set cvPublishDate = ? where cvID = ? and cID = ?',
            [$publishDate, $this->getVersionID(), $this->getCollectionID()]
        );
        $this->cvPublishDate = $publishDate;
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
     * Set the scheduled date/time when the collection is published: end.
     *
     * @param string|null $publishEndDate Date/time in format like '2018-21-31 23:59:59'
     */
    public function setPublishEndDate($publishEndDate)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->executeQuery(
            'update CollectionVersions set cvPublishEndDate = ? where cvID = ? and cID = ?',
            [$publishEndDate, $this->getVersionID(), $this->getCollectionID()]
        );
        $this->cvPublishEndDate = $publishEndDate;
    }

    /**
     * Get the date the collection version was approved.
     *
     * @var string|null
     *
     * @example '2018-21-31 23:59:59'
     */
    public function getVersionDateApproved()
    {
        return $this->cvDateApproved;
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
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $dh = $app->make('date');
        $u = new User();
        $cvID = $this->getVersionID();
        $cID = $this->getCollectionID();
        $c = Page::getByID($cID, $cvID);
        $currentActivePage = Page::getByID($cID, 'ACTIVE');

        $oldHandle = $currentActivePage->getCollectionHandle();
        $newHandle = $this->cvHandle;

        // update a collection updated record
        $db->executeQuery('update Collections set cDateModified = ? where cID = ?', [$dh->getOverridableNow(), $cID]);

        // Remove all publish dates before setting the new ones, if any
        $this->clearPublishStartDate($db);

        if ($this->getPublishEndDate() && strtotime($this->getPublishEndDate()) <= time()) {
            $this->clearPublishEndDate($db);
        }

        if ($cvPublishDate || $cvPublishEndDate) {
            // remove approval for all versions except the current one because a scheduled version is being processed
            $oldVersion = $currentActivePage->getVersionObject();
            $this->setPublishDate($cvPublishDate);
            $this->setPublishEndDate($cvPublishEndDate);
            $db->executeQuery(
                'update CollectionVersions set cvIsApproved = 0 where cID = ? and cvID != ?',
                [$cID, $oldVersion->getVersionID()]
            );
        } else {
            // remove approval for the other version of this collection
            $db->executeQuery(
                'update CollectionVersions set cvIsApproved = 0 where cID = ?',
                [$cID]
            );
        }

        $currentActivePage->refreshCache();

        // now we approve our version
        $db->update(
            'CollectionVersions',
            [
                'cvIsNew' => 0,
                'cvIsApproved' => 1,
                'cvApproverUID' => $u->getUserID(),
                'cvDateApproved' => $dh->getOverridableNow(),
            ],
            [
                'cID' => $cID,
                'cvID' => $cvID,
            ]
        );

        // next, we rescan our collection paths for the particular collection, but only if this isn't a generated collection
        if (!$c->isGeneratedCollection() && $oldHandle != $newHandle) {
            $c->rescanCollectionPath();
        }

        // check for related version edits. This only gets applied when we edit global areas.
        $rs = $db->executeQuery(
            'select cRelationID, cvRelationID from CollectionVersionRelatedEdits where cID = ? and cvID = ?',
            [$cID, $cvID]
        );
        while (($row = $rs->fetch(PDO::FETCH_ASSOC)) !== false) {
            $cn = Page::getByID($row['cRelationID'], $row['cvRelationID']);
            $cnp = new Checker($cn);
            if ($cnp->canApprovePageVersions()) {
                $v = $cn->getVersionObject();
                $v->approve();
                $db->delete('CollectionVersionRelatedEdits', [
                    'cID' => $cID,
                    'cvID' => $cvID,
                    'cRelationID' => $row['cRelationID'],
                    'cvRelationID' => $row['cvRelationID'],
                ]);
            }
        }

        if ($c->getCollectionInheritance() === 'TEMPLATE') {
            // we make sure to update the cInheritPermissionsFromCID value
            $pType = PageType::getByID($c->getPageTypeID());
            $masterC = $pType->getPageTypePageTemplateDefaultPageObject();
            $db->executeQuery(
                'update Pages set cInheritPermissionsFromCID = ? where cID = ?',
                [(int) $masterC->getCollectionID(), $c->getCollectioniD()]
            );
        }

        $ev = new Event($c);
        $ev->setCollectionVersionObject($this);
        $app->make('director')->dispatch('on_page_version_approve', $ev);

        $c->reindex(false, $doReindexImmediately);
        $c->writePageThemeCustomizations();
        $this->refreshCache();
    }

    /**
     * Mark this collection version as not approved.
     */
    public function deny()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $dh = $app->make('date');

        $cvID = $this->getVersionID();
        $cID = $this->getCollectionID();

        // Update the collection updated field
        $db->executeQuery(
            'update Collections set cDateModified = ? where cID = ?',
            [$dh->getOverridableNow(), $cID]
        );

        // Remove approval for all versions of this collection
        $db->executeQuery(
            'update CollectionVersions set cvIsApproved = 0 where cID = ?',
            [$cID]
        );

        // Deny our version
        $db->executeQuery(
            'update CollectionVersions set cvIsApproved = 0, cvApproverUID = 0 where cID = ? and cvID = ?',
            [$cID, $cvID]
        );

        $this->refreshCache();
    }

    /**
     * Is this the most recent version?
     *
     * @return bool
     */
    public function isMostRecent()
    {
        if ($this->isMostRecent === null) {
            $app = Application::getFacadeApplication();
            $db = $app->make(Connection::class);
            $cvID = $db->fetchColumn('select cvID from CollectionVersions where cID = ? order by cvID desc', [$this->getCollectionID()]);
            $this->isMostRecent = $cvID == $this->getVersionID();
        }

        return $this->isMostRecent;
    }

    /**
     * Get the custom area style IDs.
     *
     * @return array key: area handle, value: the inline stle set ID
     */
    public function getCustomAreaStyles()
    {
        if (!isset($this->customAreaStyles)) {
            $app = Application::getFacadeApplication();
            $db = $app->make(Connection::class);
            $rs = $db->fetchAll('select issID, arHandle from CollectionVersionAreaStyles where cID = ? and cvID = ?', [
                $this->getCollectionID(),
                $this->getVersionID(),
            ]);
            $this->customAreaStyles = [];
            foreach ($rs as $styles) {
                $this->customAreaStyles[$styles['arHandle']] = $styles['issID'];
            }
        }

        return $this->customAreaStyles;
    }

    /**
     * Clear the cache for this collection.
     */
    public function refreshCache()
    {
        $app = Application::getFacadeApplication();
        $cache = $app->make('cache/request');
        if ($cache->isEnabled()) {
            $cache->delete('page/' . $this->getCollectionID());
        }
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
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $dh = $app->make('date');
        $em = $app->make(EntityManagerInterface::class);
        $category = $this->getObjectAttributeCategory();

        $cID = $this->getCollectionID();
        $c = Page::getByID($cID, $this->getVersionID());
        $u = new User();

        $newVID = 1 + (int) $db->fetchColumn('select max(cvID) from CollectionVersions where cID = ?', [$cID]);
        $versionComments = (string) $versionComments;
        if ($versionComments === '') {
            $versionComments = t('New Version %s', $newVID);
        }
        $cvIsNew = 1;
        if ($c->getPageTypeHandle() === STACKS_PAGE_TYPE) {
            $cvIsNew = 0;
        }

        $db->insert('CollectionVersions', [
            'cID' => $cID,
            'cvID' => $newVID,
            'cvName' => $c->getCollectionName(),
            'cvHandle' => $c->getCollectionHandle(),
            'cvDescription' => $c->getCollectionDescription(),
            'cvDatePublic' => $c->getCollectionDatePublic(),
            'cvDateCreated' => $dh->getOverridableNow(),
            'cvComments' => $versionComments,
            'cvAuthorUID' => $u->getUserID(),
            'cvIsNew' => $cvIsNew,
            'pThemeID' => $this->pThemeID,
            'pTemplateID' => $this->pTemplateID,
            // important: cvPublishDate used to be the same for the new version as it is for the current,
            // but it made it impossible to create a version that wasn't scheduled once you scheduled a version
            // so I'm turning it off for now - AE
            'cvPublishDate' => null,
        ]);

        $values = $category->getAttributeValues($this);
        foreach ($values as $value) {
            $value = clone $value;
            /* @var \Concrete\Core\Entity\Attribute\Value\PageValue $value */
            $value->setVersionID($newVID);
            $em->persist($value);
        }
        $em->flush();

        $copyFields = 'faID';
        $db->executeQuery(
            "insert into CollectionVersionFeatureAssignments (cID, cvID, {$copyFields}) select ?, ?, {$copyFields} from CollectionVersionFeatureAssignments where cID = ? and cvID = ?",
            [$c->getCollectionID(), $newVID, $c->getCollectionID(), $this->getVersionID()]
        );

        $copyFields = 'pThemeID, scvlID, preset, sccRecordID';
        $db->executeQuery(
            "insert into CollectionVersionThemeCustomStyles (cID, cvID, {$copyFields}) select ?, ?, {$copyFields} from CollectionVersionThemeCustomStyles where cID = ? and cvID = ?",
            [$c->getCollectionID(), $newVID, $c->getCollectionID(), $this->getVersionID()]
        );

        $nv = static::get($c, $newVID);

        $ev = new Event($c);
        $ev->setCollectionVersionObject($nv);

        $app->make('director')->dispatch('on_page_version_add', $ev);

        $nv->refreshCache();

        return $nv;
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
            $app = Application::getFacadeApplication();
            $db = $app->make(Connection::class);
            if ($db->fetchColumn('select cvID from CollectionVersions where cID = ? limit 1', [$this->getCollectionID()]) !== false) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Discard my most recent edit that is pending.
     */
    public function discard()
    {
        if ($this->isNew()) {
            $app = Application::getFacadeApplication();
            $db = $app->make(Connection::class);
            // check for related version edits. This only gets applied when we edit global areas.
            $rs = $db->executeQuery(
                'select cRelationID, cvRelationID from CollectionVersionRelatedEdits where cID = ? and cvID = ?',
                [$this->getCollectionID(), $this->getVersionID()]
            );
            while (($row = $rs->fetch(PDO::FETCH_ASSOC)) !== false) {
                $cn = Page::getByID($row['cRelationID'], $row['cvRelationID']);
                $cnp = new Checker($cn);
                if ($cnp->canApprovePageVersions()) {
                    $v = $cn->getVersionObject();
                    $v->delete();
                    $db->delete('CollectionVersionRelatedEdits', [
                        'cID' => $this->getCollectionID(),
                        'cvID' => $this->getVersionID(),
                        'cRelationID' => $row['cRelationID'],
                        'cvRelationID' => $row['cvRelationID'],
                    ]);
                }
            }
            $this->delete();
            $this->refreshCache();
        }
    }

    /**
     * Delete this version and its related data (blocks, feature assignments, attributes, custom styles, ...).
     */
    public function delete()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);

        $cvID = $this->getVersionID();
        $c = Page::getByID($this->getCollectionID(), $cvID);
        $cID = $c->getCollectionID();

        $rs = $db->executeQuery(
            'select bID, arHandle from CollectionVersionBlocks where cID = ? and cvID = ?',
            [$cID, $cvID]
        );
        while (($row = $rs->fetch(PDO::FETCH_ASSOC)) !== false) {
            $b = Block::getByID($row['bID'], $c, $row['arHandle']);
            if ($b) {
                $b->deleteBlock();
            }
        }

        $features = CollectionVersionFeatureAssignment::getList($this);
        foreach ($features as $fa) {
            $fa->delete();
        }

        $category = $this->getObjectAttributeCategory();
        $attributes = $category->getAttributeValues($this);
        foreach ($attributes as $attribute) {
            $category->deleteValue($attribute);
        }

        $db->executeQuery(
            'delete from CollectionVersionBlockStyles where cID = ? and cvID = ?',
            [$cID, $cvID]
        );
        $db->executeQuery(
            'delete from CollectionVersionThemeCustomStyles where cID = ? and cvID = ?',
            [$cID, $cvID]
        );
        $db->executeQuery(
            'delete from CollectionVersionRelatedEdits where cID = ? and cvID = ?',
            [$cID, $cvID]
        );
        $db->executeQuery(
            'delete from CollectionVersionAreaStyles where cID = ? and cvID = ?',
            [$cID, $cvID]
        );
        $db->executeQuery(
            'delete from PageTypeComposerOutputBlocks where cID = ? and cvID = ?',
            [$cID, $cvID]
        );

        $db->executeQuery(
            'delete from CollectionVersions where cID = ? and cvID = ?',
            [$cID, $cvID]
        );
        $this->refreshCache();
    }

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
        return \Concrete\Core\Permission\Response\CollectionVersionResponse::class;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\ObjectInterface::getPermissionAssignmentClassName()
     */
    public function getPermissionAssignmentClassName()
    {
        return \Concrete\Core\Permission\Assignment\PageAssignment::class;
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
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\ObjectInterface::getObjectAttributeCategory()
     *
     * @return \Concrete\Core\Attribute\Category\PageCategory
     */
    public function getObjectAttributeCategory()
    {
        $app = Application::getFacadeApplication();

        return $app->make(\Concrete\Core\Attribute\Category\PageCategory::class);
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
        $akCategory = $this->getObjectAttributeCategory();
        if (!is_object($ak)) {
            $ak = $akCategory->getAttributeKeyByHandle($ak);
        }
        $value = is_object($ak) ? $akCategory->getAttributeValue($ak, $this) : false;

        if ($value) {
            return $value;
        }
        if ($createIfNotExists) {
            $attributeValue = new PageValue();
            $attributeValue->setPageID($this->getCollectionID());
            $attributeValue->setVersionID($this->getVersionID());
            $attributeValue->setAttributeKey($ak);

            return $attributeValue;
        }
    }

    /**
     * @param \Concrete\Core\Database\Connection\Connection $db
     */
    private function clearPublishStartDate(Connection $db)
    {
        $db->executeQuery(
            'update CollectionVersions set cvPublishDate = NULL where cID = ?',
            [$this->getCollectionID()]
        );
        $this->cvPublishDate = null;
    }

    /**
     * @param \Concrete\Core\Database\Connection\Connection $db
     */
    private function clearPublishEndDate(Connection $db)
    {
        $db->executeQuery(
            'update CollectionVersions set cvPublishEndDate = NULL where cID = ?',
            [$this->getCollectionID()]
        );
        $this->cvPublishEndDate = null;
    }
}
