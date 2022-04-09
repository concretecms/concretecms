<?php

namespace Concrete\Core\Entity\File;

use Carbon\Carbon;
use Concrete\Core\Attribute\ObjectInterface as AttributeObjectInterface;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Board\InstanceItem;
use Concrete\Core\Entity\Board\Item;
use Concrete\Core\File\Event\DeleteFile;
use Concrete\Core\File\Event\FileVersion;
use Concrete\Core\File\Image\Thumbnail\Type\Type;
use Concrete\Core\File\Importer;
use Concrete\Core\File\Set\Set;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerFactory;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Database;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\NodeType;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Concrete\Core\User\User;
use Core;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Mapping as ORM;
use Events;
use FileSet;
use Loader;
use Page;
use PermissionKey;
use Concrete\Core\Events\EventDispatcher;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="Files",
 *     indexes={
 *     @ORM\Index(name="uID", columns={"uID"}),
 *     @ORM\Index(name="fslID", columns={"fslID"}),
 *     @ORM\Index(name="ocID", columns={"ocID"}),
 *     @ORM\Index(name="fOverrideSetPermissions", columns={"fOverrideSetPermissions"}),
 *     }
 * )
 */
class File implements \Concrete\Core\Permission\ObjectInterface, AttributeObjectInterface
{
    public const CREATE_NEW_VERSION_THRESHOLD = 300;

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $fID;

    /**
     * @ORM\Column(type="string", length=36, options={"fixed": true}, nullable=true, unique=true)
     * @var string|null
     */
    protected $fUUID;

    /**
     * @ORM\Column(type="datetime")
     */
    public $fDateAdded;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    public $fPassword;

    /**
     * @ORM\OneToMany(targetEntity="Version", mappedBy="file", cascade={"persist"})
     * @ORM\JoinColumn(name="fID")
     */
    public $versions;

    /**
     * @ORM\Column(type="boolean")
     */
    public $fOverrideSetPermissions = false;

    /**
     * Originally placed on which page.
     *
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    public $ocID = 0;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\User\User")
     * @ORM\JoinColumn(name="uID", referencedColumnName="uID", onDelete="SET NULL")
     */
    public $author;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    public $folderTreeNodeID = 0;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\File\StorageLocation\StorageLocation", inversedBy="files")
     * @ORM\JoinColumn(name="fslID", referencedColumnName="fslID")
     */
    public $storageLocation;

    public function __construct()
    {
        $this->versions = new ArrayCollection();
    }

    /**
     * For all methods that file does not implement, we pass through to the currently active file version object.
     *
     * @param mixed $nm
     * @param mixed $a
     */
    public function __call($nm, $a)
    {
        $fv = $this->getApprovedVersion();
        if ($fv === null) {
            return;
        }

        return call_user_func_array([$fv, $nm], $a);
    }

    public function getPermissionResponseClassName()
    {
        return '\\Concrete\\Core\\Permission\\Response\\FileResponse';
    }

    public function getPermissionAssignmentClassName()
    {
        return '\\Concrete\\Core\\Permission\\Assignment\\FileAssignment';
    }

    public function getPermissionObjectKeyCategoryHandle()
    {
        return 'file';
    }

    /**
     * Returns the identifier for the object, in this case the File ID.
     *
     * @return int
     */
    public function getPermissionObjectIdentifier()
    {
        return $this->getFileID();
    }

    /**
     * Password used to protect the file if set.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->fPassword;
    }

    /**
     * Returns the FSL ID for the current file.
     *
     * @return int
     */
    public function getStorageLocationID()
    {
        return $this->getFileStorageLocationObject()->getID();
    }

    /**
     * @return \Concrete\Core\Entity\File\StorageLocation\StorageLocation
     */
    public function getFileStorageLocationObject()
    {
        return $this->storageLocation;
    }

    /**
     * @return \Concrete\Core\Entity\File\Version[]
     */
    public function getFileVersions()
    {
        return $this->versions;
    }

    /**
     * Reindex the attributes on this file.
     */
    public function reindex()
    {
        $category = \Core::make('Concrete\Core\Attribute\Category\FileCategory');
        $indexer = $category->getSearchIndexer();
        $values = $category->getAttributeValues($this);
        foreach ($values as $value) {
            $indexer->indexEntry($category, $value, $this);
        }
    }

