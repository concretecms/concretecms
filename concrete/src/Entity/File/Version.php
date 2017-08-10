<?php

namespace Concrete\Core\Entity\File;

use Carbon\Carbon;
use Concrete\Core\Attribute\Key\FileKey;
use Concrete\Core\Attribute\ObjectInterface;
use Concrete\Core\Attribute\ObjectTrait;
use Concrete\Core\Entity\Attribute\Value\FileValue;
use Concrete\Core\Entity\File\StorageLocation\StorageLocation;
use Concrete\Core\File\Exception\InvalidDimensionException;
use Concrete\Core\File\Image\Thumbnail\Path\Resolver;
use Concrete\Core\File\Image\Thumbnail\Thumbnail;
use Concrete\Core\File\Image\Thumbnail\Type\Type;
use Concrete\Core\File\Image\Thumbnail\Type\Version as ThumbnailTypeVersion;
use Concrete\Core\File\Importer;
use Concrete\Core\File\Menu;
use Concrete\Core\File\Type\TypeList as FileTypeList;
use Concrete\Core\Http\FlysystemFileResponse;
use Concrete\Core\Support\Facade\Application;
use Imagine\Exception\NotSupportedException;
use Imagine\Gd\Image;
use Imagine\Image\Metadata\ExifMetadataReader;
use League\Flysystem\AdapterInterface;
use League\Flysystem\FileNotFoundException;
use Core;
use Database;
use Events;
use Imagine\Image\ImageInterface;
use Page;
use Permissions;
use stdClass;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use User;
use View;
use Doctrine\ORM\Mapping as ORM;
use Concrete\Core\Support\Facade\Facade;
use Imagine\Image\Box;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="FileVersions",
 *     indexes={
 *     @ORM\Index(name="fvFilename", columns={"fvFilename"}),
 *     @ORM\Index(name="fvExtension", columns={"fvExtension"}),
 *     @ORM\Index(name="fvType", columns={"fvType"})
 *     }
 * )
 */
class Version implements ObjectInterface
{
    use ObjectTrait;

    const UT_REPLACE_FILE = 1;
    const UT_TITLE = 2;
    const UT_DESCRIPTION = 3;
    const UT_TAGS = 4;
    const UT_EXTENDED_ATTRIBUTE = 5;
    const UT_CONTENTS = 6;
    const UT_RENAME = 7;

    public function __construct()
    {
        $this->fvDateAdded = new \DateTime();
        $this->fvActivateDateTime = new \DateTime();
    }

    /**
     * /* @ORM\Id
     * @ORM\ManyToOne(targetEntity="File", inversedBy="versions")
     * @ORM\JoinColumn(name="fID", referencedColumnName="fID")
     *
     * @var \Concrete\Core\Entity\File\File
     */
    protected $file;
    /** @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $fvID = 0;
    /**
     * @ORM\Column(type="string")
     */
    protected $fvFilename = null;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $fvPrefix;
    /**
     * @ORM\Column(type="datetime")
     */
    protected $fvDateAdded;
    /**
     * @ORM\Column(type="datetime")
     */
    protected $fvActivateDateTime;
    /**
     * @ORM\Column(type="boolean")
     */
    protected $fvIsApproved = false;
    /**
     * @ORM\Column(type="integer")
     */
    protected $fvAuthorUID = 0;
    /**
     * @ORM\Column(type="bigint")
     */
    protected $fvSize = 0;
    /**
     * @ORM\Column(type="integer")
     */
    protected $fvApproverUID = 0;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $fvTitle = null;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $fvDescription = null;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $fvExtension = null;

    /**
     * @ORM\Column(type="integer")
     */
    protected $fvType = 0;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $fvTags = null;
    /**
     * @ORM\Column(type="boolean")
     */
    protected $fvHasListingThumbnail = false;
    /**
     * @ORM\Column(type="boolean")
     */
    protected $fvHasDetailThumbnail = false;

    private $imagineImage = null;

    /**
     * Add a new file version.
     *
     * @param File   $file
     * @param string $filename
     * @param string $prefix
     * @param array  $data
     *
     * @return static
     */
    public static function add(\Concrete\Core\Entity\File\File $file, $filename, $prefix, $data = [])
    {
        $u = new User();
        $uID = (isset($data['uID']) && $data['uID'] > 0) ? $data['uID'] : $u->getUserID();

        if ($uID < 1) {
            $uID = 0;
        }

        $fvTitle = (isset($data['fvTitle'])) ? $data['fvTitle'] : '';
        $fvDescription = (isset($data['fvDescription'])) ? $data['fvDescription'] : '';
        $fvTags = (isset($data['fvTags'])) ? self::cleanTags($data['fvTags']) : '';
        $fvIsApproved = (isset($data['fvIsApproved'])) ? $data['fvIsApproved'] : '1';

        $dh = Core::make('helper/date');
        $date = new Carbon($dh->getOverridableNow());

        $fv = new static();
        $fv->fvFilename = $filename;
        $fv->fvPrefix = $prefix;
        $fv->fvDateAdded = $date;
        $fv->fvIsApproved = (bool) $fvIsApproved;
        $fv->fvApproverUID = $uID;
        $fv->fvAuthorUID = $uID;
        $fv->fvActivateDateTime = $date;
        $fv->fvTitle = $fvTitle;
        $fv->fvDescription = $fvDescription;
        $fv->fvTags = $fvTags;
        $fv->file = $file;
        $fv->fvID = 1;

        $em = \ORM::entityManager();
        $em->persist($fv);
        $em->flush();

        $fve = new \Concrete\Core\File\Event\FileVersion($fv);
        Events::dispatch('on_file_version_add', $fve);

        return $fv;
    }

