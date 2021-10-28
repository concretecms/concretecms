<?php
namespace Concrete\Core\Backup;

use Block;
use Concrete\Core\Page\Feed;
use Concrete\Core\Page\Type\Composer\FormLayoutSetControl;
use Concrete\Core\Tree\Node\Type\FileFolder;
use File;
use FileList;
use Job;
use Loader;
use Package;
use Page;
use PageTemplate;
use PageTheme;
use PageType;

class ContentExporter
{
    protected static $mcBlockIDs = array();
    protected static $ptComposerOutputControlIDs = array();

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

    public static function replacePageWithPlaceHolder($cID)
    {
        if ($cID > 0) {
            $c = Page::getByID($cID);

            return '{ccm:export:page:' . $c->getCollectionPath() . '}';
        }
    }

    public static function replaceFileWithPlaceHolder($fID)
    {
        if ($fID > 0) {
            $f = File::getByID($fID);
            if (is_object($f)) {
                return '{ccm:export:file:' . $f->getPrefix() . ':' . $f->getFileName() . '}';
            }
        }
    }

    public static function replacePageWithPlaceHolderInMatch($cID)
    {
        if ($cID[1] > 0) {
            $cID = $cID[1];

            return self::replacePageWithPlaceHolder($cID);
        }
    }

    public static function replaceFileWithPlaceHolderInMatch($fID)
    {
        if ($fID[1] > 0) {
            $fID = $fID[1];

            return self::replaceFileWithPlaceHolder($fID);
        }
    }

    public static function replacePageTypeWithPlaceHolder($ptID)
    {
        if ($ptID > 0) {
            $ct = PageType::getByID($ptID);

            return '{ccm:export:pagetype:' . $ct->getPageTypeHandle() . '}';
        }
    }

    public static function replaceFileFolderWithPlaceHolder($treeNodeID)
    {
        if ($treeNodeID > 0) {
            $folder = FileFolder::getByID($treeNodeID);

            return '{ccm:export:filefolder:' . $folder->getTreeNodeDisplayPath() . '}';
        }
    }


    public static function replacePageFeedWithPlaceholder($pfID)
    {
        if ($pfID > 0) {
            $pf = Feed::getByID($pfID);

            return '{ccm:export:pagefeed:' . $pf->getHandle() . '}';
        }
    }
    
}
