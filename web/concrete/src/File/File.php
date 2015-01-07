<?php
namespace Concrete\Core\File;

use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Util\Debug;
use Doctrine\DBAL\Logging\EchoSQLLogger;
use FileSet;
use Concrete\Flysystem\AdapterInterface;
use Loader;
use CacheLocal;
use Core;
use User;
use Events;
use Page;
use Database;
use \Concrete\Core\Attribute\Key as AttributeKey;
use \Concrete\Core\File\StorageLocation\StorageLocation;
use FileAttributeKey;
use PermissionKey;

/**
 * @Entity
 * @Table(name="Files")
 */
class File implements \Concrete\Core\Permission\ObjectInterface
{

    const CREATE_NEW_VERSION_THRESHOLD = 300;


    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    protected $fID;

    /**
     * @Column(type="datetime")
     */
    protected $fDateAdded = null;

    /**
     * @Column(type="string")
     */
    protected $fPassword;

    /**
     * @OneToMany(targetEntity="Version", mappedBy="file", cascade={"persist"})
     * @JoinColumn(name="fID")
     */
    protected $versions;

    /**
     * @Column(type="boolean")
     */
    protected $fOverrideSetPermissions = false;

    /**
     * Originally placed on which page.
     * @Column(columnDefinition="integer unsigned")
     */
    protected $ocID = 0;

    /**
     * @Column(columnDefinition="integer unsigned")
     */
    protected $uID = 0;

    /**
     * @ManyToOne(targetEntity="\Concrete\Core\File\StorageLocation\StorageLocation")
     * @JoinColumn(name="fslID", referencedColumnName="fslID")
     **/
    protected $storageLocation;

    public function __construct()
    {
        $this->versions = new ArrayCollection();
    }