    /**
     * Clean the tags (removing whitespace).
     *
     * @param $tagsStr string Delimited by '\n'
     *
     * @return string
     */
    public static function cleanTags($tagsStr)
    {
        $tagsArray = explode("\n", str_replace(["\r", ','], "\n", $tagsStr));
        $cleanTags = [];
        foreach ($tagsArray as $tag) {
            if (!strlen(trim($tag))) {
                continue;
            }
            $cleanTags[] = trim($tag);
        }
        //the leading and trailing line break char is for searching: fvTag like %\ntag\n%
        return "\n".implode("\n", $cleanTags)."\n";
    }

    /**
     * Set the filename.
     *
     * @param string $filename
     */
    public function setFilename($filename)
    {
        $this->fvFilename = $filename;
    }

    /**
     * Path prefix for a file.
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->fvPrefix;
    }

    /**
     * If the current version is approved.
     *
     * @return bool
     */
    public function isApproved()
    {
        return $this->fvIsApproved;
    }

    /**
     * Get the tags as an array of strings.
     *
     * @return string[]
     */
    public function getTagsList()
    {
        $tags = explode("\n", str_replace("\r", "\n", trim($this->getTags())));
        $clean_tags = [];
        foreach ($tags as $tag) {
            if (strlen(trim($tag))) {
                $clean_tags[] = trim($tag);
            }
        }

        return $clean_tags;
    }

    /**
     * Get the tags for a file.
     *
     * @return null|string
     */
    public function getTags()
    {
        return $this->fvTags;
    }

    /**
     * returns the File object associated with this FileVersion object.
     *
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set the current file.
     *
     * @param File $file
     */
    public function setFile(\Concrete\Core\Entity\File\File $file)
    {
        $this->file = $file;
    }

    /**
     * File ID.
     *
     * @return int
     */
    public function getFileID()
    {
        return $this->file->getFileID();
    }

    /**
     * File Version ID.
     *
     * @return int
     */
    public function getFileVersionID()
    {
        return $this->fvID;
    }

    /**
     * Removes a version of a file. Note, does NOT remove the file because we don't know where the file might elsewhere be used/referenced.
     *
     * @param bool $deleteFilesAndThumbnails Whether we should delete all file versions and thumbnails
     */
    public function delete($deleteFilesAndThumbnails = false)
    {
        $db = Database::get();

        $category = \Core::make('Concrete\Core\Attribute\Category\FileCategory');

        foreach ($this->getAttributes() as $attribute) {
            $category->deleteValue($attribute);
        }

        $db->Execute('DELETE FROM FileVersionLog WHERE fID = ? AND fvID = ?', [$this->getFileID(), $this->fvID]);

        $types = Type::getVersionList();

        if ($deleteFilesAndThumbnails) {
            try {
                foreach ($types as $type) {
                    $this->deleteThumbnail($type);
                }

                $fsl = $this->getFile()->getFileStorageLocationObject()->getFileSystemObject();
                $fre = $this->getFileResource();
                if ($fsl->has($fre->getPath())) {
                    $fsl->delete($fre->getPath());
                }
            } catch (FileNotFoundException $e) {
            }
        }

        $em = \ORM::entityManager();
        $em->remove($this);
        $em->flush();
    }

    /**
     * Deletes the thumbnail for the particular thumbnail type.
     *
     * @param string|ThumbnailTypeVersion $type
     */
    public function deleteThumbnail($type)
    {
        if (!($type instanceof ThumbnailTypeVersion)) {
            $type = ThumbnailTypeVersion::getByHandle($type);
        }
        $fsl = $this->getFile()->getFileStorageLocationObject()->getFileSystemObject();
        $path = $type->getFilePath($this);
        if ($fsl->has($path)) {
            $fsl->delete($path);
        }
    }

    /**
     * Move the thumbnails for the current file version to a new storage location.
     *
     * @param string          $type
     * @param StorageLocation $location
     */
    public function updateThumbnailStorageLocation($type, StorageLocation $location)
    {
        if (!($type instanceof ThumbnailTypeVersion)) {
            $type = ThumbnailTypeVersion::getByHandle($type);
        }
        $fsl = $this->getFile()->getFileStorageLocationObject()->getFileSystemObject();
        $path = $type->getFilePath($this);
        $manager = new \League\Flysystem\MountManager([
            'current' => $fsl,
            'new' => $location->getFileSystemObject(),
        ]);
        try {
            $manager->move('current://'.$path, 'new://'.$path);
        } catch (FileNotFoundException $e) {
        }
    }

