<?php
namespace Concrete\Core\File;

use Carbon\Carbon;
use Concrete\Core\Attribute\Value\FileValue as FileAttributeValue;
use Concrete\Core\File\Exception\InvalidDimensionException;
use Concrete\Core\File\Image\Thumbnail\Path\Resolver;
use Concrete\Core\File\Image\Thumbnail\Thumbnail;
use Concrete\Core\File\Image\Thumbnail\Type\Type;
use Concrete\Core\File\Image\Thumbnail\Type\Version as ThumbnailTypeVersion;
use Concrete\Core\File\Type\TypeList as FileTypeList;
use Concrete\Core\Http\FlysystemFileResponse;
use Concrete\Flysystem\AdapterInterface;
use Concrete\Flysystem\FileNotFoundException;
use Core;
use Database;
use Events;
use FileAttributeKey;
use Imagine\Exception\InvalidArgumentException as ImagineInvalidArgumentException;
use Imagine\Image\ImageInterface;
use Page;
use Permissions;
use stdClass;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use User;
use View;

/**
 * @Entity
 * @Table(name="FileVersions")
 */
class Version
{
    const UT_REPLACE_FILE = 1;
    const UT_TITLE = 2;
    const UT_DESCRIPTION = 3;
    const UT_TAGS = 4;
    const UT_EXTENDED_ATTRIBUTE = 5;
    const UT_CONTENTS = 6;
    const UT_RENAME = 7;

    /**
     * /* @Id
     * @ManyToOne(targetEntity="File", inversedBy="versions")
     * @JoinColumn(name="fID", referencedColumnName="fID")
     * @var \Concrete\Core\File\File
     */
    protected $file;
    /** @Id
     * @Column(type="integer")
     */
    protected $fvID = 0;
    /**
     * @Column(type="string")
     */
    protected $fvFilename = null;
    /**
     * @Column(type="string")
     */
    protected $fvPrefix;
    /**
     * @Column(type="datetime")
     */
    protected $fvDateAdded;
    /**
     * @Column(type="datetime")
     */
    protected $fvActivateDateTime;
    /**
     * @Column(type="boolean")
     */
    protected $fvIsApproved = false;
    /**
     * @Column(type="integer")
     */
    protected $fvAuthorUID = 0;
    /**
     * @Column(type="bigint")
     */
    protected $fvSize = 0;
    /**
     * @Column(type="integer")
     */
    protected $fvApproverUID = 0;
    /**
     * @Column(type="string")
     */
    protected $fvTitle = null;
    /**
     * @Column(type="text")
     */
    protected $fvDescription = null;
    /**
     * @Column(type="string")
     */
    protected $fvExtension = null;
    /**
     * @Column(type="integer")
     */
    protected $fvType = 0;
    /**
     * @Column(type="text")
     */
    protected $fvTags = null;
    /**
     * @Column(type="boolean")
     */
    protected $fvHasListingThumbnail = false;
    /**
     * @Column(type="boolean")
     */
    protected $fvHasDetailThumbnail = false;

    // Update type constants
    protected $attributes = array();

    public static function add(\Concrete\Core\File\File $file, $filename, $prefix, $data = array())
    {
        $u = new User();
        $uID = (isset($data['uID']) && $data['uID'] > 0) ? $data['uID'] : $u->getUserID();

        if ($uID < 1) {
            $uID = 0;
        }

        $fvTitle = (isset($data['fvTitle'])) ? $data['fvTitle'] : '';
        $fvDescription = (isset($data['fvDescription'])) ? $data['fvDescription'] : '';
        $fvTags = (isset($data['fvTags'])) ? Version::cleanTags($data['fvTags']) : '';
        $fvIsApproved = (isset($data['fvIsApproved'])) ? $data['fvIsApproved'] : '1';

        $db = Database::get();
        $dh = Core::make('helper/date');
        $date = new Carbon($dh->getOverridableNow());

        $fv = new static();
        $fv->fvFilename = $filename;
        $fv->fvPrefix = $prefix;
        $fv->fvDateAdded = $date;
        $fv->fvIsApproved = (bool)$fvIsApproved;
        $fv->fvApproverUID = $uID;
        $fv->fvAuthorUID = $uID;
        $fv->fvActivateDateTime = $date;
        $fv->fvTitle = $fvTitle;
        $fv->fvDescription = $fvDescription;
        $fv->fvTags = $fvTags;
        $fv->file = $file;
        $fv->fvID = 1;

        $em = \ORM::entityManager('core');
        $em->persist($fv);
        $em->flush();

        $fve = new \Concrete\Core\File\Event\FileVersion($fv);
        Events::dispatch('on_file_version_add', $fve);

        return $fv;
    }

