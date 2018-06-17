<?php

namespace Concrete\Core\Entity\File;

use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Attribute\Category\FileCategory;
use Concrete\Core\Attribute\ObjectInterface;
use Concrete\Core\Attribute\ObjectTrait;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Attribute\Value\FileValue;
use Concrete\Core\Entity\File\StorageLocation\StorageLocation;
use Concrete\Core\File\Event\FileVersion as FileVersionEvent;
use Concrete\Core\File\Exception\InvalidDimensionException;
use Concrete\Core\File\Image\BitmapFormat;
use Concrete\Core\File\Image\Thumbnail\Path\Resolver;
use Concrete\Core\File\Image\Thumbnail\Thumbnail;
use Concrete\Core\File\Image\Thumbnail\ThumbnailFormatService;
use Concrete\Core\File\Image\Thumbnail\Type\Type;
use Concrete\Core\File\Image\Thumbnail\Type\Version as ThumbnailTypeVersion;
use Concrete\Core\File\Importer;
use Concrete\Core\File\Menu;
use Concrete\Core\File\Type\TypeList as FileTypeList;
use Concrete\Core\Http\FlysystemFileResponse;
use Concrete\Core\Http\Request;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\User\UserInfoRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Imagine\Exception\NotSupportedException;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Metadata\ExifMetadataReader;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\MountManager;
use League\Flysystem\Util;
use Page;
use Permissions;
use stdClass;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Throwable;
use User;

/**
 * Represents a version of a file.
 *
 * @ORM\Entity
 * @ORM\Table(
 *     name="FileVersions",
 *     indexes={
 *         @ORM\Index(name="fvFilename", columns={"fvFilename"}),
 *         @ORM\Index(name="fvExtension", columns={"fvExtension"}),
 *         @ORM\Index(name="fvType", columns={"fvType"})
 *     }
 * )
 */
class Version implements ObjectInterface
{
    use ObjectTrait;

    /**
     * Update type: file replaced.
     *
     * @var int
     */
    const UT_REPLACE_FILE = 1;

    /**
     * Update type: title updated.
     *
     * @var int
     */
    const UT_TITLE = 2;

    /**
     * Update type: description updated.
     *
     * @var int
     */
    const UT_DESCRIPTION = 3;

    /**
     * Update type: tags modified.
     *
     * @var int
     */
    const UT_TAGS = 4;

    /**
     * Update type: extended attributes changed.
     *
     * @var int
     */
    const UT_EXTENDED_ATTRIBUTE = 5;

    /**
     * Update type: contents changed.
     *
     * @var int
     */
    const UT_CONTENTS = 6;

    /**
     * Update type: file version renamed.
     *
     * @var int
     */
    const UT_RENAME = 7;

    /**
     * The associated File instance.
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="File", inversedBy="versions")
     * @ORM\JoinColumn(name="fID", referencedColumnName="fID")
     *
     * @var \Concrete\Core\Entity\File\File
     */
    protected $file;

    /**
     * The progressive file version identifier.
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $fvID = 0;

    /**
     * The name of the file.
     *
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $fvFilename = null;

    /**
     * The path prefix used to store the file in the file system.
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    protected $fvPrefix;

    /**
     * The date/time when the file version has been added.
     *
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    protected $fvDateAdded;

    /**
     * The date/time when the file version has been approved.
     *
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    protected $fvActivateDateTime;

    /**
     * Is this version the approved one for the associated file?
     *
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    protected $fvIsApproved = false;

    /**
     * The ID of the user that created the file version.
     *
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $fvAuthorUID = 0;

    /**
     * The ID of the user that approved the file version.
     *
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $fvApproverUID = 0;

    /**
     * The size (in bytes) of the file version.
     *
     * @ORM\Column(type="bigint")
     *
     * @var int
     */
    protected $fvSize = 0;

    /**
     * The title of the file version.
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    protected $fvTitle = null;

    /**
     * The description of the file version.
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string|null
     */
    protected $fvDescription = null;

    /**
     * The extension of the file version.
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    protected $fvExtension = null;

    /**
     * The tags assigned to the file version (separated by a newline character - '\n').
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string|null
     */
    protected $fvTags = null;

    /**
     * The type of the file version.
     *
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $fvType = 0;

    /**
     * Does this file version has a thumbnail to be used for file listing?
     *
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    protected $fvHasListingThumbnail = false;

    /**
     * Does this file version has a thumbnail to be used used for details?
     *
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    protected $fvHasDetailThumbnail = false;

    /**
     * The currently loaded Image instance.
     *
     * @var \Imagine\Image\ImageInterface|false|null null: still not loaded; false: load failed; ImageInterface otherwise
     */
    private $imagineImage = null;

    /**
     * Initialize the instance.
     */
    public function __construct()
    {
        $this->fvDateAdded = new DateTime();
        $this->fvActivateDateTime = new DateTime();
    }

    /**
     * Add a new file version.
     * You should call refreshAttributes in order to update the size, extension, type and other attributes.
     *
     * @param \Concrete\Core\Entity\File\File $file the File instance associated to this version
     * @param string $filename The name of the file
     * @param string $prefix the path prefix used to store the file in the file system
     * @param array $data Valid array keys are {
     *
     *     @var int|null $uID the ID of the user that creates the file version (if not specified or empty: we'll assume the currently user logged in user)
     *     @var string $fvTitle the title of the file version
     *     @var string $fvDescription the description of the file version
     *     @var string $fvTags the tags to be assigned to the file version (separated by newlines and/or commas)
     *     @var bool $fvIsApproved Is this version the approved one for the associated file? (default: true)
     * }
     *
     * @return static
     */
    public static function add(File $file, $filename, $prefix, $data = [])
    {
        $data += [
            'uID' => 0,
            'fvTitle' => '',
            'fvDescription' => '',
            'fvTags' => '',
            'fvIsApproved' => true,
        ];

        $app = Application::getFacadeApplication();
        $em = $app->make(EntityManagerInterface::class);
        $dh = $app->make('date');

        $date = new DateTime($dh->getOverridableNow());
        $uID = (int) $data['uID'];
        if ($uID < 1) {
            if (User::isLoggedIn()) {
                $uID = (int) (new User())->getUserID();
            } else {
                $uID = 0;
            }
        }

        $fv = new static();
        $fv->file = $file;
        $fv->fvID = 1;
        $fv->fvFilename = (string) $filename;
        $fv->fvPrefix = $prefix;
        $fv->fvDateAdded = $date;
        $fv->fvActivateDateTime = $date;
        $fv->fvIsApproved = (bool) $data['fvIsApproved'];
        $fv->fvAuthorUID = $uID;
        $fv->fvApproverUID = $uID;
        $fv->fvTitle = (string) $data['fvTitle'];
        $fv->fvDescription = (string) $data['fvDescription'];
        $fv->fvTags = self::cleanTags((string) $data['fvTags']);
        $em->persist($fv);
        $em->flush();

        $fve = new FileVersionEvent($fv);
        $app->make(EventDispatcherInterface::class)->dispatch('on_file_version_add', $fve);

        return $fv;
    }

