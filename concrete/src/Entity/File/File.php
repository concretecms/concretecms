<?php
namespace Concrete\Core\Entity\File;

use Carbon\Carbon;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\File\Event\FileVersion;
use Concrete\Core\File\Image\Thumbnail\Type\Type;
use Concrete\Core\File\Importer;
use Concrete\Core\File\Set\Set;
use Concrete\Core\Support\Facade\Database;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\NodeType;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use FileSet;
use League\Flysystem\AdapterInterface;
use Loader;
use Core;
use User;
use Events;
use Page;
use PermissionKey;
use Doctrine\ORM\Mapping as ORM;

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
class File implements \Concrete\Core\Permission\ObjectInterface
{
    const CREATE_NEW_VERSION_THRESHOLD = 300;

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $fID;

    /**
     * @ORM\Column(type="datetime")
     */
    public $fDateAdded = null;

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
     **/
    public $author;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    public $folderTreeNodeID = 0;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\File\StorageLocation\StorageLocation", inversedBy="files")
     * @ORM\JoinColumn(name="fslID", referencedColumnName="fslID")
     **/
    public $storageLocation;

    public function __construct()
    {
        $this->versions = new ArrayCollection();
    }

    /**
     * For all methods that file does not implement, we pass through to the currently active file version object.
     */
    public function __call($nm, $a)
    {
        $fv = $this->getApprovedVersion();
        if (is_null($fv)) {
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
     * Persist any object changes to the database.
     */
    protected function save()
    {
        $em = \ORM::entityManager();
        $em->persist($this);
        $em->flush();
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
     *
     * @throws \Exception
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
        $this->fOverrideSetPermissions = (bool) $fOverrideSetPermissions;
        $this->save();
    }

    /**
     * Returns the user ID of the author of the file (if available).
     *
     * @return int|null
     */
    public function getUserID()
    {
        return $this->author ? $this->author->getUserID() : null;
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
     * @return FileSet[]
     */
    public function getFileSets()
    {
        $db = Loader::db();
        $fsIDs = $db->Execute('select fsID from FileSetFiles where fID = ?', [$this->getFileID()]);
        $filesets = [];
        while ($row = $fsIDs->FetchRow()) {
            $fs = FileSet::getByID($row['fsID']);
            if (is_object($fs)) {
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
            $u = new User();
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
        $u = new User();
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
        } else {
            return $fv;
        }
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

        // duplicate the core file object
        $nf = clone $this;
        $dh = Loader::helper('date');
        $date = $dh->getOverridableNow();
        $nf->fDateAdded = new Carbon($date);

        $em->persist($nf);
        $em->flush();

        $folder = $this->getFileFolderObject();
        $folderNode = \Concrete\Core\Tree\Node\Type\File::add($nf, $folder);
        $nf->folderTreeNodeID = $folderNode->getTreeNodeID();

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

                do {
                    $prefix = $importer->generatePrefix();
                    $path = $cf->prefix($prefix, $version->getFilename());
                } while ($filesystem->has($path));
                $filesystem->write($path, $version->getFileResource()->read(), [
                    'visibility' => AdapterInterface::VISIBILITY_PUBLIC,
                    'mimetype' => Core::make('helper/mime')->mimeFromExtension($fi->getExtension($version->getFilename())),
                ]);
                $cloneVersion->updateFile($version->getFilename(), $prefix);
                $nf->versions->add($cloneVersion);
            }
        }

        $em->persist($nf);
        $em->flush();

        $v = [$this->fID];
        $q = 'select fID, paID, pkID from FilePermissionAssignments where fID = ?';
        $r = $db->query($q, $v);
        while ($row = $r->fetchRow()) {
            $v = [$nf->getFileID(), $row['paID'], $row['pkID']];
            $q = 'insert into FilePermissionAssignments (fID, paID, pkID) values (?, ?, ?)';
            $db->query($q, $v);
        }

        $fe = new \Concrete\Core\File\Event\DuplicateFile($this);
        $fe->setNewFileObject($nf);
        Events::dispatch('on_file_duplicate', $fe);

        return $nf;
    }

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
     *
     * @return bool returns false if the on_file_delete event says not to proceed, returns true on success
     *
     * @throws \Exception contains the exception type and message of why the deletion fails
     */
    public function delete($removeNode = true)
    {
        // first, we remove all files from the drive
        $db = Core::make(Connection::class);
        $em = $db->getEntityManager();
        $em->beginTransaction();
        try {
            // fire an on_page_delete event
            $fve = new \Concrete\Core\File\Event\DeleteFile($this);
            $fve = Events::dispatch('on_file_delete', $fve);
            if (!$fve->proceed()) {
                return false;
            }

            // Delete the tree node for the file.
            if ($removeNode) {
                $nodeID = $db->fetchColumn('select treeNodeID from TreeFileNodes where fID = ?', [$this->getFileID()]);
                if ($nodeID) {
                    $node = Node::getByID($nodeID);
                    $node->delete();
                }
            }

            $versions = $this->getVersionList();
            foreach ($versions as $fv) {
                $fv->delete(true);
            }

            $db->Execute('delete from FileSetFiles where fID = ?', [$this->fID]);
            $db->Execute('delete from FileSearchIndexAttributes where fID = ?', [$this->fID]);
            $db->Execute('delete from DownloadStatistics where fID = ?', [$this->fID]);
            $db->Execute('delete from FilePermissionAssignments where fID = ?', [$this->fID]);
            $db->Execute('delete from FileImageThumbnailPaths where fileID = ?', [$this->fID]);
            $db->Execute('delete from Files where fID = ?', [$this->fID]);

            $em->commit();
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
        $db = Loader::db();

        return $db->GetOne('select count(*) from DownloadStatistics where fID = ?', [$this->getFileID()]);
    }

    /**
     * Get the download statistics for the current file.
     *
     * @param int $limit max number of stats to retrieve
     *
     * @return array
     */
    public function getDownloadStatistics($limit = 20)
    {
        $db = Loader::db();
        $limitString = '';
        if ($limit != false) {
            $limitString = 'limit ' . intval($limit);
        }

        if (is_object($this) && $this instanceof self) {
            return $db->getAll(
                "SELECT * FROM DownloadStatistics WHERE fID = ? ORDER BY timestamp desc {$limitString}",
                [$this->getFileID()]
            );
        } else {
            return $db->getAll("SELECT * FROM DownloadStatistics ORDER BY timestamp desc {$limitString}");
        }
    }

    /**
     * Tracks File Download, takes the cID of the page that the file was downloaded from.
     *
     * @param int $rcID
     */
    public function trackDownload($rcID = null)
    {
        $u = new User();
        $uID = intval($u->getUserID());
        $fv = $this->getApprovedVersion();
        $fvID = $fv->getFileVersionID();
        if (!isset($rcID) || !is_numeric($rcID)) {
            $rcID = 0;
        }

        $fve = new \Concrete\Core\File\Event\FileAccess($fv);
        Events::dispatch('on_file_download', $fve);

        $config = Core::make('config');
        if ($config->get('concrete.statistics.track_downloads')) {
            $db = Loader::db();
            $db->Execute(
                'insert into DownloadStatistics (fID, fvID, uID, rcID) values (?, ?, ?, ?)',
                [$this->fID, intval($fvID), $uID, $rcID]
            );
        }
    }

    /**
     * @deprecated
     */
    public function isError()
    {
        return false;
    }
}
