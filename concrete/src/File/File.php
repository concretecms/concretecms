<?php
namespace Concrete\Core\File;

use Carbon\Carbon;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Concrete\Core\User\UserInfo;
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
use Concrete\Core\File\StorageLocation\StorageLocation;
use PermissionKey;
use Doctrine\ORM\Mapping as ORM;

class File
{

    /**
     * returns a file object for the given file ID.
     *
     * @param int $fID
     *
     * @return \Concrete\Core\Entity\File\File
     */
    public static function getByID($fID)
    {
        $em = \ORM::entityManager();
        return $em->find('\Concrete\Core\Entity\File\File', $fID);
    }

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

    public static function add($filename, $prefix, $data = array(), $fsl = false, $folder = false)
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
