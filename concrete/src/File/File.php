<?php
namespace Concrete\Core\File;

use CacheLocal;
use Carbon\Carbon;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\File\StorageLocation\StorageLocation;
use Concrete\Core\Permission\Access\Entity\FileUploaderEntity as FileUploaderPermissionAccessEntity;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Concrete\Core\User\UserInfo;
use Events;
use Loader;
use User;

class File
{
    /**
     * Return a file object for the given file ID.
     *
     * @param int $fID The file identifier
     *
     * @return \Concrete\Core\Entity\File\File|null
     */
    public static function getByID($fID)
    {
        $em = \ORM::entityManager();

        return $em->find('\Concrete\Core\Entity\File\File', $fID);
    }

    /**
     * Return the relative path for a file (may not exist).
     *
     * @param int $fID The file identifier
     *
     * @return string|false
     */
    public static function getRelativePathFromID($fID)
    {
        $path = CacheLocal::getEntry('file_relative_path', $fID);
        if ($path != false) {
            return $path;
        }

        $f = static::getByID($fID);

        if ($f) {
            $path = $f->getRelativePath();

            CacheLocal::set('file_relative_path', $fID, $path);

            return $path;
        }

        return false;
    }

    /**
     * Create and persist a File entity and a File\Version entity (the filesystem file must already have been imported).
     *
     * @param string $filename The name of the file (without path, only the file name)
     * @param string $prefix The concrete5 file prefix that has been used to store the file
     * @param array $data {
     *     @var int|null $uID The ID of the user to be set as the author of the file (if not specified, we'll use the currently logged in user)
     *     @var string $fvTitle The file title (if not specified, we'll assume an empty string)
     *     @var string $fvDescription The file description (if not specified, we'll assume an empty string)
     *     @var string $fvTags The tags to be associated to the file (separate multiple tags with commas or new lines) (if not specified, we'll assume no tags)
     *     @var bool $fvIsApproved The file title (if not specified, we'll assume an empty string)
     * }
     * @param \Concrete\Core\Entity\File\StorageLocation\StorageLocation|false $fsl The storage location to be used (we'll use the default one if it's falsy)
     * @param \Concrete\Core\Tree\Node\Type\FileFolder|false $folder The folder where the file must be added (we'll use the root folder if it's falsy)
     *
     * @return \Concrete\Core\Entity\File\Version
     */
    public static function add($filename, $prefix, $data = [], $fsl = false, $folder = false)
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

        if (!($folder instanceof FileFolder)) {
            $filesystem = new Filesystem();
            $folder = $filesystem->getRootFolder();
        }

        $f = new \Concrete\Core\Entity\File\File();
        $f->storageLocation = $fsl;
        $f->fDateAdded = new Carbon($date);
        $f->folderTreeNodeID = $folder->getTreeNodeID();

        $em = \ORM::entityManager();
        $em->persist($f);
        $em->flush();

        if ($uID > 0) {
            $ui = UserInfo::getByID($uID);
            if (is_object($ui)) {
                $ue = $ui->getEntityObject();
                if (is_object($ue)) {
                    $f->setUser($ue);
                }
            }
        }

        $node = \Concrete\Core\Tree\Node\Type\File::add($f, $folder);

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
}
