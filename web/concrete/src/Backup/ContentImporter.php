<?php
namespace Concrete\Core\Backup;

use Concrete\Core\Page\Feed;
use Concrete\Core\Sharing\SocialNetwork\Link;
use Concrete\Core\Tree\Tree;
use Page;
use Package;
use Stack;
use SinglePage;
use UserInfo;
use PageType;
use BlockType;
use Block;
use Group;
use PageTheme;
use Loader;
use Job;
use Core;
use JobSet;
use PageTemplate;
use CollectionAttributeKey;
use Concrete\Core\StyleCustomizer\Inline\StyleSet;
use \Concrete\Core\Block\BlockType\Set as BlockTypeSet;
use \Concrete\Core\Attribute\Type as AttributeType;
use \Concrete\Core\Attribute\Key\Category as AttributeKeyCategory;
use PermissionKeyCategory;
use \Concrete\Core\Permission\Access\Entity\Type as PermissionAccessEntityType;
use \Concrete\Core\Workflow\Progress\Category as WorkflowProgressCategory;
use \Concrete\Core\Permission\Access\Entity\GroupEntity as GroupPermissionAccessEntity;
use PermissionAccess;
use \Concrete\Core\Captcha\Library as SystemCaptchaLibrary;
use \Concrete\Core\Editor\Snippet as SystemContentEditorSnippet;
use \Concrete\Core\Feature\Feature;
use \Concrete\Core\Feature\Category\Category as FeatureCategory;
use \Concrete\Core\Gathering\DataSource\DataSource as GatheringDataSource;
use \Concrete\Core\Gathering\Item\Template\Template as GatheringItemTemplate;
use \Concrete\Core\Gathering\Item\Template\Type as GatheringItemTemplateType;
use \Concrete\Core\Page\Type\Composer\Control\Type\Type as PageTypeComposerControlType;
use \Concrete\Core\Page\Type\PublishTarget\Type\Type as PageTypePublishTargetType;
use \Concrete\Core\Conversation\Editor\Editor as ConversationEditor;
use \Concrete\Core\Conversation\Rating\Type as ConversationRatingType;
use \Concrete\Core\ImageEditor\ControlSet as SystemImageEditorControlSet;
use \Concrete\Core\ImageEditor\Filter as SystemImageEditorFilter;
use \Concrete\Core\ImageEditor\Component as SystemImageEditorComponent;
use \Concrete\Core\Conversation\FlagType\FlagType as ConversationFlagType;
use \Concrete\Core\Validation\BannedWord\BannedWord as BannedWord;
use FileImporter;
use \Concrete\Core\Page\Type\Composer\FormLayoutSetControl as PageTypeComposerFormLayoutSetControl;

class ContentImporter
{

    protected static $mcBlockIDs = array();
    protected static $ptComposerOutputControlIDs = array();

    public function importContentFile($file)
    {
        $sx = simplexml_load_file($file);
        $this->doImport($sx);
    }

    public function importContentString($string)
    {
        $sx = simplexml_load_string($string);
        $this->doImport($sx);
    }

    protected function doImport($sx)
    {
        $this->importSinglePageStructure($sx);
        $this->importStacksStructure($sx);
        $this->importBlockTypes($sx);
        $this->importBlockTypeSets($sx);
        $this->importConversationEditors($sx);
        $this->importConversationRatingTypes($sx);
        $this->importConversationFlagTypes($sx);
        $this->importPageTypePublishTargetTypes($sx);
        $this->importPageTypeComposerControlTypes($sx);
        $this->importBannedWords($sx);
        $this->importSocialLinks($sx);
        $this->importTrees($sx);
        $this->importFileImportantThumbnailTypes($sx);
        $this->importFeatures($sx);
        $this->importFeatureCategories($sx);
        $this->importGatheringDataSources($sx);
        $this->importGatheringItemTemplateTypes($sx);
        $this->importGatheringItemTemplates($sx);
        $this->importAttributeCategories($sx);
        $this->importAttributeTypes($sx);
        $this->importWorkflowTypes($sx);
        $this->importWorkflowProgressCategories($sx);
        $this->importAttributes($sx);
        $this->importAttributeSets($sx);
        $this->importThemes($sx);
        $this->importPermissionCategories($sx);
        $this->importPermissionAccessEntityTypes($sx);
        $this->importTaskPermissions($sx);
        $this->importPermissions($sx);
        $this->importJobs($sx);
        $this->importJobSets($sx);
        $this->importImageEditorControlSets($sx);
        $this->importImageEditorComponents($sx);
        $this->importImageEditorFilters($sx);
        // import bare page types first, then import structure, then page types blocks, attributes and composer settings, then page content, because we need the structure for certain attributes and stuff set in master collections (like composer)
        $this->importPageTemplates($sx);
        $this->importPageTypesBase($sx);
        $this->importPageStructure($sx);
        $this->importPageFeeds($sx);
        $this->importPageTypeTargets($sx);
        $this->importPageTypeDefaults($sx);
        $this->importSinglePageContent($sx);
        $this->importStacksContent($sx);
        $this->importPageContent($sx);
        $this->importPackages($sx);
        $this->importConfigValues($sx);
        $this->importSystemCaptchaLibraries($sx);
        $this->importSystemContentEditorSnippets($sx);
    }

