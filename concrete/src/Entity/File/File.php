<?php
namespace Concrete\Core\Entity\File;

use Carbon\Carbon;
use Concrete\Core\File\Importer;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Doctrine\Common\Collections\ArrayCollection;
use FileSet;
use League\Flysystem\AdapterInterface;
use Loader;
use CacheLocal;
use Core;
use User;
use Events;
use Page;
use Database;
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
     * @ORM\JoinColumn(name="uID", referencedColumnName="uID")
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
            return null;
        }

        return call_user_func_array(array($fv, $nm), $a);
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

    public function getPermissionObjectIdentifier()
    {
        return $this->getFileID();
    }

    public function getPassword()
    {
        return $this->fPassword;
    }

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
        foreach($values as $value) {
            $indexer->indexEntry($category, $value, $this);
        }
    }

    protected function save()
    {
        $em = \ORM::entityManager();
        $em->persist($this);
        $em->flush();
    }

    public function setStorageLocation(\Concrete\Core\Entity\File\StorageLocation\StorageLocation $location)
    {
        $this->storageLocation = $location;
    }

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
                $contents = $fv->getFileContents();
                $newFileSystem->put($fh->prefix($fv->getPrefix(), $fv->getFilename()), $contents);
                $currentFilesystem->delete($fh->prefix($fv->getPrefix(), $fv->getFilename()));
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        $this->setStorageLocation($newLocation);
        $this->save();
    }

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

    public function overrideFileFolderPermissions()
    {
        return $this->fOverrideSetPermissions;
    }

    public function resetPermissions($fOverrideSetPermissions = 0)
    {
        $db = Loader::db();
        $db->Execute("delete from FilePermissionAssignments where fID = ?", array($this->fID));
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

    public function getUserID()
    {
        return $this->author->getUserID();
    }

    public function setUser(\Concrete\Core\Entity\User\User $user)
    {
        $this->author = $user;
        $this->save();
    }

    public function getFileSets()
    {
        $db = Loader::db();
        $fsIDs = $db->Execute("select fsID from FileSetFiles where fID = ?", array($this->getFileID()));
        $filesets = array();
        while ($row = $fsIDs->FetchRow()) {
            $fs = FileSet::getByID($row['fsID']);
            if (is_object($fs)) {
                $filesets[] = $fs;
            }
        }

        return $filesets;
    }

    public function isStarred($u = false)
    {
        if (!$u) {
            $u = new User();
        }
        $db = Loader::db();
        $r = $db->GetOne(
            "select fsfID from FileSetFiles fsf inner join FileSets fs on fs.fsID = fsf.fsID where fsf.fID = ? and fs.uID = ? and fs.fsType = ?",
            array($this->getFileID(), $u->getUserID(), FileSet::TYPE_STARRED)
        );

        return $r > 0;
    }

    public function setDateAdded($fDateAdded)
    {
        $this->fDateAdded = $fDateAdded;
    }

    public function getDateAdded()
    {
        return $this->fDateAdded;
    }

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

    public function getFileID()
    {
        return $this->fID;
    }

    public function setFileFolder(FileFolder $folder)
    {
        $db = Loader::db();
        $em = \ORM::entityManager('core');

        $this->folderTreeNodeID = $folder->getTreeNodeID();

        $em->persist($this);
        $em->flush();

    }

    public function getFileFolderObject()
    {
        return Node::getByID($this->folderTreeNodeID);
    }

    public function getFileNodeObject()
    {
        $db = \Database::connection();
        $treeNodeID = $db->GetOne('select treeNodeID from TreeFileNodes where fID = ?', array($this->getFileID()));
        return Node::getByID($treeNodeID);
    }

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
                $filesystem->write($path, $version->getFileResource()->read(), array(
                    'visibility' => AdapterInterface::VISIBILITY_PUBLIC,
                    'mimetype' => Core::make('helper/mime')->mimeFromExtension($fi->getExtension($version->getFilename())),
                ));
                $cloneVersion->updateFile($version->getFilename(), $prefix);
                $nf->versions->add($cloneVersion);
            }
        }

        $em->persist($nf);
        $em->flush();


        $v = array($this->fID);
        $q = "select fID, paID, pkID from FilePermissionAssignments where fID = ?";
        $r = $db->query($q, $v);
        while ($row = $r->fetchRow()) {
            $v = array($nf->getFileID(), $row['paID'], $row['pkID']);
            $q = "insert into FilePermissionAssignments (fID, paID, pkID) values (?, ?, ?)";
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
        $fv = $r->findOneBy(array('file' => $this, 'fvIsApproved' => true));

        $cache->save($item->set($fv));

        return $fv;
    }

    public function inFileSet($fs)
    {
        $db = Loader::db();
        $r = $db->GetOne(
            "select fsfID from FileSetFiles where fID = ? and fsID = ?",
            array($this->getFileID(), $fs->getFileSetID())
        );

        return $r > 0;
    }

    /**
     * Removes a file, including all of its versions.
     */
    public function delete($removeNode = true)
    {
        // first, we remove all files from the drive
        $db = Loader::db();
        $em = $db->getEntityManager();

        // fire an on_page_delete event
        $fve = new \Concrete\Core\File\Event\DeleteFile($this);
        $fve = Events::dispatch('on_file_delete', $fve);
        if (!$fve->proceed()) {
            return false;
        }

        // Delete the tree node for the file.
        if ($removeNode) {
            $nodeID = $db->fetchColumn('select treeNodeID from TreeFileNodes where fID = ?', array($this->getFileID()));
            if ($nodeID) {
                $node = Node::getByID($nodeID);
                $node->delete();
            }
        }


        $versions = $this->getVersionList();
        foreach ($versions as $fv) {
            $fv->delete(true);
        }

        $db->Execute("delete from FileSetFiles where fID = ?", array($this->fID));
        $db->Execute("delete from FileSearchIndexAttributes where fID = ?", array($this->fID));
        $db->Execute("delete from DownloadStatistics where fID = ?", array($this->fID));
        $db->Execute("delete from FilePermissionAssignments where fID = ?", array($this->fID));

        $query = $em->createQuery('select fav from Concrete\Core\Entity\Attribute\Value\Value\ImageFileValue fav inner join fav.file f where f.fID = :fID');
        $query->setParameter('fID', $this->getFileID());
        $values = $query->getResult();
        foreach ($values as $value) {
            $attributeValues = $value->getAttributeValues();
            foreach ($attributeValues as $attributeValue) {
                $em->remove($attributeValue);
            }
            $em->remove($value);
        }

        // now from the DB
        $em = \ORM::entityManager();
        $em->remove($this);
        $em->flush();
    }

    /**
     * returns the most recent FileVersion object.
     *
     * @return Version
     */
    public function getRecentVersion()
    {
        $em = \ORM::entityManager();
        $r = $em->getRepository('\Concrete\Core\Entity\File\Version');

        return $r->findOneBy(
            array('file' => $this),
            array('fvID' => 'desc')
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

        return $r->findOneBy(array('file' => $this, 'fvID' => $fvID));
    }

    /**
     * Returns an array of all FileVersion objects owned by this file.
     */
    public function getVersionList()
    {
        return $this->getFileVersions();
    }

    public function getTotalDownloads()
    {
        $db = Loader::db();

        return $db->GetOne('select count(*) from DownloadStatistics where fID = ?', array($this->getFileID()));
    }

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
                array($this->getFileID())
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
                array($this->fID, intval($fvID), $uID, $rcID)
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
