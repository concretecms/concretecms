<?php
namespace Concrete\Core\Page;

use Concrete\Core\Area\Area;
use Concrete\Core\Block\Block;
use Concrete\Core\Entity\Attribute\Value\PageValue;
use Concrete\Core\Entity\Page\Relation\SiblingRelation;
use Concrete\Core\Entity\Site\SkeletonTree;
use Concrete\Core\Localization\Service\Date as DateHelper;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Page\Collection\Collection;
use Concrete\Core\Page\Collection\Version\Event as CollectionVersionEvent;
use Concrete\Core\Page\Collection\Version\Version;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Page\Statistics as PageStatistics;
use Concrete\Core\Site\Service as SiteService;
use Concrete\Core\Site\Tree\TreeInterface;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManagerInterface;
use PDO;
use Concrete\Core\Events\EventDispatcher;

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
     * @var \Concrete\Core\Events\EventDispatcher
     */
    protected $eventDispatcher;

    public function __construct(EntityManagerInterface $entityManager, SiteService $siteService, DateHelper $dateHelper, EventDispatcher $eventDispatcher)
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
     * @param \Concrete\Core\Site\Tree\TreeInterface|null $site the destination site (used if $newParentPage is NULL)
     *
     * @return \Concrete\Core\Page\Page|\Concrete\Core\Page\Stack\Stack
     */
    public function clonePage(Page $page, ClonerOptions $options, Page $newParentPage = null, TreeInterface $site = null)
    {
        // If the provided page is an alias, let's add another alias of original page
        if ($page->isAliasPage()) {
            $pointer = Page::getByID($page->getCollectionPointerID());
            $app = Application::getFacadeApplication();
            if ($app->make('multilingual/detector')->isEnabled()) {
                if ($newParentPage === null) {
                    $newParentPage = Section::getBySectionOfSite($pointer);
                }
                // If new parent page is under the different section, find related page first.
                // If related page is exists in that section, create an alias of the related page.
                $pointerSection = Section::getBySectionOfSite($pointer);
                $destinationSection = Section::getBySectionOfSite($newParentPage);
                if ($pointerSection->getCollectionID() !== $destinationSection->getCollectionID()) {
                    $sections = Section::getList();
                    /** @var Section $section */
                    foreach ($sections as $section) {
                        if ($destinationSection->getCollectionID() === $section->getCollectionID()) {
                            $relatedID = $section->getTranslatedPageID($pointer);
                            $pointer = Page::getByID($relatedID);
                            break;
                        }
                    }
                }
            } elseif ($newParentPage === null) {
                $newParentPage = Page::getByID(Page::getHomePageID());
            }
            $newCID = $pointer->addCollectionAlias($newParentPage);
        } else {
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

            PageStatistics::incrementParents($newCID);
        }

        $newPage = Page::getByID($newCID);

        if (!$newPage->isAliasPage()) {
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
                    'stMultilingualSection' => 0,
                ]);
                $newPage = Stack::getByID($newPage->getCollectionID());
            }

            $tree = $page->getSiteTreeObject();
            if ($tree instanceof SkeletonTree) {
                // Add a relation between the pages.
                // Is there already a relation used by the source page?
                $relation = $this->entityManager->getRepository('Concrete\Core\Entity\Page\Relation\SiblingRelation')
                    ->findOneBy(['cID' => $page->getCollectionID()]);
                if (!is_object($relation)) {
                    $mpRelationID = $this->entityManager->getConnection()->GetOne('select max(mpRelationID) as mpRelationID from SiblingPageRelations');
                    if (!$mpRelationID) {
                        $mpRelationID = 1;
                    } else {
                        ++$mpRelationID;
                    }

                    // Create one for the original sibling.
                    $original = new SiblingRelation();
                    $original->setPageRelationID($mpRelationID);
                    $original->setPageID($page->getCollectionID());
                    $this->entityManager->persist($original);

                } else {
                    $mpRelationID = $relation->getPageRelationID();
                }

                $new = new SiblingRelation();
                $new->setPageRelationID($mpRelationID);
                $new->setPageID($newPage->getCollectionID());
                $this->entityManager->persist($new);
                $this->entityManager->flush();
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
        $this->copyData($options, [$cID, $newCID]);

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

        $this->copyData($options, [$cSourceID, $cDestinationID], [$cvSourceID, $cvDestinationID]);

        $cvDestination = Version::get($cSource, $cvDestinationID);

        $ev = new CollectionVersionEvent($cSource);
        $ev->setCollectionVersionObject($cvDestination);
        $this->eventDispatcher->dispatch('on_page_version_add', $ev);

        $cvDestination->refreshCache();

        return $cvDestination;
    }

    /**
     * Create an alias of a block to another collection.
     *
     * @param \Concrete\Core\Block\Block $block
     * @param \Concrete\Core\Page\Collection\Collection $destinationCollection
     * @param int|null $displayOrder The forced display order
     *
     * @return bool returns FALSE if $block is not cloned because it's already present in $destinationCollection, TRUE otherwise
     */
    public function cloneBlock(Block $block, Collection $destinationCollection, int $displayOrder = null)
    {
        $bID = $block->getBlockID();
        $aHandle = $block->getAreaHandle();
        $toID = $destinationCollection->getCollectionID();
        $toCVID = $destinationCollection->getVersionID();
        $already = $this->connection->fetchColumn(
            'select bID from CollectionVersionBlocks where cID = ? and cvID = ? and bID = ? and arHandle = ? limit 1',
            [$toID, $toCVID, $bID, $aHandle]
        );
        if ($already !== false) {
            return false;
        }
        $newBlockDisplayOrder = $displayOrder ?? (int) $destinationCollection->getCollectionAreaDisplayOrder($aHandle);
        $sourceCollection = $block->getBlockCollectionID() ? $block->getBlockCollectionObject() : null;
        if ($sourceCollection) {
            $this->copyBlocks(
                [$sourceCollection->getCollectionID(), $destinationCollection->getCollectionID()],
                [$sourceCollection->getVersionID(), $destinationCollection->getVersionID()],
                [$bID, $bID],
                $newBlockDisplayOrder
            );
        } else {
            $this->connection->insert(
                'CollectionVersionBlocks',
                [
                    'cID' => $toID,
                    'cvID' => $toCVID,
                    'bID' => $bID,
                    'arHandle' => $aHandle,
                    'cbRelationID' => $block->getBlockRelationID(),
                    'cbDisplayOrder' => $newBlockDisplayOrder,
                    'isOriginal' => 0,
                    'cbOverrideAreaPermissions' => $block->overrideAreaPermissions(),
                    'cbOverrideBlockTypeCacheSettings' => $block->overrideBlockTypeCacheSettings(),
                    'cbOverrideBlockTypeContainerSettings' => $block->overrideBlockTypeContainerSettings(),
                    'cbEnableBlockContainer' => $block->enableBlockContainer() ? 1 : 0,
                ]
            );
        }

        return true;
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
        for (;;) {
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
        for (;;) {
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
     * @param int[] $cIDs An array with the ID of the source and destination collections
     * @param int[]|null $cvIDs An array with the source and destination collection versions, or NULL to copy the data of all the collection versions
     */
    protected function copyData(ClonerOptions $options, array $cIDs, array $cvIDs = null)
    {
        if ($options->copyAttributes()) {
            $this->copyAttributes($cIDs, $cvIDs);
        }
        if ($options->copyPageTypeComposerOutputBlocks()) {
            $this->copyPageTypeComposerOutputBlocks($cIDs, $cvIDs);
        }
        if ($options->copyCustomStyles()) {
            $this->copyCustomStyles($cIDs, $cvIDs);
        }
        if ($options->copyContents()) {
            $this->copyBlocks($cIDs, $cvIDs);
            $this->copyAreaStyles($cIDs, $cvIDs);
        }
    }

    /**
     * Copy the attributes from one collection version (or all versions of a collection) to another collection version (or all versions of a collection).
     *
     * @param int[] $cIDs An array with the ID of the source and destination collections
     * @param int[]|null $cvIDs An array with the source and destination collection versions, or NULL to copy the data of all the collection versions
     */
    protected function copyAttributes(array $cIDs, array $cvIDs = null)
    {
        $query = ['cID' => $cIDs[0]];
        if ($cvIDs !== null) {
            $query['cvID'] = $cvIDs[0];
        }
        $attributesRepository = $this->entityManager->getRepository(PageValue::class);
        foreach ($attributesRepository->findBy($query) as $pageValue) {
            $newPageValue = clone $pageValue;
            $newPageValue->setPageID($cIDs[1]);
            if ($cvIDs !== null) {
                $newPageValue->setVersionID($cvIDs[1]);
            }
            $this->entityManager->persist($newPageValue);
        }
        $this->entityManager->flush();
    }

    /**
     * Copy the page type composer block output records from one version to another.
     *
     * @param int[] $cIDs An array with the ID of the source and destination collections
     * @param int[]|null $cvIDs An array with the source and destination collection versions, or NULL to copy the data of all the collection versions
     */
    protected function copyPageTypeComposerOutputBlocks(array $cIDs, array $cvIDs = null)
    {
        $this->directCopy('PageTypeComposerOutputBlocks', 'arHandle, cbDisplayOrder, ptComposerFormLayoutSetControlID, bID', ['cID' => $cIDs, 'cvID' => $cvIDs]);
    }

    /**
     * Copy the custom theme styles from one collection version (or all versions of a collection) to another collection version (or all versions of a collection).
     *
     * @param int[] $cIDs An array with the ID of the source and destination collections
     * @param int[]|null $cvIDs An array with the source and destination collection versions, or NULL to copy the data of all the collection versions
     */
    protected function copyCustomStyles(array $cIDs, array $cvIDs = null)
    {
        $this->directCopy('CollectionVersionThemeCustomStyles', 'pThemeID, scvlID, preset, sccRecordID', ['cID' => $cIDs, 'cvID' => $cvIDs]);
    }

    /**
     * Copy the blocks from one collection version (or all versions of a collection) to another collection version (or all versions of a collection).
     *
     * @param int[] $cIDs An array with the ID of the source and destination collections
     * @param int[]|null $cvIDs An array with the source and destination collection versions, or NULL to copy the data of all the collection versions
     * @param int[]|null $bIDs An array with the source and destination block IDs, or NULL to copy all the blocks
     * @param int|null $newBlockDisplayOrder The display order of the new blocks (NULL: copy)
     */
    protected function copyBlocks($cIDs, array $cvIDs = null, array $bIDs = null, $newBlockDisplayOrder = null)
    {
        $copyFields = 'arHandle, cbRelationID, cbOverrideAreaPermissions, cbIncludeAll, cbOverrideBlockTypeCacheSettings, cbOverrideBlockTypeContainerSettings, cbEnableBlockContainer';
        $copyFieldsFrom = "{$copyFields}, 0";
        $copyFieldsTo = "{$copyFields}, isOriginal";
        if ($newBlockDisplayOrder === null) {
            $copyFieldsFrom = "{$copyFieldsFrom}, cbDisplayOrder";
            $copyFieldsTo = "{$copyFieldsTo}, cbDisplayOrder";
        } else {
            $copyFieldsFrom = "{$copyFieldsFrom}, " . (int) $newBlockDisplayOrder;
            $copyFieldsTo = "{$copyFieldsTo}, cbDisplayOrder";
        }
        $this->directCopy(
            'CollectionVersionBlocks',
            [$copyFieldsFrom, $copyFieldsTo],
            [
                'cID' => $cIDs,
                'cvID' => $cvIDs,
                'bID' => $bIDs,
            ]
        );
        $this->directCopy(
            'CollectionVersionBlockStyles',
            'arHandle, issID',
            [
                'cID' => $cIDs,
                'cvID' => $cvIDs,
                'bID' => $bIDs,
            ]
        );
        $this->directCopy(
            'CollectionVersionBlocksCacheSettings',
            'arHandle, btCacheBlockOutput, btCacheBlockOutputOnPost, btCacheBlockOutputForRegisteredUsers, btCacheBlockOutputLifetime',
            [
                'cID' => $cIDs,
                'cvID' => $cvIDs,
                'bID' => $bIDs,
            ]
        );

        $copyFields = 'paID, pkID';
        $this->directCopy(
            'BlockPermissionAssignments',
            [preg_replace('/(^|,\s*)/', '\\1BlockPermissionAssignments.', $copyFields), $copyFields],
            [
                'cID' => $cIDs,
                'cvID' => $cvIDs,
                'bID' => $bIDs,
            ],
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
     * @param int[] $cIDs An array with the ID of the source and destination collections
     * @param int[]|null $cvIDs An array with the source and destination collection versions, or NULL to copy the data of all the collection versions
     */
    protected function copyAreaStyles(array $cIDs, array $cvIDs = null)
    {
        $this->directCopy('CollectionVersionAreaStyles', 'arHandle, issID', ['cID' => $cIDs, 'cvID' => $cvIDs]);
    }

    /**
     * @param string $table
     * @param string|string[] $copyFields
     * @param array $keyFields An array with key fields; keys are the field names; values are: NULL (to copy the data), mixed[] to change the value
     * @param string|null $from
     */
    private function directCopy($table, $copyFields, array $keyFields, $from = null)
    {
        if (is_array($copyFields)) {
            list($copyFieldsFrom, $copyFieldsTo) = $copyFields;
        } else {
            $copyFieldsTo = $copyFieldsFrom = $copyFields;
        }
        if ($from === null) {
            $from = $table;
        }
        $wheres = [];
        $insertParams = [];
        $whereParams = [];
        foreach ($keyFields as $keyField => $keyValues) {
            $copyFieldsTo .= ", {$keyField}";
            if ($keyValues === null) {
                $copyFieldsFrom .= ", {$table}.{$keyField}";
            } else {
                $copyFieldsFrom .= ', ?';
                $insertParams[] = $keyValues[1];
                $wheres[] = "{$table}.{$keyField} = ?";
                $whereParams[] = $keyValues[0];
            }
        }
        $query = "insert into {$table} ($copyFieldsTo) select {$copyFieldsFrom} from {$from}";
        if (count($wheres) > 0) {
            $query .= ' where ' . implode(' and ', $wheres);
        }
        $this->connection->executeQuery($query, array_merge($insertParams, $whereParams));
    }
}