    /**
     * Returns an abstracted File object for the resource. NOT a concrete5 file object.
     *
     * @return \League\Flysystem\File
     */
    public function getFileResource()
    {
        $cf = Core::make('helper/concrete/file');
        $fs = $this->getFile()->getFileStorageLocationObject()->getFileSystemObject();
        $fo = $fs->get($cf->prefix($this->fvPrefix, $this->fvFilename));

        return $fo;
    }

    /**
     * Get the mime type of the file if known.
     *
     * @return string
     */
    public function getMimeType()
    {
        $fre = $this->getFileResource();

        return $fre->getMimetype();
    }

    /**
     * Get the formatted filesize of a file e.g. 123KB.
     *
     * @return mixed|string
     */
    public function getSize()
    {
        return Core::make('helper/number')->formatSize($this->fvSize, 'KB');
    }

    /**
     * File size of the file.
     *
     * @return int
     */
    public function getFullSize()
    {
        return $this->fvSize;
    }

    /**
     * The author of the file (or Unknown).
     *
     * @return string
     */
    public function getAuthorName()
    {
        $ui = \UserInfo::getByID($this->fvAuthorUID);
        if (is_object($ui)) {
            return $ui->getUserDisplayName();
        }

        return t('(Unknown)');
    }

    /**
     * Return the uID for the author of the file.
     *
     * @return int
     */
    public function getAuthorUserID()
    {
        return $this->fvAuthorUID;
    }

    /**
     * Gets the date a file version was added.
     *
     * @return string date formated like: 2009-01-01 00:00:00
     */
    public function getDateAdded()
    {
        return $this->fvDateAdded;
    }

    /**
     * Get the file extension for a file.
     *
     * @return null|string
     */
    public function getExtension()
    {
        return $this->fvExtension;
    }

    /**
     * Set the ID for the file version.
     *
     * @param int $fvID
     */
    public function setFileVersionID($fvID)
    {
        $this->fvID = $fvID;
    }

    /**
     * Takes the current value of the file version and makes a new one with the same values.
     *
     * @return Version
     */
    public function duplicate()
    {
        $em = \ORM::entityManager();
        $qq = $em->createQuery('SELECT max(v.fvID) FROM \Concrete\Core\Entity\File\Version v where v.file = :file');
        $qq->setParameter('file', $this->file);
        $fvID = $qq->getSingleScalarResult();
        ++$fvID;

        $fv = clone $this;
        $fv->fvID = $fvID;
        $fv->fvIsApproved = false;
        $fv->fvDateAdded = new \DateTime();
        $uID = (int) (new User())->getUserID();
        if ($uID !== 0) {
            $fv->fvAuthorUID = $uID;
        }

        $em->persist($fv);

        $this->deny();

        foreach ($this->getAttributes() as $value) {
            $value = clone $value;
            /*
             * @var $value AttributeValue
             */
            $value->setVersion($fv);
            $em->persist($value);
        }

        $em->flush();

        $fe = new \Concrete\Core\File\Event\FileVersion($fv);
        Events::dispatch('on_file_version_duplicate', $fe);

        return $fv;
    }

    /**
     * Deny a file version update.
     */
    public function deny()
    {
        $this->fvIsApproved = false;
        $this->save();
        $fe = new \Concrete\Core\File\Event\FileVersion($this);
        Events::dispatch('on_file_version_deny', $fe);
    }

    /**
     * Save changes to a file.
     *
     * @param bool $flush Flush the EM cache
     */
    protected function save($flush = true)
    {
        $em = \ORM::entityManager();
        $em->persist($this);
        if ($flush) {
            $em->flush();
        }
    }

    /**
     * Get the file type name.
     *
     * @return string
     */
    public function getType()
    {
        $ftl = $this->getTypeObject();

        return $ftl->getName();
    }

    /**
     * Get the file type display name (localized).
     *
     * @return string
     */
    public function getDisplayType()
    {
        $ftl = $this->getTypeObject();

        return $ftl->getDisplayName();
    }

    /**
     * @return \Concrete\Core\File\Type\Type
     */
    public function getTypeObject()
    {
        $fh = Core::make('helper/file');
        $ext = $fh->getExtension($this->fvFilename);

        $ftl = FileTypeList::getType($ext);

        return $ftl;
    }

