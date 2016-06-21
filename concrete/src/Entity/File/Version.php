<?php
namespace Concrete\Core\Entity\File;

use Carbon\Carbon;
use Concrete\Core\Attribute\Key\FileKey;
use Concrete\Core\Attribute\ObjectTrait;
use Concrete\Core\Entity\Attribute\Value\FileValue;
use Concrete\Core\Entity\Attribute\Value\Value\Value;
use Concrete\Core\File\Exception\InvalidDimensionException;
use Concrete\Core\File\Image\Thumbnail\Path\Resolver;
use Concrete\Core\File\Image\Thumbnail\Thumbnail;
use Concrete\Core\File\Image\Thumbnail\Type\Type;
use Concrete\Core\File\Image\Thumbnail\Type\Version as ThumbnailTypeVersion;
use Concrete\Core\File\Menu;
use Concrete\Core\File\Type\TypeList as FileTypeList;
use Concrete\Core\Http\FlysystemFileResponse;
use Doctrine\Common\Collections\ArrayCollection;
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
class Version
{
    use ObjectTrait {
        setAttribute as setFileVersionAttribute;
    }

    public function setAttribute($ak, $value)
    {
        $value = $this->setFileVersionAttribute($ak, $value);
        if (is_object($value)) {
            $this->attributes->add($value);
        }
    }

    const UT_REPLACE_FILE = 1;
    const UT_TITLE = 2;
    const UT_DESCRIPTION = 3;
    const UT_TAGS = 4;
    const UT_EXTENDED_ATTRIBUTE = 5;
    const UT_CONTENTS = 6;
    const UT_RENAME = 7;

