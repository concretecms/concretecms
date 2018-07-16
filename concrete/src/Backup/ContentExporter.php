<?php
namespace Concrete\Core\Backup;

use Concrete\Core\Page\Feed;
use Concrete\Core\Page\PageList;
use Concrete\Core\Page\Type\Composer\FormLayoutSetControl;
use Concrete\Core\Tree\Tree;
use Page;
use Package;
use PageType;
use Block;
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
use Concrete\Core\Workflow\Type as WorkflowType;
use Concrete\Core\Page\Stack\StackList;
use PageTemplate;
use Concrete\Core\Block\BlockType\Set as BlockTypeSet;
use Concrete\Core\Attribute\Type as AttributeType;
use Concrete\Core\Attribute\Key\Category as AttributeKeyCategory;
use Concrete\Core\Permission\Access\Entity\Type as PermissionAccessEntityType;
use Concrete\Core\Captcha\Library as SystemCaptchaLibrary;
use Concrete\Core\Feature\Feature;
use Concrete\Core\Feature\Category\Category as FeatureCategory;
use Concrete\Core\Gathering\DataSource\DataSource as GatheringDataSource;
use Concrete\Core\Gathering\Item\Template\Template as GatheringItemTemplate;
use Concrete\Core\Page\Type\Composer\Control\Type\Type as PageTypeComposerControlType;
use Concrete\Core\Page\Type\PublishTarget\Type\Type as PageTypePublishTargetType;
use Concrete\Core\Conversation\Editor\Editor as ConversationEditor;
use Concrete\Core\Conversation\Rating\Type as ConversationRatingType;
use FileList;
use ZipArchive;

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

    public static function replacePageFeedWithPlaceholder($pfID)
    {
        if ($pfID > 0) {
            $pf = Feed::getByID($pfID);

            return '{ccm:export:pagefeed:' . $pf->getHandle() . '}';
        }
    }
    
}