    /**
     * Returns an array containing human-readable descriptions of everything that happened in this version.
     *
     * @return string[]
     */
    public function getVersionLogComments()
    {
        $updates = [];
        $db = Database::get();
        $ga = $db->GetAll(
            'SELECT fvUpdateTypeID, fvUpdateTypeAttributeID FROM FileVersionLog WHERE fID = ? AND fvID = ? ORDER BY fvlID ASC',
            [$this->getFileID(), $this->getFileVersionID()]
        );
        foreach ($ga as $a) {
            switch ($a['fvUpdateTypeID']) {
                case self::UT_REPLACE_FILE:
                    $updates[] = t('File');
                    break;
                case self::UT_TITLE:
                    $updates[] = t('Title');
                    break;
                case self::UT_DESCRIPTION:
                    $updates[] = t('Description');
                    break;
                case self::UT_TAGS:
                    $updates[] = t('Tags');
                    break;
                case self::UT_CONTENTS:
                    $updates[] = t('File Content');
                    break;
                case self::UT_RENAME:
                    $updates[] = t('File Name');
                    break;
                case self::UT_EXTENDED_ATTRIBUTE:
                    $val = $db->GetOne(
                        'SELECT akName FROM AttributeKeys WHERE akID = ?',
                        [$a['fvUpdateTypeAttributeID']]
                    );
                    if ($val != '') {
                        $updates[] = $val;
                    }
                    break;
            }
        }
        $updates = array_unique($updates);
        $updates1 = [];
        foreach ($updates as $val) {
            // normalize the keys
            $updates1[] = $val;
        }

        return $updates1;
    }

    /**
     * Update the Title for a file.
     *
     * @param string $title
     */
    public function updateTitle($title)
    {
        $this->fvTitle = $title;
        $this->save();
        $this->logVersionUpdate(self::UT_TITLE);
        $fe = new \Concrete\Core\File\Event\FileVersion($this);
        Events::dispatch('on_file_version_update_title', $fe);
    }

    /**
     * Duplicate a file (adds a new version).
     */
    public function duplicateUnderlyingFile()
    {
        $importer = new Importer();
        $fi = Core::make('helper/file');
        $cf = Core::make('helper/concrete/file');
        $filesystem = $this->getFile()->
            getFileStorageLocationObject()->getFileSystemObject();
        do {
            $prefix = $importer->generatePrefix();
            $path = $cf->prefix($prefix, $this->getFilename());
        } while ($filesystem->has($path));
        $filesystem->write(
            $path,
            $this->getFileResource()->read(),
            [
                'visibility' => AdapterInterface::VISIBILITY_PUBLIC,
                'mimetype' => Core::make('helper/mime')->mimeFromExtension($fi->getExtension($this->getFilename())),
            ]
        );
        $this->updateFile($this->getFilename(), $prefix);
    }

    /**
     * Log updates to files.
     *
     * @param int $updateTypeID          Refers to the constants
     * @param int $updateTypeAttributeID
     */
    public function logVersionUpdate($updateTypeID, $updateTypeAttributeID = 0)
    {
        $db = Database::get();
        $db->Execute(
            'INSERT INTO FileVersionLog (fID, fvID, fvUpdateTypeID, fvUpdateTypeAttributeID) VALUES (?, ?, ?, ?)',
            [
                $this->getFileID(),
                $this->getFileVersionID(),
                $updateTypeID,
                $updateTypeAttributeID,
            ]
        );
    }

    /**
     * Update the tags for a file.
     *
     * @param string $tags
     */
    public function updateTags($tags)
    {
        $tags = self::cleanTags($tags);
        $this->fvTags = $tags;
        $this->save();
        $this->logVersionUpdate(self::UT_TAGS);
        $fe = new \Concrete\Core\File\Event\FileVersion($this);
        Events::dispatch('on_file_version_update_tags', $fe);
    }

    /**
     * Update the description of a file.
     *
     * @param string $descr
     */
    public function updateDescription($descr)
    {
        $this->fvDescription = $descr;
        $this->save();
        $this->logVersionUpdate(self::UT_DESCRIPTION);
        $fe = new \Concrete\Core\File\Event\FileVersion($this);
        Events::dispatch('on_file_version_update_description', $fe);
    }

    /**
     * Rename a file.
     *
     * @param string $filename
     */
    public function rename($filename)
    {
        $cf = Core::make('helper/concrete/file');
        $storage = $this->getFile()->getFileStorageLocationObject();
        $oldFilename = $this->fvFilename;
        if (is_object($storage)) {
            $path = $cf->prefix($this->fvPrefix, $oldFilename);
            $newPath = $cf->prefix($this->fvPrefix, $filename);
            $filesystem = $storage->getFileSystemObject();
            if ($filesystem->has($path)) {
                $filesystem->rename($path, $newPath);
            }
            $this->fvFilename = $filename;
            if ($this->fvTitle == $oldFilename) {
                $this->fvTitle = $filename;
            }
            $this->logVersionUpdate(self::UT_RENAME);
            $this->save();
        }
    }

    /**
     * Update the contents of a file.
     *
     * @param string $contents
     */
    public function updateContents($contents)
    {
        $cf = Core::make('helper/concrete/file');
        $storage = $this->getFile()->getFileStorageLocationObject();
        if (is_object($storage)) {
            $path = $cf->prefix($this->fvPrefix, $this->fvFilename);
            $filesystem = $storage->getFileSystemObject();
            if ($filesystem->has($path)) {
                $filesystem->delete($path);
            }
            $filesystem->write($path, $contents);
            $this->logVersionUpdate(self::UT_CONTENTS);
            $fe = new \Concrete\Core\File\Event\FileVersion($this);
            Events::dispatch('on_file_version_update_contents', $fe);
            $this->refreshAttributes();
        }
    }