    public function __construct()
    {
        $this->attributes = new ArrayCollection();
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
     * @ORM\OneToMany(targetEntity="\Concrete\Core\Entity\Attribute\Value\FileValue",  mappedBy="version")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fID", referencedColumnName="fID"),
     *   @ORM\JoinColumn(name="fvID", referencedColumnName="fvID")
     * })
     */
    protected $attributes;

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

        $db = Database::get();
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

    public static function cleanTags($tagsStr)
    {
        $tagsArray = explode("\n", str_replace(["\r", ","], "\n", $tagsStr));
        $cleanTags = [];
        foreach ($tagsArray as $tag) {
            if (!strlen(trim($tag))) {
                continue;
            }
            $cleanTags[] = trim($tag);
        }
        //the leading and trailing line break char is for searching: fvTag like %\ntag\n%
        return "\n" . implode("\n", $cleanTags) . "\n";
    }

    public function setFilename($filename)
    {
        $this->fvFilename = $filename;
    }

    public function getPrefix()
    {
        return $this->fvPrefix;
    }

    public function isApproved()
    {
        return $this->fvIsApproved;
    }

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

    public function getTags()
    {
        return $this->fvTags;
    }

    /**
     * returns the File object associated with this FileVersion object.
     *
     * @return \File
     */
    public function getFile()
    {
        return $this->file;
    }

    public function setFile(\Concrete\Core\Entity\File\File $file)
    {
        $this->file = $file;
    }

    public function getFileID()
    {
        return $this->file->getFileID();
    }

    //returns an array of tags, instead of a string

    public function getFileVersionID()
    {
        return $this->fvID;
    }

    /**
     * Removes a version of a file. Note, does NOT remove the file because we don't know where the file might elsewhere be used/referenced.
     */
    public function delete($deleteFilesAndThumbnails = false)
    {
        $db = Database::get();
        $em = $db->getEntityManager();

        $values = [];

        $category = \Core::make('Concrete\Core\Attribute\Category\FileCategory');

        foreach ($this->attributes as $attribute) {
            $category->deleteValue($attribute);
        }

        $db->Execute("DELETE FROM FileVersionLog WHERE fID = ? AND fvID = ?", [$this->getFileID(), $this->fvID]);

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

    public function getMimeType()
    {
        $fre = $this->getFileResource();

        return $fre->getMimetype();
    }

    public function getSize()
    {
        return Core::make('helper/number')->formatSize($this->fvSize, 'KB');
    }

    public function getFullSize()
    {
        return $this->fvSize;
    }

    public function getAuthorName()
    {
        $ui = \UserInfo::getByID($this->fvAuthorUID);
        if (is_object($ui)) {
            return $ui->getUserDisplayName();
        }

        return t('(Unknown)');
    }

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

    public function getExtension()
    {
        return $this->fvExtension;
    }

    public function setFileVersionID($fvID)
    {
        $this->fvID = $fvID;
    }

    /**
     * Takes the current value of the file version and makes a new one with the same values.
     */
    public function duplicate()
    {
        $db = Database::get();
        $em = \ORM::entityManager();
        $qq = $em->createQuery('SELECT max(v.fvID) FROM \Concrete\Core\Entity\File\Version v where v.file = :file');
        $qq->setParameter('file', $this->file);
        $fvID = $qq->getSingleScalarResult();
        ++$fvID;

        $fv = clone $this;
        $fv->fvID = $fvID;
        $fv->fvIsApproved = false;
        $fv->fvDateAdded = new \DateTime();

        $em->persist($fv);

        $this->deny();

        foreach ($this->attributes as $value) {
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

    public function deny()
    {
        $this->fvIsApproved = false;
        $this->save();
        $fe = new \Concrete\Core\File\Event\FileVersion($this);
        Events::dispatch('on_file_version_deny', $fe);
    }

    protected function save($flush = true)
    {
        $em = \ORM::entityManager();
        $em->persist($this);
        if ($flush) {
            $em->flush();
        }
    }

    public function getType()
    {
        $ftl = $this->getTypeObject();
        if (is_object($ftl)) {
            return $ftl->getName();
        }
    }

    public function getTypeObject()
    {
        $fh = Core::make('helper/file');
        $ext = $fh->getExtension($this->fvFilename);

        $ftl = FileTypeList::getType($ext);

        return $ftl;
    }

    /**
     * Returns an array containing human-readable descriptions of everything that happened in this version.
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
                        "SELECT akName FROM AttributeKeys WHERE akID = ?",
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

    public function updateTitle($title)
    {
        $this->fvTitle = $title;
        $this->save();
        $this->logVersionUpdate(self::UT_TITLE);
        $fe = new \Concrete\Core\File\Event\FileVersion($this);
        Events::dispatch('on_file_version_update_title', $fe);
    }

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

    public function updateTags($tags)
    {
        $tags = self::cleanTags($tags);
        $this->fvTags = $tags;
        $this->save();
        $this->logVersionUpdate(self::UT_TAGS);
        $fe = new \Concrete\Core\File\Event\FileVersion($this);
        Events::dispatch('on_file_version_update_tags', $fe);
    }

    public function updateDescription($descr)
    {
        $this->fvDescription = $descr;
        $this->save();
        $this->logVersionUpdate(self::UT_DESCRIPTION);
        $fe = new \Concrete\Core\File\Event\FileVersion($this);
        Events::dispatch('on_file_version_update_description', $fe);
    }

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

    public function updateFile($filename, $prefix)
    {
        $this->fvFilename = $filename;
        $this->fvPrefix = $prefix;
        $this->save();
        $this->logVersionUpdate(self::UT_REPLACE_FILE);
    }

    public function approve()
    {
        foreach ($this->file->getFileVersions() as $fv) {
            $fv->fvIsApproved = false;
            $fv->save(false);
        }

        $this->fvIsApproved = true;
        $this->save();

        $fe = new \Concrete\Core\File\Event\FileVersion($this);
        Events::dispatch('on_file_version_approve', $fe);

        $fo = $this->getFile();
        $fo->reindex();

        \Core::make('cache/request')->delete('file/version/approved/' . $this->getFileID());
    }

    /**
     * Return the contents of a file.
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

    public function getFileName()
    {
        return $this->fvFilename;
    }

    public function getRelativePath()
    {
        $cf = Core::make('helper/concrete/file');
        $fsl = $this->getFile()->getFileStorageLocationObject();
        if (is_object($fsl)) {
            $configuration = $fsl->getConfigurationObject();
            if ($configuration->hasRelativePath()) {
                return $configuration->getRelativePathToFile($cf->prefix($this->fvPrefix, $this->fvFilename));
            }
        }
    }

    public function getThumbnails()
    {
        $thumbnails = [];
        $types = Type::getVersionList();
        $width = $this->getAttribute('width');
        $file = $this->getFile();

        if (!$width || $width < 0) {
            throw new InvalidDimensionException($this->getFile(), $this, t('Invalid dimensions.'));
        }

        foreach ($types as $type) {
            if ($width <= $type->getWidth()) {
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

    public function getObjectAttributeCategory()
    {
        return \Core::make('\Concrete\Core\Attribute\Category\FileCategory');
    }

    /**
     * Necessary because getAttribute() returns the Value Value object, and this returns the
     * File Attribute Value object.
     *
     * @param $ak
     * @param $createIfNotExists bool
     *
     * @return mixed
     */
    public function getAttributeValueObject($ak, $createIfNotExists = false)
    {
        $handle = $ak;
        if (is_object($ak)) {
            $handle = $ak->getAttributeKeyHandle();
        }
        foreach ($this->attributes as $value) {
            if ($value->getAttributeKey()->getAttributeKeyHandle() == $handle) {
                return $value;
            }
        }

        if ($createIfNotExists) {
            if (!is_object($ak)) {
                $ak = FileKey::getByHandle($ak);
            }

            $attributeValue = new FileValue();
            $attributeValue->setVersion($this);
            $attributeValue->setAttributeKey($ak);

            return $attributeValue;
        }
    }

    public function rescanThumbnails()
    {
        if ($this->fvType != \Concrete\Core\File\Type\Type::T_IMAGE) {
            return false;
        }

        $app = Facade::getFacadeApplication();
        $width = $this->getAttribute('width');
        $types = Type::getVersionList();

        $fr = $this->getFileResource();
        try {
            $mimetype = $fr->getMimeType();
            $imageLibrary = \Image::getFacadeRoot();
            switch ($mimetype) {
                case 'image/svg+xml':
                case 'image/svg-xml':
                    if ($imageLibrary instanceof \Imagine\Gd\Imagine) {
                        try {
                            $imageLibrary = $app->make('image/imagick');
                        } catch (\Exception $x) {
                            return false;
                        }
                    }
                    break;
            }
            $image = $imageLibrary->load($fr->read());
            /* @var \Imagine\Imagick\Image $image */
            if (!$width) {
                $width = $image->getSize()->getWidth();
            }
            foreach ($types as $type) {

                // delete the file if it exists
                $this->deleteThumbnail($type);

                if ($width <= $type->getWidth()) {
                    continue;
                }

                $filesystem = $this->getFile()
                    ->getFileStorageLocationObject()
                    ->getFileSystemObject();

                $height = $type->getHeight();
                if ($height) {
                    $size = new Box($type->getWidth(), $height);
                    $thumbnailMode = ImageInterface::THUMBNAIL_OUTBOUND;
                } else {
                    $size = $image->getSize()->widen($type->getWidth());
                    $thumbnailMode = ImageInterface::THUMBNAIL_INSET;
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
        } catch (\Imagine\Exception\InvalidArgumentException $e) {
            return false;
        } catch (\Imagine\Exception\RuntimeException $e) {
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

    public function getDetailThumbnailImage()
    {
        if ($this->fvHasDetailThumbnail) {
            $type = Type::getByHandle(\Config::get('concrete.icons.file_manager_detail.handle'));
            $baseSrc = $this->getThumbnailURL($type->getBaseVersion());
            $doubledSrc = $this->getThumbnailURL($type->getDoubledVersion());

            return '<img src="' . $baseSrc . '" data-at2x="' . $doubledSrc . '" />';
        } else {
            return $this->getTypeObject()->getThumbnail();
        }
    }

    /**
     * Resolve a path using the default core path resolver.
     * Avoid using this method when you have access to your a resolver instance.
     *
     * @param $type
     *
     * @return null|string
     */
    public function getThumbnailURL($type)
    {
        if (!($type instanceof ThumbnailTypeVersion)) {
            $type = ThumbnailTypeVersion::getByHandle($type);
        }

        /** @var Resolver $path_resolver */
        $path_resolver = Core::make('Concrete\Core\File\Image\Thumbnail\Path\Resolver');

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
     * @param $path
     */
    public function importThumbnail(\Concrete\Core\File\Image\Thumbnail\Type\Version $version, $path)
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
        return $this->attributes;
    }

    /**
     * Responsible for taking a particular version of a file and rescanning all its attributes
     * This will run any type-based import routines, and store those attributes, generate thumbnails,
     * etc...
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

    public function getTitle()
    {
        return $this->fvTitle;
    }

    /**
     * Return a representation of the current FileVersion object as something easily serializable.
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
     */
    public function canView()
    {
        $to = $this->getTypeObject();
        if (is_object($to) && $to->getView() != '') {
            return true;
        }

        return false;
    }

    public function canEdit()
    {
        $to = $this->getTypeObject();
        if (is_object($to) && $to->getEditor() != '') {
            return true;
        }

        return false;
    }

    public function getGenericTypeText()
    {
        $to = $this->getTypeObject();

        return $to->getGenericTypeText($to->getGenericType());
    }

    //takes a string of comma or new line delimited tags, and puts them in the appropriate format

    public function getDescription()
    {
        return $this->fvDescription;
    }

    public function getListingThumbnailImage()
    {
        if ($this->fvHasListingThumbnail) {
            $type = Type::getByHandle(\Config::get('concrete.icons.file_manager_listing.handle'));
            $baseSrc = $this->getThumbnailURL($type->getBaseVersion());
            $doubledSrc = $this->getThumbnailURL($type->getDoubledVersion());
            $width = $type->getWidth();
            $height = $type->getHeight();

            return sprintf('<img class="ccm-file-manager-list-thumbnail" src="%s" data-at2x="%s">', $baseSrc, $doubledSrc);
        } else {
            return $this->getTypeObject()->getThumbnail();
        }
    }
}
