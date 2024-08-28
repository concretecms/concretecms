<?php
namespace Concrete\Core\Page\Collection\Version;

use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Attribute\ObjectTrait;
use Concrete\Core\Cache\Level\RequestCache;
use Concrete\Core\Entity\Attribute\Value\PageValue;
use Concrete\Core\Foundation\ConcreteObject;
use Block;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Page\Collection\Collection;
use Concrete\Core\Page\Page;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Concrete\Core\Workflow\Command\DeletePageVersionRequestsCommand;
use Doctrine\DBAL\Query\QueryBuilder;
use PageType;
use Permissions;
use Concrete\Core\User\User;
use Concrete\Core\Attribute\ObjectInterface as AttributeObjectInterface;
use Concrete\Core\Permission\ObjectInterface as PermissionObjectInterface;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Page\Cloner;
use Concrete\Core\Page\ClonerOptions;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Error\UserMessageException;

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
     * @deprecated what's deprecated is the public part of this property: use the isApproved() or the isApprovedNow() methods instead
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
     * The identifier of any custom skin attached to this page version.
     *
     * @var string
     */
    public $pThemeSkinIdentifier;

    /**
     * @deprecated what's deprecated is the public part of this property: use the getPublishDate() / setPublishDate() / setPublishInterval() methods instead
     *
     * @var string|null
     */
    public $cvPublishDate;

    /**
     * @deprecated what's deprecated is the public part of this property: use the getPublishEndDate() / setPublishEndDate() / setPublishInterval() methods instead
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
     * @param int|string $cvID the specific version ID (or 'ACTIVE', 'SCHEDULED', 'RECENT', 'RECENT_UNAPPROVED')
     *
     * @return static
     */
    public static function get($c, $cvID)
    {
        $cv = new static();

        $app = Facade::getFacadeApplication();
        /** @var Numbers $numbers */
        $numbers = $app->make(Numbers::class);

        if ($c instanceof Collection && !$c->isError() && ($numbers->integer($cvID) || in_array($cvID, ['ACTIVE', 'SCHEDULED', 'RECENT', 'RECENT_UNAPPROVED']))) {
            /** @var RequestCache $cache */
            $cache = $app->make('cache/request');
            $key = '/Page/Collection/' . $c->getCollectionID() . '/Version/' . $cvID;
            if ($cache->isEnabled()) {
                $item = $cache->getItem($key);
                if ($item->isHit()) {
                    return $item->get();
                }
            }

            $db = $app->make('database')->connection();
            $now = $app->make('date')->getOverridableNow();

            $cID = false;
            if ($c instanceof \Concrete\Core\Page\Page) {
                $cID = $c->getCollectionPointerID();
            }
            if (!$cID) {
                $cID = $c->getCollectionID();
            }
            $v = [$cID];

            $q = 'select * from CollectionVersions where cID = ?';

            switch ($cvID) {
                case 'ACTIVE':
                    $q .= ' and cvIsApproved = 1 and (cvPublishDate is null or cvPublishDate <= ?) and (cvPublishEndDate is null or cvPublishEndDate >= ?) order by cvPublishDate desc limit 1';
                    $v[] = $now;
                    $v[] = $now;
                    break;
                case 'SCHEDULED':
                    $q .= ' and cvIsApproved = 1 and (cvPublishDate is not null and cvPublishDate > ?) order by cvPublishDate limit 1';
                    $v[] = $now;
                    break;
                case 'RECENT':
                    $q .= ' order by cvID desc limit 1';
                    break;
                case 'RECENT_UNAPPROVED':
                    $q .= 'and (cvIsApproved = 0 or cvIsApproved IS NULL) order by cvID desc limit 1';
                    break;
                default:
                    $v[] = $cvID;
                    $q .= ' and cvID = ?';
                    break;
            }

            $row = $db->fetchAssoc($q, $v);

            if ($row !== false) {
                $cv->setPropertiesFromArray($row);
            } else {
                $cv->loadError(VERSION_NOT_FOUND);
            }

            $cv->cID = $c->getCollectionID();

            if (isset($item) && $item->isMiss()) {
                $item->set($cv);
                $cache->save($item);
            }
        } else {
            $cv->loadError(VERSION_NOT_FOUND);
        }

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
     *
     * @see isApprovedNow()
     */
    public function isApproved()
    {
        return $this->cvIsApproved;
    }

    /**
     * Is this version approved and in the publish interval?
     *
     * @var string|int|\DateTime|null $when a date/time representation (empty: now)
     * @return bool
     */
    public function isApprovedNow($when = null)
    {
        if (!$this->isApproved()) {
            return false;
        }
        $start = $this->getPublishDate();
        $end = $this->getPublishEndDate();
        if (!$start && !$end) {
            return true;
        }
        $dh = Facade::getFacadeApplication()->make('date');
        if ($when) {
            $when = $dh->toDB($when);
        }
        if (!$when) {
            $when = $dh->getOverridableNow();
        }
        if ($start && $start > $when) {
            return false;
        }
        if ($end && $end < $when) {
            return false;
        }

        return true;
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
    public function getVersionComments($sanitize = true)
    {
        return $sanitize ? h($this->cvComments) : $this->cvComments;
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
     * @param string|\DateTime|int|null $publishDate the scheduled date/time when the collection is published (start)
     *
     * @throws \Concrete\Core\Error\UserMessageException if the start of the publish date/time is its end.
     */
    public function setPublishDate($publishDate)
    {
        $this->setPublishInterval($publishDate, $this->getPublishEndDate());
    }

    /**
     * Set the scheduled date/time when the collection is published: end.
     *
     * @param string|\DateTime|int|null $publishEndDate the scheduled date/time when the collection is published (end)
     *
     * @throws \Concrete\Core\Error\UserMessageException if the start of the publish date/time is its end.
     */
    public function setPublishEndDate($publishEndDate)
    {
        $this->setPublishInterval($this->getPublishDate(), $publishEndDate);
    }

    /**
     * Set the scheduled date/time when the collection is published.
     *
     * @param string|\DateTime|int|null $startDateTime the scheduled date/time when the collection is published (start)
     * @param string|\DateTime|int|null $endDateTime the scheduled date/time when the collection is published (end)
     *
     * @throws \Concrete\Core\Error\UserMessageException if the start of the publish date/time is its end.
     */
    public function setPublishInterval($startDateTime, $endDateTime)
    {
        $app = Facade::getFacadeApplication();
        $dh = $app->make('helper/date');
        $startDateTime = $dh->toDB($startDateTime) ?: null;
        $endDateTime = $dh->toDB($endDateTime) ?: null;
        if ($startDateTime && $endDateTime && $startDateTime > $endDateTime) {
            throw new UserMessageException(t('The initial date/time must be before the final date/time.'));
        }
        $db = $app->make(Connection::class);
        $db->update(
            'CollectionVersions',
            [
                'cvPublishDate' => $startDateTime,
                'cvPublishEndDate' => $endDateTime,
            ],
            [
                'cID' => $this->getCollectionID(),
                'cvID' => $this->getVersionID(),
            ]
        );
        $this->cvPublishDate = $startDateTime;
        $this->cvPublishEndDate = $endDateTime;
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
     * @param string|\DateTime|int|null $cvPublishDate the scheduled date/time when the collection is published (start)
     * @param string|\DateTime|int|null $cvPublishEndDate the scheduled date/time when the collection is published (end)
     * @param bool $keepOtherScheduling Set to true to keep scheduling of other versions
     *                                  (e.g., users can view the current live version until the publish date of this version).
     *                                  Set to false (default) to disapprove all other versions
     *                                  (e.g., users can't see this page until the publish date of this version, even if this page is live now).
     *
     * @since 9.0.0 Added $keepOtherScheduling argument
     *
     * @throws \Concrete\Core\Error\UserMessageException if the start of the publish date/time is its end.
     */
    public function approve($doReindexImmediately = true, $cvPublishDate = null, $cvPublishEndDate = null, bool $keepOtherScheduling = false)
    {
        $app = Facade::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);
        /** @var Date $dh */
        $dh = $app->make('helper/date');
        $u = $app->make(User::class);
        $uID = $u->getUserID();
        $cvID = $this->getVersionID();
        $cID = $this->getCollectionID();
        $now = $dh->getOverridableNow();

        $cvPublishDate = $dh->toDB($cvPublishDate) ?: null;
        $cvPublishEndDate = $dh->toDB($cvPublishEndDate) ?: null;

        if ($cvPublishDate !== null && $cvPublishEndDate !== null && $cvPublishDate > $cvPublishEndDate) {
            throw new UserMessageException(t('The initial date/time must be before the final date/time.'));
        }

        $pageWithActiveVersion = Page::getByID($cID, 'ACTIVE');

        $oldHandle = $pageWithActiveVersion->getCollectionHandle();
        $newHandle = $this->cvHandle;

        // update a collection updated record
        $this->updateCollectionDateModified();

        // disapprove other versions
        $disapproveVersionsQuery = $db->createQueryBuilder();
        $disapproveVersionsQuery->update('CollectionVersions')
            ->set('cvIsApproved', ':cvIsApproved')
            ->where('cID = :cID')
            ->andWhere('cvIsApproved = 1')
            ->setParameter('cvIsApproved', 0)
            ->setParameter('cID', $cID);
        if ($keepOtherScheduling && ($cvPublishDate || $cvPublishEndDate)) {
            // We can disapprove live versions only their scheduling is already end
            $expr = $disapproveVersionsQuery->expr();
            $disapproveVersionsQuery->andWhere($expr->and($expr->isNotNull('cvPublishEndDate'), $expr->lte('cvPublishEndDate', ':now')));
            $disapproveVersionsQuery->setParameter(':now', $dh->getOverridableNow());
        }
        $disapproveVersionsQuery->execute();

        $pageWithActiveVersion->refreshCache();

        // now we approve our version
        $db->update(
            'CollectionVersions',
            [
                'cvIsNew' => 0,
                'cvIsApproved' => 1,
                'cvApproverUID' => $uID,
                'cvDateApproved' => $now,
                'cvPublishDate' => $cvPublishDate,
                'cvPublishEndDate' => $cvPublishEndDate,
            ],
            [
                'cID' => $cID,
                'cvID' => $cvID,
            ]
        );
        $this->cvIsNew = 0;
        $this->cvIsApproved = 1;
        $this->cvApproverUID = $uID;
        $this->cvDateApproved = $now;
        $this->cvPublishDate = $cvPublishDate;
        $this->cvPublishEndDate = $cvPublishEndDate;
        $c = Page::getByID($cID, $cvID);
        // next, we rescan our collection paths for the particular collection, but only if this isn't a generated collection
        if ($oldHandle != $newHandle && !$c->isGeneratedCollection()) {
            $c->rescanCollectionPath();
        }

        // check for related version edits. This only gets applied when we edit global areas.
        $r = $db->executeQuery(
            'select cRelationID, cvRelationID from CollectionVersionRelatedEdits where cID = ? and cvID = ?',
            [$cID, $cvID]
        );
        while (($row = $r->fetch()) !== false) {
            $cn = Page::getByID($row['cRelationID'], $row['cvRelationID']);
            $cnp = new Checker($cn);
            if ($cnp->canApprovePageVersions()) {
                $v = $cn->getVersionObject();
                $v->approve();
                $db->delete(
                    'CollectionVersionRelatedEdits',
                    [
                        'cID' => $cID,
                        'cvID' => $cvID,
                        'cRelationID' => $row['cRelationID'],
                        'cvRelationID' => $row['cvRelationID'],
                    ]
                );
            }
        }

        if ($c->getCollectionInheritance() == 'TEMPLATE') {
            // we make sure to update the cInheritPermissionsFromCID value
            $pType = PageType::getByID($c->getPageTypeID());
            $masterC = $pType->getPageTypePageTemplateDefaultPageObject();
            $db->executeQuery(
                'update Pages set cInheritPermissionsFromCID = ? where cID = ?',
                [(int) $masterC->getCollectionID(), $c->getCollectionID()]
            );
        }
        $ev = new Event($c);
        $ev->setUser($u);
        $ev->setCollectionVersionObject($this);
        $app->make('director')->dispatch('on_page_version_approve', $ev);

        $c->reindex($doReindexImmediately);
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

    private function updateCollectionDateModified(): void
    {
        $app = Facade::getFacadeApplication();
        $cID = $this->getCollectionID();
        /** @var Date $dh */
        $dh = $app->make(Date::class);
        $now = $dh->getOverridableNow();
        /** @var QueryBuilder $qb */
        $qb = $app->make(Connection::class)->createQueryBuilder();
        $qb->update('Collections')
            ->set('cDateModified', ':cDateModified')
            ->where('cID = :cID')
            ->setParameter('cDateModified', $now)
            ->setParameter('cID', $cID)
            ->execute();
    }

    /**
     * Mark this collection version as not approved.
     */
    public function deny(): void
    {
        $app = Facade::getFacadeApplication();
        $cvID = $this->getVersionID();
        $cID = $this->getCollectionID();

        // first we update a collection updated record
        $this->updateCollectionDateModified();

        // now we deny our version
        /** @var QueryBuilder $qb */
        $qb = $app->make(Connection::class)->createQueryBuilder();
        $qb->update('CollectionVersions')
            ->set('cvIsApproved', ':cvIsApproved')
            ->set('cvApproverUID', ':cvApproverUID')
            ->where('cID = :cID')
            ->andWhere('cvID = :cvID')
            ->setParameter('cvIsApproved', 0)
            ->setParameter('cvApproverUID', 0)
            ->setParameter('cID', $cID)
            ->setParameter('cvID', $cvID)
            ->execute();

        $this->refreshCache();
    }

    /**
     * Delete this version and its related data (blocks, attributes, custom styles, ...).
     */
    public function delete()
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();

        $cvID = $this->cvID;
        $c = Page::getByID($this->cID, $cvID);
        $cID = $c->getCollectionID();
        $u = $app->make(User::class);

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

        $db->executeQuery('delete from CollectionVersionBlocks where cID = ? and cvID = ?', array(
            $cID,
            $cvID,
        ));

        $q = "delete from CollectionVersions where cID = '{$cID}' and cvID='{$cvID}'";
        $db->executeQuery($q);
        $this->refreshCache();

        // Delete uncompleted workflow requests for this version
        $deletePageVersionRequestsCommand = new DeletePageVersionRequestsCommand($cID, $cvID);
        $app->executeCommand($deletePageVersionRequestsCommand);

        $ev = new Event($c);
        $ev->setUser($u);
        $ev->setCollectionVersionObject($this);
        $app->make('director')->dispatch('on_page_version_delete', $ev);
    }

    /**
     * Make sure that other collection versions aren't approved and valid at the same time as this version.
     */
    private function avoidApprovalOverlapping()
    {
        if (!$this->isApproved()) {
            return;
        }
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $dh = $app->make('helper/date');
        $qbBase = $db->createQueryBuilder();
        $x = $qbBase->expr();
        $qbBase->update('CollectionVersions', 'cv')
            ->andWhere($x->eq('cv.cvIsApproved', 1))
            ->andWhere($x->eq('cv.cID', $qbBase->createNamedParameter($this->getCollectionID())))
            ->andWhere($x->neq('cv.cvID', $qbBase->createNamedParameter($this->getVersionID())))
        ;
        $startDate = $this->getPublishDate() ?: null;
        $endDate = $this->getPublishEndDate() ?: null;
        $changes = [];

        // Let's unapprove other approved collection versions whose approval time is all within this collection version
        if ($startDate !== null && $endDate !== null) {
            // This collection version is published from $startDate until $endDate:
            // let's unapprove the other collection versions that start at or after $startDate and end at or before $endDate
            $qb = clone $qbBase;
            $changes[] = $qb
                ->set('cv.cvIsApproved', 0)
                ->andWhere($x->isNotNull('cv.cvPublishDate'))
                ->andWhere($x->gte('cv.cvPublishDate', $qb->createNamedParameter($startDate)))
                ->andWhere($x->isNotNull('cv.cvPublishEndDate'))
                ->andWhere($x->lte('cv.cvPublishEndDate', $qb->createNamedParameter($endDate)))
                ->execute()
            ;
        } elseif ($startDate !== null) {
            // This collection version is published from $startDate until forever:
            // let's unapprove the other collection versions that start at or after $startDate
            $qb = clone $qbBase;
            $changes[] = $qb
                ->set('cv.cvIsApproved', 0)
                ->andWhere($x->isNotNull('cv.cvPublishDate'))
                ->andWhere($x->gte('cv.cvPublishDate', $qb->createNamedParameter($startDate)))
                ->execute()
            ;
        } elseif ($endDate !== null) {
            // This collection version is published from ever until $endDate:
            // let's unapprove the other collection versions that end at or before $endDate
            $qb = clone $qbBase;
            $changes[] = $qb
                ->set('cv.cvIsApproved', 0)
                ->andWhere($x->isNotNull('cv.cvPublishEndDate'))
                ->andWhere($x->lte('cv.cvPublishEndDate', $qb->createNamedParameter($endDate)))
                ->execute()
            ;
        } else {
            // This collection version is published from ever and until forever:
            // let's unapprove all the other collection versions
            $qb = clone $qbBase;
            $changes[] = $qb
                ->set('cv.cvIsApproved', 0)
                ->execute()
            ;
        }

        if ($endDate != null) {
            // This collection version is published until $endDate:
            // set the initial date/time of the other collection versions that start and end at or before $endDate
            $minOthersStartDate = $endDate ? $dh->toDB(strtotime($endDate) + 1) : null;
            $qb = clone $qbBase;
            $changes[] = $qb
                ->set('cv.cvPublishDate', $qb->createNamedParameter($minOthersStartDate))
                ->andWhere($x->orX(
                    $x->isNull('cv.cvPublishDate'),
                    $x->lte('cv.cvPublishDate', $qb->createNamedParameter($endDate))
                ))
                ->andWhere($x->orX(
                    $x->isNull('cv.cvPublishEndDate'),
                    $x->gte('cv.cvPublishEndDate', $qb->createNamedParameter($minOthersStartDate))
                ))
                ->execute()
            ;
        }

        if ($startDate !== null) {
            // This collection version is published from $startDate
            // set the final date/time of the other collection versions that end at or after $startDate
            $maxOthersEndDate = $startDate ? $dh->toDB(strtotime($startDate) - 1) : null;
            $qb = clone $qbBase;
            $changes[] = $qb
                ->set('cv.cvPublishEndDate', $qb->createNamedParameter($maxOthersEndDate))
                ->andWhere($x->orX(
                    $x->isNull('cv.cvPublishEndDate'),
                    $x->gte('cv.cvPublishEndDate', $qb->createNamedParameter($startDate))
                ))
                ->andWhere($x->orX(
                    $x->isNull('cv.cvPublishDate'),
                    $x->lte('cv.cvPublishDate', $qb->createNamedParameter($maxOthersEndDate))
                ))
                ->execute()
            ;
        }
        if (count(array_filter($changes)) > 0) {
            $this->refreshCache();
        }
    }
}