    /**
     * returns a file object for the given file ID
     * @param int $fID
     * @return File
     */
    public static function getByID($fID)
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        return $em->find('\Concrete\Core\File\File', $fID);
    }

    /**
     * For all methods that file does not implement, we pass through to the currently active file version object
     */
    public function __call($nm, $a)
    {
        $fv = $this->getApprovedVersion();
        if(is_null($fv)) {
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
        return $this->storageLocation;
    }

    /**
     * @return \Concrete\Core\File\StorageLocation\StorageLocation
     */
    public function getFileStorageLocationObject()
    {
        return $this->storageLocation;
    }

    /**
     * @return array \Concrete\Core\File\Version
     */
    public function getFileVersions()
    {
        return $this->versions;
    }

    /**
     * Reindex the attributes on this file.
     * @return void
     */
    public function reindex()
    {
        $attribs = FileAttributeKey::getAttributes(
            $this->getFileID(),
            $this->getFileVersionID(),
            'getSearchIndexValue'
        );
        $db = Loader::db();

        $db->Execute('delete from FileSearchIndexAttributes where fID = ?', array($this->getFileID()));
        $searchableAttributes = array('fID' => $this->getFileID());

        $key = new FileAttributeKey();
        $key->reindex('FileSearchIndexAttributes', $searchableAttributes, $attribs);
    }

    public static function getRelativePathFromID($fID)
    {
        $path = CacheLocal::getEntry('file_relative_path', $fID);
        if ($path != false) {
            return $path;
        }

        $f = static::getByID($fID);
        $path = $f->getRelativePath();

        CacheLocal::set('file_relative_path', $fID, $path);
        return $path;
    }

    protected function save()
    {
        $em = Database::get()->getEntityManager();
        $em->persist($this);
        $em->flush();
    }

    public function setFileStorageLocation(StorageLocation $newLocation)
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
        } catch(\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        $this->storageLocation = $newLocation;
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

    public function overrideFileSetPermissions()
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
        $this->fOverrideSetPermissions = (bool)$fOverrideSetPermissions;
        $this->save();
    }


    public function getUserID()
    {
        return $this->uID;
    }

    public function setUserID($uID)
    {
        $this->uID = $uID;
        $this->save();
    }

    public function getFileSets()
    {
        $db = Loader::db();
        $fsIDs = $db->Execute("select fsID from FileSetFiles where fID = ?", array($this->getFileID()));
        $filesets = array();
        while ($row = $fsIDs->FetchRow()) {
            $filesets[] = FileSet::getByID($row['fsID']);
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

    public function getDateAdded()
    {
        return $this->fDateAdded;
    }

    /**
     * Returns a file version object that is to be written to. Computes whether we can use the current most recent version, OR a new one should be created
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
        if ($diff > File::CREATE_NEW_VERSION_THRESHOLD) {
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

    public function duplicate()
    {

        $db = Loader::db();
        $em = $db->getEntityManager();

        $versions = $this->versions;

        // duplicate the core file object
        $nf = clone $this;
        $dh = Loader::helper('date');
        $date = $dh->getOverridableNow();
        $nf->fDateAdded = new Carbon($date);

        $em->persist($nf);
        $em->flush();

        // clear out the versions
        $nf->versions = new ArrayCollection();

        $fi = Core::make('helper/file');
        $cf = Core::make('helper/concrete/file');
        $importer = new Importer();
        $filesystem = $this->getFileStorageLocationObject()->getFileSystemObject();
        foreach($versions as $version) {
            if ($version->isApproved()) {
                $cloneVersion = clone $version;
                $cloneVersion->setFile($nf);
                $prefix = $importer->generatePrefix();
                $filesystem->write($cf->prefix($prefix, $version->getFilename()), $version->getFileResource()->read(), array(
                    'visibility' => AdapterInterface::VISIBILITY_PUBLIC,
                    'mimetype' => Core::make('helper/mime')->mimeFromExtension($fi->getExtension($version->getFilename()))
                ));
                $cloneVersion->updateFile($version->getFilename(), $prefix);
                $nf->versions->add($cloneVersion);
            }
        }

        $em->persist($nf);
        $em->flush();


        $r = $db->Execute('select fvID, akID, avID from FileAttributeValues where fID = ?', array($this->getFileID()));
        while ($row = $r->fetchRow()) {
            $db->Execute(
                "insert into FileAttributeValues (fID, fvID, akID, avID) values (?, ?, ?, ?)",
                array(
                    $nf->getFileID(),
                    $row['fvID'],
                    $row['akID'],
                    $row['avID']
                )
            );
        }

        $v = array($this->fID);
        $q = "select fID, paID, pkID from FilePermissionAssignments where fID = ?";
        $r = $db->query($q, $v);
        while ($row = $r->fetchRow()) {
            $v = array($nf->getFileID(), $row['paID'], $row['pkID']);
            $q = "insert into FilePermissionAssignments (fID, paID, pkID) values (?, ?, ?)";
            $db->query($q, $v);
        }

        foreach($nf->getVersionList() as $v) {
            $v->refreshAttributes();
        }

        $fe = new \Concrete\Core\File\Event\DuplicateFile($this);
        $fe->setNewFileObject($nf);
        Events::dispatch('on_file_duplicate', $fe);

        return $nf;
    }

    public static function add($filename, $prefix, $data = array(), $fsl = false)
    {
        $db = Loader::db();
        $dh = Loader::helper('date');
        $date = $dh->getOverridableNow();

        if (!is_object($fsl)) {
            $fsl = StorageLocation::getDefault();
        }

        $uID = 0;
        $u = new User();
        if (isset($data['uID'])) {
            $uID = $data['uID'];
        } else {
            if ($u->isRegistered()) {
                $uID = $u->getUserID();
            }
        }

        $f = new self;
        $f->uID = $uID;
        $f->storageLocation = $fsl;
        $f->fDateAdded = new Carbon($date);
        $em = $db->getEntityManager();
        $em->persist($f);
        $em->flush();

        $fv = Version::add($f, $filename, $prefix, $data);

        $f->versions->add($fv);

        $fve = new \Concrete\Core\File\Event\FileVersion($fv);
        Events::dispatch('on_file_add', $fve);

        $entities = $u->getUserAccessEntityObjects();
        $hasUploader = false;
        foreach ($entities as $obj) {
            if ($obj instanceof FileUploaderPermissionAccessEntity) {
                $hasUploader = true;
            }
        }
        if (!$hasUploader) {
            $u->refreshUserGroups();
        }
        return $fv;
    }

    public function getApprovedVersion()
    {
        // Ideally, doctrine's caching would handle this. Unfortunately, something is wrong with the $file
        // object going into the query, so none of them are ever marked as cacheable, which means we always
        // run the query even though we've run it multiple times in the same request. So we're going to
        // step between doctrine this time.
        $item = \Core::make('cache/request')->getItem('file/version/approved/' . $this->getFileID());
        if (!$item->isMiss()) {
            return $item->get();
        }

        $db = Loader::db();
        $em = $db->getEntityManager();
        $r = $em->getRepository('\Concrete\Core\File\Version');
        $fv = $r->findOneBy(array('file' => $this, 'fvIsApproved' => true));

        $item->set($fv);

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
     * Removes a file, including all of its versions
     */
    public function delete()
    {
        // first, we remove all files from the drive
        $db = Loader::db();

        // fire an on_page_delete event
        $fve = new \Concrete\Core\File\Event\DeleteFile($this);
        $fve = Events::dispatch('on_file_delete', $fve);
        if (!$fve->proceed()) {
            return false;
        }

        $versions = $this->getVersionList();
        foreach ($versions as $fv) {
            $fv->delete(true);
        }

        // now from the DB
        $em = $db->getEntityManager();
        $em->remove($this);
        $em->flush();
        $db->Execute("delete from FileSetFiles where fID = ?", array($this->fID));
        $db->Execute("delete from FileSearchIndexAttributes where fID = ?", array($this->fID));
        $db->Execute("delete from DownloadStatistics where fID = ?", array($this->fID));
        $db->Execute("delete from FilePermissionAssignments where fID = ?", array($this->fID));
    }

    /**
     * returns the most recent FileVersion object
     * @return FileVersion
     */
    public function getRecentVersion()
    {
        $db = Loader::db();
        $em = $db->getEntityManager();
        $r = $em->getRepository('\Concrete\Core\File\Version');
        return $r->findOneBy(
            array('file' => $this),
            array('fvID' => 'desc')
        );
    }

    /**
     * returns the FileVersion object for the provided fvID
     * if none provided returns the approved version
     * @param int $fvID
     * @return Version
     */
    public function getVersion($fvID = null)
    {
        if (!$fvID) {
            return $this->getApprovedVersion();
        }

        $db = Loader::db();
        $em = $db->getEntityManager();
        $r = $em->getRepository('\Concrete\Core\File\Version');
        return $r->findOneBy(array('file' => $this, 'fvID' => $fvID));
    }

    /**
     * Returns an array of all FileVersion objects owned by this file
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

        if (is_object($this) && $this instanceof File) {
            return $db->getAll(
                "SELECT * FROM DownloadStatistics WHERE fID = ? ORDER BY timestamp desc {$limitString}",
                array($this->getFileID())
            );
        } else {
            return $db->getAll("SELECT * FROM DownloadStatistics ORDER BY timestamp desc {$limitString}");
        }
    }

    /**
     * Tracks File Download, takes the cID of the page that the file was downloaded from
     * @param int $rcID
     * @return void
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

        $db = Loader::db();
        $db->Execute(
            'insert into DownloadStatistics (fID, fvID, uID, rcID) values (?, ?, ?, ?)',
            array($this->fID, intval($fvID), $uID, $rcID)
        );
    }

    /**
     * @deprecated
     */
    public function isError()
    {
        return false;
    }
}
