<?php

namespace Concrete\Core\Page\Collection\Version;

use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Attribute\ObjectInterface as AttributeObjectInterface;
use Concrete\Core\Attribute\ObjectTrait;
use Concrete\Core\Block\Block;
use Concrete\Core\Entity\Attribute\Value\PageValue;
use Concrete\Core\Feature\Assignment\CollectionVersionAssignment as CollectionVersionFeatureAssignment;
use Concrete\Core\Foundation\ConcreteObject;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\Type as PageType;
use Concrete\Core\Permission\Checker as Permissions;
use Concrete\Core\Permission\ObjectInterface as PermissionObjectInterface;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\User\User;

class Version extends ConcreteObject implements PermissionObjectInterface, AttributeObjectInterface
{
    use ObjectTrait;

    // Properties from database record
    public $cvID;
    public $cvIsApproved;
    public $cvIsNew;
    public $cvHandle;
    public $cvName;
    public $cvDescription;
    public $cvDateCreated;
    public $cvDatePublic;
    public $pTemplateID;
    public $cvAuthorUID;
    public $cvApproverUID;
    public $cvComments;
    public $pThemeID;
    public $cvPublishDate;
    public $cvPublishEndDate;

    // Other properties
    public $cID;
    protected $attributes = [];
    public $layoutStyles = [];
    protected $isMostRecent;
    protected $customAreaStyles;

    /**
     * @return string
     */
    public function getPermissionObjectIdentifier()
    {
        return $this->getCollectionID() . ':' . $this->getVersionID();
    }

    /**
     * @return string
     */
    public function getPermissionResponseClassName()
    {
        return '\\Concrete\\Core\\Permission\\Response\\CollectionVersionResponse';
    }

    /**
     * @return string
     */
    public function getPermissionAssignmentClassName()
    {
        return '\\Concrete\\Core\\Permission\\Assignment\\PageAssignment';
    }

    /**
     * @return string
     */
    public function getPermissionObjectKeyCategoryHandle()
    {
        return 'page';
    }

    /**
     * Refreshes the cache of the collection
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
     * @param \Concrete\Core\Page\Collection $c The collection for which you want the version.
     * @param int|string $cvID The specific version ID (or 'ACTIVE', 'SCHEDULED', 'RECENT').
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
        $v = [$cID];

        $q = "select * from CollectionVersions where cID = ?";

        $now = new \DateTime();

        switch ($cvID) {
            case 'ACTIVE':
                $q .= ' and cvIsApproved = 1 and (cvPublishDate is NULL or cvPublishDate <= ?) ';
                $v[] = $now->format('Y-m-d H:i:s');
                break;
            case 'SCHEDULED':
                $q .= ' and cvIsApproved = 1 and (cvPublishDate is not NULL or cvPublishEndDate is not null) ';
                break;
            case 'RECENT':
                $q .= ' order by cvID desc';
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
     * @return \Concrete\Core\Attribute\Category\PageCategory
     */
    public function getObjectAttributeCategory()
    {
        $app = Facade::getFacadeApplication();

        return $app->make('\Concrete\Core\Attribute\Category\PageCategory');
    }

    /**
     * Gets the object of a collection attribute.
     *
     * @param \Concrete\Core\Attribute\Key\CollectionKey|string $ak The object or the handle of the collection attribute
     * @param bool $createIfNotExists Whether the collection attribute has to be generated if not exists
     *
     * @return PageValue|null
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
     * Checks if the collection version is approved
     *
     * @return bool
     */
    public function isApproved()
    {
        return $this->cvIsApproved;
    }

    /**
     * Gets the publish date of the collection version
     *
     * @return string date formated like: 2009-01-01 00:00:00
     */
    public function getPublishDate()
    {
        return $this->cvPublishDate;
    }

    /**
     * Gets the publish end date of the collection version
     *
     * @return string date formated like: 2009-01-01 00:00:00
     */
    public function getPublishEndDate()
    {
        return $this->cvPublishEndDate;
    }