    /**
     * Update the filename and prefix of a file.
     *
     * @param string $filename
     * @param string $prefix
     */
    public function updateFile($filename, $prefix)
    {
        $this->fvFilename = $filename;
        $this->fvPrefix = $prefix;
        $this->save();
        $this->logVersionUpdate(self::UT_REPLACE_FILE);
    }

    /**
     * Approve the change to a file version.
     */
    public function approve()
    {
        foreach ($this->file->getFileVersions() as $fv) {
            $fv->fvIsApproved = false;
            $fv->save(false);
        }

        $this->fvIsApproved = true;
        $uID = (int) (new User())->getUserID();
        if ($uID !== 0) {
            $this->fvApproverUID = $uID;
        }
        $this->save();

        $fe = new \Concrete\Core\File\Event\FileVersion($this);
        Events::dispatch('on_file_version_approve', $fe);

        $fo = $this->getFile();
        $fo->reindex();

        \Core::make('cache/request')->delete('file/version/approved/'.$this->getFileID());
    }

    /**
     * Return the contents of a file.
     *
     * @return string
     */
    public function getFileContents()
    {
        $cf = Core::make('helper/concrete/file');
        $fsl = $this->getFile()->getFileStorageLocationObject();
        if (is_object($fsl)) {
            return $fsl->getFileSystemObject()->read($cf->prefix($this->fvPrefix, $this->fvFilename));
        }
    }

    /**
     * Returns a url that can be used to download a file, will force the download of all file types, even if your browser can display them.
     *
     * @return string
     */
    public function getForceDownloadURL()
    {
        $c = Page::getCurrentPage();
        $cID = ($c instanceof Page) ? $c->getCollectionID() : 0;

        return View::url('/download_file', 'force', $this->getFileID(), $cID);
    }

    /**
     * Forces the download of a file.
     */
    public function forceDownload()
    {
        session_write_close();
        $fre = $this->getFileResource();

        $fs = $this->getFile()->getFileStorageLocationObject()->getFileSystemObject();
        $response = new FlysystemFileResponse($fre->getPath(), $fs);

        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        $response->prepare(\Request::getInstance());

        ob_end_clean();
        $response->send();
        \Core::shutdown();
        exit;
    }

    /**
     * Return the filename for a file if it exists.
     *
     * @return null|string
     */
    public function getFileName()
    {
        return $this->fvFilename;
    }

    /**
     * Return the relative path for a file (may not exist).
     *
     * @return string
     */
    public function getRelativePath()
    {
        $cf = Core::make('helper/concrete/file');
        $fsl = $this->getFile()->getFileStorageLocationObject();
        $url = null;
        if (is_object($fsl)) {
            $configuration = $fsl->getConfigurationObject();
            if ($configuration->hasRelativePath()) {
                $url = $configuration->getRelativePathToFile($cf->prefix($this->fvPrefix, $this->fvFilename));
            }
            if ($configuration->hasPublicURL() && !$url) {
                $url = $configuration->getPublicURLToFile($cf->prefix($this->fvPrefix, $this->fvFilename));
            }
            if (!$url) {
                $url =  $this->getDownloadURL();
            }
        }
        return $url;
    }

    /**
     * Get an array of thumbnails.
     *
     * @return Thumbnail[]
     *
     * @throws InvalidDimensionException
     */
    public function getThumbnails()
    {
        $thumbnails = [];
        $types = Type::getVersionList();
        $width = $this->getAttribute('width');
        $height = $this->getAttribute('height');
        $file = $this->getFile();

        if (!$width || $width < 0) {
            throw new InvalidDimensionException($this->getFile(), $this, t('Invalid dimensions.'));
        }

        foreach ($types as $type) {
            if ($width < $type->getWidth()) {
                continue;
            }

            if ($width == $type->getWidth() && (!$type->getHeight() || $height <= $type->getHeight())) {
                continue;
            }

            $thumbnailPath = $type->getFilePath($this);
            $location = $file->getFileStorageLocationObject();
            $configuration = $location->getConfigurationObject();
            $filesystem = $location->getFileSystemObject();
            if ($filesystem->has($thumbnailPath)) {
                $thumbnails[] = new Thumbnail($type, $configuration->getPublicURLToFile($thumbnailPath));
            }
        }

        return $thumbnails;
    }

    /**
     * @return \Concrete\Core\Attribute\Category\FileCategory|mixed
     */
    public function getObjectAttributeCategory()
    {
        return \Core::make('\Concrete\Core\Attribute\Category\FileCategory');
    }

    /**
     * Necessary because getAttribute() returns the Value Value object, and this returns the
     * File Attribute Value object.
     *
     * @param string|FileKey $ak
     * @param bool           $createIfNotExists
     *
     * @return mixed|FileValue
     */
    public function getAttributeValueObject($ak, $createIfNotExists = false)
    {
        if (!is_object($ak)) {
            $ak = FileKey::getByHandle($ak);
        }
        $value = false;
        if (is_object($ak)) {
            $value = $this->getObjectAttributeCategory()->getAttributeValue($ak, $this);
        }

        if ($value) {
            return $value;
        } elseif ($createIfNotExists) {
            if (!is_object($ak)) {
                $ak = FileKey::getByHandle($ak);
            }
            $attributeValue = new FileValue();
            $attributeValue->setVersion($this);
            $attributeValue->setAttributeKey($ak);

            return $attributeValue;
        }
    }

