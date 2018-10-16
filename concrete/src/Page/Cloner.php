<?php

namespace Concrete\Core\Page;

use Concrete\Core\Area\Area;
use Concrete\Core\Block\Block;
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
use Concrete\Core\User\User;
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
     * @param \Concrete\Core\Page\Page|null $newParentPage The page under which this page should be copied to
     * @param \Concrete\Core\User\User|null $newAuthor Override the original page authors
     * @param \Concrete\Core\Site\Tree\TreeInterface|null $site the destination site (used if $toParentPage is NULL)
     * @param Page
     *
     * @return \Concrete\Core\Page\Page||\Concrete\Core\Page\Stack\Stack
     */
    public function clonePage(Page $page, Page $newParentPage = null, User $newAuthor = null, TreeInterface $site = null)
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
        $uID = $newAuthor === null ? $page->getCollectionUserID() : $newAuthor->getUserID();
        $cParentID = $newParentPage === null ? 0 : $newParentPage->getCollectionID();

        $newCollectionName = $this->getUniquePageName($page->getCollectionName(), $cParentID);
        $newCollectionHandle = $this->getUniquePageHandle($page->getCollectionHandle(), $cParentID);

        $newC = $this->cloneCollection(Collection::getByID($cID));
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

        $copyFields = 'cvID, arHandle, cbDisplayOrder, ptComposerFormLayoutSetControlID, bID';
        $this->connection->executeQuery(
            "insert into PageTypeComposerOutputBlocks (cID, {$copyFields}) select ?, {$copyFields} from PageTypeComposerOutputBlocks where cID = ?",
            [$newCID, $cID]
        );

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
                        $this->clonePage($localized, $newPage, $newAuthor, $site);
                    }
                }
            }
        }

        return $newPage;
    }

    /**
     * Create a clone of a collection, and all its versions, contents and attributes.
     *
     * @param Collection $c
     *
     * @return \Concrete\Core\Page\Collection\Collection
     */
    public function cloneCollection(Collection $c)
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
                'cvAuthorUID' => $row['cvAuthorUID'],
                'cvIsApproved' => $row['cvIsApproved'],
                'pThemeID' => $row['pThemeID'],
                'pTemplateID' => $row['pTemplateID'],
            ]);
        }

        $copyFields = 'cvID, bID, arHandle, issID';
        $this->connection->executeQuery(
            "insert into CollectionVersionBlockStyles (cID, {$copyFields}) select ?, {$copyFields} from CollectionVersionBlockStyles where cID = ?",
            [$newCID, $cID]
        );

        $copyFields = 'cvID, arHandle, issID';
        $this->connection->executeQuery(
            "insert into CollectionVersionAreaStyles (cID, {$copyFields}) select ?, {$copyFields} from CollectionVersionAreaStyles where cID = ?",
            [$newCID, $cID]
        );

        $copyFields = 'cvID, pThemeID, scvlID, preset, sccRecordID';
        $this->connection->executeQuery(
            "insert into CollectionVersionThemeCustomStyles (cID, {$copyFields}) select ?, {$copyFields} from CollectionVersionThemeCustomStyles where cID = ?",
            [$newCID, $cID]
        );

        $copyFields = 'cvID, bID, arHandle, btCacheBlockOutput, btCacheBlockOutputOnPost, btCacheBlockOutputForRegisteredUsers, btCacheBlockOutputLifetime';
        $this->connection->executeQuery(
            "insert into CollectionVersionBlocksCacheSettings (cID, {$copyFields}) select ?, {$copyFields} from CollectionVersionBlocksCacheSettings where cID = ?",
            [$newCID, $cID]
        );

        $copyFields = 'cvID, bID, arHandle, cbDisplayOrder, cbRelationID, cbOverrideAreaPermissions, cbIncludeAll, cbOverrideBlockTypeCacheSettings, cbOverrideBlockTypeContainerSettings, cbEnableBlockContainer';
        $this->connection->executeQuery(
            "insert into CollectionVersionBlocks (cID, isOriginal, {$copyFields}) select ?, ?, {$copyFields} from CollectionVersionBlocks where cID = ?",
            [$newCID, 0, $cID]
        );

        $copyFields = 'cvID, bID, paID, pkID';
        $copyFieldsSource = 'BlockPermissionAssignments.' . str_replace(', ', ', BlockPermissionAssignments.', $copyFields);
        $this->connection->executeQuery(
            <<<EOT
insert into BlockPermissionAssignments (cID, {$copyFields})
    select ?, {$copyFieldsSource}
    from BlockPermissionAssignments
    inner join CollectionVersionBlocks on BlockPermissionAssignments.cID = CollectionVersionBlocks.cID and BlockPermissionAssignments.bID = CollectionVersionBlocks.bID and BlockPermissionAssignments.cvID = CollectionVersionBlocks.cvID
where
    CollectionVersionBlocks.cID = ?
    and cbOverrideAreaPermissions is not null and cbOverrideAreaPermissions <> 0
EOT
            ,
            [$newCID, $cID]
        );

        // duplicate any attributes belonging to the collection
        $attributesRepository = $this->entityManager->getRepository(PageValue::class);
        foreach ($attributesRepository->findBy(['cID' => $cID]) as $pageValue) {
            $newPageValue = new PageValue();
            $newPageValue->setPageID($newCID);
            $newPageValue->setVersionID($pageValue->getVersionID());
            $newPageValue->setGenericValue($pageValue->getGenericValue());
            $newPageValue->setAttributeKey($pageValue->getAttributeKey());
            $this->entityManager->persist($newPageValue);
        }
        $this->entityManager->flush();

        return Collection::getByID($newCID);
    }

    /**
     * Create a copy of a collection version to another collection.
     *
     * @param \Concrete\Core\Page\Collection\Version\Version $cvSource
     * @param \Concrete\Core\Page\Collection\Collection $cDestination
     * @param string $versionComments
     * @param \Concrete\Core\User\User $author
     * @param bool $copyContents
     *
     * @return \Concrete\Core\Page\Collection\Version\Version
     */
    public function cloneCollectionVersion(Version $cvSource, Collection $cDestination, $versionComments, User $author, $copyContents)
    {
        $attributesCategory = $cvSource->getObjectAttributeCategory();
        $cSourceID = $cvSource->getCollectionID();
        $cvSourceID = $cvSource->getVersionID();

        $cSource = Page::getByID($cSourceID, $cvSourceID);

        $cDestinationID = $cDestination->getCollectionID();
        $cvDestinationID = 1 + (int) $this->connection->fetchColumn('select max(cvID) from CollectionVersions where cID = ?', [$cDestinationID]);
        $versionComments = (string) $versionComments;
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
            'cvAuthorUID' => $author->getUserID(),
            'cvIsNew' => $cvIsNew,
            'pThemeID' => $cvSource->pThemeID,
            'pTemplateID' => $cvSource->pTemplateID,
            // important: cvPublishDate used to be the same for the new version as it is for the current,
            // but it made it impossible to create a version that wasn't scheduled once you scheduled a version
            // so I'm turning it off for now - AE
            'cvPublishDate' => null,
        ]);

        $values = $attributesCategory->getAttributeValues($cvSource);
        foreach ($values as $value) {
            $value = clone $value;
            /* @var \Concrete\Core\Entity\Attribute\Value\PageValue $value */
            $value->setPageID($cDestinationID);
            $value->setVersionID($cvDestinationID);
            $this->entityManager->persist($value);
        }
        $this->entityManager->flush();

        $copyFields = 'faID';
        $this->connection->executeQuery(
            "insert into CollectionVersionFeatureAssignments (cID, cvID, {$copyFields}) select ?, ?, {$copyFields} from CollectionVersionFeatureAssignments where cID = ? and cvID = ?",
            [$cDestinationID, $cvDestinationID, $cSourceID, $cvSourceID]
        );

        $copyFields = 'pThemeID, scvlID, preset, sccRecordID';
        $this->connection->executeQuery(
            "insert into CollectionVersionThemeCustomStyles (cID, cvID, {$copyFields}) select ?, ?, {$copyFields} from CollectionVersionThemeCustomStyles where cID = ? and cvID = ?",
            [$cDestinationID, $cvDestinationID, $cSourceID, $cvSourceID]
        );

        $cvDestination = Version::get($cSource, $cvDestinationID);

        $ev = new CollectionVersionEvent($cSource);
        $ev->setCollectionVersionObject($cvDestination);

        $this->eventDispatcher->dispatch('on_page_version_add', $ev);

        $cvDestination->refreshCache();

        if ($copyContents) {
            // Dublicate block objects
            $rs = $this->connection->executeQuery(
                'select bID, arHandle from CollectionVersionBlocks where cID = ? and cvID = ? and cbIncludeAll = 0 order by cbDisplayOrder asc',
                [$cSourceID, $cvSourceID]
            );
            while (($row = $rs->fetch(PDO::FETCH_ASSOC)) !== false) {
                $b = Block::getByID($row['bID'], $cSource, $row['arHandle']);
                if ($b) {
                    $b->alias($cDestination);
                }
            }
            // Duplicate area styles
            $copyFields = 'arHandle, issID';
            $this->connection->executeQuery(
                "insert into CollectionVersionAreaStyles (cID, cvID, {$copyFields}) select ?, ?, {$copyFields} from CollectionVersionAreaStyles where cID = ? and cvID = ?",
                [$cDestinationID, $cvDestinationID, $cSourceID, $cvSourceID]
            );
        }

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
}