    /**
     * Checks if the collection version is the most recent one
     *
     * @return bool
     */
    public function isMostRecent()
    {
        if (!isset($this->isMostRecent)) {
            $app = Facade::getFacadeApplication();
            $db = $app->make('database')->connection();
            $cvID = $db->fetchColumn('select cvID from CollectionVersions where cID = ? order by cvID desc', [$this->cID]);
            $this->isMostRecent = ($cvID == $this->cvID);
        }

        return $this->isMostRecent;
    }

    /**
     * Checks if the collection version is the most recent
     *
     * @return bool
     */
    public function isNew()
    {
        return $this->cvIsNew;
    }

    /**
     * Gets the id of the collection version
     *
     * @return int
     */
    public function getVersionID()
    {
        return $this->cvID;
    }

    /**
     * Gets the id of the collection
     *
     * @return int
     */
    public function getCollectionID()
    {
        return $this->cID;
    }

    /**
     * Gets the name of the collection
     *
     * @return string
     */
    public function getVersionName()
    {
        return $this->cvName;
    }

    /**
     * Gets the comments of the collection version
     *
     * @return string
     */
    public function getVersionComments()
    {
        return $this->cvComments;
    }

    /**
     * Gets the id of the user that created the version
     *
     * @return int
     */
    public function getVersionAuthorUserID()
    {
        return $this->cvAuthorUID;
    }

    /**
     * Gets the id of the user that approved the version
     *
     * @return int
     */
    public function getVersionApproverUserID()
    {
        return $this->cvApproverUID;
    }

    /**
     * Gets the username of the user that created the version
     *
     * @return string|null
     */
    public function getVersionAuthorUserName()
    {
        if ($this->cvAuthorUID > 0) {
            $app = Facade::getFacadeApplication();
            $db = $app->make('database')->connection();

            return $db->fetchColumn('select uName from Users where uID = ?', [
                $this->cvAuthorUID,
            ]);
        }
    }

    /**
     * Gets the username of the user that approved the version
     *
     * @return string|null
     */
    public function getVersionApproverUserName()
    {
        if ($this->cvApproverUID > 0) {
            $app = Facade::getFacadeApplication();
            $db = $app->make('database')->connection();

            return $db->fetchColumn('select uName from Users where uID = ?', [
                $this->cvApproverUID,
            ]);
        }
    }