    protected static function getPackageObject($pkgHandle)
    {
        $pkg = false;
        if ($pkgHandle) {
            $pkg = Package::getByHandle($pkgHandle);
        }
        return $pkg;
    }

    protected function importStacksStructure(\SimpleXMLElement $sx)
    {
        if (isset($sx->stacks)) {
            foreach ($sx->stacks->stack as $p) {
                if (isset($p['type'])) {
                    $type = Stack::mapImportTextToType($p['type']);
                    Stack::addStack($p['name'], $type);
                } else {
                    Stack::addStack($p['name']);
                }
            }
        }
    }

    protected function importStacksContent(\SimpleXMLElement $sx)
    {
        if (isset($sx->stacks)) {
            foreach ($sx->stacks->stack as $p) {
                $stack = Stack::getByName($p['name']);
                if (isset($p->area)) {
                    $this->importPageAreas($stack, $p);
                }
            }
        }
    }

    protected function importSinglePageStructure(\SimpleXMLElement $sx)
    {
        if (isset($sx->singlepages)) {
            foreach ($sx->singlepages->page as $p) {
                $pkg = static::getPackageObject($p['package']);
                $spl = SinglePage::add($p['path'], $pkg);
                if (is_object($spl)) {
                    if (isset($p['root']) && $p['root'] == true) {
                        $spl->moveToRoot();
                    }
                    if ($p['name']) {
                        $spl->update(array('cName' => $p['name'], 'cDescription' => $p['description']));
                    }
                }
            }
        }
    }

    protected function importSinglePageContent(\SimpleXMLElement $sx)
    {
        if (isset($sx->singlepages)) {
            foreach ($sx->singlepages->page as $px) {
                $page = Page::getByPath($px['path'], 'RECENT');
                if (isset($px->area)) {
                    $this->importPageAreas($page, $px);
                }
                if (isset($px->attributes)) {
                    foreach ($px->attributes->children() as $attr) {
                        $ak = CollectionAttributeKey::getByHandle($attr['handle']);
                        if (is_object($ak)) {
                            $page->setAttribute((string)$attr['handle'], $ak->getController()->importValue($attr));
                        }
                    }
                }
            }
        }
    }

    protected function setupPageNodeOrder($pageNodeA, $pageNodeB)
    {
        $pathA = (string)$pageNodeA['path'];
        $pathB = (string)$pageNodeB['path'];
        $numA = count(explode('/', $pathA));
        $numB = count(explode('/', $pathB));
        if ($numA == $numB) {
            if (intval($pageNodeA->originalPos) < intval($pageNodeB->originalPos)) {
                return -1;
            } else {
                if (intval($pageNodeA->originalPos) > intval($pageNodeB->originalPos)) {
                    return 1;
                } else {
                    return 0;
                }
            }
        } else {
            return ($numA < $numB) ? -1 : 1;
        }
    }

    protected function importPageContent(\SimpleXMLElement $sx)
    {
        if (isset($sx->pages)) {
            foreach ($sx->pages->page as $px) {
                if ($px['path'] != '') {
                    $page = Page::getByPath($px['path'], 'RECENT');
                } else {
                    $page = Page::getByID(HOME_CID, 'RECENT');
                }
                if (isset($px->area)) {
                    $this->importPageAreas($page, $px);
                }
                if (isset($px->attributes)) {
                    foreach ($px->attributes->children() as $attr) {
                        $ak = CollectionAttributeKey::getByHandle($attr['handle']);
                        if (is_object($ak)) {
                            $page->setAttribute((string)$attr['handle'], $ak->getController()->importValue($attr));
                        }
                    }
                }
                $page->reindex();
            }
        }
    }

