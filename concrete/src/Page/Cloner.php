<?php

namespace Concrete\Core\Page;

use Concrete\Core\Area\Area;
use Concrete\Core\Entity\Attribute\Value\PageValue;
use Concrete\Core\Localization\Service\Date as DateHelper;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Page\Collection\Collection;
use Concrete\Core\Page\Collection\Version\Event as CollectionVersionEvent;
use Concrete\Core\Page\Collection\Version\Version;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Page\Statistics as PageStatistics;
use Concrete\Core\Site\Service as SiteService;
use Concrete\Core\Site\Tree\TreeInterface;
use Doctrine\ORM\EntityManagerInterface;
use PDO;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * A class to copy pages and page versions.
 *
 * @since concrete5 8.5.0a2
 */
class Cloner
{
    /**
     * @var \Concrete\Core\Database\Connection\Connection
     */
    protected $connection;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var \Concrete\Core\Site\Service
     */
    protected $siteService;

    /**
     * @var \Concrete\Core\Localization\Service\Date
     */
    protected $dateHelper;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;

    public function __construct(EntityManagerInterface $entityManager, SiteService $siteService, DateHelper $dateHelper, EventDispatcherInterface $eventDispatcher)
    {
        $this->connection = $entityManager->getConnection();
        $this->entityManager = $entityManager;
        $this->siteService = $siteService;
        $this->dateHelper = $dateHelper;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Duplicate a page and return the newly created page.
     *
     * @param \Concrete\Core\Page\Page|\Concrete\Core\Page\Stack\Stack $page The page (or the stack) to be copied
     * @param \Concrete\Core\Page\ClonerOptions $options The options for the cloning process
     * @param \Concrete\Core\Page\Page|null $newParentPage The page under which this page should be copied to
     * @param \Concrete\Core\Site\Tree\TreeInterface|null $site the destination site (used if $toParentPage is NULL)
     * @param Page
     *
     * @return \Concrete\Core\Page\Page||\Concrete\Core\Page\Stack\Stack
     */
    public function clonePage(Page $page, ClonerOptions $options, Page $newParentPage = null, TreeInterface $site = null)
    {
        if ($page->getPageTypeHandle() === STACKS_PAGE_TYPE) {
            if (!$page instanceof Stack) {
                $page = Stack::getByID($page->getCollectionID(), $page->getVersionID());
            }
            if ($newParentPage === null) {
                $newParentPage = Page::getByID($page->getCollectionParentID());
            }
        }
        $cID = $page->getCollectionID();
        $uID = $options->keepOriginalAuthor() ? $page->getCollectionUserID() : $options->getCurrentUser()->getUserID();
        $cParentID = $newParentPage === null ? 0 : $newParentPage->getCollectionID();

        $newCollectionName = $this->getUniquePageName($page->getCollectionName(), $cParentID);
        $newCollectionHandle = $this->getUniquePageHandle($page->getCollectionHandle(), $cParentID);

        $newC = $this->cloneCollection(Collection::getByID($cID), $options);
        $newCID = $newC->getCollectionID();

        if ($newParentPage !== null) {
            $siteTreeID = $newParentPage->getSiteTreeID();
        } elseif ($site !== null) {
            $siteTreeID = $site->getSiteTreeID();
        } else {
            $siteTreeID = $this->siteService->getSite()->getSiteTreeID();
        }

        switch ($page->getCollectionInheritance()) {
            case 'OVERRIDE':
                $cInheritPermissionsFromCID = $newCID;
                break;
            case 'PARENT':
                $cInheritPermissionsFromCID = $newParentPage ? $newParentPage->getPermissionsCollectionID() : $page->getPermissionsCollectionID();
                break;
            default:
                $cInheritPermissionsFromCID = $page->getPermissionsCollectionID();
                break;
        }
        $cInheritPermissionsFromCID =
        $this->connection->insert('Pages', [
            'cID' => $newCID,
            'siteTreeID' => $siteTreeID,
            'ptID' => $page->getPageTypeID(),
            'cParentID' => $cParentID,
            'uID' => $uID,
            'cOverrideTemplatePermissions' => $page->overrideTemplatePermissions(),
            'cInheritPermissionsFromCID' => $cInheritPermissionsFromCID,
            'cInheritPermissionsFrom' => $page->getCollectionInheritance(),
            'cFilename' => $page->getCollectionFilename(),
            'cPointerID' => $page->getCollectionPointerID(),
            'cPointerExternalLink' => $page->getCollectionPointerExternalLink(),
            'cPointerExternalLinkNewWindow' => $page->openCollectionPointerExternalLinkInNewWindow(),
            'cDisplayOrder' => $page->getCollectionDisplayOrder(),
            'pkgID' => $page->getPackageID(),
        ]);

        $this->directCopy('PageTypeComposerOutputBlocks', 'arHandle, cbDisplayOrder, ptComposerFormLayoutSetControlID, bID', $cID, $newCID);

        PageStatistics::incrementParents($newCID);

        $newPage = Page::getByID($newCID);

        if ($newPage->getCollectionInheritance() === 'OVERRIDE') {
            $newPage->acquirePagePermissions($page->getPermissionsCollectionID());
            $newPage->acquireAreaPermissions($page->getPermissionsCollectionID());
        }

        $args = [];
        if ($newCollectionName !== $page->getCollectionName()) {
            $args['cName'] = $newCollectionName;
        }
        if ($newCollectionHandle !== $page->getCollectionHandle()) {
            $args['cHandle'] = $newCollectionHandle;
        }
        $newPage->update($args);

        Section::registerDuplicate($newPage, $page);

        $pe = new DuplicatePageEvent($page);
        $pe->setNewPageObject($newPage);
        $this->eventDispatcher->dispatch('on_page_duplicate', $pe);

        $newPage->rescanCollectionPath();
        $newPage->movePageDisplayOrderToBottom();

        if ($page instanceof Stack) {
            Area::getOrCreate($newPage, STACKS_AREA_NAME);
            $this->connection->insert('Stacks', [
                'stName' => $newPage->getCollectionName(),
                'cID' => $newPage->getCollectionID(),
                'stType' => $page->getStackType(),
                'stMultilingualSection' => $page->getMultilingualSectionID(),
            ]);
            $newPage = Stack::getByID($newPage->getCollectionID());
            if ($page->isNeutralStack()) {
                foreach (Section::getList() as $section) {
                    $localized = $page->getLocalizedStack($section);
                    if ($localized !== null) {
                        $this->clonePage($localized, $options, $newPage, $site);
                    }
                }
            }
        }

        return $newPage;
    }

    /**
     * Create a clone of a collection, and all its versions, contents and attributes.
     *
     * @param \Concrete\Core\Page\Collection\Collection $c the collection to be cloned
     * @param \Concrete\Core\Page\ClonerOptions $options the options for the cloning process
     *
     * @return \Concrete\Core\Page\Collection\Collection
     */
    public function cloneCollection(Collection $c, ClonerOptions $options)
    {
        $cDate = $this->dateHelper->getOverridableNow();
        $this->connection->insert('Collections', [
            'cDateAdded' => $cDate,
            'cDateModified' => $cDate,
            'cHandle' => $c->getCollectionHandle(),
        ]);
        $cID = $c->getCollectionID();
        $newCID = $this->connection->lastInsertId();
        $rs = $this->connection->executeQuery('select * from CollectionVersions where cID = ? order by cvDateCreated asc', [$cID]);
        while (($row = $rs->fetch(PDO::FETCH_ASSOC)) !== false) {
            $cDate = date('Y-m-d H:i:s', strtotime($cDate) + 1);
            $this->connection->insert('CollectionVersions', [
                'cID' => $newCID,
                'cvID' => $row['cvID'],
                'cvName' => $row['cvName'],
                'cvHandle' => $row['cvHandle'],
                'cvDescription' => $row['cvDescription'],
                'cvDatePublic' => $row['cvDatePublic'],
                'cvDateCreated' => $cDate,
                'cvComments' => $row['cvComments'],
                'cvAuthorUID' => $options->keepOriginalAuthor() ? $row['cvAuthorUID'] : $options->getCurrentUser()->getUserID(),
                'cvIsApproved' => $options->forceUnapproved() ? 0 : $row['cvIsApproved'],
                'pThemeID' => $row['pThemeID'],
                'pTemplateID' => $row['pTemplateID'],
            ]);
        }
        $this->copyData($options, $cID, $newCID);

        return Collection::getByID($newCID);
    }

    /**
     * Create a copy of a collection version to another collection.
     *
     * @param \Concrete\Core\Page\Collection\Version\Version $cvSource
     * @param \Concrete\Core\Page\Collection\Collection $cDestination
     * @param \Concrete\Core\Page\ClonerOptions $options the options for the cloning process
     *
     * @return \Concrete\Core\Page\Collection\Version\Version
     */
    public function cloneCollectionVersion(Version $cvSource, Collection $cDestination, ClonerOptions $options)
    {
        $cSourceID = $cvSource->getCollectionID();
        $cvSourceID = $cvSource->getVersionID();

        $cSource = Page::getByID($cSourceID, $cvSourceID);

        $cDestinationID = $cDestination->getCollectionID();
        $cvDestinationID = 1 + (int) $this->connection->fetchColumn('select max(cvID) from CollectionVersions where cID = ?', [$cDestinationID]);
        $versionComments = $options->getVersionComments();
        if ($versionComments === '') {
            $versionComments = t('New Version %s', $cvDestinationID);
        }
        if ($cSourceID == $cDestinationID) {
            $cvIsNew = 1;
            if ($cSource->getPageTypeHandle() === STACKS_PAGE_TYPE) {
                $cvIsNew = 0;
            }
        } else {
            $cvIsNew = 0;
        }

        $this->connection->insert('CollectionVersions', [
            'cID' => $cDestinationID,
            'cvID' => $cvDestinationID,
            'cvName' => $cvSource->getVersionName(),
            'cvHandle' => $cDestination->getCollectionHandle(),
            'cvDescription' => $cvSource->cvDescription,
            'cvDatePublic' => $cvSource->cvDatePublic,
            'cvDateCreated' => $this->dateHelper->getOverridableNow(),
            'cvComments' => $versionComments,
            'cvAuthorUID' => $options->keepOriginalAuthor() ? $cvSource->getVersionAuthorUserID() : $options->getCurrentUser()->getUserID(),
            'cvIsNew' => $cvIsNew,
            'pThemeID' => $cvSource->pThemeID,
            'pTemplateID' => $cvSource->pTemplateID,
            // important: cvPublishDate used to be the same for the new version as it is for the current,
            // but it made it impossible to create a version that wasn't scheduled once you scheduled a version
            // so I'm turning it off for now - AE
            'cvPublishDate' => null,
        ]);

        $this->copyData($options, $cSourceID, $cDestinationID, $cvSourceID, $cvDestinationID);

        $cvDestination = Version::get($cSource, $cvDestinationID);

        $ev = new CollectionVersionEvent($cSource);
        $ev->setCollectionVersionObject($cvDestination);
        $this->eventDispatcher->dispatch('on_page_version_add', $ev);

        $cvDestination->refreshCache();

        return $cvDestination;
    }

    /**
     * Get the name of a page that's unique for the parent.
     *
     * @param string $pageName
     * @param int $parentID
     *
     * @return string
     */
    protected function getUniquePageName($pageName, $parentID)
    {
        $uniquePageName = $pageName;
        $parentID = (int) $parentID;
        $index = 1;
        for (; ;) {
            $pageWithSameName = $this->connection->fetchColumn(
                'select Pages.cID from CollectionVersions inner join Pages on (CollectionVersions.cID = Pages.cID and CollectionVersions.cvIsApproved = 1) where Pages.cParentID = ? and CollectionVersions.cvName = ? limit 1',
                [$parentID, $uniquePageName]
            );
            if ($pageWithSameName === false) {
                return $uniquePageName;
            }
            ++$index;
            $uniquePageName = $pageName . ' ' . $index;
        }
    }

    /**
     * Get the handle of a page that's unique for the parent.
     *
     * @param string $handle
     * @param int $parentID
     *
     * @return string
     */
    protected function getUniquePageHandle($handle, $parentID)
    {
        $uniqueHandle = $handle;
        $parentID = (int) $parentID;
        $index = 1;
        for (; ;) {
            $pageWithSameHandle = $this->connection->fetchColumn(
                'select Pages.cID from CollectionVersions inner join Pages on (CollectionVersions.cID = Pages.cID and CollectionVersions.cvIsApproved = 1) where Pages.cParentID = ? and CollectionVersions.cvHandle = ? limit 1',
                [$parentID, $uniqueHandle]
            );
            if ($pageWithSameHandle === false) {
                return $uniqueHandle;
            }
            ++$index;
            $uniqueHandle = $handle . '-' . $index;
        }
    }

    /**
     * Copy the data from one collection version (or all versions of a collection) to another collection version (or all versions of a collection).
     *
     * @param \Concrete\Core\Page\ClonerOptions $options
     * @param int $fromCID The ID of the source collection
     * @param int $toCID The ID of the destination collection
     * @param int|null $fromVID the version ID of the source collection (if NULL we'll copy the data of all the collection versions)
     * @param int|null $toVID the version ID of the destination collection (if NULL we'll assume $fromVID)
     */
    protected function copyData(ClonerOptions $options, $fromCID, $toCID, $fromVID = null, $toVID = null)
    {
        if ($options->copyAttributes()) {
            $this->copyAttributes($fromCID, $toCID, $fromVID, $toVID);
        }
        if ($options->copyFeatureAssignments()) {
            $this->copyFeatureAssignments($fromCID, $toCID, $fromVID, $toVID);
        }
        if ($options->copyCustomStyles()) {
            $this->copyCustomStyles($fromCID, $toCID, $fromVID, $toVID);
        }
        if ($options->copyContents()) {
            $this->copyBlocks($fromCID, $toCID, $fromVID, $toVID);
            $this->copyAreaStyles($fromCID, $toCID, $fromVID, $toVID);
        }
    }

    /**
     * Copy the attributes from one collection version (or all versions of a collection) to another collection version (or all versions of a collection).
     *
     * @param int $fromCID The ID of the source collection
     * @param int $toCID The ID of the destination collection
     * @param int|null $fromVID the version ID of the source collection (if NULL we'll copy the data of all the collection versions)
     * @param int|null $toVID the version ID of the destination collection (if NULL we'll assume $fromVID)
     */
    protected function copyAttributes($fromCID, $toCID, $fromVID = null, $toVID = null)
    {
        if ($toVID === null) {
            $toVID = $fromVID;
        }
        $query = ['cID' => $fromCID];
        if ($fromVID !== null) {
            $query['cvID'] = $fromVID;
        }
        $attributesRepository = $this->entityManager->getRepository(PageValue::class);
        foreach ($attributesRepository->findBy($query) as $pageValue) {
            $newPageValue = clone $pageValue;
            $newPageValue->setPageID($toCID);
            if ($toVID !== null) {
                $newPageValue->setVersionID($toVID);
            }
            $this->entityManager->persist($newPageValue);
        }
        $this->entityManager->flush();
    }

    /**
     * Copy the feature assignments from one collection version (or all versions of a collection) to another collection version (or all versions of a collection).
     *
     * @param int $fromCID The ID of the source collection
     * @param int $toCID The ID of the destination collection
     * @param int|null $fromVID the version ID of the source collection (if NULL we'll copy the data of all the collection versions)
     * @param int|null $toVID the version ID of the destination collection (if NULL we'll assume $fromVID)
     */
    protected function copyFeatureAssignments($fromCID, $toCID, $fromVID = null, $toVID = null)
    {
        $this->directCopy('CollectionVersionFeatureAssignments', 'faID', $fromCID, $toCID, $fromVID, $toVID);
    }

    /**
     * Copy the custom theme styles from one collection version (or all versions of a collection) to another collection version (or all versions of a collection).
     *
     * @param int $fromCID The ID of the source collection
     * @param int $toCID The ID of the destination collection
     * @param int|null $fromVID the version ID of the source collection (if NULL we'll copy the data of all the collection versions)
     * @param int|null $toVID the version ID of the destination collection (if NULL we'll assume $fromVID)
     */
    protected function copyCustomStyles($fromCID, $toCID, $fromVID = null, $toVID = null)
    {
        $this->directCopy('CollectionVersionThemeCustomStyles', 'pThemeID, scvlID, preset, sccRecordID', $fromCID, $toCID, $fromVID, $toVID);
    }

    /**
     * Copy the blocks from one collection version (or all versions of a collection) to another collection version (or all versions of a collection).
     *
     * @param int $fromCID The ID of the source collection
     * @param int $toCID The ID of the destination collection
     * @param int|null $fromVID the version ID of the source collection (if NULL we'll copy the data of all the collection versions)
     * @param int|null $toVID the version ID of the destination collection (if NULL we'll assume $fromVID)
     */
    protected function copyBlocks($fromCID, $toCID, $fromVID = null, $toVID = null)
    {
        $copyFields = 'bID, arHandle, cbDisplayOrder, cbRelationID, cbOverrideAreaPermissions, cbIncludeAll, cbOverrideBlockTypeCacheSettings, cbOverrideBlockTypeContainerSettings, cbEnableBlockContainer';
        $this->directCopy(
            'CollectionVersionBlocks',
            ["{$copyFields}, 0", "{$copyFields}, isOriginal"],
            $fromCID, $toCID, $fromVID, $toVID
        );
        $this->directCopy(
            'CollectionVersionBlockStyles',
            'bID, arHandle, issID',
            $fromCID, $toCID, $fromVID, $toVID
        );
        $this->directCopy(
            'CollectionVersionBlocksCacheSettings',
            'bID, arHandle, btCacheBlockOutput, btCacheBlockOutputOnPost, btCacheBlockOutputForRegisteredUsers, btCacheBlockOutputLifetime',
            $fromCID, $toCID, $fromVID, $toVID
        );
        $this->directCopy(
            'BlockFeatureAssignments',
            'bID, faID',
            $fromCID, $toCID, $fromVID, $toVID
        );

        $copyFields = 'bID, paID, pkID';
        $this->directCopy(
            'BlockPermissionAssignments',
            [preg_replace('/(^|,\s*)/', '\\1BlockPermissionAssignments.', $copyFields), $copyFields],
            $fromCID, $toCID, $fromVID, $toVID,
            <<<'EOT'
BlockPermissionAssignments
inner join CollectionVersionBlocks
on BlockPermissionAssignments.cID = CollectionVersionBlocks.cID
and BlockPermissionAssignments.bID = CollectionVersionBlocks.bID
and BlockPermissionAssignments.cvID = CollectionVersionBlocks.cvID
and CollectionVersionBlocks.cbOverrideAreaPermissions is not null and CollectionVersionBlocks.cbOverrideAreaPermissions <> 0
EOT
        );
    }

    /**
     * Copy the area styles from one collection version (or all versions of a collection) to another collection version (or all versions of a collection).
     *
     * @param int $fromCID The ID of the source collection
     * @param int $toCID The ID of the destination collection
     * @param int|null $fromVID the version ID of the source collection (if NULL we'll copy the data of all the collection versions)
     * @param int|null $toVID the version ID of the destination collection (if NULL we'll assume $fromVID)
     */
    protected function copyAreaStyles($fromCID, $toCID, $fromVID = null, $toVID = null)
    {
        $this->directCopy('CollectionVersionAreaStyles', 'arHandle, issID', $fromCID, $toCID, $fromVID, $toVID);
    }

    /**
     * @param string $table
     * @param string|string[] $copyFields
     * @param int $fromCID
     * @param int $toCID
     * @param int|null $fromVID
     * @param int|null $toVID
     * @param string|null $from
     */
    private function directCopy($table, $copyFields, $fromCID, $toCID, $fromVID = null, $toVID = null, $from = null)
    {
        if (is_array($copyFields)) {
            list($copyFieldsFrom, $copyFieldsTo) = $copyFields;
        } else {
            $copyFieldsTo = $copyFieldsFrom = $copyFields;
        }
        if ($from === null) {
            $from = $table;
        }
        if ($fromVID === null) {
            $query = "insert into {$table} (cID, cvID, {$copyFieldsTo}) select ?, {$table}.cvID, {$copyFieldsFrom} from {$from} where {$table}.cID = ?";
            $params = [$toCID, $fromCID];
        } else {
            if ($toVID === null) {
                $toVID = $fromVID;
            }
            $query = "insert into {$table} (cID, cvID, {$copyFieldsTo}) select ?, ?, {$copyFieldsFrom} from {$from} where {$table}.cID = ? and {$table}.cvID = ?";
            $params = [$toCID, $toVID, $fromCID, $fromVID];
        }
        $this->connection->executeQuery($query, $params);
    }
}