    /**
     * Gets the custom styles of an area of the collection version.
     *
     * @return array|null
     */
    public function getCustomAreaStyles()
    {
        if (!isset($this->customAreaStyles)) {
            $app = Facade::getFacadeApplication();
            $db = $app->make('database')->connection();
            $r = $db->fetchAll('select issID, arHandle from CollectionVersionAreaStyles where cID = ? and cvID = ?', [
                $this->getCollectionID(),
                $this->cvID,
            ]);
            $this->customAreaStyles = [];
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

    /**
     * Sets the comment of a collection version
     *
     * @param string $comment Comment of the collection version
     */
    public function setComment($comment)
    {
        $thisCVID = $this->getVersionID();
        $comment = ($comment != null) ? $comment : "Version {$thisCVID}";
        $v = [
            $comment,
            $thisCVID,
            $this->cID,
        ];
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $q = "update CollectionVersions set cvComments = ? where cvID = ? and cID = ?";
        $db->executeQuery($q, $v);
        $this->cvComments = $comment;
    }

    /**
     * Sets the publish date of a collection version
     *
     * @param DateTime $publishDate Publish date of the collection version
     */
    public function setPublishDate($publishDate)
    {
        $thisCVID = $this->getVersionID();
        $v = [
            $publishDate,
            $thisCVID,
            $this->cID,
        ];

        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $q = "update CollectionVersions set cvPublishDate = ? where cvID = ? and cID = ?";
        $db->executeQuery($q, $v);
        $this->cvPublishDate = $publishDate;
    }

    /**
     * Sets the publish end date of a collection version
     *
     * @param DateTime $publishEndDate Publish end date of the collection version
     */
    public function setPublishEndDate($publishEndDate)
    {
        $thisCVID = $this->getVersionID();
        $v = [
            $publishEndDate,
            $thisCVID,
            $this->cID,
        ];

        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $q = "update CollectionVersions set cvPublishEndDate = ? where cvID = ? and cID = ?";
        $db->executeQuery($q, $v);
        $this->cvPublishEndDate = $publishEndDate;
    }

    /**
     * Creates a new collection version
     *
     * @param DateTime $versionComments Comment of the collection version
     *
     * @return static
     */
    public function createNew($versionComments)
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $highestVID = $db->fetchColumn('select max(cvID) from CollectionVersions where cID = ?', [
            $this->cID,
        ]);
        $newVID = ($highestVID === false) ? 1 : ($highestVID + 1);
        $c = Page::getByID($this->cID, $this->cvID);

        $u = new User();
        $versionComments = (!$versionComments) ? t("New Version %s", $newVID) : $versionComments;
        $cvIsNew = 1;
        if ($c->getPageTypeHandle() === STACKS_PAGE_TYPE) {
            $cvIsNew = 0;
        }
        $dh = $app->make('helper/date');
        $v = [
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
            null,
        ];
        // important: cvPublishDate used to be the same for the new version as it is for the current , but it made it
        // impossible to create a version that wasn't scheduled once you scheduled a version so I'm turning it off for
        // now - AE

        $q = "insert into CollectionVersions (cID, cvID, cvName, cvHandle, cvDescription, cvDatePublic, " .
            "cvDateCreated, cvComments, cvAuthorUID, cvIsNew, pThemeID, pTemplateID, cvPublishDate) " .
            "values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $db->executeQuery($q, $v);

        $category = $this->getObjectAttributeCategory();
        $values = $category->getAttributeValues($this);
        $em = $app->make('Doctrine\ORM\EntityManagerInterface');

        foreach ($values as $value) {
            $value = clone $value;
            /*
             * @var $value PageValue
             */
            $value->setVersionID($newVID);
            $em->persist($value);
        }
        $em->flush();

        $q3 = "select faID from CollectionVersionFeatureAssignments where cID = ? and cvID = ?";
        $v3 = [
            $c->getCollectionID(),
            $this->getVersionID(),
        ];
        $r3 = $db->executeQuery($q3, $v3);
        while ($row3 = $r3->fetch()) {
            $v3 = [
                intval($c->getCollectionID()),
                $newVID,
                $row3['faID'],
            ];
            $db->query("insert into CollectionVersionFeatureAssignments (cID, cvID, faID) values (?, ?, ?)", $v3);
        }

        $q4 = "select pThemeID, scvlID, preset, sccRecordID from CollectionVersionThemeCustomStyles where cID = ? and cvID = ?";
        $v4 = [
            $c->getCollectionID(),
            $this->getVersionID(),
        ];
        $r4 = $db->executeQuery($q4, $v4);
        while ($row4 = $r4->fetch()) {
            $v4 = [
                (int) $c->getCollectionID(),
                $newVID,
                $row4['pThemeID'],
                $row4['scvlID'],
                $row4['preset'],
                $row4['sccRecordID'],
            ];
            $db->executeQuery("insert into CollectionVersionThemeCustomStyles (cID, cvID, pThemeID, scvlID, preset, sccRecordID) values (?, ?, ?, ?, ?, ?)", $v4);
        }

        $nv = static::get($c, $newVID);

        $ev = new Event($c);
        $ev->setCollectionVersionObject($nv);

        $app->make('director')->dispatch('on_page_version_add', $ev);

        $nv->refreshCache();
        // now we return it
        return $nv;
    }

    /**
     * Approves a collection version
     *
     * @param bool $doReindexImmediately Whether we want to reindex the contents of the collection after its approval
     * @param DateTime $cvPublishDate Publish date of the collection version
     * @param DateTime $cvPublishEndDate Publish end date of the collection version
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
        $db->executeQuery('update Collections set cDateModified = ? where cID = ?', [
            $dh->getOverridableNow(),
            $cID,
        ]);

        // Remove all publish dates before setting the new ones, if any
        $this->clearPublishStartDate();

        if ($cvPublishDate || $cvPublishEndDate) {
            // remove approval for all versions except the current one because a scheduled version is being processed
            $oldVersion = $ov->getVersionObject();
            $v = [$cID, $oldVersion->cvID];
            $q = "update CollectionVersions set cvIsApproved = 0 where cID = ? and cvID != ?";
            $this->setPublishDate($cvPublishDate);
            $this->setPublishEndDate($cvPublishEndDate);
        } else {
            // remove approval for the other version of this collection
            $v = [$cID];
            $q = "update CollectionVersions set cvIsApproved = 0 where cID = ?";
        }

        $r = $db->executeQuery($q, $v);
        $ov->refreshCache();

        // now we approve our version
        $v2 = [
            $uID,
            $cID,
            $cvID,
        ];
        $q2 = "update CollectionVersions set cvIsNew = 0, cvIsApproved = 1, cvApproverUID = ? where cID = ? and cvID = ?";
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
        $r = $db->executeQuery('select cRelationID, cvRelationID from CollectionVersionRelatedEdits where cID = ? and cvID = ?', [
            $cID,
            $cvID,
        ]);
        while ($row = $r->fetch()) {
            $cn = Page::getByID($row['cRelationID'], $row['cvRelationID']);
            $cnp = new Permissions($cn);
            if ($cnp->canApprovePageVersions()) {
                $v = $cn->getVersionObject();
                $v->approve();
                $db->executeQuery('delete from CollectionVersionRelatedEdits where cID = ? and cvID = ? and cRelationID = ? and cvRelationID = ?', [
                    $cID,
                    $cvID,
                    $row['cRelationID'],
                    $row['cvRelationID'],
                ]);
            }
        }

        if ($c->getCollectionInheritance() == 'TEMPLATE') {
            // we make sure to update the cInheritPermissionsFromCID value
            $pType = PageType::getByID($c->getPageTypeID());
            $masterC = $pType->getPageTypePageTemplateDefaultPageObject();
            $db->executeQuery('update Pages set cInheritPermissionsFromCID = ? where cID = ?', [
                (int) $masterC->getCollectionID(),
                $c->getCollectioniD(),
            ]);
        }

        $ev = new Event($c);
        $ev->setCollectionVersionObject($this);
        $app->make('director')->dispatch('on_page_version_approve', $ev);

        $c->reindex(false, $doReindexImmediately);
        $c->writePageThemeCustomizations();
        $this->refreshCache();
    }

    /**
     * Discards the collection version
     */
    public function discard()
    {
        // discard's my most recent edit that is pending
        if ($this->isNew()) {
            $app = Facade::getFacadeApplication();
            $db = $app->make('database')->connection();
            // check for related version edits. This only gets applied when we edit global areas.
            $r = $db->executeQuery('select cRelationID, cvRelationID from CollectionVersionRelatedEdits where cID = ? and cvID = ?', [
                $this->cID,
                $this->cvID,
            ]);
            while ($row = $r->fetch()) {
                $cn = Page::getByID($row['cRelationID'], $row['cvRelationID']);
                $cnp = new Permissions($cn);
                if ($cnp->canApprovePageVersions()) {
                    $v = $cn->getVersionObject();
                    $v->delete();
                    $db->executeQuery('delete from CollectionVersionRelatedEdits where cID = ? and cvID = ? and cRelationID = ? and cvRelationID = ?', [
                        $this->cID,
                        $this->cvID,
                        $row['cRelationID'],
                        $row['cvRelationID'],
                    ]);
                }
            }
            $this->delete();
        }
        $this->refreshCache();
    }

    /**
     * Checks if a collection version can be discarded
     *
     * @return bool
     */
    public function canDiscard()
    {
        $result = false;
        if ($this->isNew()) {
            $app = Facade::getFacadeApplication();
            $db = $app->make('database')->connection();
            $total = $db->fetchColumn('select count(cvID) from CollectionVersions where cID = ?', [
                $this->cID,
            ]);
            if ($total > 1) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Sets the collection version as not new
     */
    public function removeNewStatus()
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $db->executeQuery("update CollectionVersions set cvIsNew = 0 where cID = ? and cvID = ?", [
            $this->cID,
            $this->cvID,
        ]);
        $this->refreshCache();
    }

    /**
     * Denies a collection version
     */
    public function deny()
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $cvID = $this->cvID;
        $cID = $this->cID;

        // first we update a collection updated record
        $dh = $app->make('helper/date');
        $db->executeQuery('update Collections set cDateModified = ? where cID = ?', [
            $dh->getOverridableNow(),
            $cID,
        ]);

        // first we remove approval for all versions of this collection
        $v = [
            $cID,
        ];
        $q = "update CollectionVersions set cvIsApproved = 0 where cID = ?";
        $db->executeQuery($q, $v);

        // now we deny our version
        $v2 = [
            $cID,
            $cvID,
        ];
        $q2 = "update CollectionVersions set cvIsApproved = 0, cvApproverUID = 0 where cID = ? and cvID = ?";
        $db->executeQuery($q2, $v2);
        $this->refreshCache();
    }

    /**
     * Deletes a collection version
     */
    public function delete()
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();

        $cvID = $this->cvID;
        $c = Page::getByID($this->cID, $cvID);
        $cID = $c->getCollectionID();

        $q = "select bID, arHandle from CollectionVersionBlocks where cID = ? and cvID = ?";
        $r = $db->executeQuery($q, [
            $cID,
            $cvID,
        ]);
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

        $db->executeQuery('delete from CollectionVersionBlockStyles where cID = ? and cvID = ?', [
            $cID,
            $cvID,
        ]);
        $db->executeQuery('delete from CollectionVersionThemeCustomStyles where cID = ? and cvID = ?', [
            $cID,
            $cvID,
        ]);
        $db->executeQuery('delete from CollectionVersionRelatedEdits where cID = ? and cvID = ?', [
            $cID,
            $cvID,
        ]);
        $db->executeQuery('delete from CollectionVersionAreaStyles where cID = ? and cvID = ?', [
            $cID,
            $cvID,
        ]);

        $db->executeQuery('delete from PageTypeComposerOutputBlocks where cID = ? and cvID = ?', [
            $cID,
            $cvID,
        ]);

        $q = "delete from CollectionVersions where cID = '{$cID}' and cvID='{$cvID}'";
        $db->executeQuery($q);
        $this->refreshCache();
    }

