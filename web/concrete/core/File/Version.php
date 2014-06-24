<?
namespace Concrete\Core\File;

use Carbon\Carbon;
use \Concrete\Core\Foundation\Object;
use League\Flysystem\AdapterInterface;
use Loader;
use \File as ConcreteFile;
use \Concrete\Core\File\Type\TypeList as FileTypeList;
use FileAttributeKey;
use \Concrete\Core\Attribute\Value\FileValue as FileAttributeValue;
use stdClass;
use Permissions;
use User;
use View;
use Page;
use Events;
use Core;

/**
 * @Entity
 * @Table(name="FileVersions")
 */
class Version
{

    /**
    /* @Id
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
    protected $fvHasThumbnail1 = false;

    /**
     * @Column(type="boolean")
     */
    protected $fvHasThumbnail2 = false;

    /**
     * @Column(type="boolean")
     */
    protected $fvHasThumbnail3 = false;

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

        $db = Loader::db();
        $dh = Loader::helper('date');
        $date = new Carbon($dh->getSystemDateTime());

        $fv = new static();
        $fv->fvFilename = $filename;
        $fv->fvPrefix = $prefix;
        $fv->fvDateAdded = $date;
        $fv->fvIsApproved = (bool)$fvIsApproved;
        $fv->fvApproverUID = $uID;
        $fv->fvAuthorUID = $uID;
        $fv->fvActivateDateTime = $date;
        $fv->fvTitle = $fvTitle;
        $fv->fvTags = $fvTags;
        $fv->file = $file;
        $fv->fvID = 1;

        $em = Loader::db()->getEntityManager();
        $em->persist($fv);
        $em->flush();

        $fve = new \Concrete\Core\File\Event\FileVersion($fv);
        Events::dispatch('on_file_version_add', $fve);