    /**
     * Normalize the tags separator, remove empty tags.
     *
     * @param string $tagsStr The list of tags, delimited by '\n', '\r' or ','
     *
     * @return string
     */
    public static function cleanTags($tagsStr)
    {
        $tagsArray = explode("\n", str_replace(["\r", ','], "\n", $tagsStr));
        $cleanTags = [];
        foreach ($tagsArray as $tag) {
            $tag = trim($tag);
            if ($tag !== '') {
                $cleanTags[] = $tag;
            }
        }
        //the leading and trailing line break char is for searching: fvTag like %\ntag\n%
        return isset($cleanTags[0]) ? "\n" . implode("\n", $cleanTags) . "\n" : '';
    }

    /**
     * Set the associated File instance.
     *
     * @param \Concrete\Core\Entity\File\File $file
     */
    public function setFile(File $file)
    {
        $this->file = $file;
    }

    /**
     * Get the associated File instance.
     *
     * @return \Concrete\Core\Entity\File\File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Get the ID of the associated file instance.
     *
     * @return int
     */
    public function getFileID()
    {
        return $this->file->getFileID();
    }

    /**
     * Set the progressive file version identifier.
     *
     * @param int $fvID
     */
    public function setFileVersionID($fvID)
    {
        $this->fvID = (int) $fvID;
    }

    /**
     * Get the progressive file version identifier.
     *
     * @return int
     */
    public function getFileVersionID()
    {
        return $this->fvID;
    }

    /**
     * Rename the file.
     *
     * @param string $filename
     */
    public function rename($filename)
    {
        $storage = $this->getFile()->getFileStorageLocationObject();
        if ($storage !== null) {
            $filename = (string) $filename;
            $app = Application::getFacadeApplication();
            $cf = $app->make('helper/concrete/file');
            $oldFilename = $this->fvFilename;
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
            $fve = new FileVersionEvent($this);
            $app->make(EventDispatcherInterface::class)->dispatch('on_file_version_rename', $fve);
        }
    }

    /**
     * Update the filename and the path prefix of the file.
     *
     * @param string $filename The new name of file
     * @param string $prefix The new path prefix
     */
    public function updateFile($filename, $prefix)
    {
        $this->fvFilename = $filename;
        $this->fvPrefix = $prefix;
        $this->save();
        $this->logVersionUpdate(self::UT_REPLACE_FILE);
    }

    /**
     * Set the name of the file.
     *
     * @param string $filename
     */
    public function setFilename($filename)
    {
        $this->fvFilename = $filename;
    }

    /**
     * Get the name of the file.
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fvFilename;
    }

    /**
     * Get the path prefix used to store the file in the file system.
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->fvPrefix;
    }

    /**
     * Get the date/time when the file version has been added.
     *
     * @return \DateTime
     */
    public function getDateAdded()
    {
        return $this->fvDateAdded;
    }

    /**
     * Get the date/time when the file version has been activated (or NULL if the file version is not approved).
     *
     * @return \DateTime|null
     */
    public function getActivateDateTime()
    {
        return $this->fvIsApproved ? $this->fvActivateDateTime : null;
    }

    /**
     * Mark this file version as approved (and disapprove all the other versions of the file).
     * The currently logged in user (if any) will be stored as the approver.
     */
    public function approve()
    {
        $app = Application::getFacadeApplication();
        foreach ($this->file->getFileVersions() as $fv) {
            $fv->fvIsApproved = false;
            $fv->save(false);
        }

        $this->fvIsApproved = true;
        $this->fvActivateDateTime = new DateTime();
        if (User::isLoggedIn()) {
            $uID = (int) (new User())->getUserID();
            if ($uID > 0) {
                $this->fvApproverUID = $uID;
            }
        }
        $this->save();

        $fve = new FileVersionEvent($this);
        $app->make(EventDispatcherInterface::class)->dispatch('on_file_version_approve', $fve);

        $fo = $this->getFile();
        $fo->reindex();
        $app->make('cache/request')->delete('file/version/approved/' . $this->getFileID());
    }

    /**
     * Mark this file version as not approved.
     */
    public function deny()
    {
        $app = Application::getFacadeApplication();
        $this->fvIsApproved = false;
        $this->save();
        $fve = new FileVersionEvent($this);
        $app->make(EventDispatcherInterface::class)->dispatch('on_file_version_deny', $fve);
        $app->make('cache/request')->delete('file/version/approved/' . $this->getFileID());
    }

    /**
     * Is this version the approved one for the associated file?
     *
     * @return bool
     */
    public function isApproved()
    {
        return $this->fvIsApproved;
    }

    /**
     * Get the ID of the user that created the file version.
     *
     * @return int
     */
    public function getAuthorUserID()
    {
        return $this->fvAuthorUID;
    }