    public static function cleanTags($tagsStr)
    {
        $tagsArray = explode("\n", str_replace(array("\r", ","), "\n", $tagsStr));
        $cleanTags = array();
        foreach ($tagsArray as $tag) {
            if (!strlen(trim($tag))) {
                continue;
            }
            $cleanTags[] = trim($tag);
        }
        //the leading and trailing line break char is for searching: fvTag like %\ntag\n%
        return "\n" . join("\n", $cleanTags) . "\n";
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
        $clean_tags = array();
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

    public function setAttribute($ak, $value)
    {
        if (!is_object($ak)) {
            $ak = FileAttributeKey::getByHandle($ak);
        }
        $ak->setAttribute($this, $value);
        $fo = $this->getFile();
        $fo->reindex();
        unset($ak);
    }

    /**
     * returns the File object associated with this FileVersion object
     *
     * @return \File
     */
    public function getFile()
    {
        return $this->file;
    }

    public function setFile(\Concrete\Core\File\File $file)
    {
        $this->file = $file;
    }

    public function clearAttribute($ak)
    {
        $db = Database::get();
        $cav = $this->getAttributeValueObject($ak);
        if (is_object($cav)) {
            $cav->delete();
        }
        $fo = $this->getFile();
        $fo->reindex();
    }

    public function getAttributeValueObject($ak, $createIfNotFound = false)
    {
        $db = Database::get();
        $av = false;
        $v = array($this->getFileID(), $this->getFileVersionID(), $ak->getAttributeKeyID());
        $avID = $db->GetOne("SELECT avID FROM FileAttributeValues WHERE fID = ? AND fvID = ? AND akID = ?", $v);
        if ($avID > 0) {
            $av = FileAttributeValue::getByID($avID);
            if (is_object($av)) {
                $av->setFile($this->getFile());
                $av->setAttributeKey($ak);
            }
        }

        if ($createIfNotFound) {
            $cnt = 0;

            // Is this avID in use ?
            if (is_object($av)) {
                $cnt = $db->GetOne(
                    "SELECT count(avID) FROM FileAttributeValues WHERE avID = ?",
                    $av->getAttributeValueID()
                );
            }

            if ((!is_object($av)) || ($cnt > 1)) {
                $newAV = $ak->addAttributeValue();
                $av = FileAttributeValue::getByID($newAV->getAttributeValueID());
                $av->setFile($this->getFile());
            }
        }

        return $av;
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

        $db->Execute("DELETE FROM FileAttributeValues WHERE fID = ? AND fvID = ?", array($this->getFileID(), $this->fvID));
        $db->Execute("DELETE FROM FileVersionLog WHERE fID = ? AND fvID = ?", array($this->getFileID(), $this->fvID));

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

        $em = \ORM::entityManager('core');
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
     * @return \Concrete\Flysystem\File
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
     * Gets the date a file version was added
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

    /**
     * Takes the current value of the file version and makes a new one with the same values
     */
    public function duplicate()
    {
        $db = Database::get();
        $em = \ORM::entityManager('core');
        $qq = $em->createQuery('SELECT max(v.fvID) FROM \Concrete\Core\File\Version v where v.file = :file');
        $qq->setParameter('file', $this->file);
        $fvID = $qq->getSingleScalarResult();
        $fvID++;

        $fv = clone $this;
        $fv->fvID = $fvID;
        $fv->fvIsApproved = false;
        $fv->fvDateAdded = new \DateTime();

        $em->persist($fv);
        $em->flush();

        $this->deny();

        $r = $db->Execute(
            'SELECT fvID, akID, avID FROM FileAttributeValues WHERE fID = ? AND fvID = ?',
            array($this->getFileID(), $this->fvID)
        );
        while ($row = $r->fetchRow()) {
            $db->Execute(
                "INSERT INTO FileAttributeValues (fID, fvID, akID, avID) VALUES (?, ?, ?, ?)",
                array(
                    $this->getFileID(),
                    $fvID,
                    $row['akID'],
                    $row['avID']
                )
            );
        }

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
        $em = \ORM::entityManager('core');
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
     * Returns an array containing human-readable descriptions of everything that happened in this version
     */
    public function getVersionLogComments()
    {
        $updates = array();
        $db = Database::get();
        $ga = $db->GetAll(
            'SELECT fvUpdateTypeID, fvUpdateTypeAttributeID FROM FileVersionLog WHERE fID = ? AND fvID = ? ORDER BY fvlID ASC',
            array($this->getFileID(), $this->getFileVersionID())
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
                        array($a['fvUpdateTypeAttributeID'])
                    );
                    if ($val != '') {
                        $updates[] = $val;
                    }
                    break;
            }
        }
        $updates = array_unique($updates);
        $updates1 = array();
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
        } while($filesystem->has($path));
        $filesystem->write($path, $this->getFileResource()->read(), array(
            'visibility' => AdapterInterface::VISIBILITY_PUBLIC,
            'mimetype' => Core::make('helper/mime')->mimeFromExtension($fi->getExtension($this->getFilename()))
        ));
        $this->updateFile($this->getFilename(), $prefix);
    }