    /**
     * @return bool|Image
     */
    public function getImagineImage()
    {
        if (null === $this->imagineImage) {
            $resource = $this->getFileResource();
            $mimetype = $resource->getMimeType();
            $imageLibrary = \Image::getFacadeRoot();

            switch ($mimetype) {
                case 'image/svg+xml':
                case 'image/svg-xml':
                    if ($imageLibrary instanceof \Imagine\Gd\Imagine) {
                        try {
                            $app = Facade::getFacadeApplication();
                            $imageLibrary = $app->make('image/imagick');
                        } catch (\Exception $x) {
                            $this->imagineImage = false;
                        }
                    }
                    break;
            }

            $metadataReader = $imageLibrary->getMetadataReader();
            if (!$metadataReader instanceof ExifMetadataReader) {
                if (\Config::get('concrete.file_manager.images.use_exif_data_to_rotate_images')) {
                    try {
                        $imageLibrary->setMetadataReader(new ExifMetadataReader());
                    } catch (NotSupportedException $e) {
                    }
                }
            }

            $this->imagineImage = $imageLibrary->load($resource->read());
        }

        return $this->imagineImage;
    }

    /**
     * Unload the loaded image.
     */
    public function releaseImagineImage()
    {
        $this->imagineImage = null;
    }

    /**
     * Rescan the thumbnails for a file (images only).
     *
     * @return bool False on failure
     */
    public function rescanThumbnails()
    {
        if ($this->fvType != \Concrete\Core\File\Type\Type::T_IMAGE) {
            return false;
        }

        $imagewidth = $this->getAttribute('width');
        $imageheight = $this->getAttribute('height');
        $types = Type::getVersionList();

        try {
            $image = $this->getImagineImage();

            if ($image) {
                /* @var \Imagine\Imagick\Image $image */
                if (!$imagewidth) {
                    $imagewidth = $image->getSize()->getWidth();
                }
                if (!$imageheight) {
                    $imageheight = $image->getSize()->getHeight();
                }

                foreach ($types as $type) {
                    // delete the file if it exists
                    $this->deleteThumbnail($type);
                    
                    // if image is smaller than size requested, don't create thumbnail
                    if ($imagewidth < $type->getWidth() && $imageheight < $type->getHeight()) {
                        continue;
                    }

                    // This should not happen as it is not allowed when creating thumbnail types and both width and heght
                    // are required for Exact sizing but it's here just in case
                    if ($type->getSizingMode() === Type::RESIZE_EXACT && (!$type->getWidth() || !$type->getHeight())) {
                        continue;
                    }
                    
                    // If requesting an exact size and any of the dimensions requested is larger than the image's
                    // don't process as we won't get an exact size
                    if ($type->getSizingMode() === Type::RESIZE_EXACT && ($imagewidth < $type->getWidth() || $imageheight < $type->getHeight())) {
                        continue;
                    }

                    // if image is the same width as thumbnail, and there's no thumbnail height set,
                    // or if a thumbnail height set and the image has a smaller or equal height, don't create thumbnail
                    if ($imagewidth == $type->getWidth() && (!$type->getHeight() || $imageheight <= $type->getHeight())) {
                        continue;
                    }

                    // if image is the same height as thumbnail, and there's no thumbnail width set,
                    // or if a thumbnail width set and the image has a smaller or equal width, don't create thumbnail
                    if ($imageheight == $type->getHeight() && (!$type->getWidth() || $imagewidth <= $type->getWidth())) {
                        continue;
                    }

                    // otherwise file is bigger than thumbnail in some way, proceed to create thumbnail
                    $this->generateThumbnail($type);
                }
            }
            unset($image);
            $this->releaseImagineImage();
        } catch (\Imagine\Exception\InvalidArgumentException $e) {
            unset($image);
            $this->releaseImagineImage();
            return false;
        } catch (\Imagine\Exception\RuntimeException $e) {
            unset($image);
            $this->releaseImagineImage();
            return false;
        }
    }

    /**
     * @deprecated
     *
     * @param $level
     *
     * @return mixed
     */
    public function hasThumbnail($level)
    {
        switch ($level) {
            case 1:
                return $this->fvHasListingThumbnail;
            case 2:
                return $this->fvHasDetailThumbnail;
        }

        return false;
    }

    /**
     *  Return the thumbnail for an image or a generic type icon for a file.
     *
     * @return string
     */
    public function getDetailThumbnailImage()
    {
        if ($this->fvHasDetailThumbnail) {
            $type = Type::getByHandle(\Config::get('concrete.icons.file_manager_detail.handle'));
            $baseSrc = $this->getThumbnailURL($type->getBaseVersion());
            $doubledSrc = $this->getThumbnailURL($type->getDoubledVersion());

            return '<img src="'.$baseSrc.'" data-at2x="'.$doubledSrc.'" />';
        } else {
            return $this->getTypeObject()->getThumbnail();
        }
    }