    /**
     * Get the username of the user that created the file version (or "Unknown").
     *
     * @return string
     */
    public function getAuthorName()
    {
        if ($this->fvAuthorUID) {
            $app = Application::getFacadeApplication();
            $ui = $app->make(UserInfoRepository::class)->getByID($this->fvAuthorUID);
        } else {
            $ui = null;
        }

        return $ui === null ? t('(Unknown)') : $ui->getUserDisplayName();
    }

    /**
     * Get the ID of the user that approved the file version.
     *
     * @return int
     */
    public function getApproverUserID()
    {
        return $this->fvApproverUID;
    }

    /**
     * Get the username of the user that approved the file version (or "Unknown").
     *
     * @return string
     */
    public function getApproverName()
    {
        if ($this->fvApproverUID) {
            $app = Application::getFacadeApplication();
            $ui = $app->make(UserInfoRepository::class)->getByID($this->fvApproverUID);
        } else {
            $ui = null;
        }

        return $ui === null ? t('(Unknown)') : $ui->getUserDisplayName();
    }

    /**
     * Get the file size of the file (in bytes).
     *
     * @return int
     */
    public function getFullSize()
    {
        return $this->fvSize;
    }

    /**
     * Get the formatted file size.
     *
     * @return string
     *
     * @example 123 KB
     */
    public function getSize()
    {
        $app = Application::getFacadeApplication();

        return $app->make('helper/number')->formatSize($this->fvSize, 'KB');
    }

    /**
     * Update the title of the file.
     *
     * @param string $title
     */
    public function updateTitle($title)
    {
        $app = Application::getFacadeApplication();
        $this->fvTitle = $title;
        $this->save();
        $this->logVersionUpdate(self::UT_TITLE);
        $fve = new FileVersionEvent($this);
        $app->make(EventDispatcherInterface::class)->dispatch('on_file_version_update_title', $fve);
    }

    /**
     * Get the title of the file version.
     *
     * @return null|string
     */
    public function getTitle()
    {
        return $this->fvTitle;
    }

    /**
     * Update the description of the file.
     *
     * @param string $descr
     */
    public function updateDescription($descr)
    {
        $app = Application::getFacadeApplication();
        $this->fvDescription = $descr;
        $this->save();
        $this->logVersionUpdate(self::UT_DESCRIPTION);
        $fve = new FileVersionEvent($this);
        $app->make(EventDispatcherInterface::class)->dispatch('on_file_version_update_description', $fve);
    }

    /**
     * Get the description of the file version.
     *
     * @return null|string
     */
    public function getDescription()
    {
        return $this->fvDescription;
    }

    /**
     * Get the extension of the file version.
     *
     * @return null|string
     */
    public function getExtension()
    {
        return $this->fvExtension;
    }

    /**
     * Update the tags associated to the file.
     *
     * @param string $tags List of tags separated by newlines and/or commas
     */
    public function updateTags($tags)
    {
        $app = Application::getFacadeApplication();
        $tags = self::cleanTags($tags);
        $this->fvTags = $tags;
        $this->save();
        $this->logVersionUpdate(self::UT_TAGS);
        $fve = new FileVersionEvent($this);
        $app->make(EventDispatcherInterface::class)->dispatch('on_file_version_update_tags', $fve);
    }

    /**
     * Get the tags assigned to the file version (as a list of strings).
     *
     * @return string[]
     */
    public function getTagsList()
    {
        $tags = explode("\n", str_replace("\r", "\n", trim($this->getTags())));
        $clean_tags = [];
        foreach ($tags as $tag) {
            $tag = trim($tag);
            if ($tag !== '') {
                $clean_tags[] = $tag;
            }
        }

        return $clean_tags;
    }

    /**
     * Get the tags assigned to the file version (one tag per line - lines are separated by '\n').
     *
     * @return null|string
     */
    public function getTags()
    {
        return $this->fvTags;
    }

    /**
     * Get the mime type of the file if known.
     *
     * @return string|false
     */
    public function getMimeType()
    {
        try {
            $fre = $this->getFileResource();
            $result = $fre->getMimetype();
        } catch (FileNotFoundException $x) {
            $result = false;
        }

        return $result;
    }

    /**
     * Get the type of the file.
     *
     * @return \Concrete\Core\File\Type\Type
     */
    public function getTypeObject()
    {
        $app = Application::getFacadeApplication();
        $fh = $app->make('helper/file');
        $ext = $fh->getExtension($this->fvFilename);
        $ftl = FileTypeList::getType($ext);

        return $ftl;
    }

    /**
     * Get the name of the file type.
     *
     * @return string
     */
    public function getType()
    {
        $ftl = $this->getTypeObject();

        return $ftl->getName();
    }

    /**
     * Get the localized name of the file type.
     *
     * @return string
     */
    public function getDisplayType()
    {
        $ftl = $this->getTypeObject();

        return $ftl->getDisplayName();
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
     * Log updates to files.
     *
     * @param int $updateTypeID One of the Version::UT_... constants
     * @param int $updateTypeAttributeID the ID of the attribute that has been updated (if any - useful when $updateTypeID is UT_EXTENDED_ATTRIBUTE)
     */
    public function logVersionUpdate($updateTypeID, $updateTypeAttributeID = 0)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->executeQuery(
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
     * Get an array containing human-readable descriptions of everything that happened to this file version.
     *
     * @return string[]
     */
    public function getVersionLogComments()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $updates = [];
        $ga = $db->fetchAll(
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
                    $val = $db->fetchColumn(
                        'SELECT akName FROM AttributeKeys WHERE akID = ?',
                        [$a['fvUpdateTypeAttributeID']]
                    );
                    if ($val !== false) {
                        $updates[] = $val;
                    }
                    break;
            }
        }

        return array_values(array_unique($updates));
    }

