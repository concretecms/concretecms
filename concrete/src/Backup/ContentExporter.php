<?php
namespace Concrete\Core\Backup;

use Concrete\Core\Entity\File\File as FileEntity;
use Concrete\Core\File\File;
use Concrete\Core\Page\Feed;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\Composer\FormLayoutSetControl;
use Concrete\Core\Page\Type\Type as PageType;
use Concrete\Core\Tree\Node\Type\FileFolder;

class ContentExporter
{
    protected static $mcBlockIDs = [];
    protected static $ptComposerOutputControlIDs = [];

    /**
     * @var \Concrete\Core\Backup\ContentExporterOptions|null
     */
    private static $options = null;

    /**
     * Get the options driving the export.
     * If they haven't been set with setOptions(), they are automatically detected from the current request.
     */
    public static function getOptions(): ContentExporterOptions
    {
        if (self::$options === null) {
            self::$options = app(ContentExporterOptions::class);
        }

        return self::$options;
    }

    /**
     * Set the options driving the export, overriding the ones automatically detected from the current request.
     */
    public static function setOptions(ContentExporterOptions $value): void
    {
        self::$options = $value;
    }

    /**
     * @deprecated
     */
    public function run()
    {
        $this->exportAll();
    }

    /**
     * @deprecated
     */
    public function exportAll()
    {
        throw new \Exception(t('Internal content exporter no longer supported. Use the Migration Tool instead.'));
    }

    public static function addMasterCollectionBlockID($b, $id)
    {
        self::$mcBlockIDs[$b->getBlockID()] = $id;
    }

    public static function getMasterCollectionTemporaryBlockID($b)
    {
        if (isset(self::$mcBlockIDs[$b->getBlockID()])) {
            return self::$mcBlockIDs[$b->getBlockID()];
        }
    }

    public static function addPageTypeComposerOutputControlID(FormLayoutSetControl $control, $id)
    {
        self::$ptComposerOutputControlIDs[$control->getPageTypeComposerFormLayoutSetControlID()] = $id;
    }

    public static function getPageTypeComposerOutputControlTemporaryID(FormLayoutSetControl $control)
    {
        if (isset(self::$ptComposerOutputControlIDs[$control->getPageTypeComposerFormLayoutSetControlID()])) {
            return self::$ptComposerOutputControlIDs[$control->getPageTypeComposerFormLayoutSetControlID()];
        }
    }

    /**
     * @param int|string|mixed $cID the ID of the page
     *
     * @return string|null
     *
     * @example {ccm:export:page:/path/to/page}
     */
    public static function replacePageWithPlaceHolder($cID)
    {
        if (!is_numeric($cID) || ($cID = (int) $cID) <= 0) {
            return null;
        }
        $c = Page::getByID($cID);
        if (!$c || $c->isError()) {
            return null;
        }

        return static::getOptions()->isExportIDs() ? "{ccm:export:page::id={$c->getCollectionID()}}" : "{ccm:export:page:{$c->getCollectionPath()}}";
    }

    /**
     * @param int|string|mixed $fID the ID or the UUID of the file
     *
     * @return string|null
     *
     * @example {ccm:export:file:123456789012:atomik-logo.png}
     */
    public static function replaceFileWithPlaceHolder($fID)
    {
        if (uuid_is_valid((string) $fID)) {
            $f = File::getByUUID($fID);
        } elseif (is_numeric($fID) && ($fID = (int) $fID) > 0) {
            $f = File::getByID($fID);
        } else {
            return null;
        }
        $fv = $f ? $f->getApprovedVersion() : null;
        if (!$fv) {
            return null;
        }

        $options = static::getOptions();
        if ($options->isExportIDs()) {
            $identifier = static::getFileIdentifier($f);

            return "{ccm:export:file::id={$identifier}}";
        }
        return $options->isExportFilesWithoutPrefix() ? "{ccm:export:file:{$fv->getFileName()}}" : "{ccm:export:file:{$fv->getPrefix()}:{$fv->getFileName()}}";
    }

    /**
     * Get the identifier to be used when exporting a reference to a file: its UUID if the
     * isExportFilesAsUUID() option is turned on and the file has one, its ID otherwise.
     *
     * @return int|string
     */
    public static function getFileIdentifier(FileEntity $file)
    {
        if (static::getOptions()->isExportFilesAsUUID() && $file->hasFileUUID()) {
            return $file->getFileUUID();
        }

        return $file->getFileID();
    }

    /**
     * @param array|int[]|string[] $cID
     *
     * @return string|null
     *
     * @example {ccm:export:page:/path/to/page}
     */
    public static function replacePageWithPlaceHolderInMatch($cID)
    {
        return empty($cID[1]) ? null : self::replacePageWithPlaceHolder($cID[1]);
    }

    /**
     * @param array|int[]|string[] $fID
     *
     * @return string|null
     *
     * @example {ccm:export:file:123456789012:atomik-logo.png}
     */
    public static function replaceFileWithPlaceHolderInMatch($fID)
    {
        return empty($fID[1]) ? null : self::replaceFileWithPlaceHolder($fID[1]);
    }

    /**
     * @param int|string|mixed $ptID the ID of the page type
     *
     * @return string|null
     *
     * @example {ccm:export:pagetype:blog_entry}
     */
    public static function replacePageTypeWithPlaceHolder($ptID)
    {
        if (!is_numeric($ptID) || ($ptID = (int) $ptID) <= 0) {
            return null;
        }
        $ct = PageType::getByID($ptID);
        if (!$ct) {
            return null;
        }

        return static::getOptions()->isExportIDs() ? "{ccm:export:pagetype::id={$ct->getPageTypeID()}}" : "{ccm:export:pagetype:{$ct->getPageTypeHandle()}}";
    }

    /**
     * @param int|string|mixed $treeNodeID the ID of the file folder
     *
     * @return string|null
     *
     * @example {ccm:export:filefolder:/Documents}
     */
    public static function replaceFileFolderWithPlaceHolder($treeNodeID)
    {
        if (!is_numeric($treeNodeID) || ($treeNodeID = (int) $treeNodeID) <= 0) {
            return null;
        }
        $folder = FileFolder::getByID($treeNodeID);
        if (!$folder) {
            return null;
        }

        return static::getOptions()->isExportIDs() ? "{ccm:export:filefolder::id={$folder->getTreeNodeID()}}" : "{ccm:export:filefolder:{$folder->getTreeNodeDisplayPath()}}";
    }

    /**
     * @param int|string|mixed $pfID the ID of the page feed
     *
     * @return string|null
     *
     * @example {ccm:export:pagefeed:blog}
     */
    public static function replacePageFeedWithPlaceholder($pfID)
    {
        if (!is_numeric($pfID) || ($pfID = (int) $pfID) <= 0) {
            return null;
        }
        $pf = Feed::getByID($pfID);
        if (!$pf) {
            return null;
        }

        return static::getOptions()->isExportIDs() ? "{ccm:export:pagefeed::id={$pf->getID()}}" : "{ccm:export:pagefeed:{$pf->getHandle()}}";
    }
}