    /**
     * Resolve a path using the default core path resolver.
     * Avoid using this method when you have access to your a resolver instance.
     *
     * @param string|ThumbnailTypeVersion $type
     *
     * @return null|string
     */
    public function getThumbnailURL($type)
    {
        $app = Application::getFacadeApplication();

        if (!($type instanceof ThumbnailTypeVersion)) {
            $type = ThumbnailTypeVersion::getByHandle($type);
        }

        /** @var Resolver $path_resolver */
        $path_resolver = $app->make('Concrete\Core\File\Image\Thumbnail\Path\Resolver');

        if ($path = $path_resolver->getPath($this, $type)) {
            return $path;
        }

        return $this->getURL();
    }

    /**
     * When given a thumbnail type versin object and a full path to a file on the server
     * the file is imported into the system as is as the thumbnail.
     *
     * @param ThumbnailTypeVersion $version
     * @param string               $path
     */
    public function importThumbnail(ThumbnailTypeVersion $version, $path)
    {
        $thumbnailPath = $version->getFilePath($this);
        $filesystem = $this->getFile()
            ->getFileStorageLocationObject()
            ->getFileSystemObject();
        if ($filesystem->has($thumbnailPath)) {
            $filesystem->delete($thumbnailPath);
        }

        $filesystem->write(
            $thumbnailPath,
            file_get_contents($path),
            [
                'visibility' => AdapterInterface::VISIBILITY_PUBLIC,
                'mimetype' => 'image/jpeg',
            ]
        );

        if ($version->getHandle() == \Config::get('concrete.icons.file_manager_listing.handle')) {
            $this->fvHasListingThumbnail = true;
        }

        if ($version->getHandle() == \Config::get('concrete.icons.file_manager_detail.handle')) {
            $this->fvHasDetailThumbnail = true;
        }

        $this->save();
    }

    /**
     * Returns a full URL to the file on disk.
     *
     * @return null|string Url to a file
     */
    public function getURL()
    {
        $cf = Core::make('helper/concrete/file');
        $fsl = $this->getFile()->getFileStorageLocationObject();
        if (is_object($fsl)) {
            $configuration = $fsl->getConfigurationObject();
            if ($configuration->hasPublicURL()) {
                return $configuration->getPublicURLToFile($cf->prefix($this->fvPrefix, $this->fvFilename));
            } else {
                return $this->getDownloadURL();
            }
        }
    }

    /**
     * Returns a URL that can be used to download the file. This passes through the download_file single page.
     *
     * @return string download_file url for a file
     */
    public function getDownloadURL()
    {
        $c = Page::getCurrentPage();
        $cID = ($c instanceof Page) ? $c->getCollectionID() : 0;

        return View::url('/download_file', $this->getFileID(), $cID);
    }

    /**
     * @return mixed
     */
    public function getAttributes()
    {
        return $this->getObjectAttributeCategory()->getAttributeValues($this);
    }

    /**
     * Responsible for taking a particular version of a file and rescanning all its attributes
     * This will run any type-based import routines, and store those attributes, generate thumbnails,
     * etc...
     *
     * @param bool $rescanThumbnails Whether or not we should rescan thumbnails as well
     *
     * @return null|int
     */
    public function refreshAttributes($rescanThumbnails = true)
    {
        $fh = Core::make('helper/file');
        $ext = $fh->getExtension($this->fvFilename);
        $ftl = FileTypeList::getType($ext);

        if (is_object($ftl)) {
            if ($ftl->getCustomImporter() != false) {
                $this->fvGenericType = $ftl->getGenericType();
                $cl = $ftl->getCustomInspector();
                $cl->inspect($this);
            }
        }

        \ORM::entityManager()->refresh($this);

        $fsr = $this->getFileResource();
        if (!$fsr->isFile()) {
            return Importer::E_FILE_INVALID;
        }

        $size = $fsr->getSize();

        $this->fvExtension = $ext;
        $this->fvType = $ftl->getGenericType();
        if ($this->fvTitle === null) {
            $this->fvTitle = $this->getFilename();
        }
        $this->fvSize = $size;

        if ($rescanThumbnails) {
            $this->rescanThumbnails();
        }

        $this->save();

        $f = $this->getFile();
        $f->reindex();
    }

    /**
     * Title for the file if one exists.
     *
     * @return null|string
     */
    public function getTitle()
    {
        return $this->fvTitle;
    }