    protected function importPageStructure(\SimpleXMLElement $sx)
    {
        if (isset($sx->pages)) {
            $nodes = array();
            $i = 0;
            foreach ($sx->pages->page as $p) {
                $p->originalPos = $i;
                $nodes[] = $p;
                $i++;
            }
            usort($nodes, array('static', 'setupPageNodeOrder'));
            $home = Page::getByID(HOME_CID, 'RECENT');

            foreach ($nodes as $px) {
                $pkg = static::getPackageObject($px['package']);
                $data = array();
                $user = (string)$px['user'];
                if ($user != '') {
                    $ui = UserInfo::getByUserName($user);
                    if (is_object($ui)) {
                        $data['uID'] = $ui->getUserID();
                    } else {
                        $data['uID'] = USER_SUPER_ID;
                    }
                }
                $cDatePublic = (string)$px['public-date'];
                if ($cDatePublic) {
                    $data['cDatePublic'] = $cDatePublic;
                }

                $data['pkgID'] = 0;
                if (is_object($pkg)) {
                    $data['pkgID'] = $pkg->getPackageID();
                }
                $args = array();
                $ct = PageType::getByHandle($px['pagetype']);
                $template = PageTemplate::getByHandle($px['template']);
                if ($px['path'] != '') {
                    // not home page
                    $page = Page::getByPath($px['path']);
                    if (!is_object($page) || ($page->isError())) {
                        $lastSlash = strrpos((string)$px['path'], '/');
                        $parentPath = substr((string)$px['path'], 0, $lastSlash);
                        $data['cHandle'] = substr((string)$px['path'], $lastSlash + 1);
                        if (!$parentPath) {
                            $parent = $home;
                        } else {
                            $parent = Page::getByPath($parentPath);
                        }
                        $page = $parent->add($ct, $data);
                    }
                } else {
                    $page = $home;
                }

                $args['cName'] = $px['name'];
                $args['cDescription'] = $px['description'];
                if (is_object($ct)) {
                    $args['ptID'] = $ct->getPageTypeID();
                }
                $args['pTemplateID'] = $template->getPageTemplateID();
                $page->update($args);
            }
        }
    }

