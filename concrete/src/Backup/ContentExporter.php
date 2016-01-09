<?php
namespace Concrete\Core\Backup;

use Concrete\Core\Page\Feed;
use Concrete\Core\Page\PageList;
use Concrete\Core\Page\Type\Composer\FormLayoutSetControl;
use Concrete\Core\Tree\Tree;
use Page;
use Package;
use Stack;
use SinglePage;
use UserInfo;
use PageType;
use BlockType;
use Config;
use Block;
use Group;
use File;
use PageTheme;
use Concrete\Core\Block\BlockType\BlockTypeList;
use Loader;
use Concrete\Core\Attribute\Key\Key as AttributeKey;
use Concrete\Core\Attribute\Set as AttributeSet;
use Concrete\Core\Package\PackageList;
use Concrete\Core\Permission\Key\Key as PermissionKey;
use Job;
use SimpleXMLElement;
use Core;
use JobSet;
use \Concrete\Core\Workflow\Type as WorkflowType;
use Concrete\Core\Page\Stack\StackList;
use PageTemplate;
use CollectionAttributeKey;
use \Concrete\Core\Block\BlockType\Set as BlockTypeSet;
use \Concrete\Core\Attribute\Type as AttributeType;
use \Concrete\Core\Attribute\Key\Category as AttributeKeyCategory;
use PermissionKeyCategory;
use \Concrete\Core\Permission\Access\Entity\Type as PermissionAccessEntityType;
use PermissionAccess;
use \Concrete\Core\Captcha\Library as SystemCaptchaLibrary;
use \Concrete\Core\Feature\Feature;
use \Concrete\Core\Feature\Category\Category as FeatureCategory;
use \Concrete\Core\Gathering\DataSource\DataSource as GatheringDataSource;
use \Concrete\Core\Gathering\Item\Template\Template as GatheringItemTemplate;
use \Concrete\Core\Page\Type\Composer\Control\Type\Type as PageTypeComposerControlType;
use \Concrete\Core\Page\Type\PublishTarget\Type\Type as PageTypePublishTargetType;
use \Concrete\Core\Conversation\Editor\Editor as ConversationEditor;
use \Concrete\Core\Conversation\Rating\Type as ConversationRatingType;
use FileImporter;
use FileList;
use ZipArchive;

class ContentExporter
{

    protected $x; // the xml object for export
    protected static $mcBlockIDs = array();
    protected static $ptComposerOutputControlIDs = array();


    /**
     * @deprecated
     */
    public function run()
    {
        $this->exportAll();
    }

    protected function getXMLRoot()
    {
        $root = new SimpleXMLElement("<concrete5-cif></concrete5-cif>");
        $root->addAttribute('version', '1.0');
        return $root;
    }

    public function getXMLNode()
    {
        return $this->x;
    }

    public function exportAll()
    {
        $this->x = $this->getXMLRoot();

        // First, attribute categories
        AttributeKeyCategory::exportList($this->x);

        // Features
        Feature::exportList($this->x);
        FeatureCategory::exportList($this->x);

        ConversationEditor::exportList($this->x);

        ConversationRatingType::exportList($this->x);

        // composer
        PageTypePublishTargetType::exportList($this->x);
        PageTypeComposerControlType::exportList($this->x);
        PageType::exportList($this->x);

        // attribute types
        AttributeType::exportList($this->x);

        // then block types
        BlockTypeList::exportList($this->x);

        // now block type sets (including user)
        BlockTypeSet::exportList($this->x);

        // gathering
        GatheringDataSource::exportList($this->x);
        GatheringItemTemplate::exportList($this->x);

        // now attribute keys (including user)
        AttributeKey::exportList($this->x);

        // now attribute keys (including user)
        AttributeSet::exportList($this->x);

        PageTemplate::exportList($this->x);

        // now theme
        PageTheme::exportList($this->x);

        // now packages
        PackageList::export($this->x);

        // permission access entity types
        PermissionAccessEntityType::exportList($this->x);

        // now task permissions
        PermissionKey::exportList($this->x);

        // workflow types
        WorkflowType::exportList($this->x);

        // now jobs

        Job::exportList($this->x);

        // now single pages
        $singlepages = $this->x->addChild("singlepages");
        $db = Loader::db();
        $r = $db->Execute('select cID from Pages where cFilename is not null and cFilename <> "" and cID not in (select cID from Stacks) order by cID asc');
        while ($row = $r->FetchRow()) {
            $pc = Page::getByID($row['cID'], 'RECENT');
            $pc->export($singlepages);
        }

        // now stacks/global areas

        StackList::export($this->x);

        // now content pages
        $this->exportPages($this->x);

        SystemCaptchaLibrary::exportList($this->x);

        \Concrete\Core\Sharing\SocialNetwork\Link::exportList($this->x);

        \Concrete\Core\Page\Feed::exportList($this->x);

        \Concrete\Core\File\Image\Thumbnail\Type\Type::exportList($this->x);

        Tree::exportList($this->x);

    }

    public function exportPages($xml = null, PageList $pl = null)
    {
        if (!$xml) {
            $this->x = $this->getXMLRoot();
        }
        $node = $this->x->addChild("pages");
        if (!$pl) {
            $pl = new PageList();
        }
        $pl->ignorePermissions();
        $pl->getQueryObject()->andWhere("cFilename is null or cFilename = ''");
        $pages = $pl->getResults();
        foreach ($pages as $pc) {
            $pc->export($node);
        }
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

    public function output()
    {
        $xml = $this->x->asXML();

        // remove crappy characters
        return preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '', $xml);
    }

    public function getFilesArchive()
    {

        $vh = Loader::helper("validation/identifier");
        $archive = $vh->getString();

        $fl = new FileList();
        $files = $fl->getResults();
        $fh = Loader::helper('file');
        $filename = $fh->getTemporaryDirectory() . '/' . $archive . '.zip';
        if (count($files) > 0) {
            try {
                $zip = new ZipArchive;
                $res = $zip->open($filename, ZipArchive::CREATE);
                if ($res === true) {
                    foreach ($files as $f) {
                        $zip->addFromString($f->getFilename(), $f->getFileContents());
                    }
                    $zip->close();
                    $fh->forceDownload($filename);
                } else {
                    throw new Exception(t('Could not open with ZipArchive::CREATE'));
                }
            } catch (Exception $e) {
                throw new Exception(t('Failed to create zip file as "%s": %s', $filename, $e->getMessage()));
            }
        }

        return $archive;
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
                return '{ccm:export:file:' . $f->getFileName() . '}';
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

    public static function replacePageFeedWithPlaceholder($pfID)
    {
        if ($pfID > 0) {
            $pf = Feed::getByID($pfID);
            return '{ccm:export:pagefeed:' . $pf->getHandle() . '}';
        }
    }

    /**
     * Removes an item from the export xml registry
     */
    public function removeItem($parent, $node, $handle)
    {
        $query = '//' . $node . '[@handle=\'' . $handle . '\' or @package=\'' . $handle . '\']';
        $r = $this->x->xpath($query);
        if ($r && isset($r[0]) && $r[0] instanceof SimpleXMLElement) {
            $dom = dom_import_simplexml($r[0]);
            $dom->parentNode->removeChild($dom);
        }

        $query = '//' . $parent;
        $r = $this->x->xpath($query);
        if ($r && isset($r[0]) && $r[0] instanceof SimpleXMLElement) {
            $dom = dom_import_simplexml($r[0]);
            if ($dom->childNodes->length < 1) {
                $dom->parentNode->removeChild($dom);
            }
        }
    }


}