    /**
     * Get the path to the file relative to the webroot (may not exist).
     * Return NULL if the file storage location is invalid.
     * If the storage location does not support relative paths, you'll get the URL to the file (or the download URL if the file is not directly accessible).
     *
     * @return string|null
     *
     * @example /application/files/0000/0000/0000/file.png
     */
    public function getRelativePath()
    {
        $url = null;
        $fsl = $this->getFile()->getFileStorageLocationObject();
        if ($fsl !== null) {
            $app = Application::getFacadeApplication();
            $cf = $app->make('helper/concrete/file');
            $configuration = $fsl->getConfigurationObject();
            if ($configuration->hasRelativePath()) {
                $url = $configuration->getRelativePathToFile($cf->prefix($this->fvPrefix, $this->fvFilename));
            }
            if ($configuration->hasPublicURL() && !$url) {
                $url = $configuration->getPublicURLToFile($cf->prefix($this->fvPrefix, $this->fvFilename));
            }
            if (!$url) {
                $url = (string) $this->getDownloadURL();
            }
        }

        return $url;
    }

    /**
     * Get an URL that points to the file on disk (if not available, you'll get the result of the getDownloadURL method).
     * Return NULL if the file storage location is invalid.
     * If the file is not directly accessible, you'll get the download URL.
     *
     * @return string|null
     */
    public function getURL()
    {
        $url = null;
        $fsl = $this->getFile()->getFileStorageLocationObject();
        if ($fsl !== null) {
            $app = Application::getFacadeApplication();
            $cf = $app->make('helper/concrete/file');
            $configuration = $fsl->getConfigurationObject();
            if ($configuration->hasPublicURL()) {
                $url = $configuration->getPublicURLToFile($cf->prefix($this->fvPrefix, $this->fvFilename));
            }
            if (!$url) {
                $url = (string) $this->getDownloadURL();
            }
        }

        return $url;
    }

    /**
     * Get an URL that can be used to download the file.
     * This passes through the download_file single page.
     *
     * @return \League\URL\URLInterface
     */
    public function getDownloadURL()
    {
        $app = Application::getFacadeApplication();
        $urlResolver = $app->make(ResolverManagerInterface::class);
        $c = Page::getCurrentPage();
        $cID = $c instanceof Page && !$c->isError() ? $c->getCollectionID() : 0;

        return $urlResolver->resolve(['/download_file', $this->getFileID(), $cID]);
    }

    /**
     * Get an URL that can be used to download the file (it will force the download of all file types, even if the browser can display them).
     *
     * @return \League\URL\URLInterface
     */
    public function getForceDownloadURL()
    {
        $app = Application::getFacadeApplication();
        $c = Page::getCurrentPage();
        $cID = $c instanceof Page && !$c->isError() ? $c->getCollectionID() : 0;
        $urlResolver = $app->make(ResolverManagerInterface::class);

        return $urlResolver->resolve(['/download_file', 'force', $this->getFileID(), $cID]);
    }

    /**
     * Get a Response instance that will force the browser to download the file, even if the browser can display it.
     *
     * @return \Concrete\Core\Http\Response
     */
    public function buildForceDownloadResponse()
    {
        $app = Application::getFacadeApplication();
        $fre = $this->getFileResource();

        $fs = $this->getFile()->getFileStorageLocationObject()->getFileSystemObject();
        $response = new FlysystemFileResponse($fre->getPath(), $fs);

        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

        return $response;
    }

    /**
     * Check if there is a viewer for the type of the file.
     *
     * @return bool
     */
    public function canView()
    {
        $to = $this->getTypeObject();

        return (string) $to->getView() !== '';
    }

    /**
     * Check if there is an editor for the type of the file.
     *
     * @return bool
     */
    public function canEdit()
    {
        $to = $this->getTypeObject();

        return (string) $to->getEditor() !== '';
    }

    /**
     * Create a new (unapproved) copy of this file version.
     * The new Version instance will have the current user as the author (if available), and a new version ID.
     *
     * @return Version
     */
    public function duplicate()
    {
        $app = Application::getFacadeApplication();
        $em = $app->make(EntityManagerInterface::class);
        $qq = $em->createQuery('SELECT max(v.fvID) FROM \Concrete\Core\Entity\File\Version v where v.file = :file');
        $qq->setParameter('file', $this->file);
        $fvID = $qq->getSingleScalarResult();
        ++$fvID;

        $fv = clone $this;
        $fv->fvID = $fvID;
        $fv->fvIsApproved = false;
        $fv->fvDateAdded = new DateTime();
        if (User::isLoggedIn()) {
            $uID = (int) (new User())->getUserID();
            if ($uID !== 0) {
                $fv->fvAuthorUID = $uID;
            }
        }

        $em->persist($fv);

        $this->deny();

        foreach ($this->getAttributes() as $value) {
            $value = clone $value;
            $value->setVersion($fv);
            $em->persist($value);
        }

        $em->flush();

        $fve = new FileVersionEvent($fv);
        $app->make(EventDispatcherInterface::class)->dispatch('on_file_version_duplicate', $fve);

        return $fv;
    }

    /**
     * Duplicate the underlying file and assign its new position to this instance.
     */
    public function duplicateUnderlyingFile()
    {
        $app = Application::getFacadeApplication();
        $importer = new Importer();
        $fi = $app->make('helper/file');
        $cf = $app->make('helper/concrete/file');
        $filesystem = $this->getFile()->getFileStorageLocationObject()->getFileSystemObject();
        $fileName = $this->getFileName();
        do {
            $prefix = $importer->generatePrefix();
            $path = $cf->prefix($prefix, $fileName);
        } while ($filesystem->has($path));
        $fileContents = $this->getFileResource()->read();
        $mimeType = Util::guessMimeType($fileName, $fileContents);
        $filesystem->write(
            $path,
            $fileContents,
            [
                'visibility' => AdapterInterface::VISIBILITY_PUBLIC,
                'mimetype' => $mimeType,
            ]
        );
        $this->updateFile($fileName, $prefix);
    }

    /**
     * Delete this version of the file.
     *
     * @param bool $deleteFilesAndThumbnails should we delete the actual file and the thumbnails?
     */
    public function delete($deleteFilesAndThumbnails = false)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $em = $app->make(EntityManagerInterface::class);
        $category = $this->getObjectAttributeCategory();