    /**
     * Return a representation of the current FileVersion object as something easily serializable.
     *
     * @return string A JSON object with all the information about a file (including permissions)
     */
    public function getJSONObject()
    {
        $r = new stdClass();
        $fp = new Permissions($this->getFile());
        $r->canCopyFile = $fp->canCopyFile();
        $r->canEditFileProperties = $fp->canEditFileProperties();
        $r->canEditFilePermissions = $fp->canEditFilePermissions();
        $r->canDeleteFile = $fp->canDeleteFile();
        $r->canReplaceFile = $fp->canEditFileContents();
        $r->canEditFileContents = $fp->canEditFileContents();
        $r->canViewFileInFileManager = $fp->canRead();
        $r->canRead = $fp->canRead();
        $r->canViewFile = $this->canView();
        $r->canEditFile = $this->canEdit();
        $r->url = $this->getURL();
        $r->urlInline = (string) View::url('/download_file', 'view_inline', $this->getFileID());
        $r->urlDownload = (string) View::url('/download_file', 'view', $this->getFileID());
        $r->title = $this->getTitle();
        $r->genericTypeText = $this->getGenericTypeText();
        $r->description = $this->getDescription();
        $r->fileName = $this->getFilename();
        $r->resultsThumbnailImg = $this->getListingThumbnailImage();
        $r->fID = $this->getFileID();
        $r->treeNodeMenu = new Menu($this->getfile());

        return $r;
    }

    /**
     * Checks current viewers for this type and returns true if there is a viewer for this type, false if not.
     *
     * @return bool
     */
    public function canView()
    {
        $to = $this->getTypeObject();
        if ($to->getView() != '') {
            return true;
        }

        return false;
    }

    /**
     * Checks current viewers for this type and returns true if there is a viewer for this type, false if not.
     *
     * @return bool
     */
    public function canEdit()
    {
        $to = $this->getTypeObject();
        if ($to->getEditor() != '') {
            return true;
        }

        return false;
    }

    /**
     * Get the localized name of the generic category type.
     *
     * @return string
     */
    public function getGenericTypeText()
    {
        $to = $this->getTypeObject();

        return $to->getGenericDisplayType();
    }

    /**
     * Returns the description for the file if there is one.
     *
     * @return null|string
     */
    public function getDescription()
    {
        return $this->fvDescription;
    }

    /**
     * Return the thumbnail for an image or a generic type icon for a file.
     *
     * @return string
     */
    public function getListingThumbnailImage()
    {
        if ($this->fvHasListingThumbnail) {
            $type = Type::getByHandle(\Config::get('concrete.icons.file_manager_listing.handle'));
            $baseSrc = $this->getThumbnailURL($type->getBaseVersion());
            $doubledSrc = $this->getThumbnailURL($type->getDoubledVersion());

            return sprintf('<img class="ccm-file-manager-list-thumbnail" src="%s" data-at2x="%s">', $baseSrc, $doubledSrc);
        } else {
            return $this->getTypeObject()->getThumbnail();
        }
    }

    /**
     * Generate a thumbnail given a type
     * @param \Concrete\Core\File\Image\Thumbnail\Type\Version $type
     */
    public function generateThumbnail(ThumbnailTypeVersion $type)
    {
        $image = $this->getImagineImage();
        $mimetype = $this->getMimetype();

        $filesystem = $this->getFile()
            ->getFileStorageLocationObject()
            ->getFileSystemObject();
            
        $height = $type->getHeight();
        $width = $type->getWidth();
        $sizingMode = $type->getSizingMode();

        if ($height && $width) {
            $size = new Box($width, $height);
        } else if ($width) {
            $size = $image->getSize()->widen($width);
        } else {
            $size = $image->getSize()->heighten($height);
        }

        if ($sizingMode === Type::RESIZE_EXACT) {
             $thumbnailMode = ImageInterface::THUMBNAIL_OUTBOUND;
        } else if ($sizingMode === Type::RESIZE_PROPORTIONAL) {
            $thumbnailMode = ImageInterface::THUMBNAIL_INSET;
        }

        // isCropped only exists on the CustomThumbnail type
        if (method_exists($type, 'isCropped') && $type->isCropped()) {
            $thumbnailMode = ImageInterface::THUMBNAIL_OUTBOUND;
        }

        $thumbnail = $image->thumbnail($size, $thumbnailMode);
        $thumbnailPath = $type->getFilePath($this);
        $thumbnailOptions = [];

        switch ($mimetype) {
            case 'image/jpeg':
                $thumbnailType = 'jpeg';
                $thumbnailOptions = ['jpeg_quality' => \Config::get('concrete.misc.default_jpeg_image_compression')];
                break;
            case 'image/png':
                $thumbnailType = 'png';
                break;
            case 'image/gif':
                $thumbnailType = 'gif';
                break;
            case 'image/xbm':
                $thumbnailType = 'xbm';
                break;
            case 'image/vnd.wap.wbmp':
                $thumbnailType = 'wbmp';
                break;
            default:
                $thumbnailType = 'png';
                break;
        }

        $filesystem->write(
            $thumbnailPath,
            $thumbnail->get($thumbnailType, $thumbnailOptions),
            [
                'visibility' => AdapterInterface::VISIBILITY_PUBLIC,
                'mimetype' => $mimetype,
            ]
        );

        if ($type->getHandle() == \Config::get('concrete.icons.file_manager_listing.handle')) {
            $this->fvHasListingThumbnail = true;
        }

        if ($type->getHandle() == \Config::get('concrete.icons.file_manager_detail.handle')) {
            $this->fvHasDetailThumbnail = true;
        }

        unset($size);
        unset($thumbnail);
        unset($filesystem);
    }
}