    /**
     * Set the storage location for the file.
     * THIS DOES NOT MOVE THE FILE to move the file use `setFileStorageLocation()`
     * Must call `save()` to persist changes.
     *
     * @param StorageLocation\StorageLocation $location
     */
    public function setStorageLocation(\Concrete\Core\Entity\File\StorageLocation\StorageLocation $location)
    {
        $this->storageLocation = $location;
    }

    /**
     * Move a file from its current FSL to a new FSL.
     *
     * @param StorageLocation\StorageLocation $newLocation
     *
     * @return bool false if the storage location is the same
     * @throws \Exception
     *
     */
    public function setFileStorageLocation(\Concrete\Core\Entity\File\StorageLocation\StorageLocation $newLocation)
    {
        $fh = Loader::helper('concrete/file');
        $currentLocation = $this->getFileStorageLocationObject();
        if ($newLocation->getID() == $currentLocation->getID()) {
            return false;
        }

        $currentFilesystem = $currentLocation->getFileSystemObject();

        $newFileSystem = $newLocation->getFileSystemObject();

        $list = $this->getVersionList();
        try {
            foreach ($list as $fv) {
                $manager = new \League\Flysystem\MountManager([
                    'current' => $currentFilesystem,
                    'new' => $newFileSystem,
                ]);
                $fp = $fh->prefix($fv->getPrefix(), $fv->getFilename());
                $manager->move('current://' . $fp, 'new://' . $fp);

                $thumbs = Type::getVersionList();
                foreach ($thumbs as $type) {
                    $fv->updateThumbnailStorageLocation($type, $newLocation);
                }
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        $this->setStorageLocation($newLocation);
        $this->save();
    }

    /**
     * Sets the access password on a file.
     *
     * @param $pw string
     */
    public function setPassword($pw)
    {
        $fe = new \Concrete\Core\File\Event\FileWithPassword($this);
        $fe->setFilePassword($pw);
        Events::dispatch('on_file_set_password', $fe);

        $this->fPassword = $pw;
        $this->save();
    }

    public function setOriginalPage($ocID)
    {
        if ($ocID < 1) {
            return false;
        }
        $this->ocID = $ocID;
        $this->save();
    }

    public function getOriginalPageObject()
    {
        if ($this->ocID > 0) {
            $c = Page::getByID($this->ocID);
            if (is_object($c) && !$c->isError()) {
                return $c;
            }
        }
    }

    /**
     * @return bool
     */
    public function overrideFileFolderPermissions()
    {
        return $this->fOverrideSetPermissions;
    }

    public function resetPermissions($fOverrideSetPermissions = 0)
    {
        $db = Loader::db();
        $db->Execute('delete from FilePermissionAssignments where fID = ?', [$this->fID]);
        if ($fOverrideSetPermissions) {
            $permissions = PermissionKey::getList('file');
            foreach ($permissions as $pk) {
                $pk->setPermissionObject($this);
                $pk->copyFromFileSetToFile();
            }
        }
        $this->fOverrideSetPermissions = (bool)$fOverrideSetPermissions;
        $this->save();
    }

    /**
     * Get the user ID of the author of the file (if available).
     *
     * @return int|null
     */
    public function getUserID()
    {
        $user = $this->getUser();

        return $user ? $user->getUserID() : null;
    }

    /**
     * Get the author of the file (if available).
     *
     * @return \Concrete\Core\Entity\User\User|null
     *
     * @since concrete5 8.5.2
     */
    public function getUser()
    {
        if ($this->author) {
            // Check that the user was not deleted
            try {
                $this->author->getUserID();
            } catch (EntityNotFoundException $x) {
                $this->author = null;
            }
        }

        return $this->author;
    }

    /**
     * Set the user who authored the file.
     *
     * @param \Concrete\Core\Entity\User\User $user
     */
    public function setUser(\Concrete\Core\Entity\User\User $user)
    {
        $this->author = $user;
        $this->save();
    }

    /**
     * Get the IDs of the file sets that this file belongs to.
     *
     * @return int[]
     */
    public function getFileSetIDs()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $rows = $db->fetchAll('select fsID from FileSetFiles where fID = ?', [$this->getFileID()]);
        $ids = [];
        foreach ($rows as $row) {
            $ids[] = (int) $row['fsID'];
        }
        return $ids;
    }

    /**
     * Get the file sets that this file belongs to.
     *
     * @return FileSet[]
     */
    public function getFileSets()
    {
        $filesets = [];
        foreach ($this->getFileSetIDs() as $fsID) {
            $fs = FileSet::getByID($fsID);
            if ($fs !== null) {
                $filesets[] = $fs;
            }
        }

        return $filesets;
    }

    /**
     * Tell if a file is starred by a user.
     *
     * @param bool|User $u User to check against if they starred a file, If no user is provided we used the current user
     *
     * @return bool true if the user starred
     */
    public function isStarred($u = false)
    {
        if (!$u) {
            $u = Core::make(User::class);
        }
        $db = Loader::db();
        $r = $db->GetOne(
            'select fsfID from FileSetFiles fsf inner join FileSets fs on fs.fsID = fsf.fsID where fsf.fID = ? and fs.uID = ? and fs.fsType = ?',
            [$this->getFileID(), $u->getUserID(), FileSet::TYPE_STARRED]
        );

        return $r > 0;
    }

    /**
     * Set the date a file was added
     * Must call `save()` to persist.
     *
     * @param $fDateAdded
     */
    public function setDateAdded($fDateAdded)
    {
        $this->fDateAdded = $fDateAdded;
    }

    public function getDateAdded()
    {
        return $this->fDateAdded;
    }

    /**
     * Create a new version of a file.
     *
     * @param bool $copyUnderlyingFile If we should copy the underlying file, or reference the original
     *
     * @return Version
     */
    public function createNewVersion($copyUnderlyingFile = false)
    {
        $fv = $this->getRecentVersion();
        $fav = $this->getApprovedVersion();
        $fv2 = $fv->duplicate();

        if ($fv->getFileVersionID() == $fav->getFileVersionID()) {
            $fv2->approve();
        }

        if ($copyUnderlyingFile) {
            $fv2->duplicateUnderlyingFile();
        }

        return $fv2;
    }

    /**
     * Returns a file version object that is to be written to. Computes whether we can use the current most recent version, OR a new one should be created.
     *
     * @param bool $forceCreateNew If we should always create a new version even if we are below the threshold
     *
     * @return Version
     */
    public function getVersionToModify($forceCreateNew = false)
    {
        $u = Core::make(User::class);
        $createNew = false;

        $fv = $this->getRecentVersion();
        $fav = $this->getApprovedVersion();

        // first test. Does the user ID of the most recent version match ours? If not, then we create new
        if ($u->getUserID() != $fv->getAuthorUserID()) {
            $createNew = true;
        }

        // second test. If the date the version was added is older than File::CREATE_NEW_VERSION_THRESHOLD, we create new
        $diff = time() - $fv->getDateAdded()->getTimestamp();
        if ($diff > self::CREATE_NEW_VERSION_THRESHOLD) {
            $createNew = true;
        }

        if ($forceCreateNew) {
            $createNew = true;
        }

        if ($createNew) {
            $fv2 = $fv->duplicate();

            // Are the recent and active versions the same? If so, we approve this new version we just made
            if ($fv->getFileVersionID() == $fav->getFileVersionID()) {
                $fv2->approve();
            }

            return $fv2;
        }

        return $fv;
    }

    /**
     * File ID of the file.
     *
     * @return int
     */
    public function getFileID()
    {
        return $this->fID;
    }

    /**
     * Get the file UUID.
     *
     * @return string|null
     */
    public function getFileUUID()
    {
        return $this->fUUID;
    }

    /**
     * Assign a new UUID to this file.
     *
     * @return $this
     */
    public function generateFileUUID(): self
    {
        $this->fUUID = uuid_create(UUID_TYPE_DEFAULT);

        return $this;
    }

    /**
     * Remove the the UUID from this file.
     *
     * @return $this
     */
    public function resetFileUUID(): self
    {
        $this->fUUID = null;

        return $this;
    }

    /**
     *
     * @return bool
     */
    public function hasFileUUID()
    {
        return $this->fUUID !== null;
    }

    /**
     * Folder to put the file in.
     *
     * @param FileFolder $folder
     */
    public function setFileFolder(FileFolder $folder)
    {
        $em = \ORM::entityManager('core');

        $this->folderTreeNodeID = $folder->getTreeNodeID();

        $em->persist($this);
        $em->flush();
    }

    /**
     * @return NodeType
     */
    public function getFileFolderObject()
    {
        return Node::getByID($this->folderTreeNodeID);
    }

    /**
     * @return NodeType
     */
    public function getFileNodeObject()
    {
        $db = \Database::connection();
        $treeNodeID = $db->GetOne('select treeNodeID from TreeFileNodes where fID = ?', [$this->getFileID()]);

        return Node::getByID($treeNodeID);
    }

    /**
     * Duplicate a file
     * The new file will have no version history.
     *
     * @return File
     */
    public function duplicate()
    {
        $db = Loader::db();
        $em = \ORM::entityManager();

        $versions = $this->versions;
        $thumbs = Type::getVersionList();

        // duplicate the core file object
        $nf = clone $this;
        if ($this->hasFileUUID()) {
            $nf->generateFileUUID();
        }
        $dh = Loader::helper('date');
        $date = $dh->getOverridableNow();
        $nf->fDateAdded = new Carbon($date);

        $em->persist($nf);
        $em->flush();

        $folder = $this->getFileFolderObject();
        $folderNode = \Concrete\Core\Tree\Node\Type\File::add($nf, $folder);
        $nf->folderTreeNodeID = $folder->getTreeNodeID();

        $em->persist($nf);
        $em->flush();

        // clear out the versions
        $nf->versions = new ArrayCollection();

        $fi = Core::make('helper/file');
        $cf = Core::make('helper/concrete/file');
        $importer = new Importer();
        $filesystem = $this->getFileStorageLocationObject()->getFileSystemObject();
        foreach ($versions as $version) {
            if ($version->isApproved()) {
                $cloneVersion = clone $version;
                $cloneVersion->setFileVersionID(1);
                $cloneVersion->setFile($nf);

                $em->persist($cloneVersion);
                $em->flush();

                $cloneVersion->duplicateUnderlyingFile();

                foreach ($version->getAttributes() as $value) {
                    $value = clone $value;
                    $value->setVersion($cloneVersion);
                    $em->persist($value);
                }

                $em->flush();

                $nf->versions->add($cloneVersion);
                foreach ($thumbs as $type) {
                    $cloneVersion->duplicateUnderlyingThumbnailFiles($type, $version);
                }
            }
        }

        $em->persist($nf);
        $em->flush();

        $v = [$this->fID];
        $q = 'select fID, paID, pkID from FilePermissionAssignments where fID = ?';
        $r = $db->query($q, $v);
        while ($row = $r->fetch()) {
            $v = [$nf->getFileID(), $row['paID'], $row['pkID']];
            $q = 'insert into FilePermissionAssignments (fID, paID, pkID) values (?, ?, ?)';
            $db->query($q, $v);
        }

        $fe = new \Concrete\Core\File\Event\DuplicateFile($this);
        $fe->setNewFileObject($nf);
        Events::dispatch('on_file_duplicate', $fe);

        return $nf;
    }

    /**
     * @return \Concrete\Core\Entity\File\Version|null
     */
    public function getApprovedVersion()
    {
        // Ideally, doctrine's caching would handle this. Unfortunately, something is wrong with the $file
        // object going into the query, so none of them are ever marked as cacheable, which means we always
        // run the query even though we've run it multiple times in the same request. So we're going to
        // step between doctrine this time.
        $cache = \Core::make('cache/request');
        $item = $cache->getItem('file/version/approved/' . $this->getFileID());
        if (!$item->isMiss()) {
            return $item->get();
        }

        $em = \ORM::entityManager();
        $r = $em->getRepository('\Concrete\Core\Entity\File\Version');
        $fv = $r->findOneBy(['file' => $this, 'fvIsApproved' => true]);

        $cache->save($item->set($fv));

        return $fv;
    }

    /**
     * If a file is in a particular file set.
     *
     * @param Set $fs
     *
     * @return bool true if its in the set
     */
    public function inFileSet(Set $fs)
    {
        $db = Loader::db();
        $r = $db->GetOne(
            'select fsfID from FileSetFiles where fID = ? and fsID = ?',
            [$this->getFileID(), $fs->getFileSetID()]
        );

        return $r > 0;
    }

    /**
     * Removes a file, including all of its versions.
     **
     * @return bool returns false if the on_file_delete event says not to proceed, returns true on success
     * @throws \Exception contains the exception type and message of why the deletion fails
     *
     */
    public function delete()
    {
        // first, we remove all files from the drive
        $app = Application::getFacadeApplication();
        /** @var LoggerFactory $loggerFactory */
        $loggerFactory = $app->make(LoggerFactory::class);
        $logger = $loggerFactory->createLogger(Channels::CHANNEL_FILES);
        /** @var EntityManagerInterface $em */
        $em = $app->make(EntityManagerInterface::class);
        /** @var EventDispatcher $eventDispatcher */
        $eventDispatcher = $app->make(EventDispatcher::class);

        $fileName = $this->getFileID();

        if ($this->getApprovedVersion() instanceof Version) {
            $fileName = $this->getApprovedVersion()->getFileName();
        }

        $em->beginTransaction();

        $db = $em->getConnection();

        try {
            // fire an on_page_delete event
            $fve = new DeleteFile($this);

            $fve = $eventDispatcher->dispatch('on_file_delete', $fve);

            /** @noinspection PhpPossiblePolymorphicInvocationInspection */
            if (!$fve->proceed()) {
                return false;
            }

            // Delete the tree node for the file.
            $nodeID = $db->fetchColumn('SELECT treeNodeID FROM TreeFileNodes WHERE fID = ?', [$this->getFileID()]);
            if ($nodeID) {
                $node = Node::getByID($nodeID);
                $node->delete();
            }

            $versions = $this->getVersionList();

            foreach ($versions as $fv) {
                $fv->delete(true);
            }

            $items = $em->getRepository(Item::class)->findByRelevantThumbnail($this);
            $instanceItemRepository = $em->getRepository(InstanceItem::class);
            foreach ($items as $item) {
                $instanceItems = $instanceItemRepository->findByItem($item);
                foreach ($instanceItems as $instanceItem) {
                    $em->remove($instanceItem);
                }
            }
            $em->flush();

            $db->executeQuery('DELETE FROM FileSetFiles WHERE fID = ?', [$this->fID]);
            $db->executeQuery('DELETE FROM FileSearchIndexAttributes WHERE fID = ?', [$this->fID]);
            $db->executeQuery('DELETE FROM FilePermissionAssignments WHERE fID = ?', [$this->fID]);
            $db->executeQuery('DELETE FROM FileImageThumbnailPaths WHERE fileID = ?', [$this->fID]);
            $db->executeQuery('DELETE FROM Files WHERE fID = ?', [$this->fID]);

            $em->commit();

            try {
                $logger->notice(t("File %s successfully deleted.", $fileName));
            } catch (\Exception $err) {
                // Skip any errors while logging to pass the automated tests
            }

        } catch (\Exception $e) {
            $em->rollback();
            throw $e;
        }

        return true;
    }

    /**
     * Returns the most recent FileVersion object.
     *
     * @return Version
     */
    public function getRecentVersion()
    {
        $em = \ORM::entityManager();
        $r = $em->getRepository('\Concrete\Core\Entity\File\Version');

        return $r->findOneBy(
            ['file' => $this],
            ['fvID' => 'desc']
        );
    }

    /**
     * returns the FileVersion object for the provided fvID
     * if none provided returns the approved version.
     *
     * @param int $fvID
     *
     * @return Version
     */
    public function getVersion($fvID = null)
    {
        if (!$fvID) {
            return $this->getApprovedVersion();
        }

        $em = \ORM::entityManager();
        $r = $em->getRepository('\Concrete\Core\Entity\File\Version');

        return $r->findOneBy(['file' => $this, 'fvID' => $fvID]);
    }

    /**
     * Returns an array of all FileVersion objects owned by this file.
     *
     * @return Version[]
     */
    public function getVersionList()
    {
        return $this->getFileVersions();
    }

    /**
     * Total number of downloads for a file.
     *
     * @return int
     */
    public function getTotalDownloads()
    {
        $em = app(EntityManagerInterface::class);
        $qb = $em->createQueryBuilder();
        $x = $qb->expr();
        $qb
            ->select($x->count('ds.id'))
            ->from(DownloadStatistics::class, 'ds')
            ->andWhere($x->eq('ds.file', $this->getFileID()));

        return (int)$qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param int $limit
     *
     * @return array
     * @deprecated Use the DownloadStatistics entity
     *
     */
    public function getDownloadStatistics($limit = 20)
    {
        $em = app(EntityManagerInterface::class);
        $qb = $em->createQueryBuilder();
        $x = $qb->expr();
        $qb
            ->select('ds')
            ->from(DownloadStatistics::class, 'ds')
            ->andWhere($x->eq('ds.file', $this->getFileID()))
            ->addOrderBy('ds.downloadDateTime', 'DESC');
        $limit = (int)$limit;
        if ($limit > 0) {
            $qb->setMaxResults($limit);
        }
        $dtFormat = $em->getConnection()->getDatabasePlatform()->getDateTimeFormatString();
        $result = [];
        foreach ($qb->getQuery()->execute() as $ds) {
            $result[] = [
                'dsID' => $ds->getID(),
                'fID' => $ds->getFile()->getFileID(),
                'fvID' => $ds->getFileVersion(),
                'uID' => $ds->getDownloaderID() ?: 0,
                'rcID' => $ds->getRelatedPageID() ?: 0,
                'timestamp' => $ds->getDownloadDateTime()->format($dtFormat),
            ];
        }

        return $result;
    }

    /**
     * Tracks File Download, takes the cID of the page that the file was downloaded from.
     *
     * @param int $rcID
     */
    public function trackDownload($rcID = null)
    {
        $u = Core::make(User::class);
        $uID = (int)($u->getUserID());
        $fv = $this->getApprovedVersion();
        $fvID = $fv->getFileVersionID();
        if (!isset($rcID) || !is_numeric($rcID)) {
            $rcID = 0;
        }

        $fve = new \Concrete\Core\File\Event\FileAccess($fv);
        Events::dispatch('on_file_download', $fve);

        $config = Core::make('config');
        if ($config->get('concrete.statistics.track_downloads')) {
            $em = app(EntityManagerInterface::class);
            $ds = DownloadStatistics::create($this, $fvID, $uID, $rcID);
            $em->persist($ds);
            $em->flush($ds);
        }
    }

    /**
     * @deprecated
     */
    public function isError()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\ObjectInterface::getAttributeValueObject()
     */
    public function getAttributeValueObject($ak, $createIfNotExists = false)
    {
        $fv = $this->getApprovedVersion();
        if ($fv !== null) {
            return $fv->getAttributeValueObject($ak, $createIfNotExists);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\ObjectInterface::getAttributeValue()
     */
    public function getAttributeValue($ak)
    {
        $fv = $this->getApprovedVersion();
        if ($fv !== null) {
            return $fv->getAttributeValue($ak);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\ObjectInterface::getAttribute()
     */
    public function getAttribute($ak, $mode = false)
    {
        $fv = $this->getApprovedVersion();
        if ($fv !== null) {
            return $fv->getAttribute($ak);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\ObjectInterface::getObjectAttributeCategory()
     */
    public function getObjectAttributeCategory()
    {
        $app = Application::getFacadeApplication();

        return $app->make('\Concrete\Core\Attribute\Category\FileCategory');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\ObjectInterface::clearAttribute()
     */
    public function clearAttribute($ak)
    {
        $fv = $this->getApprovedVersion();
        if ($fv !== null) {
            $fv->clearAttribute($ak);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\ObjectInterface::setAttribute()
     */
    public function setAttribute($ak, $value)
    {
        $fv = $this->getApprovedVersion();
        if ($fv !== null) {
            return $fv->setAttribute($ak, $value);
        }
    }

    /**
     * Persist any object changes to the database.
     */
    protected function save()
    {
        $em = \ORM::entityManager();
        $em->persist($this);
        $em->flush();
    }
}