        foreach ($this->getAttributes() as $attribute) {
            $category->deleteValue($attribute);
        }

        $db->executeQuery('DELETE FROM FileVersionLog WHERE fID = ? AND fvID = ?', [$this->getFileID(), $this->fvID]);

        if ($deleteFilesAndThumbnails) {
            $types = Type::getVersionList();
            foreach ($types as $type) {
                $this->deleteThumbnail($type);
            }
            try {
                $fsl = $this->getFile()->getFileStorageLocationObject()->getFileSystemObject();
                $fre = $this->getFileResource();
                if ($fsl->has($fre->getPath())) {
                    $fsl->delete($fre->getPath());
                }
            } catch (FileNotFoundException $e) {
            }
        }
        $em->remove($this);
        $em->flush();
    }

    /**
     * Get an abstract object to work with the actual file resource (note: this is NOT a concrete5 File object).
     *
     * @throws \League\Flysystem\FileNotFoundException
     *
     * @return \League\Flysystem\File
     */
    public function getFileResource()
    {
        $app = Application::getFacadeApplication();
        $cf = $app->make('helper/concrete/file');
        $fs = $this->getFile()->getFileStorageLocationObject()->getFileSystemObject();
        $fo = $fs->get($cf->prefix($this->fvPrefix, $this->fvFilename));

        return $fo;
    }

    /**
     * Update the contents of the file.
     *
     * @param string $contents The new content of the file
     * @param bool $rescanThumbnails Should thumbnails be rescanned as well?
     */
    public function updateContents($contents, $rescanThumbnails = true)
    {
        $this->releaseImagineImage();
        $storage = $this->getFile()->getFileStorageLocationObject();
        if ($storage !== null) {
            $app = Application::getFacadeApplication();
            $cf = $app->make('helper/concrete/file');
            $path = $cf->prefix($this->fvPrefix, $this->fvFilename);
            $filesystem = $storage->getFileSystemObject();
            try {
                if ($filesystem->has($path)) {
                    $filesystem->delete($path);
                }
            } catch (FileNotFoundException $x) {
            }
            $filesystem->write($path, $contents);
            $this->logVersionUpdate(self::UT_CONTENTS);
            $fve = new FileVersionEvent($this);
            $app->make(EventDispatcherInterface::class)->dispatch('on_file_version_update_contents', $fve);
            $this->refreshAttributes($rescanThumbnails);
        }
    }

    /**
     * Get the contents of the file.
     *
     * @return string|null return NULL if the actual file does not exist or can't be read
     */
    public function getFileContents()
    {
        $result = null;
        $fsl = $this->getFile()->getFileStorageLocationObject();
        if ($fsl !== null) {
            $app = Application::getFacadeApplication();
            $cf = $app->make('helper/concrete/file');
            try {
                $result = $fsl->getFileSystemObject()->read($cf->prefix($this->fvPrefix, $this->fvFilename));
                if ($result === false) {
                    $result = null;
                }
            } catch (FileNotFoundException $x) {
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\ObjectInterface::getObjectAttributeCategory()
     *
     * @return \Concrete\Core\Attribute\Category\FileCategory
     */
    public function getObjectAttributeCategory()
    {
        $app = Application::getFacadeApplication();

        return $app->make(FileCategory::class);
    }

    /**
     * Rescan all the attributes of this file version.
     * This will run any type-based import routines, and store those attributes, generate thumbnails, etc...
     *
     * @param bool $rescanThumbnails Should thumbnails be rescanned as well?
     *
     * @return null|int Return one of the \Concrete\Core\File\Importer::E_... constants in case of errors, NULL otherwise.
     */
    public function refreshAttributes($rescanThumbnails = true)
    {
        $app = Application::getFacadeApplication();

        $storage = $this->getFile()->getFileStorageLocationObject();
        if ($storage !== null) {
            $fs = $storage->getFileSystemObject();
            $adapter = $fs->getAdapter();
            if ($adapter instanceof CachedAdapter) {
                $cache = $adapter->getCache();
                $cf = $app->make('helper/concrete/file');
                $path = Util::normalizePath($cf->prefix($this->fvPrefix, $this->fvFilename));
                $cache->delete($path);
            }
        }

        $em = $app->make(EntityManagerInterface::class);
        $fh = $app->make('helper/file');
        $ext = $fh->getExtension($this->fvFilename);
        $ftl = FileTypeList::getType($ext);

        $cl = $ftl->getCustomInspector();
        if ($cl !== null) {
            $this->fvGenericType = $ftl->getGenericType();
            $cl->inspect($this);
        }

        $em->refresh($this);

        try {
            $fsr = $this->getFileResource();
            if (!$fsr->isFile()) {
                return Importer::E_FILE_INVALID;
            }
        } catch (FileNotFoundException $e) {
            return Importer::E_FILE_INVALID;
        }

        $this->fvExtension = $ext;
        $this->fvType = $ftl->getGenericType();
        if ($this->fvTitle === null) {
            $this->fvTitle = $this->getFileName();
        }
        $this->fvSize = $fsr->getSize();

        if ($rescanThumbnails) {
            $this->rescanThumbnails();
        }

        $this->save();

        $f = $this->getFile();
        $f->reindex();
    }

    /**
     * Get the list of attributes associated to this file version.
     *
     * @return \Concrete\Core\Entity\Attribute\Value\FileValue[]
     */
    public function getAttributes()
    {
        return $this->getObjectAttributeCategory()->getAttributeValues($this);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\ObjectInterface::getAttributeValueObject()
     *
     * @return \Concrete\Core\Entity\Attribute\Value\FileValue|null
     */
    public function getAttributeValueObject($ak, $createIfNotExists = false)
    {
        if (!($ak instanceof AttributeKeyInterface)) {
            $ak = $ak ? $this->getObjectAttributeCategory()->getAttributeKeyByHandle((string) $ak) : null;
        }
        if ($ak === null) {
            $result = null;
        } else {
            $result = $this->getObjectAttributeCategory()->getAttributeValue($ak, $this);
            if ($result === null && $createIfNotExists) {
                $result = new FileValue();
                $result->setVersion($this);
                $result->setAttributeKey($ak);
            }
        }

        return $result;
    }

    /**
     * Get an \Imagine\Image\ImageInterface representing the image.
     *
     * @return \Imagine\Image\ImageInterface|null return NULL if the image coulnd't be read, an ImageInterface otherwise
     */
    public function getImagineImage()
    {
        if (null === $this->imagineImage) {
            $app = Application::getFacadeApplication();
            $resource = $this->getFileResource();
            $mimetype = $resource->getMimeType();
            $imageLibrary = $app->make(ImagineInterface::class);

            switch ($mimetype) {
                case 'image/svg+xml':
                case 'image/svg-xml':
                case 'text/plain':
                    if ($imageLibrary instanceof \Imagine\Gd\Imagine) {
                        try {
                            $imageLibrary = $app->make('image/imagick');
                        } catch (Exception $x) {
                            $this->imagineImage = false;
                        } catch (Throwable $x) {
                            $this->imagineImage = false;
                        }
                    }
                    break;
            }

            if (null === $this->imagineImage) {
                $metadataReader = $imageLibrary->getMetadataReader();
                if (!$metadataReader instanceof ExifMetadataReader) {
                    if ($app->make('config')->get('concrete.file_manager.images.use_exif_data_to_rotate_images')) {
                        try {
                            $imageLibrary->setMetadataReader(new ExifMetadataReader());
                        } catch (NotSupportedException $e) {
                        }
                    }
                }
                try {
                    $this->imagineImage = $imageLibrary->load($resource->read());
                } catch (FileNotFoundException $e) {
                    $this->imagineImage = false;
                }
            }
        }

        return $this->imagineImage ?: null;
    }

    /**
     * Does the \Imagine\Image\ImageInterface instance have already been loaded?
     *
     * @return bool
     */
    public function hasImagineImage()
    {
        return $this->imagineImage ? true : false;
    }

    /**
     * Unload the loaded Image instance.
     */
    public function releaseImagineImage()
    {
        $this->imagineImage = null;
    }

    /**
     * Create missing thumbnails (or re-create all the thumbnails).
     *
     * @param bool $deleteExistingThumbnails Set to true to delete existing thumbnails (they will be re-created from scratch)
     *
     * @return bool return true on success, false on failure (file is not an image, problems during image processing, ...)
     */
    public function refreshThumbnails($deleteExistingThumbnails)
    {
        $result = false;
        if ($this->fvType == \Concrete\Core\File\Type\Type::T_IMAGE) {
            try {
                $image = $this->getImagineImage();
                if ($image) {
                    $imageWidth = (int) $this->getAttribute('width') ?: (int) $image->getSize()->getWidth();
                    $imageHeight = (int) $this->getAttribute('height') ?: (int) $image->getSize()->getHeight();
                    $types = Type::getVersionList();
                    $file = $this->getFile();
                    $fsl = null;
                    foreach ($types as $type) {
                        if ($type->shouldExistFor($imageWidth, $imageHeight, $file)) {
                            if ($deleteExistingThumbnails) {
                                $this->deleteThumbnail($type);
                            } else {
                                if ($fsl === null) {
                                    $fsl = $this->getFile()->getFileStorageLocationObject()->getFileSystemObject();
                                }
                                $path = $type->getFilePath($this);
                                try {
                                    $exists = $fsl->has($path);
                                } catch (FileNotFoundException $e) {
                                    $exists = false;
                                }
                                if ($exists) {
                                    continue;
                                }
                            }
                            $this->generateThumbnail($type);
                        } else {
                            // delete the file if it exists
                            $this->deleteThumbnail($type);
                        }
                    }
                    $result = true;
                }
            } catch (\Imagine\Exception\InvalidArgumentException $e) {
            } catch (\Imagine\Exception\RuntimeException $e) {
            }
        }

        return $result;
    }

    /**
     * Generate a thumbnail for a specific thumbnail type version.
     *
     * @param \Concrete\Core\File\Image\Thumbnail\Type\Version $type
     */
    public function generateThumbnail(ThumbnailTypeVersion $type)
    {
        $app = Application::getFacadeApplication();
        $config = $app->make('config');
        $image = $this->getImagineImage();
        $bitmapFormat = $app->make(BitmapFormat::class);

        $filesystem = $this->getFile()
            ->getFileStorageLocationObject()
            ->getFileSystemObject();

        $height = $type->getHeight();
        $width = $type->getWidth();
        if ($height && $width) {
            $size = new Box($width, $height);
        } elseif ($width) {
            $size = $image->getSize()->widen($width);
        } else {
            $size = $image->getSize()->heighten($height);
        }

        // isCropped only exists on the CustomThumbnail type
        if (method_exists($type, 'isCropped') && $type->isCropped()) {
            $thumbnailMode = ImageInterface::THUMBNAIL_OUTBOUND;
        } else {
            switch ($type->getSizingMode()) {
                case Type::RESIZE_EXACT:
                    $thumbnailMode = ImageInterface::THUMBNAIL_OUTBOUND;
                    break;
                case Type::RESIZE_PROPORTIONAL:
                default:
                    $thumbnailMode = ImageInterface::THUMBNAIL_INSET;
                    break;
            }
        }

        $imageForThumbnail = $image;
        if ($type->isUpscalingEnabled()) {
            $imageSize = $image->getSize();
            if ($size->contains($imageSize) && $imageSize->getWidth() !== $size->getWidth() && $imageSize->getHeight() !== $size->getHeight()) {
                if (($imageSize->getWidth() / $imageSize->getHeight()) >= ($size->getWidth() / $size->getHeight())) {
                    $newImageSize = $imageSize->heighten($size->getHeight());
                } else {
                    $newImageSize = $imageSize->widen($size->getWidth());
                }
                $imageForThumbnail = $image->copy()->resize($newImageSize);
            }
        }
        $thumbnail = $imageForThumbnail->thumbnail($size, $thumbnailMode);
        unset($imageForThumbnail);
        $thumbnailPath = $type->getFilePath($this);
        $thumbnailFormat = $app->make(ThumbnailFormatService::class)->getFormatForFile($this);

        $mimetype = $bitmapFormat->getFormatMimeType($thumbnailFormat);
        $thumbnailOptions = $bitmapFormat->getFormatImagineSaveOptions($thumbnailFormat);

        $filesystem->write(
            $thumbnailPath,
            $thumbnail->get($thumbnailFormat, $thumbnailOptions),
            [
                'visibility' => AdapterInterface::VISIBILITY_PUBLIC,
                'mimetype' => $mimetype,
            ]
        );

        $app['director']->dispatch('on_thumbnail_generate',
            new \Concrete\Core\File\Event\ThumbnailGenerate($thumbnailPath, $type)
        );

        if ($type->getHandle() == $config->get('concrete.icons.file_manager_listing.handle') && !$this->fvHasListingThumbnail) {
            $this->fvHasListingThumbnail = true;
            $this->save();
        }
        if ($type->getHandle() == $config->get('concrete.icons.file_manager_detail.handle') && !$this->fvHasDetailThumbnail) {
            $this->fvHasDetailThumbnail = true;
            $this->save();
        }

        unset($size);
        unset($thumbnail);
        unset($filesystem);
    }

    /**
     * Import an existing file as a thumbnail type version.
     *
     * @param \Concrete\Core\File\Image\Thumbnail\Type\Version $version
     * @param string $path
     */
    public function importThumbnail(ThumbnailTypeVersion $version, $path)
    {
        $app = Application::getFacadeApplication();
        $config = $app->make('config');
        $thumbnailPath = $version->getFilePath($this);
        $filesystem = $this->getFile()
            ->getFileStorageLocationObject()
            ->getFileSystemObject();
        try {
            if ($filesystem->has($thumbnailPath)) {
                $filesystem->delete($thumbnailPath);
            }
        } catch (FileNotFoundException $e) {
        }
        $fileContents = file_get_contents($path);
        $mimeType = Util::guessMimeType($path, $fileContents);
        $filesystem->write(
            $thumbnailPath,
            $fileContents,
            [
                'visibility' => AdapterInterface::VISIBILITY_PUBLIC,
                'mimetype' => $mimeType,
            ]
        );

        if ($version->getHandle() == $config->get('concrete.icons.file_manager_listing.handle')) {
            $this->fvHasListingThumbnail = true;
        }

        if ($version->getHandle() == $config->get('concrete.icons.file_manager_detail.handle')) {
            $this->fvHasDetailThumbnail = true;
        }

        $this->save();
    }

    /**
     * Get the URL of a thumbnail type.
     * If the thumbnail is smaller than the image (or if the file does not satisfy the Conditional Thumbnail criterias) you'll get the URL of the image itself.
     *
     * Please remark that the path is resolved using the default core path resolver: avoid using this method when you have access to the resolver instance.
     *
     * @param \Concrete\Core\File\Image\Thumbnail\Type\Version|string $type the thumbnail type version (or its handle)
     *
     * @return string|null return NULL if the thumbnail does not exist and the file storage location is invalid
     *
     * @example /application/files/thumbnails/file_manager_listing/0000/0000/0000/file.png
     */
    public function getThumbnailURL($type)
    {
        $app = Application::getFacadeApplication();

        $path = null;
        if (!($type instanceof ThumbnailTypeVersion)) {
            $type = ThumbnailTypeVersion::getByHandle($type);
        }
        if ($type !== null) {
            $imageWidth = (int) $this->getAttribute('width');
            $imageHeight = (int) $this->getAttribute('height');
            $file = $this->getFile();
            if ($type->shouldExistFor($imageWidth, $imageHeight, $file)) {
                $path_resolver = $app->make(Resolver::class);
                $path = $path_resolver->getPath($this, $type);
            }
        }
        if (!$path) {
            $url = $this->getURL();
            $path = $url ? (string) $url : null;
        }

        return $path;
    }

    /**
     * Get the list of all the thumbnails.
     *
     * @throws \Concrete\Core\File\Exception\InvalidDimensionException
     *
     * @return \Concrete\Core\File\Image\Thumbnail\Thumbnail[]
     */
    public function getThumbnails()
    {
        $thumbnails = [];
        $imageWidth = (int) $this->getAttribute('width');
        $imageHeight = (int) $this->getAttribute('height');
        if ($imageWidth < 1 || $imageHeight < 1) {
            throw new InvalidDimensionException($this->getFile(), $this, t('Invalid dimensions.'));
        }
        $types = Type::getVersionList();
        $file = $this->getFile();
        foreach ($types as $type) {
            if ($type->shouldExistFor($imageWidth, $imageHeight, $file)) {
                $thumbnailPath = $type->getFilePath($this);
                $location = $file->getFileStorageLocationObject();
                $configuration = $location->getConfigurationObject();
                $filesystem = $location->getFileSystemObject();
                if ($filesystem->has($thumbnailPath)) {
                    $thumbnails[] = new Thumbnail($type, $configuration->getPublicURLToFile($thumbnailPath));
                }
            }
        }

        return $thumbnails;
    }

    /**
     * Get the HTML that renders the thumbnail for the details (a generic type icon if the thumbnail does not exist).
     * Return the thumbnail for an image or a generic type icon for a file.
     *
     * @return string
     */
    public function getDetailThumbnailImage()
    {
        if ($this->fvHasDetailThumbnail) {
            $app = Application::getFacadeApplication();
            $config = $app->make('config');
            $type = Type::getByHandle($config->get('concrete.icons.file_manager_detail.handle'));
            $result = '<img src="' . $this->getThumbnailURL($type->getBaseVersion()) . '"';
            if ($config->get('concrete.file_manager.images.create_high_dpi_thumbnails')) {
                $result .= ' data-at2x="' . $this->getThumbnailURL($type->getDoubledVersion()) . '"';
            }
            $result .= ' />';
        } else {
            $result = $this->getTypeObject()->getThumbnail();
        }

        return $result;
    }

    /**
     * Get the HTML that renders the thumbnail for the file listing (a generic type icon if the thumbnail does not exist).
     *
     * @return string
     */
    public function getListingThumbnailImage()
    {
        if ($this->fvHasListingThumbnail) {
            $app = Application::getFacadeApplication();
            $config = $app->make('config');
            $type = Type::getByHandle($config->get('concrete.icons.file_manager_listing.handle'));
            $result = '<img class="ccm-file-manager-list-thumbnail" src="' . $this->getThumbnailURL($type->getBaseVersion()) . '"';
            if ($config->get('concrete.file_manager.images.create_high_dpi_thumbnails')) {
                $result .= '  data-at2x="' . $this->getThumbnailURL($type->getDoubledVersion()) . '"';
            }
            $result .= ' />';
        } else {
            return $this->getTypeObject()->getThumbnail();
        }

        return $result;
    }

    /**
     * Delete the thumbnail for a specific thumbnail type version.
     *
     * @param \Concrete\Core\File\Image\Thumbnail\Type\Version|string $type the thumbnail type version (or its handle)
     */
    public function deleteThumbnail($type)
    {
        $app = Application::getFacadeApplication();
        $config = $app->make('config');
        if (!($type instanceof ThumbnailTypeVersion)) {
            $type = ThumbnailTypeVersion::getByHandle($type);
        }
        $fsl = $this->getFile()->getFileStorageLocationObject()->getFileSystemObject();
        $path = $type->getFilePath($this);
        try {
            if ($fsl->has($path)) {
                $fsl->delete($path);

                $app['director']->dispatch('on_thumbnail_delete',
                    new \Concrete\Core\File\Event\ThumbnailDelete($path, $type)
                );
            }
        } catch (FileNotFoundException $e) {
        }
        if ($type->getHandle() == $config->get('concrete.icons.file_manager_listing.handle') && $this->fvHasListingThumbnail) {
            $this->fvHasListingThumbnail = false;
            $this->save();
        }
        if ($type->getHandle() == $config->get('concrete.icons.file_manager_detail.handle') && $this->fvHasDetailThumbnail) {
            $this->fvHasDetailThumbnail = false;
            $this->save();
        }
    }

    /**
     * Move the thumbnail of a specific thumbnail type version to a new storage location.
     *
     * @param \Concrete\Core\File\Image\Thumbnail\Type\Version|string $type the thumbnail type version (or its handle)
     * @param StorageLocation $location The destination storage location
     */
    public function updateThumbnailStorageLocation($type, StorageLocation $location)
    {
        if (!($type instanceof ThumbnailTypeVersion)) {
            $type = ThumbnailTypeVersion::getByHandle($type);
        }
        $fsl = $this->getFile()->getFileStorageLocationObject()->getFileSystemObject();
        $path = $type->getFilePath($this);
        $manager = new MountManager([
            'current' => $fsl,
            'new' => $location->getFileSystemObject(),
        ]);
        try {
            $manager->move('current://' . $path, 'new://' . $path);
        } catch (FileNotFoundException $e) {
        }
    }

    /**
     * Copy the thumbnail of a specific thumbnail type version from another file version (useful for instance when duplicating a file).
     *
     * @param \Concrete\Core\File\Image\Thumbnail\Type\Version|string $type the thumbnail type version (or its handle)
     * @param Version $source The File Version instance to copy the thumbnail from
     */
    public function duplicateUnderlyingThumbnailFiles($type, Version $source)
    {
        if (!($type instanceof ThumbnailTypeVersion)) {
            $type = ThumbnailTypeVersion::getByHandle($type);
        }
        $new = $this->getFile()->getFileStorageLocationObject()->getFileSystemObject();
        $current = $source->getFile()->getFileStorageLocationObject()->getFileSystemObject();
        $newPath = $type->getFilePath($this);
        $currentPath = $type->getFilePath($source);
        $manager = new MountManager([
            'current' => $current,
            'new' => $new,
        ]);
        try {
            $manager->copy('current://' . $currentPath, 'new://' . $newPath);
        } catch (FileNotFoundException $e) {
        }
    }

    /**
     * Get a representation of this Version instance that's easily serializable.
     *
     * @return stdClass A \stdClass instance with all the information about a file (including permissions)
     */
    public function getJSONObject()
    {
        $app = Application::getFacadeApplication();
        $urlResolver = $app->make(ResolverManagerInterface::class);
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
        $r->urlInline = (string) $urlResolver->resolve(['/download_file', 'view_inline', $this->getFileID()]);
        $r->urlDownload = (string) $urlResolver->resolve(['/download_file', 'view', $this->getFileID()]);
        $r->title = $this->getTitle();
        $r->genericTypeText = $this->getGenericTypeText();
        $r->description = $this->getDescription();
        $r->fileName = $this->getFileName();
        $r->resultsThumbnailImg = $this->getListingThumbnailImage();
        $r->fID = $this->getFileID();
        $r->treeNodeMenu = new Menu($this->getfile());

        return $r;
    }

    /**
     * @deprecated Use buildForceDownloadResponse
     */
    public function forceDownload()
    {
        $app = Application::getFacadeApplication();
        $response = $this->buildForceDownloadResponse();
        $response->prepare($app->make(Request::class));

        session_write_close();
        ob_end_clean();
        $response->send();
        $app->shutdown();
        exit;
    }

    /**
     * @deprecated
     *
     * @param int $level
     *
     * @return bool
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
     * @deprecated use refreshThumbnails(true) instead
     *
     * @return bool
     */
    public function rescanThumbnails()
    {
        return $this->refreshThumbnails(true);
    }

    /**
     * Save the instance changes.
     *
     * @param bool $flush Flush the EM cache?
     */
    protected function save($flush = true)
    {
        $app = Application::getFacadeApplication();
        $em = $app->make(EntityManagerInterface::class);
        $em->persist($this);
        if ($flush) {
            $em->flush();
        }
    }
}
