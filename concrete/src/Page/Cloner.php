<?php

namespace Concrete\Core\Page;

use Concrete\Core\Block\Block;
use Concrete\Core\Localization\Service\Date as DateHelper;
use Concrete\Core\Page\Collection\Collection;
use Concrete\Core\Page\Collection\Version\Event as CollectionVersionEvent;
use Concrete\Core\Page\Collection\Version\Version;
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
     * @var \Concrete\Core\Localization\Service\Date
     */
    protected $dateHelper;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;

    public function __construct(EntityManagerInterface $entityManager, DateHelper $dateHelper, EventDispatcherInterface $eventDispatcher)
    {
        $this->connection = $entityManager->getConnection();
        $this->entityManager = $entityManager;
        $this->dateHelper = $dateHelper;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Clone the currently loaded collection version to another collection, and returns a Page instance containing the new version.
     *
     * @param \Concrete\Core\Page\Collection\Collection $cSource
     * @param \Concrete\Core\Page\Collection\Collection $cDestination
     * @param string $versionComments
     * @param \Concrete\Core\User\User $author
     * @param bool $createEmpty
     *
     * @return \Concrete\Core\Page\Page
     */
    public function cloneLoadedCollection(Collection $cSource, Collection $cDestination, $versionComments, User $author, $createEmpty = false)
    {
        $cvSource = $cSource->getVersionObject();
        $cvDestination = $this->cloneCollectionVersion($cvSource, $cDestination, $versionComments, $author);
        $cDestinationID = $cDestination->getCollectionID();
        $cDestination = Page::getByID($cDestinationID, $cvDestination->getVersionID());
        if (!$createEmpty) {
            $cSourceID = $cSource->getCollectionID();
            $cvSourceID = $cvSource->getVersionID();
            $cvDestinationID = $cvDestination->getVersionID();
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

        return $cDestination;
    }

    /**
     * Create a copy of a collection version to another collection.
     *
     * @param \Concrete\Core\Page\Collection\Version\Version $cvSource
     * @param \Concrete\Core\Page\Collection\Collection $cDestination
     * @param string $versionComments
     * @param \Concrete\Core\User\User $author
     *
     * @return \Concrete\Core\Page\Collection\Version\Version
     */
    public function cloneCollectionVersion(Version $cvSource, Collection $cDestination, $versionComments, User $author)
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

        return $cvDestination;
    }
}
