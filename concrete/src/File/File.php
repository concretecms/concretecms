<?php
namespace Concrete\Core\File;

use Carbon\Carbon;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\File\File as FileEntity;
use Concrete\Core\Entity\File\Version as FileVersion;
use Concrete\Core\File\Event\FileVersion as FileVersionEvent;
use Concrete\Core\File\StorageLocation\StorageLocationFactory;
use Concrete\Core\Permission\Access\Entity\FileUploaderEntity as FileUploaderPermissionAccessEntity;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Tree\Node\Type\File as FileNode;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Concrete\Core\User\UserInfoRepository;
use Doctrine\ORM\EntityManagerInterface;
use User;

/**
 * Service class for the File entity
 */
class File
{
    /**
     * Return a file object for the given file ID.
     *
     * @param int $fID The file identifier
     *
     * @return FileEntity|null
     */
    public static function getByID($fID)
    {
        return $fID ? Application::getFacadeApplication()->make(EntityManagerInterface::class)->find(FileEntity::class, $fID) : null;
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
        $result = false;
        if ($fID) {
            $cache = Application::getFacadeApplication()->make('cache/request');
            /** @var \Concrete\Core\Cache\Cache $cache */
            if ($cache->isEnabled()) {
                $cacheItem = $cache->getItem('file_relative_path/' . $fID);
                if (!$cacheItem->isMiss()) {
                    return $cacheItem->get();
                }
            }
            $f = static::getByID($fID);
            if ($f !== null) {
                $result = $f->getRelativePath();
                if (isset($cacheItem)) {
                    $cacheItem->set($result)->save();
                }
            }
        }

        return $result;
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
     *
     * @param \Concrete\Core\Entity\File\StorageLocation\StorageLocation|false $fsl The storage location to be used (we'll use the default one if it's falsy)
     * @param FileFolder|false $folder The folder where the file must be added (we'll use the root folder if it's falsy)
     *
     * @return FileVersion
     */
    public static function add($filename, $prefix, $data = [], $fsl = false, $folder = false)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $date = $app->make('helper/date')->getOverridableNow();

        if (!$fsl) {
            $fsl = $app->make(StorageLocationFactory::class)->fetchDefault();
        }

        $u = new User();
        if (isset($data['uID'])) {
            $uID = (int) $data['uID'];
        } else {
            if ($u->isRegistered()) {
                $uID = (int) $u->getUserID();
            } else {
                $uID = 0;
            }
        }

        if (!($folder instanceof FileFolder)) {
            $filesystem = new Filesystem();
            $folder = $filesystem->getRootFolder();
        }

        $f = new FileEntity();
        $f->storageLocation = $fsl;
        $f->fDateAdded = new Carbon($date);
        $f->folderTreeNodeID = $folder->getTreeNodeID();
        if ($uID > 0) {
            $ui = $app->make(UserInfoRepository::class)->getByID($uID);
            if ($ui !== null) {
                $ue = $ui->getEntityObject();
                if ($ue) {
                    $f->setUser($ue);
                }
            }
        }
        $em = $app->make(EntityManagerInterface::class);
        $em->persist($f);
        $em->flush();

        $node = FileNode::add($f, $folder);

        $fv = FileVersion::add($f, $filename, $prefix, $data);

        $f->versions->add($fv);

        $fve = new FileVersionEvent($fv);
        $app->make('director')->dispatch('on_file_add', $fve);

        $entities = $u->getUserAccessEntityObjects();
        $hasUploader = false;
        foreach ($entities as $obj) {
            if ($obj instanceof FileUploaderPermissionAccessEntity) {
                $hasUploader = true;
                break;
            }
        }
        if (!$hasUploader) {
            $u->refreshUserGroups();
        }

        return $fv;
    }
}