    public function logVersionUpdate($updateTypeID, $updateTypeAttributeID = 0)
    {
        $db = Database::get();
        $db->Execute(
            'INSERT INTO FileVersionLog (fID, fvID, fvUpdateTypeID, fvUpdateTypeAttributeID) VALUES (?, ?, ?, ?)',
            array(
                $this->getFileID(),
                $this->getFileVersionID(),
                $updateTypeID,
                $updateTypeAttributeID
            )
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
     * Return the contents of a file
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
     *
     * @return void
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
        $thumbnails = array();
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

    /**
     * Gets an attribute for the file. If "nice mode" is set, we display it nicely
     * for use in the file attributes table
     */

    public function getAttribute($ak, $mode = false)
    {
        if (is_object($ak)) {
            $akHandle = $ak->getAttributeKeyHandle();
        } else {
            $akHandle = $ak;
        }

        if (!isset($this->attributes[$akHandle . $mode])) {
            $this->attributes[$akHandle . $mode] = false;
            $ak = FileAttributeKey::getByHandle($akHandle);
            if (is_object($ak)) {
                $av = $this->getAttributeValueObject($ak);
                if (is_object($av)) {
                    $this->attributes[$akHandle . $mode] = $av->getValue($mode);
                }
            }
        }
        return $this->attributes[$akHandle . $mode];
    }

    public function rescanThumbnails()
    {
        if ($this->fvType != \Concrete\Core\File\Type\Type::T_IMAGE) {
            return false;
        }

        $types = Type::getVersionList();

        $fr = $this->getFileResource();
        try {
            $image = \Image::load($fr->read());
            $mimetype = $fr->getMimeType();

            foreach ($types as $type) {


                // delete the file if it exists
                $this->deleteThumbnail($type);

                if ($this->getAttribute('width') <= $type->getWidth()) {
                    continue;
                }

                $filesystem = $this->getFile()
                    ->getFileStorageLocationObject()
                    ->getFileSystemObject();

                $height = $type->getHeight();
                $thumbnailMode = ImageInterface::THUMBNAIL_OUTBOUND;
                if (!$height) {
                    $height = $type->getWidth();
                    $thumbnailMode = ImageInterface::THUMBNAIL_INSET;
                }
                $thumbnail = $image->thumbnail(new \Imagine\Image\Box($type->getWidth(), $height), $thumbnailMode);
                $thumbnailPath = $type->getFilePath($this);
                $thumbnailOptions = array();

                switch ($mimetype) {
                  case 'image/jpeg':
                    $thumbnailType = 'jpeg';
                    $thumbnailOptions = array('jpeg_quality' => \Config::get('concrete.misc.default_jpeg_image_compression'));
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
                    array(
                        'visibility' => AdapterInterface::VISIBILITY_PUBLIC,
                        'mimetype' => $mimetype
                    )
                );

                if ($type->getHandle() == \Config::get('concrete.icons.file_manager_listing.handle')) {
                    $this->fvHasListingThumbnail = true;
                }

                if ($type->getHandle() == \Config::get('concrete.icons.file_manager_detail.handle')) {
                    $this->fvHasDetailThumbnail = true;
                }

                unset($thumbnail);
                unset($filesystem);
            }
        } catch (ImagineInvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * @deprecated
     * @param $level
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
            array(
                'visibility' => AdapterInterface::VISIBILITY_PUBLIC,
                'mimetype'   => 'image/jpeg'
            )
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
     * Returns a full URL to the file on disk
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
     * Responsible for taking a particular version of a file and rescanning all its attributes
     * This will run any type-based import routines, and store those attributes, generate thumbnails,
     * etc...
     */
    public function refreshAttributes($rescanThumbnails = true)
    {
        $fh = Core::make('helper/file');
        $ext = $fh->getExtension($this->fvFilename);
        $ftl = FileTypeList::getType($ext);
        $db = Database::get();

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

        if (is_object($ftl)) {
            if ($ftl->getCustomImporter() != false) {
                $this->fvGenericType = $ftl->getGenericType();
                $cl = $ftl->getCustomInspector();
                $cl->inspect($this);
            }
        }

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
        $r = new stdClass;
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
        return $r;
    }

    /**
     * Checks current viewers for this type and returns true if there is a viewer for this type, false if not
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
            return sprintf('<img width="%s" height="%s" src="%s" data-at2x="%s">', $width, $height, $baseSrc, $doubledSrc);
        } else {
            return $this->getTypeObject()->getThumbnail();
        }
    }
}