    public function importPageAreas(Page $page, \SimpleXMLElement $px)
    {
        foreach ($px->area as $ax) {
            if (isset($ax->blocks)) {
                foreach ($ax->blocks->block as $bx) {
                    if ($bx['type'] != '') {
                        // we check this because you might just get a block node with only an mc-block-id, if it's an alias
                        $bt = BlockType::getByHandle((string) $bx['type']);
                        if (!is_object($bt)) {
                            throw new \Exception(t('Invalid block type handle: %s', strval($bx['type'])));
                        }
                        $btc = $bt->getController();
                        $btc->import($page, (string)$ax['name'], $bx);
                    } else {
                        if ($bx['mc-block-id'] != '') {
                            // we find that block in the master collection block pool and alias it out
                            $bID = array_search((string)$bx['mc-block-id'], self::$mcBlockIDs);
                            if ($bID) {
                                $mc = Page::getByID($page->getMasterCollectionID(), 'RECENT');
                                $block = Block::getByID($bID, $mc, (string)$ax['name']);
                                $block->alias($page);

                                if ($block->getBlockTypeHandle() == BLOCK_HANDLE_LAYOUT_PROXY) {
                                    // we have to go get the blocks on that page in this layout.
                                    $btc = $block->getController();
                                    $arLayout = $btc->getAreaLayoutObject();
                                    $columns = $arLayout->getAreaLayoutColumns();
                                    foreach($columns as $column) {
                                        $area = $column->getAreaObject();
                                        $blocks = $area->getAreaBlocksArray($mc);
                                        foreach($blocks as $_b) {
                                            $_b->alias($page);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if (isset($ax->style)) {
                $area = \Area::get($page, (string) $ax['name']);
                $set = StyleSet::import($ax->style);
                $page->setCustomStyleSet($area, $set);
            }
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

    public static function addPageTypeComposerOutputControlID(PageTypeComposerFormLayoutSetControl $control, $id)
    {
        self::$ptComposerOutputControlIDs[$id] = $control->getPageTypeComposerFormLayoutSetControlID();
    }

    public static function getPageTypeComposerFormLayoutSetControlFromTemporaryID($id)
    {
        if (isset(self::$ptComposerOutputControlIDs[$id])) {
            return self::$ptComposerOutputControlIDs[$id];
        }
    }

    protected function importPageTemplates(\SimpleXMLElement $sx)
    {
        if (isset($sx->pagetemplates)) {
            foreach ($sx->pagetemplates->pagetemplate as $pt) {
                $pkg = static::getPackageObject($pt['package']);
                $ptt = PageTemplate::getByHandle($pt['handle']);
                if (!is_object($ptt)) {
                    $ptt = PageTemplate::add(
                        (string)$pt['handle'],
                        (string)$pt['name'],
                        (string)$pt['icon'],
                        $pkg,
                        (string)$pt['internal']
                    );
                }
            }
        }
    }

    protected function importBlockTypes(\SimpleXMLElement $sx)
    {
        if (isset($sx->blocktypes)) {
            foreach ($sx->blocktypes->blocktype as $bt) {
                $pkg = static::getPackageObject($bt['package']);
                if (is_object($pkg)) {
                    BlockType::installBlockTypeFromPackage((string) $bt['handle'], $pkg);
                } else {
                    BlockType::installBlockType((string) $bt['handle']);
                }
            }
        }
    }

    protected function importWorkflowTypes(\SimpleXMLElement $sx)
    {
        if (isset($sx->workflowtypes)) {
            foreach ($sx->workflowtypes->workflowtype as $wt) {
                $pkg = static::getPackageObject($wt['package']);
                $name = $wt['name'];
                if (!$name) {
                    $name = Loader::helper('text')->unhandle($wt['handle']);
                }
                $type = \Concrete\Core\Workflow\Type::add($wt['handle'], $name, $pkg);
            }
        }
    }

    protected function importAttributeTypes(\SimpleXMLElement $sx)
    {
        if (isset($sx->attributetypes)) {
            foreach ($sx->attributetypes->attributetype as $at) {
                $pkg = static::getPackageObject($at['package']);
                $name = $at['name'];
                if (!$name) {
                    $name = Loader::helper('text')->unhandle($at['handle']);
                }
                $type = AttributeType::getByHandle($at['handle']);
                if (!is_object($type)) {
                    $type = AttributeType::add($at['handle'], $name, $pkg);
                }
                if (isset($at->categories)) {
                    foreach ($at->categories->children() as $cat) {
                        $catobj = AttributeKeyCategory::getByHandle((string)$cat['handle']);
                        $catobj->associateAttributeKeyType($type);
                    }
                }
            }
        }
    }

    protected function importPermissionAccessEntityTypes(\SimpleXMLElement $sx)
    {
        if (isset($sx->permissionaccessentitytypes)) {
            foreach ($sx->permissionaccessentitytypes->permissionaccessentitytype as $pt) {
                $pkg = static::getPackageObject($pt['package']);
                $name = $pt['name'];
                if (!$name) {
                    $name = Loader::helper('text')->unhandle($pt['handle']);
                }
                $type = PermissionAccessEntityType::add($pt['handle'], $name, $pkg);
                if (isset($pt->categories)) {
                    foreach ($pt->categories->children() as $cat) {
                        $catobj = PermissionKeyCategory::getByHandle((string)$cat['handle']);
                        $catobj->associateAccessEntityType($type);
                    }
                }
            }
        }
    }

    protected function importPackages(\SimpleXMLElement $sx)
    {
        if (isset($sx->packages)) {
            foreach ($sx->packages->package as $p) {
                $pkg = Loader::package((string)$p['handle']);
                $pkg->install();
            }
        }
    }

    protected function importThemes(\SimpleXMLElement $sx)
    {
        if (isset($sx->themes)) {
            foreach ($sx->themes->theme as $th) {
                $pkg = static::getPackageObject($th['package']);
                $pThemeHandle = (string)$th['handle'];
                $pt = PageTheme::getByHandle($pThemeHandle);
                if (!is_object($pt)) {
                    $pt = PageTheme::add($pThemeHandle, $pkg);
                }
                if ($th['activated'] == '1') {
                    $pt->applyToSite();
                }
            }
        }
    }

    protected function importPageTypePublishTargetTypes(\SimpleXMLElement $sx)
    {
        if (isset($sx->pagetypepublishtargettypes)) {
            foreach ($sx->pagetypepublishtargettypes->type as $th) {
                $pkg = static::getPackageObject($th['package']);
                $ce = PageTypePublishTargetType::add((string)$th['handle'], (string)$th['name'], $pkg);
            }
        }
    }

    protected function importPageTypeComposerControlTypes(\SimpleXMLElement $sx)
    {
        if (isset($sx->pagetypecomposercontroltypes)) {
            foreach ($sx->pagetypecomposercontroltypes->type as $th) {
                $pkg = static::getPackageObject($th['package']);
                $ce = PageTypeComposerControlType::add((string)$th['handle'], (string)$th['name'], $pkg);
            }
        }
    }

    protected function importPageTypesBase(\SimpleXMLElement $sx)
    {
        if (isset($sx->pagetypes)) {
            foreach ($sx->pagetypes->pagetype as $p) {
                PageType::import($p);
            }
        }
    }

    protected function importPageTypeTargets(\SimpleXMLElement $sx)
    {
        if (isset($sx->pagetypes)) {
            foreach ($sx->pagetypes->pagetype as $p) {
                PageType::importTargets($p);
            }
        }
    }

    protected function importPageTypeDefaults(\SimpleXMLElement $sx)
    {
        if (isset($sx->pagetypes)) {
            foreach ($sx->pagetypes->pagetype as $p) {
                PageType::importContent($p);
            }
        }
    }

    protected function importConversationEditors(\SimpleXMLElement $sx)
    {
        if (isset($sx->conversationeditors)) {
            foreach ($sx->conversationeditors->editor as $th) {
                $pkg = static::getPackageObject($th['package']);
                $ce = ConversationEditor::add((string)$th['handle'], (string)$th['name'], $pkg);
                if ($th['activated'] == '1') {
                    $ce->activate();
                }
            }
        }
    }

    protected function importConversationRatingTypes(\SimpleXMLElement $sx)
    {
        if (isset($sx->conversationratingtypes)) {
            foreach ($sx->conversationratingtypes->conversationratingtype as $th) {
                $pkg = static::getPackageObject($th['package']);
                $ce = ConversationRatingType::add((string)$th['handle'], (string)$th['name'], $th['points'], $pkg);
            }
        }
    }


    protected function importBannedWords(\SimpleXMLElement $sx)
    {
        if (isset($sx->banned_words)) {
            foreach ($sx->banned_words->banned_word as $p) {
                $bw = BannedWord::add(str_rot13($p));
            }
        }
    }

    protected function importSocialLinks(\SimpleXMLElement $sx)
    {
        if (isset($sx->sociallinks)) {
            foreach ($sx->sociallinks->link as $l) {
                $sociallink = Link::getByServiceHandle((string)$l['service']);
                if (!is_object($sociallink)) {
                    $sociallink = new Link();
                    $sociallink->setURL((string)$l['url']);
                    $sociallink->setServiceHandle((string)$l['service']);
                    $sociallink->save();
                }
            }
        }
    }

    protected function importPageFeeds(\SimpleXMLElement $sx)
    {
        if (isset($sx->pagefeeds)) {
            foreach ($sx->pagefeeds->feed as $f) {
                $feed = Feed::getByHandle((string) $f->handle);
                if (!is_object($feed)) {
                    $feed = new Feed();
                }
                if ($f->parent) {
                    $feed->setParentID(self::getValue((string) $f->parent));
                }
                $feed->setTitle((string) $f->title);
                $feed->setDescription((string) $f->description);
                $feed->setHandle((string) $f->handle);
                if ($f->descendents) {
                    $feed->setIncludeAllDescendents(true);
                }
                if ($f->aliases) {
                    $feed->setDisplayAliases(true);
                }
                if ($f->featured) {
                    $feed->setDisplayFeaturedOnly(true);
                }
                if ($f->pagetype) {
                    $feed->setPageTypeID(self::getValue((string) $f->pagetype));
                }
                $contentType = $f->contenttype;
                $type = (string) $contentType['type'];
                if ($type == 'description') {
                    $feed->displayShortDescriptionContent();
                } else if ($type == 'area') {
                    $feed->displayAreaContent((string) $contentType['handle']);
                }
                $feed->save();
            }
        }
    }
    protected function importTrees(\SimpleXMLElement $sx)
    {
        if (isset($sx->trees)) {
            foreach ($sx->trees->tree as $t) {
                Tree::import($t);
            }
        }
    }

    protected function importFileImportantThumbnailTypes(\SimpleXMLElement $sx)
    {
        if (isset($sx->thumbnailtypes)) {
            foreach ($sx->thumbnailtypes->thumbnailtype as $l) {
                $type = new \Concrete\Core\File\Image\Thumbnail\Type\Type();
                $type->setName((string) $l['name']);
                $type->setHandle((string) $l['handle']);
                $type->setWidth((string) $l['width']);
                $required = (string) $l['required'];
                if ($required) {
                    $type->requireType();
                }
                $type->save();
            }
        }
    }

    protected function importConversationFlagTypes(\SimpleXMLElement $sx)
    {
        if (isset($sx->flag_types)) {
            foreach ($sx->flag_types->flag_type as $p) {
                $bw = ConversationFlagType::add((string) $p);
            }
        }
    }

    protected function importSystemCaptchaLibraries(\SimpleXMLElement $sx)
    {
        if (isset($sx->systemcaptcha)) {
            foreach ($sx->systemcaptcha->library as $th) {
                $pkg = static::getPackageObject($th['package']);
                $scl = SystemCaptchaLibrary::add($th['handle'], $th['name'], $pkg);
                if ($th['activated'] == '1') {
                    $scl->activate();
                }
            }
        }
    }

    protected function importSystemContentEditorSnippets(\SimpleXMLElement $sx)
    {
        if (isset($sx->systemcontenteditorsnippets)) {
            foreach ($sx->systemcontenteditorsnippets->snippet as $th) {
                $pkg = static::getPackageObject($th['package']);
                $scs = SystemContentEditorSnippet::add($th['handle'], $th['name'], $pkg);
                if ($th['activated'] == '1') {
                    $scs->activate();
                }
            }
        }
    }

    protected function importJobs(\SimpleXMLElement $sx)
    {
        if (isset($sx->jobs)) {
            foreach ($sx->jobs->job as $jx) {
                $pkg = static::getPackageObject($jx['package']);
                if (is_object($pkg)) {
                    Job::installByPackage($jx['handle'], $pkg);
                } else {
                    Job::installByHandle($jx['handle']);
                }
            }
        }
    }

    protected function importJobSets(\SimpleXMLElement $sx)
    {
        if (isset($sx->jobsets)) {
            foreach ($sx->jobsets->jobset as $js) {
                $jso = JobSet::getByName((string)$js['name']);
                if (!is_object($jso)) {
                    $pkg = static::getPackageObject($js['package']);
                    if (is_object($pkg)) {
                        $jso = JobSet::add((string)$js['name'], $pkg);
                    } else {
                        $jso = JobSet::add((string)$js['name']);
                    }
                }
                foreach ($js->children() as $jsk) {
                    $j = Job::getByHandle((string)$jsk['handle']);
                    if (is_object($j)) {
                        $jso->addJob($j);
                    }
                }
            }
        }
    }

    protected function importConfigValues(\SimpleXMLElement $sx)
    {
        if (isset($sx->config)) {
            foreach ($sx->config->children() as $key) {
                $pkg = static::getPackageObject($key['package']);
                if (is_object($pkg)) {
                    \Config::save($pkg->getPackageHandle() . '::' . $key->getName(), (string)$key);
                } else {
                    \Config::save($key->getName(), (string)$key);
                }
            }
        }
    }

    protected function importDatabaseConfigValues(\SimpleXMLElement $sx)
    {
        if (isset($sx->databaseconfig)) {
            $config = \Core::make('config/database');
            foreach ($sx->databaseconfig->children() as $key) {
                $pkg = static::getPackageObject($key['package']);
                if (is_object($pkg)) {
                    $config->save($pkg->getPackageHandle() . '::' . $key->getName(), (string)$key);
                } else {
                    $config->save($key->getName(), (string)$key);
                }
            }
        }
    }

    protected function importTaskPermissions(\SimpleXMLElement $sx)
    {
        if (isset($sx->taskpermissions)) {
            foreach ($sx->taskpermissions->taskpermission as $tp) {
                $pkg = static::getPackageObject($tp['package']);
                $tpa = TaskPermission::addTask($tp['handle'], $tp['name'], $tp['description'], $pkg);
                if (isset($tp->access)) {
                    foreach ($tp->access->children() as $ch) {
                        if ($ch->getName() == 'group') {
                            $g = Group::getByName($ch['name']);
                            if (!is_object($g)) {
                                $g = Group::add($ch['name'], $ch['description']);
                            }
                            $tpa->addAccess($g);
                        }
                    }
                }
            }
        }
    }

    protected function importPermissionCategories(\SimpleXMLElement $sx)
    {
        if (isset($sx->permissioncategories)) {
            foreach ($sx->permissioncategories->category as $pkc) {
                $pkg = static::getPackageObject($pkc['package']);
                PermissionKeyCategory::add((string)$pkc['handle'], $pkg);
            }
        }
    }

    protected function importWorkflowProgressCategories(\SimpleXMLElement $sx)
    {
        if (isset($sx->workflowprogresscategories)) {
            foreach ($sx->workflowprogresscategories->category as $wpc) {
                $pkg = static::getPackageObject($wpc['package']);
                WorkflowProgressCategory::add((string)$wpc['handle'], $pkg);
            }
        }
    }

    protected function importPermissions(\SimpleXMLElement $sx)
    {
        if (isset($sx->permissionkeys)) {
            foreach ($sx->permissionkeys->permissionkey as $pk) {
                $pkc = PermissionKeyCategory::getByHandle((string)$pk['category']);
                $pkg = static::getPackageObject($pk['package']);
                $txt = Loader::helper('text');
                $c1 = '\\Concrete\\Core\\Permission\\Key\\' . $txt->camelcase(
                        $pkc->getPermissionKeyCategoryHandle()
                    ) . 'Key';
                $pkx = call_user_func(array($c1, 'import'), $pk);
                if (isset($pk->access)) {
                    foreach ($pk->access->children() as $ch) {
                        if ($ch->getName() == 'group') {
                            $g = Group::getByName($ch['name']);
                            if (!is_object($g)) {
                                $g = Group::add($g['name'], $g['description']);
                            }
                            $pae = GroupPermissionAccessEntity::getOrCreate($g);
                            $pa = PermissionAccess::create($pkx);
                            $pa->addListItem($pae);
                            $pt = $pkx->getPermissionAssignmentObject();
                            $pt->assignPermissionAccess($pa);
                        }
                    }
                }

            }
        }
    }

    protected function importFeatures(\SimpleXMLElement $sx)
    {
        if (isset($sx->features)) {
            foreach ($sx->features->feature as $fea) {
                $feHasCustomClass = false;
                if ($fea['has-custom-class']) {
                    $feHasCustomClass = true;
                }
                $pkg = static::getPackageObject($fea['package']);
                $fx = Feature::add((string)$fea['handle'], (string)$fea['score'], $feHasCustomClass, $pkg);
            }
        }
    }

    protected function importFeatureCategories(\SimpleXMLElement $sx)
    {
        if (isset($sx->featurecategories)) {
            foreach ($sx->featurecategories->featurecategory as $fea) {
                $pkg = static::getPackageObject($fea['package']);
                $fx = FeatureCategory::add($fea['handle'], $pkg);
            }
        }
    }

    protected function importAttributeCategories(\SimpleXMLElement $sx)
    {
        if (isset($sx->attributecategories)) {
            foreach ($sx->attributecategories->category as $akc) {
                $pkg = static::getPackageObject($akc['package']);
                $akx = AttributeKeyCategory::getByHandle($akc['handle']);
                if (!is_object($akx)) {
                    $akx = AttributeKeyCategory::add($akc['handle'], $akc['allow-sets'], $pkg);
                }
            }
        }
    }

    protected function importAttributes(\SimpleXMLElement $sx)
    {
        if (isset($sx->attributekeys)) {
            foreach ($sx->attributekeys->attributekey as $ak) {
                $akc = AttributeKeyCategory::getByHandle($ak['category']);
                $pkg = static::getPackageObject($ak['package']);
                $type = AttributeType::getByHandle($ak['type']);
                $txt = Loader::helper('text');
                $c1 = '\\Concrete\\Core\\Attribute\\Key\\' . $txt->camelcase(
                        $akc->getAttributeKeyCategoryHandle()
                    ) . 'Key';
                $ak = call_user_func(array($c1, 'import'), $ak);
            }
        }
    }

    protected function importAttributeSets(\SimpleXMLElement $sx)
    {
        if (isset($sx->attributesets)) {
            foreach ($sx->attributesets->attributeset as $as) {
                $set = \Concrete\Core\Attribute\Set::getByHandle((string) $as['handle']);
                $akc = AttributeKeyCategory::getByHandle($as['category']);
                if (!is_object($set)) {
                    $pkg = static::getPackageObject($as['package']);
                    $set = $akc->addSet((string)$as['handle'], (string)$as['name'], $pkg, $as['locked']);
                }
                foreach ($as->children() as $ask) {
                    $ak = $akc->getAttributeKeyByHandle((string)$ask['handle']);
                    if (is_object($ak)) {
                        $set->addKey($ak);
                    }
                }
            }
        }
    }

    protected function importGatheringDataSources(\SimpleXMLElement $sx)
    {
        if (isset($sx->gatheringsources)) {
            foreach ($sx->gatheringsources->gatheringsource as $ags) {
                $pkg = static::getPackageObject($ags['package']);
                $source = GatheringDataSource::add((string)$ags['handle'], (string)$ags['name'], $pkg);
            }
        }
    }

    protected function importGatheringItemTemplateTypes(\SimpleXMLElement $sx)
    {
        if (isset($sx->gatheringitemtemplatetypes)) {
            foreach ($sx->gatheringitemtemplatetypes->gatheringitemtemplatetype as $at) {
                $pkg = static::getPackageObject($at['package']);
                GatheringItemTemplateType::add((string)$at['handle'], $pkg);
            }
        }
    }

    protected function importGatheringItemTemplates(\SimpleXMLElement $sx)
    {
        if (isset($sx->gatheringitemtemplates)) {
            foreach ($sx->gatheringitemtemplates->gatheringitemtemplate as $at) {
                $pkg = static::getPackageObject($at['package']);
                $type = GatheringItemTemplateType::getByHandle((string)$at['type']);
                $gatHasCustomClass = false;
                $gatForceDefault = false;
                $gatFixedSlotWidth = 0;
                $gatFixedSlotHeight = 0;
                if ($at['has-custom-class']) {
                    $gatHasCustomClass = true;
                }
                if ($at['force-default']) {
                    $gatForceDefault = true;
                }
                if ($at['fixed-slot-width']) {
                    $gatFixedSlotWidth = (string)$at['fixed-slot-width'];
                }
                if ($at['fixed-slot-height']) {
                    $gatFixedSlotHeight = (string)$at['fixed-slot-height'];
                }
                $template = GatheringItemTemplate::add(
                    $type,
                    (string)$at['handle'],
                    (string)$at['name'],
                    $gatFixedSlotWidth,
                    $gatFixedSlotHeight,
                    $gatHasCustomClass,
                    $gatForceDefault,
                    $pkg
                );
                foreach ($at->children() as $fe) {
                    $feo = Feature::getByHandle((string)$fe['handle']);
                    if (is_object($feo)) {
                        $template->addGatheringItemTemplateFeature($feo);
                    }
                }
            }
        }
    }

    protected function importImageEditorControlSets(\SimpleXMLElement $sx)
    {
        if (isset($sx->imageeditor_controlsets)) {
            foreach ($sx->imageeditor_controlsets->imageeditor_controlset as $controlset) {
                $handle = $controlset['handle'];
                $name = $controlset['name'];
                $ob = SystemImageEditorControlSet::getByHandle($handle);
                if ($ob->getHandle() != $handle) {
                    SystemImageEditorControlSet::add($handle, $name);
                }
            }
        }
    }

    protected function importImageEditorComponents(\SimpleXMLElement $sx)
    {
        if (isset($sx->imageeditor_components)) {
            foreach ($sx->imageeditor_components->imageeditor_component as $component) {
                $handle = $component['handle'];
                $name = $component['name'];
                $ob = SystemImageEditorComponent::getByHandle($handle);
                if ($ob->getImageEditorComponentHandle() != $handle) {
                    SystemImageEditorComponent::add($handle, $name);
                }
            }
        }
    }

    protected function importImageEditorFilters(\SimpleXMLElement $sx)
    {
        if (isset($sx->imageeditor_filters)) {
            foreach ($sx->imageeditor_filters->imageeditor_filter as $filter) {
                $handle = $filter['handle'];
                $name = $filter['name'];
                $ob = SystemImageEditorFilter::getByHandle($handle);
                if ($ob->getHandle() != $handle) {
                    SystemImageEditorFilter::add($handle, $name);
                }
            }
        }
    }

    protected function importBlockTypeSets(\SimpleXMLElement $sx)
    {
        if (isset($sx->blocktypesets)) {
            foreach ($sx->blocktypesets->blocktypeset as $bts) {
                $pkg = static::getPackageObject($bts['package']);
                $set = BlockTypeSet::add((string)$bts['handle'], (string)$bts['name'], $pkg);
                foreach ($bts->children() as $btk) {
                    $bt = BlockType::getByHandle((string)$btk['handle']);
                    if (is_object($bt)) {
                        $set->addBlockType($bt);
                    }
                }
            }
        }
    }

    public static function getValue($value)
    {
        if (preg_match(
            '/\{ccm:export:page:(.*?)\}|' .
            '\{ccm:export:file:(.*?)\}|' .
            '\{ccm:export:image:(.*?)\}|' .
            '\{ccm:export:pagetype:(.*?)\}|' .
            '\{ccm:export:pagefeed:(.*?)\}/i',
            $value,
            $matches
        )
        ) {
            if ($matches[1]) {
                $c = Page::getByPath($matches[1]);
                return $c->getCollectionID();
            }
            if ($matches[2]) {
                $db = Loader::db();
                $fID = $db->GetOne('select fID from FileVersions where fvFilename = ?', array($matches[2]));
                return $fID;
            }
            if ($matches[3]) {
                $db = Loader::db();
                $fID = $db->GetOne('select fID from FileVersions where fvFilename = ?', array($matches[3]));
                return $fID;
            }
            if ($matches[4]) {
                $ct = PageType::getByHandle($matches[4]);
                return $ct->getPageTypeID();
            }
            if ($matches[5]) {
                $pf = Feed::getByHandle($matches[5]);
                return $pf->getID();
            }
        } else {
            return $value;
        }
    }

}