        return $fv;
    }


    public function getFileID()
    {
        return $this->file->getFileID();
    }

    public function getFileVersionID()
    {
        return $this->fvID;
    }

    protected $attributes = array();


    // Update type constants
    const UT_REPLACE_FILE = 1;
    const UT_TITLE = 2;
    const UT_DESCRIPTION = 3;
    const UT_TAGS = 4;
    const UT_EXTENDED_ATTRIBUTE = 5;

    public function getPrefix()
    {
        return $this->fvPrefix;
    }

    public function getFileName()
    {
        return $this->fvFilename;
    }

    public function getTitle()
    {
        return $this->fvTitle;
    }

    public function getTags()
    {
        return $this->fvTags;
    }

    public function getDescription()
    {
        return $this->fvDescription;
    }

    public function isApproved()
    {
        return $this->fvIsApproved;
    }

    public function getGenericTypeText()
    {
        $to = $this->getTypeObject();
        return $to->getGenericTypeText($to->getGenericType());
    }

    /**
     * returns the File object associated with this FileVersion object
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    //returns an array of tags, instead of a string
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

    public function clearAttribute($ak)
    {
        $db = Loader::db();
        $cav = $this->getAttributeValueObject($ak);
        if (is_object($cav)) {
            $cav->delete();
        }
        $fo = $this->getFile();
        $fo->reindex();
    }

    public function getAttributeValueObject($ak, $createIfNotFound = false)
    {
        $db = Loader::db();
        $av = false;
        $v = array($this->getFileID(), $this->getFileVersionID(), $ak->getAttributeKeyID());
        $avID = $db->GetOne("select avID from FileAttributeValues where fID = ? and fvID = ? and akID = ?", $v);
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
                    "select count(avID) from FileAttributeValues where avID = ?",
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

    /**
     * Gets an associative array of all attributes for a file version
     */
    public function getAttributeList()
    {
        $attributes = FileAttributeKey::getAttributes($this->fID, $this->fvID);
        return $attributes;
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


    public function getMimeType()
    {
        $fre = $this->getFileResource();
        return $fre->getMimetype();
    }

    public function getSize()
    {
        return Loader::helper('number')->formatSize($this->fvSize, 'KB');
    }

    public function getFullSize()
    {
        return $this->fvSize;
    }

    public function getAuthorName()
    {
        return $this->fvAuthorName;
    }

    public function getAuthorUserID()
    {
        return $this->fvAuthorUID;
    }

    /**
     * Gets the date a file version was added
     * if user is specified, returns in the current user's timezone
     * @param string $type (system || user)
     * @return string date formated like: 2009-01-01 00:00:00
     */
    function getDateAdded($type = 'system')
    {
        if (ENABLE_USER_TIMEZONES && $type == 'user') {
            $dh = Loader::helper('date');
            return $dh->getLocalDateTime($this->fvDateAdded);
        } else {
            return $this->fvDateAdded;
        }
    }

    public function getExtension()
    {
        return $this->fvExtension;
    }

    public function logVersionUpdate($updateTypeID, $updateTypeAttributeID = 0)
    {
        $db = Loader::db();
        $db->Execute(
            'insert into FileVersionLog (fID, fvID, fvUpdateTypeID, fvUpdateTypeAttributeID) values (?, ?, ?, ?)',
            array(
                $this->getFileID(),
                $this->getFileVersionID(),
                $updateTypeID,
                $updateTypeAttributeID
            )
        );
    }

    protected function save($flush = true)
    {
        $em = Loader::db()->getEntityManager();
        $em->persist($this);
        if ($flush) {
            $em->flush();
        }
    }

    /**
     * Takes the current value of the file version and makes a new one with the same values
     */
    public function duplicate()
    {

        $db = Loader::db();
        $em = $db->getEntityManager();
        $qq = $em->createQuery('select max(v.fvID) from \Concrete\Core\File\Version v where v.file = :file');
        $qq->setParameter('file', $this->file);
        $fvID = $qq->getSingleScalarResult();
        $fvID++;

        $fv = clone $this;
        $fv->fvID = $fvID;
        $fv->fvIsApproved = false;

        $em->persist($fv);
        $em->flush();

        $this->deny();

        $r = $db->Execute(
            'select fvID, akID, avID from FileAttributeValues where fID = ? and fvID = ?',
            array($this->getFileID(), $this->fvID)
        );
        while ($row = $r->fetchRow()) {
            $db->Execute(
                "insert into FileAttributeValues (fID, fvID, akID, avID) values (?, ?, ?, ?)",
                array(
                    $this->fID,
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


    public function getType()
    {
        $ftl = $this->getTypeObject();
        if (is_object($ftl)) {
            return $ftl->getName();
        }
    }

    public function getTypeObject()
    {
        $fh = Loader::helper('file');
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
        $db = Loader::db();
        $ga = $db->GetAll(
            'select fvUpdateTypeID, fvUpdateTypeAttributeID from FileVersionLog where fID = ? and fvID = ? order by fvlID asc',
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
                case self::UT_EXTENDED_ATTRIBUTE:
                    $val = $db->GetOne(
                        "select akName from AttributeKeys where akID = ?",
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

    public function updateTags($tags)
    {
        $tags = self::cleanTags($tags);
        $this->fvTags = $tags;
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
    }


    public function deny()
    {
        $this->fvIsApproved = false;
        $this->save();
        $fe = new \Concrete\Core\File\Event\FileVersion($this);
        Events::dispatch('on_file_version_deny', $fe);
    }


    /**
     * Removes a version of a file. Note, does NOT remove the file because we don't know where the file might elsewhere be used/referenced.
     */
    public function delete()
    {

        $db = Loader::db();
        $em = $db->getEntityManager();
        $em->remove($this);
        $em->flush();

        $db->Execute("delete from FileAttributeValues where fID = ? and fvID = ?", array($this->fID, $this->fvID));
        $db->Execute("delete from FileVersionLog where fID = ? and fvID = ?", array($this->fID, $this->fvID));

        foreach (array(1, 2, 3) as $level) {
            if ($this->{"fvHasThumbnail{$level}"}) {
                $this->deleteThumbnail($level);
            }
        }

        $fsl = $this->getFile()->getFileStorageLocationObject()->getFileSystemObject();
        $fre = $this->getFileResource();
        $fsl->delete($fre->getPath());
    }

    /**
     * Deletes the thumbnail for the particular level.
     */
    public function deleteThumbnail($level)
    {
        $fsl = $this->getFile()->getFileStorageLocationObject()->getFileSystemObject();
        $fh = Loader::helper('concrete/file');
        $path = $fh->getThumbnailFilePath($this->getPrefix(), $this->getFilename(), $level);
        if ($path) {
            $fsl->delete($path);
        }
    }

    /**
     * Returns an abstracted File object for the resource. NOT a concrete5 file object.
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
     * Returns a URL that can be used to download the file. This passes through the download_file single page.
     */
    public function getDownloadURL()
    {
        $c = Page::getCurrentPage();
        $cID = ($c instanceof Page) ? $c->getCollectionID() : 0;
        return BASE_URL . View::url('/download_file', $this->getFileID(), $cID);
    }

    /**
     * Returns a url that can be used to download a file, will force the download of all file types, even if your browser can display them.
     */
    public function getForceDownloadURL()
    {
        $c = Page::getCurrentPage();
        $cID = ($c instanceof Page) ? $c->getCollectionID() : 0;
        return BASE_URL . View::url('/download_file', 'force', $this->getFileID(), $cID);
    }

    /**
     * Forces the download of a file.
     * @return void
     */
    public function forceDownload()
    {
        session_write_close();
        $fre = $this->getFileResource();
        ob_clean();
        header('Content-type: application/octet-stream');
        header("Content-Disposition: attachment; filename=\"" . $this->getFilename() . "\"");
        header('Content-Length: ' . $fre->getSize());
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false);
        header("Content-Transfer-Encoding: binary");
        header("Content-Encoding: plainbinary");

        $fs = $this->getFile()->getFileStorageLocationObject()->getFileSystemObject();

        $stream = $fs->readStream($fre->getPath());
        $contents = stream_get_contents($stream);
        fclose($stream);

        print $contents;
        exit;
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

    public function getThumbnailURL($level)
    {
        if ($this->{"fvHasThumbnail{$level}"}) {
            $fsl = $this->getFile()->getFileStorageLocationObject();
            if ($fsl) {
                $configuration = $fsl->getConfigurationObject();
                $f = Loader::helper('concrete/file');
                $path = $f->getThumbnailFilePath($this->getPrefix(), $this->getFilename(), $level);
                return $configuration->getPublicURLToFile($path);
            }
        }
    }

    public function rescanThumbnail($level)
    {

        $fr = $this->getFileResource();

        // delete the file if it exists
        if ($this->hasThumbnail($level)) {
            $this->deleteThumbnail($level);
        }

        $image = \Image::load($fr->read());

        $filesystem = $this->getFile()
            ->getFileStorageLocationObject()
            ->getFileSystemObject();

        switch ($level) {
            case 1:
                $width = AL_THUMBNAIL_WIDTH;
                $height = AL_THUMBNAIL_HEIGHT;
                break;
            case 2:
                $width = AL_THUMBNAIL_WIDTH_LEVEL2;
                $height = AL_THUMBNAIL_HEIGHT_LEVEL2;
                break;
            case 3:
                $width = AL_THUMBNAIL_WIDTH_LEVEL3;
                $height = AL_THUMBNAIL_HEIGHT_LEVEL3;
        }

        $helper = Loader::helper('concrete/file');

        $thumbnail = $image->thumbnail(new \Imagine\Image\Box($width, $height));
        $thumbnailPath = $helper->getThumbnailFilePath($this->getPrefix(), $this->getFilename(), $level);

        $o = new stdClass;
        $o->visibility = AdapterInterface::VISIBILITY_PUBLIC;
        $o->mimetype = 'image/jpeg';

        if ($filesystem->has($thumbnailPath)) {
            $filesystem->delete($thumbnailPath);
        }

        $filesystem->write(
            $thumbnailPath,
            $thumbnail,
            array(
                'visibility' => AdapterInterface::VISIBILITY_PUBLIC,
                'mimetype' => 'image/jpeg'
            )
        );

        switch ($level) {
            case 1:
                $this->fvHasThumbnail1 = true;
                break;
            case 2:
                $this->fvHasThumbnail2 = true;
                break;
            case 3:
                $this->fvHasThumbnail3 = true;
                break;
        }

        $this->save();
    }

    public function hasThumbnail($level)
    {
        switch ($level) {
            case 1:
                return $this->fvHasThumbnail1;
            case 2:
                return $this->fvHasThumbnail2;
            case 3:
                return $this->fvHasThumbnail3;
        }
    }

    public function getThumbnail($level, $fullImageTag = true)
    {
        $html = Loader::helper('html');
        if ($this->hasThumbnail($level)) {
            if ($fullImageTag) {
                return $html->image($this->getThumbnailURL($level));
            } else {
                return $this->getThumbnailURL($level);
            }
        } else {
            $ft = FileTypeList::getType($this->fvFilename);
            return $ft->getThumbnail($level, $fullImageTag);
        }
    }

    /**
     * Responsible for taking a particular version of a file and rescanning all its attributes
     * This will run any type-based import routines, and store those attributes, generate thumbnails,
     * etc...
     */
    public function refreshAttributes($firstRun = false)
    {
        $fh = Loader::helper('file');
        $ext = $fh->getExtension($this->fvFilename);
        $ftl = FileTypeList::getType($ext);
        $db = Loader::db();

        $fsr = $this->getFileResource();
        if (!$fsr->isFile()) {
            return Importer::E_FILE_INVALID;
        }

        $size = $fsr->getSize();

        $title = ($firstRun) ? $this->getFilename() : $this->getTitle();

        $this->fvExtension = $ext;
        $this->fvType = $ftl->getGenericType();
        $this->fvTitle = $title;
        $this->fvSize = $size;

        if (is_object($ftl)) {
            if ($ftl->getCustomImporter() != false) {
                $this->fvGenericType = $ftl->getGenericType();
                $cl = $ftl->getCustomInspector();
                $cl->inspect($this);
            }
        }

        $this->save();

        $f = $this->getFile();
        $f->reindex();
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


    //takes a string of comma or new line delimited tags, and puts them in the appropriate format
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

    /**
     * Return a representation of the current FileVersion object as something easily serializable.
     */
    public function getJSONObject()
    {
        $ats = $this->getAttributeList();
        $r = new stdClass;
        $fp = new Permissions($this->getFile());
        $r->canCopyFile = $fp->canCopyFile();
        $r->canEditFilePermissions = $fp->canEditFilePermissions();
        $r->canDeleteFile = $fp->canDeleteFile();
        $r->canReplaceFile = $fp->canEditFileContents();
        $r->canViewFile = $this->canView();
        $r->canEditFile = $this->canEdit();
        $r->url = $this->getURL();
        $r->urlInline = View::url('/download_file', 'view_inline', $this->getFileID());
        $r->urlDownload = View::url('/download_file', 'view', $this->getFileID());
        $r->title = $this->getTitle();
        $r->description = $this->getDescription();
        $r->fileName = $this->getFilename();
        $r->resultsThumbnail = $this->getThumbnail(1, false);
        $r->thumbnailLevel1 = $this->getThumbnailURL(1);
        $r->thumbnailLevel2 = $this->getThumbnailURL(2);
        $r->thumbnailLevel3 = $this->getThumbnailURL(3);
        $r->fID = $this->getFileID();
        foreach ($ats as $key => $value) {
            $r->{$key} = $value;
        }
        return $r;
    }
}