    /**
     * Clear the publish start date of the collection version
     */
    private function clearPublishStartDate()
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $q = "update CollectionVersions set cvPublishDate = NULL where cID = ?";

        $db->executeQuery($q, [$this->cID]);
    }

    /**
     * Checks if there are scheduled versions of pages to approve or to deny
     */
    public static function checkScheduledVersions()
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $datetime = $app->make('helper/date');
        $now = $datetime->date('Y-m-d G:i:s');
        $r = $db->executeQuery('select * from CollectionVersions where cvIsApproved = 1 and (cvPublishDate is not NULL or cvPublishEndDate is not null)');

        while ($row = $r->fetch()) {
            if ($row['cID']) {
                $collection = Page::getByID($row['cID']);
                if (is_object($collection)) {
                    $scheduledVersion = self::get($collection, "SCHEDULED");
                    $publishDate = $scheduledVersion->getPublishDate();
                    $publishEndDate = $scheduledVersion->getPublishEndDate();

                    if ($publishEndDate) {
                        if (strtotime($now) >= strtotime($publishEndDate)) {
                            $scheduledVersion->deny();
                        }
                    }

                    if ($publishDate) {
                        if (strtotime($now) >= strtotime($publishDate)) {
                            $scheduledVersion->approve();
                        }
                    }
                }
            }
        }
    }
}
