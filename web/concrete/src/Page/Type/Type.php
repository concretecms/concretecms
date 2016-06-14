<?php

namespace Concrete\Core\Page\Type;

use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Page\Template;
use Concrete\Core\Page\Type\Composer\Control\CorePageProperty\NameCorePageProperty;
use Concrete\Core\Page\Type\Composer\FormLayoutSet;
use Concrete\Core\Permission\Key\Key;
use Loader;
use Concrete\Core\Foundation\Object;
use PageTemplate;
use PermissionKey;
use PermissionAccess;
use Concrete\Core\Permission\Access\Entity\PageOwnerEntity as PageOwnerPermissionAccessEntity;
use Concrete\Core\Page\Type\Composer\FormLayoutSet as PageTypeComposerFormLayoutSet;
use Concrete\Core\Page\Type\Composer\Control\Type\Type as PageTypeComposerControlType;
use Concrete\Core\Page\Type\Composer\Control\Control as PageTypeComposerControl;
use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Package\PackageList;
use CollectionVersion;
use Collection;
use Page;
use Config;
use User;
use Package;
use Concrete\Core\Workflow\Request\ApprovePageRequest as ApprovePagePageWorkflowRequest;
use CacheLocal;
use Concrete\Core\Page\Type\PublishTarget\Configuration\Configuration as PageTypePublishTargetConfiguration;
use Concrete\Core\Page\Type\PublishTarget\Type\Type as PageTypePublishTargetType;
use Concrete\Core\Page\Type\Composer\FormLayoutSetControl as PageTypeComposerFormLayoutSetControl;
use Concrete\Core\Page\Collection\Version\VersionList;
use Concrete\Core\Page\Type\Composer\Control\CorePageProperty\CorePageProperty as CorePagePropertyPageTypeComposerControl;

class Type extends Object implements \Concrete\Core\Permission\ObjectInterface
{
    protected $ptDraftVersionsToSave = 10;
    protected $ptDefaultPageTemplateID = 0;

    public function getPageTypeID()
    {
        return $this->ptID;
    }

    public function getPageTypeName()
    {
        return $this->ptName;
    }

    public function getPageTypeDisplayName($format = 'html')
    {
        $value = t($this->getPageTypeName());
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }

    public function getPageTypeHandle()
    {
        return $this->ptHandle;
    }

    public function getPageTypePublishTargetTypeID()
    {
        return $this->ptPublishTargetTypeID;
    }

    public function getPageTypePublishTargetObject()
    {
        return $this->ptPublishTargetObject;
    }

    public function getPageTypeAllowedPageTemplates()
    {
        return $this->ptAllowedPageTemplates;
    }

    public function getPageTypeDefaultPageTemplateID()
    {
        return $this->ptDefaultPageTemplateID;
    }

    public function getPageTypeDefaultPageTemplateObject()
    {
        return PageTemplate::getByID($this->ptDefaultPageTemplateID);
    }

    public function getPermissionObjectIdentifier()
    {
        return $this->getPageTypeID();
    }

    public function isPageTypeFrequentlyAdded()
    {
        return $this->ptIsFrequentlyAdded;
    }

    public function getPageTypeDisplayOrder()
    {
        return $this->ptDisplayOrder;
    }

    public function getPermissionResponseClassName()
    {
        return '\\Concrete\\Core\\Permission\\Response\\PageTypeResponse';
    }

    public function getPermissionAssignmentClassName()
    {
        return '\\Concrete\\Core\\Permission\\Assignment\\PageTypeAssignment';
    }

    public function getPermissionObjectKeyCategoryHandle()
    {
        return 'page_type';
    }

    public function isPageTypeInternal()
    {
        return $this->ptIsInternal;
    }

    public function doesPageTypeLaunchInComposer()
    {
        return $this->ptLaunchInComposer;
    }

    public function getPackageID()
    {
        return $this->pkgID;
    }

    public function getPackageHandle()
    {
        return PackageList::getHandle($this->pkgID);
    }

    protected function stripEmptyPageTypeComposerControls(Page $c)
    {
        $controls = PageTypeComposerControl::getList($this);
        foreach ($controls as $cn) {
            $cn->setPageObject($c);
            if ($cn->shouldPageTypeComposerControlStripEmptyValuesFromPage(
                ) && $cn->isPageTypeComposerControlValueEmpty()
            ) {
                $cn->removePageTypeComposerControlFromPage();
            }
        }
    }

    public function publish(Page $c)
    {
        $this->stripEmptyPageTypeComposerControls($c);
        $parent = Page::getByID($c->getPageDraftTargetParentPageID());
        if ($c->isPageDraft()) { // this is still a draft, which means it has never been properly published.
            // so we need to move it, check its permissions, etc...
            Section::registerPage($c);
            $c->move($parent);
            if (!$parent->overrideTemplatePermissions()) {
                // that means the permissions of pages added beneath here inherit from page type permissions
                // this is a very poorly named method. Template actually used to mean Type.
                // so this means we need to set the permissions of this current page to inherit from page types.
                $c->inheritPermissionsFromDefaults();
            }
            $c->activate();
        } else {
            $c->rescanCollectionPath();
        }

        $u = new User();
        $v = CollectionVersion::get($c, 'RECENT');
        $pkr = new ApprovePagePageWorkflowRequest();
        $pkr->setRequestedPage($c);
        $pkr->setRequestedVersionID($v->getVersionID());
        $pkr->setRequesterUserID($u->getUserID());
        $pkr->trigger();

        $u->unloadCollectionEdit($c);
        CacheLocal::flush();

        $ev = new Event($c);
        $ev->setPageType($this);
        $ev->setUser($u);
        \Events::dispatch('on_page_type_publish', $ev);
    }

    public function savePageTypeComposerForm(Page $c)
    {
        $controls = PageTypeComposerControl::getList($this);
        $outputControls = array();
        foreach ($controls as $cn) {
            $data = $cn->getRequestValue();
            $cn->publishToPage($c, $data, $controls);
            $outputControls[] = $cn;
        }

        // set page name from controls
        // now we see if there's a page name field in there
        $containsPageNameControl = false;
        foreach ($outputControls as $cn) {
            if ($cn instanceof NameCorePageProperty) {
                $containsPageNameControl = true;
                break;
            }
        }
        if (!$containsPageNameControl) {
            foreach ($outputControls as $cn) {
                if ($cn->canPageTypeComposerControlSetPageName()) {
                    $pageName = $cn->getPageTypeComposerControlPageNameValue($c);
                    $c->updateCollectionName($pageName);
                }
            }
        }

        // remove all but the most recent X drafts.
        if ($c->isPageDraft()) {
            $vl = new VersionList($c);
            $vl->setItemsPerPage(-1);
            // this will ensure that we only ever keep X versions.
            $vArray = $vl->getPage();
            if (count($vArray) > $this->ptDraftVersionsToSave) {
                for ($i = $this->ptDraftVersionsToSave; $i < count($vArray); $i++) {
                    $v = $vArray[$i];
                    @$v->delete();
                }
            }
        }

        $c = Page::getByID($c->getCollectionID(), 'RECENT');
        $controls = array();
        foreach ($outputControls as $oc) {
            $oc->setPageObject($c);
            $controls[] = $oc;
        }

        $ev = new Event($c);
        $ev->setPageType($this);
        $ev->setArgument('controls', $controls);
        \Events::dispatch('on_page_type_save_composer_form', $ev);

        return $controls;
    }

    public function getPageTypeSelectedPageTemplateObjects()
    {
        $templates = array();
        $db = Loader::db();
        $r = $db->Execute(
            'select pTemplateID from PageTypePageTemplates where ptID = ? order by pTemplateID asc',
            array($this->ptID)
        );
        while ($row = $r->FetchRow()) {
            $pt = PageTemplate::getByID($row['pTemplateID']);
            if (is_object($pt)) {
                $templates[] = $pt;
            }
        }

        return $templates;
    }

    public static function getByDefaultsPage(Page $c)
    {
        if ($c->isMasterCollection()) {
            $db = Loader::db();
            $ptID = $db->GetOne(
                'select ptID from PageTypePageTemplateDefaultPages where cID = ?',
                array($c->getCollectionID())
            );
            if ($ptID) {
                return static::getByID($ptID);
            }
        }
    }

    public function getPageTypePageTemplateDefaultPageObject(Template $template = null)
    {
        if (!$template) {
            $template = $this->getPageTypeDefaultPageTemplateObject();
        }

        $db = Loader::db();
        $cID = $db->GetOne(
            'select cID from PageTypePageTemplateDefaultPages where ptID = ? and pTemplateID = ?',
            array(
                $this->ptID,
                $template->getPageTemplateID(),
            )
        );
        if (!$cID) {
            // we create one.
            $dh = Loader::helper('date');
            $cDate = $dh->getOverridableNow();
            $data = array(
                'pTemplateID' => $template->getPageTemplateID(),
            );
            $cobj = Collection::createCollection($data);
            $cID = $cobj->getCollectionID();

            $v2 = array($cID, 1, $this->getPageTypeID());
            $q2 = "insert into Pages (cID, cIsTemplate, ptID) values (?, ?, ?)";
            $r2 = $db->prepare($q2);
            $res2 = $db->execute($r2, $v2);

            $db->Execute(
                'insert into PageTypePageTemplateDefaultPages (ptID, pTemplateID, cID) values (?, ?, ?)',
                array(
                    $this->ptID,
                    $template->getPageTemplateID(),
                    $cID,
                )
            );
        }

        $template = Page::getByID($cID, 'RECENT');
        if ($template->getCollectionInheritance() != 'OVERRIDE') {
            $template->setPermissionsToManualOverride();
        }

        return $template;
    }

    public function getPageTypePageTemplateObjects()
    {
        $_templates = array();
        if ($this->ptAllowedPageTemplates == 'C') {
            $_templates = $this->getPageTypeSelectedPageTemplateObjects();
        } else {
            $templates = PageTemplate::getList();
            $db = Loader::db();
            if ($this->ptAllowedPageTemplates == 'X') {
                $_templates = array();
                $pageTemplateIDs = $db->GetCol(
                    'select pTemplateID from PageTypePageTemplates where ptID = ? order by pTemplateID asc',
                    array($this->ptID)
                );
                foreach ($templates as $pt) {
                    if (!in_array($pt->getPageTemplateID(), $pageTemplateIDs)) {
                        $_templates[] = $pt;
                    }
                }
            } else {
                $_templates = $templates;
            }
        }
        $defaultTemplate = PageTemplate::getByID($this->getPageTypeDefaultPageTemplateID());
        if (is_object($defaultTemplate) && (!in_array($defaultTemplate, $_templates))) {
            $_templates[] = $defaultTemplate;
        }

        return $_templates;
    }

    public static function importTargets($node)
    {
        $ptHandle = (string) $node['handle'];
        $db = Loader::db();
        $ptID = $db->GetOne('select ptID from PageTypes where ptHandle = ?', array($ptHandle));
        $cm = static::getByID($ptID);
        if (is_object($cm) && isset($node->target)) {
            $target = \Concrete\Core\Page\Type\PublishTarget\Type\Type::importConfiguredPageTypePublishTarget(
                $node->target
            );
            $cm->setConfiguredPageTypePublishTargetObject($target);
        }
    }

    public static function import($node)
    {
        $types = array();
        if ((string) $node->pagetemplates['type'] == 'custom' || (string) $node->pagetemplates['type'] == 'except') {
            if ((string) $node->pagetemplates['type'] == 'custom') {
                $ptAllowedPageTemplates = 'C';
            } else {
                $ptAllowedPageTemplates = 'X';
            }

            foreach ($node->pagetemplates->pagetemplate as $pagetemplate) {
                $types[] = PageTemplate::getByHandle((string) $pagetemplate['handle']);
            }
        } else {
            $ptAllowedPageTemplates = 'A';
        }

        $ptName = (string) $node['name'];
        $ptHandle = (string) $node['handle'];
        $db = Loader::db();
        $defaultPageTemplate = PageTemplate::getByHandle((string) $node->pagetemplates['default']);

        $ptID = $db->GetOne('select ptID from PageTypes where ptHandle = ?', array($ptHandle));
        $data = array(
            'handle' => $ptHandle,
            'name' => $ptName,
        );
        if ($defaultPageTemplate) {
            $data['defaultTemplate'] = $defaultPageTemplate;
        }
        if ($ptAllowedPageTemplates) {
            $data['allowedTemplates'] = $ptAllowedPageTemplates;
        }
        if ($node['internal']) {
            $data['internal'] = true;
        }

        $data['ptLaunchInComposer'] = 0;
        if ($node['launch-in-composer'] == '1') {
            $data['ptLaunchInComposer'] = 1;
        }

        $data['ptIsFrequentlyAdded'] = 0;
        if ($node['is-frequently-added'] == '1') {
            $data['ptIsFrequentlyAdded'] = 1;
        }

        $data['templates'] = $types;
        $pkg = false;
        if ($node['package']) {
            $pkg = Package::getByHandle((string) $node['package']);
        }

        if ($ptID) {
            $cm = static::getByID($ptID);
            $cm->update($data);
        } else {
            $cm = static::add($data, $pkg);
        }
        $node = $node->composer;
        if (isset($node->formlayout->set)) {
            foreach ($node->formlayout->set as $setnode) {
                $set = $cm->addPageTypeComposerFormLayoutSet((string) $setnode['name'], (string) $setnode['description']);
                if (isset($setnode->control)) {
                    foreach ($setnode->control as $controlnode) {
                        $controltype = PageTypeComposerControlType::getByHandle((string) $controlnode['type']);
                        $control = $controltype->configureFromImportHandle((string) $controlnode['handle']);
                        $setcontrol = $control->addToPageTypeComposerFormLayoutSet($set, true);
                        $required = (string) $controlnode['required'];
                        $customTemplate = (string) $controlnode['custom-template'];
                        $label = (string) $controlnode['custom-label'];
                        $description = (string) $controlnode['description'];
                        $outputControlID = (string) $controlnode['output-control-id'];
                        if ($required == '1') {
                            $setcontrol->updateFormLayoutSetControlRequired(true);
                        } else {
                            $setcontrol->updateFormLayoutSetControlRequired(false);
                        }
                        if ($customTemplate) {
                            $setcontrol->updateFormLayoutSetControlCustomTemplate($customTemplate);
                        }
                        if ($label) {
                            $setcontrol->updateFormLayoutSetControlCustomLabel($label);
                        }
                        if ($description) {
                            $setcontrol->updateFormLayoutSetControlDescription($description);
                        }
                        if ($outputControlID) {
                            ContentImporter::addPageTypeComposerOutputControlID($setcontrol, $outputControlID);
                        }
                    }
                }
            }
        }
    }

    public static function importContent($node)
    {
        $db = Loader::db();
        $ptHandle = (string) $node['handle'];
        $ptID = $db->GetOne('select ptID from PageTypes where ptHandle = ?', array($ptHandle));
        if ($ptID) {
            $pt = static::getByID($ptID);
            $defaultTemplate = $pt->getPageTypeDefaultPageTemplateObject();
            if (isset($node->composer->output->pagetemplate)) {
                $ci = new ContentImporter();
                foreach ($node->composer->output->pagetemplate as $pagetemplate) {
                    $handle = (string) $pagetemplate['handle'];
                    $ptt = PageTemplate::getByHandle($handle);
                    if (is_object($ptt)) {

                        // let's get the defaults page for this
                        $xc = $pt->getPageTypePageTemplateDefaultPageObject($ptt);

                        // if the $handle matches the default page template for this page type, then we ALSO check in here
                        // and see if there are any attributes
                        if (is_object($defaultTemplate) && $defaultTemplate->getPageTemplateHandle() == $handle) {
                            if (isset($pagetemplate->page->attributes)) {
                                foreach ($pagetemplate->page->attributes->children() as $attr) {
                                    $ak = CollectionKey::getByHandle((string) $attr['handle']);
                                    if (is_object($ak)) {
                                        $xc->setAttribute((string) $attr['handle'], $ak->getController()->importValue($attr));
                                    }
                                }
                            }
                        }

                        // now that we have the defaults page, let's import this content into it.
                        if (isset($pagetemplate->page)) {
                            $ci->importPageAreas($xc, $pagetemplate->page);
                        }
                    }
                }
            }
        }
    }

    public function export($nxml)
    {
        $templates = $this->getPageTypePageTemplateObjects();
        $pagetype = $nxml->addChild('pagetype');
        $pagetype->addAttribute('name', $this->getPageTypeName());
        $pagetype->addAttribute('handle', $this->getPageTypeHandle());
        $pagetype->addAttribute('package', $this->getPackageHandle());
        if ($this->isPageTypeInternal()) {
            $pagetype->addAttribute('internal', 'true');
        }
        if ($this->doesPageTypeLaunchInComposer()) {
            $pagetype->addAttribute('launch-in-composer', '1');
        } else {
            $pagetype->addAttribute('launch-in-composer', '0');
        }
        if ($this->isPageTypeFrequentlyAdded()) {
            $pagetype->addAttribute('is-frequently-added', '1');
        }
        $pagetemplates = $pagetype->addChild('pagetemplates');
        if ($this->getPageTypeAllowedPageTemplates() == 'A') {
            $pagetemplates->addAttribute('type', 'all');
        } else {
            if ($this->getPageTypeAllowedPageTemplates() == 'X') {
                $pagetemplates->addAttribute('type', 'except');
            } else {
                $pagetemplates->addAttribute('type', 'custom');
            }
            foreach ($templates as $tt) {
                $pagetemplates->addChild('pagetemplate')->addAttribute('handle', $tt->getPageTemplateHandle());
            }
        }

        $defaultPageTemplate = PageTemplate::getByID($this->getPageTypeDefaultPageTemplateID());
        if (is_object($defaultPageTemplate)) {
            $pagetemplates->addAttribute('default', $defaultPageTemplate->getPageTemplateHandle());
        }
        $target = $this->getPageTypePublishTargetObject();
        $target->export($pagetype);

        $cfsn = $pagetype->addChild('composer');
        $fsn = $cfsn->addChild('formlayout');

        $fieldsets = PageTypeComposerFormLayoutSet::getList($this);
        foreach ($fieldsets as $fs) {
            $fs->export($fsn);
        }

        $osn = $cfsn->addChild('output');
        foreach ($templates as $tt) {
            $pagetemplate = $osn->addChild('pagetemplate');
            $pagetemplate->addAttribute('handle', $tt->getPageTemplateHandle());
            $xc = $this->getPageTypePageTemplateDefaultPageObject($tt);
            $xc->export($pagetemplate);
        }
    }
    public static function exportList($xml)
    {
        $list = self::getList();
        $nxml = $xml->addChild('pagetypes');
        foreach ($list as $sc) {
            $sc->export($nxml);
        }
    }

    public function rescanPageTypeComposerOutputControlObjects()
    {
        $sets = PageTypeComposerFormLayoutSet::getList($this);
        foreach ($sets as $s) {
            $controls = PageTypeComposerFormLayoutSetControl::getList($s);
            foreach ($controls as $cs) {
                $type = $cs->getPageTypeComposerControlTypeObject();
                if ($type->controlTypeSupportsOutputControl()) {
                    $cs->ensureOutputControlExists();
                }
            }
        }
    }

    public function getPageTypeUsageCount()
    {
        $db = Loader::db();
        $count = $db->GetOne('select count(cID) from Pages where cIsTemplate = 0 and ptID = ? and cIsActive = 1', array($this->ptID));

        return $count;
    }

    public function duplicate($ptHandle, $ptName)
    {
        $data = array(
            'handle' => $ptHandle,
            'name' => $ptName,
            'defaultTemplate' => $this->getPageTypeDefaultPageTemplateObject(),
            'allowedTemplates' => $this->getPageTypeAllowedPageTemplates(),
            'templates' => $this->getPageTypeSelectedPageTemplateObjects(),
            'ptLaunchInComposer' => $this->doesPageTypeLaunchInComposer(),
            'ptIsFrequentlyAdded' => $this->isPageTypeFrequentlyAdded(),
        );

        $new = static::add($data);

        // now copy the edit form
        $sets = FormLayoutSet::getList($this);
        foreach ($sets as $set) {
            $set->duplicate($new);
        }

        // now copy the master pages for defaults and attributes
        $db = \Database::get();
        $r = $db->Execute('select cID from Pages where cIsTemplate = 1 and ptID = ?', array($this->getPageTypeID()));
        $home = Page::getByID(HOME_CID);
        while ($row = $r->FetchRow()) {
            $c = Page::getByID($row['cID']);
            if (is_object($c)) {
                $nc = $c->duplicate($home);
                $nc->setPageType($new);
                $db->update('Pages', array(
                    'cParentID' => 0,
                    'cIsTemplate' => 1,
                ), array('cID' => $nc->getCollectionID()));
                $db->insert('PageTypePageTemplateDefaultPages', array(
                    'pTemplateID' => $nc->getPageTemplateID(),
                    'ptID' => $new->getPageTypeID(),
                    'cID' => $nc->getCollectionID(),
                ));

                // clear out output control blocks because they will be pointing to the wrong thing

                $composerBlocksIDs = $db->GetAll('select cvb.bID, cvb.arHandle from btCorePageTypeComposerControlOutput o inner join CollectionVersionBlocks cvb on cvb.bID = o.bID inner join Pages p on cvb.cID = p.cID where p.cID = ?',
                    array($nc->getCollectionID()));
                foreach ($composerBlocksIDs as $row) {
                    $b = \Block::getByID($row['bID'], $nc, $row['arHandle']);
                    $b->deleteBlock();
                }
            }
        }

        // copy permissions from the defaults to the page type
        $list = Key::getList('page_type');
        foreach ($list as $pk) {
            $pk->setPermissionObject($this);
            $rpa = $pk->getPermissionAccessObject();
            if (is_object($rpa)) {
                $pk->setPermissionObject($new);
                $pt = $pk->getPermissionAssignmentObject();
                if (is_object($pt)) {
                    $pt->clearPermissionAssignment();
                    $pt->assignPermissionAccess($rpa);
                }
            }
        }
        // copy permissions from the default page to the page type
        $list = Key::getList('page');
        foreach ($list as $pk) {
            $pk->setPermissionObject($this->getPageTypePageTemplateDefaultPageObject());
            $rpa = $pk->getPermissionAccessObject();
            if (is_object($rpa)) {
                $pk->setPermissionObject($new->getPageTypePageTemplateDefaultPageObject());
                $pt = $pk->getPermissionAssignmentObject();
                if (is_object($pt)) {
                    $pt->clearPermissionAssignment();
                    $pt->assignPermissionAccess($rpa);
                }
            }
        }

        // duplicate the target object.
        $target = $this->getPageTypePublishTargetObject();
        $new->setConfiguredPageTypePublishTargetObject($target);
    }

    /**
     * Add a page type.
     *
     * @param array $data {
     *     @var string          $handle              A string which can be used to identify the page type
     *     @var string          $name                A user friendly display name
     *     @var \PageTemplate   $defaultTemplate     The default template object or handle
     *     @var string          $allowedTemplates    (A|C|X) A for all, C for selected only, X for non-selected only
     *     @var \PageTemplate[] $templates           Array or Iterator of selected templates, see `$allowedTemplates`, or Page Template Handles
     *     @var bool            $internal            Is this an internal only page type? Default: `false`
     *     @var bool            $ptLaunchInComposer  Does this launch in composer? Default: `false`
     *     @var bool            $ptIsFrequentlyAdded Should this always be displayed in the pages panel? Default: `false`
     * }
     * @param bool|Package $pkg This should be false if the type is not tied to a package, or a package object
     *
     * @return static|mixed|null
     */
    public static function add($data, $pkg = false)
    {
        $data = $data + array(
            'defaultTemplate' => null,
            'allowedTemplates' => null,
            'templates' => null,
            'internal' => null,
            'ptLaunchInComposer' => null,
            'ptIsFrequentlyAdded' => null,
        );
        $ptHandle = $data['handle'];
        $ptName = $data['name'];
        $ptDefaultPageTemplateID = 0;
        $ptIsFrequentlyAdded = 0;
        $ptLaunchInComposer = 0;
        $pkgID = 0;
        if (is_object($pkg)) {
            $pkgID = $pkg->getPackageID();
        }

        if (is_object($data['defaultTemplate'])) {
            $ptDefaultPageTemplateID = $data['defaultTemplate']->getPageTemplateID();
        } elseif (!empty($data['defaultTemplate'])) {
            $ptDefaultPageTemplateID = PageTemplate::getByHandle($data['defaultTemplate'])->getPageTemplateID();
        }
        $ptAllowedPageTemplates = 'A';
        if ($data['allowedTemplates']) {
            $ptAllowedPageTemplates = $data['allowedTemplates'];
        }
        $templates = array();
        if (is_array($data['templates'])) {
            $templates = $data['templates'];
        }
        $ptIsInternal = 0;
        if ($data['internal']) {
            $ptIsInternal = 1;
        }

        if ($data['ptLaunchInComposer']) {
            $ptLaunchInComposer = 1;
        }

        if ($data['ptIsFrequentlyAdded']) {
            $ptIsFrequentlyAdded = 1;
        }

        $db = Loader::db();
        $ptDisplayOrder = 0;
        $count = $db->GetOne('select count(ptID) from PageTypes where ptIsInternal = ?', array($ptIsInternal));
        if ($count > 0) {
            $ptDisplayOrder = $count;
        }

        $db->Execute(
            'insert into PageTypes (ptName, ptHandle, ptDefaultPageTemplateID, ptAllowedPageTemplates, ptIsInternal, ptLaunchInComposer, ptDisplayOrder, ptIsFrequentlyAdded, pkgID) values (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            array(
                $ptName,
                $ptHandle,
                $ptDefaultPageTemplateID,
                $ptAllowedPageTemplates,
                $ptIsInternal,
                $ptLaunchInComposer,
                $ptDisplayOrder,
                $ptIsFrequentlyAdded,
                $pkgID,
            )
        );
        $ptID = $db->Insert_ID();
        if ($ptAllowedPageTemplates != 'A') {
            foreach ($templates as $pt) {
                if (!is_object($pt)){
                    $pt = PageTemplate::getByHandle($pt);
                }
                $db->Execute(
                    'insert into PageTypePageTemplates (ptID, pTemplateID) values (?, ?)',
                    array(
                        $ptID,
                        $pt->getPageTemplateID(),
                    )
                );
            }
        }

        $ptt = static::getByID($ptID);

        // set all type publish target as default
        $target = PageTypePublishTargetType::getByHandle('all');
        if (is_object($target)) {
            $configuredTarget = $target->configurePageTypePublishTarget($ptt, array());
            $ptt->setConfiguredPageTypePublishTargetObject($configuredTarget);
        }

        // copy permissions from the defaults to the page type
        $cpk = PermissionKey::getByHandle('access_page_type_permissions');
        $permissions = PermissionKey::getList('page_type');
        foreach ($permissions as $pk) {
            $pk->setPermissionObject($ptt);
            $pk->copyFromDefaultsToPageType($cpk);
        }

        // now we clear the default from edit page drafts
        $pk = PermissionKey::getByHandle('edit_page_type_drafts');
        if (is_object($pk)) {
            $pk->setPermissionObject($ptt);
            $pt = $pk->getPermissionAssignmentObject();
            if (is_object($pt)) {
                $pt->clearPermissionAssignment();
            }
            // now we assign the page draft owner access entity
            $pa = PermissionAccess::create($pk);
            $pe = PageOwnerPermissionAccessEntity::getOrCreate();
            $pa->addListItem($pe);
            $pt->assignPermissionAccess($pa);

            return $ptt;
        }
    }

    public function update($data)
    {
        $ptHandle = $this->getPageTypeHandle();
        $ptName = $this->getPageTypeName();
        $ptDefaultPageTemplateID = $this->getPageTypeDefaultPageTemplateID();
        $ptAllowedPageTemplates = $this->getPageTypeAllowedPageTemplates();
        $ptIsFrequentlyAdded = $this->isPageTypeFrequentlyAdded();
        $ptLaunchInComposer = $this->doesPageTypeLaunchInComposer();
        $ptDisplayOrder = $this->getPageTypeDisplayOrder();

        if ($data['name']) {
            $ptName = $data['name'];
        }
        if ($data['handle']) {
            $ptHandle = $data['handle'];
        }
        if (is_object($data['defaultTemplate'])) {
            $ptDefaultPageTemplateID = $data['defaultTemplate']->getPageTemplateID();
        } elseif (!empty($data['defaultTemplate'])) {
            $ptDefaultPageTemplateID = PageTemplate::getByHandle($data['defaultTemplate'])->getPageTemplateID();
        }
        if ($data['allowedTemplates']) {
            $ptAllowedPageTemplates = $data['allowedTemplates'];
        }
        if (isset($data['ptLaunchInComposer'])) {
            $ptLaunchInComposer = $data['ptLaunchInComposer'];
        }
        if (isset($data['ptIsFrequentlyAdded'])) {
            $ptIsFrequentlyAdded = $data['ptIsFrequentlyAdded'];
        }
        if (isset($data['ptDisplayOrder'])) {
            $ptDisplayOrder = $data['ptDisplayOrder'];
        }

        $templates = $this->getPageTypePageTemplateObjects();
        if (is_array($data['templates'])) {
            $templates = $data['templates'];
        }
        $ptIsInternal = $this->isPageTypeInternal();
        if ($data['internal']) {
            $ptIsInternal = 1;
        }
        $db = Loader::db();
        $db->Execute(
            'update PageTypes set ptName = ?, ptHandle = ?, ptDefaultPageTemplateID = ?, ptAllowedPageTemplates = ?, ptIsInternal = ?, ptLaunchInComposer = ?, ptIsFrequentlyAdded = ?, ptDisplayOrder = ? where ptID = ?',
            array(
                $ptName,
                $ptHandle,
                $ptDefaultPageTemplateID,
                $ptAllowedPageTemplates,
                $ptIsInternal,
                $ptLaunchInComposer,
                $ptIsFrequentlyAdded,
                $ptDisplayOrder,
                $this->ptID,
            )
        );
        $db->Execute('delete from PageTypePageTemplates where ptID = ?', array($this->ptID));
        if ($ptAllowedPageTemplates != 'A') {
            foreach ($templates as $pt) {
                if (!is_object($pt)) {
                    $pt = PageTemplate::getByHandle($pt);
                }
                $db->Execute(
                    'insert into PageTypePageTemplates (ptID, pTemplateID) values (?, ?)',
                    array(
                        $this->ptID,
                        $pt->getPageTemplateID(),
                    )
                );
            }
        }
        $this->rescanPageTypeComposerOutputControlObjects();
        $this->rescanPageTypePageTemplateDefaultPages();
    }

    protected function rescanPageTypePageTemplateDefaultPages()
    {
        $db = Loader::db();
        $templates = $this->getPageTypePageTemplateObjects();
        $templateIDs = array();
        foreach ($templates as $template) {
            $templateIDs[] = $template->getPageTemplateID();
        }
        $existingDefaultTemplateIDs = $db->GetCol('select pTemplateID from PageTypePageTemplateDefaultPages where ptID = ?', array($this->getPageTypeID()));
        foreach ($existingDefaultTemplateIDs as $existingPageTemplateID) {
            if (!in_array($existingPageTemplateID, $templateIDs)) {
                $existingPageTemplate = Template::getByID($existingPageTemplateID);
                if (is_object($existingPageTemplate)) {
                    $c = $this->getPageTypePageTemplateDefaultPageObject($existingPageTemplate);
                    if (is_object($c)) {
                        $c->delete();
                    }
                }
                $db->Execute('delete from PageTypePageTemplateDefaultPages where pTemplateID = ? and ptID = ?', array($existingPageTemplateID, $this->getPageTypeID()));
            }
        }
    }

    public static function getList($includeInternal = false)
    {
        $db = Loader::db();
        if (!$includeInternal) {
            $ptIDs = $db->GetCol('select ptID from PageTypes where ptIsInternal = false order by ptDisplayOrder asc');
        } else {
            $ptIDs = $db->GetCol('select ptID from PageTypes order by ptDisplayOrder asc');
        }

        return static::returnList($ptIDs);
    }

    protected static function returnList($ptIDs)
    {
        if (!is_array($ptIDs)) {
            return array();
        }
        $list = array();
        foreach ($ptIDs as $ptID) {
            $cm = static::getByID($ptID);
            if (is_object($cm)) {
                $list[] = $cm;
            }
        }

        return $list;
    }

    public static function getFrequentlyUsedList()
    {
        $db = Loader::db();
        $ptIDs = $db->GetCol('select ptID from PageTypes where ptIsInternal = 0 and ptIsFrequentlyAdded = 1 order by ptDisplayOrder asc');

        return static::returnList($ptIDs);
    }

    public static function getInfrequentlyUsedList()
    {
        $db = Loader::db();
        $ptIDs = $db->GetCol('select ptID from PageTypes where ptIsInternal = 0 and ptIsFrequentlyAdded = 0 order by ptDisplayOrder asc');

        return static::returnList($ptIDs);
    }

    public static function getListByPackage(Package $pkg)
    {
        $db = Loader::db();
        $ptIDs = $db->GetCol(
            'select ptID from PageTypes where pkgID = ? order by ptDisplayOrder asc',
            array($pkg->getPackageID())
        );
        $list = array();
        foreach ($ptIDs as $ptID) {
            $cm = static::getByID($ptID);
            if (is_object($cm)) {
                $list[] = $cm;
            }
        }

        return $list;
    }

    public static function getListByDefaultPageTemplate($templateOrTemplateID)
    {
        $pTemplateID = is_object($templateOrTemplateID) ?
            $templateOrTemplateID->getPageTemplateID() : $templateOrTemplateID;

        $db = \Database::connection();
        $stmt = $db->prepare("SELECT ptID FROM PageTypes WHERE ptDefaultPageTemplateID = ?");
        $stmt->bindValue(1, $pTemplateID);
        $stmt->execute();

        $list = array();
        while ($ptID = $stmt->fetchColumn(0)) {
            $cm = static::getByID($ptID);
            if (is_object($cm)) {
                $list[] = $cm;
            }
        }
        return $list;
    }

    public static function getByID($ptID)
    {
        $cache = \Core::make('cache/request');
        $item = $cache->getItem(sprintf('pagetype/%s', $ptID));
        if (!$item->isMiss()) {
            return $item->get();
        }

        $db = Loader::db();
        $r = $db->GetRow('select * from PageTypes where ptID = ?', array($ptID));
        if (is_array($r) && isset($r['ptID']) && $r['ptID']) {
            $cm = new static();
            $cm->setPropertiesFromArray($r);
            $cm->ptPublishTargetObject = unserialize($r['ptPublishTargetObject']);
            $item->set($cm);

            return $cm;
        }
    }

    public static function getByHandle($ptHandle)
    {
        $db = Loader::db();
        $ptID = $db->GetOne('select ptID from PageTypes where ptHandle = ?', array($ptHandle));
        if ($ptID) {
            return static::getByID($ptID);
        }
    }

    public function delete()
    {
        $sets = PageTypeComposerFormLayoutSet::getList($this);
        foreach ($sets as $set) {
            $set->delete();
        }
        $db = Loader::db();
        $db->Execute('delete from PageTypes where ptID = ?', array($this->ptID));
        $db->Execute('delete from PageTypePageTemplates where ptID = ?', array($this->ptID));
        $db->Execute('delete from PageTypePageTemplateDefaultPages where ptID = ?', array($this->ptID));
        $db->Execute('delete from PageTypeComposerOutputControls where ptID = ?', array($this->ptID));

        foreach ($this->getPageTypePageTemplateObjects() as $pt) {
            $c = $this->getPageTypePageTemplateDefaultPageObject($pt);
            $c->delete();
        }
    }

    public function setConfiguredPageTypePublishTargetObject(PageTypePublishTargetConfiguration $configuredTarget)
    {
        $db = Loader::db();
        if (is_object($configuredTarget)) {
            $db->Execute(
                'update PageTypes set ptPublishTargetTypeID = ?, ptPublishTargetObject = ? where ptID = ?',
                array(
                    $configuredTarget->getPageTypePublishTargetTypeID(),
                    @serialize($configuredTarget),
                    $this->getPageTypeID(),
                )
            );
        }
    }

    public function rescanFormLayoutSetDisplayOrder()
    {
        $sets = PageTypeComposerFormLayoutSet::getList($this);
        $displayOrder = 0;
        foreach ($sets as $s) {
            $s->updateFormLayoutSetDisplayOrder($displayOrder);
            $displayOrder++;
        }
    }

    public function addPageTypeComposerFormLayoutSet($ptComposerFormLayoutSetName, $ptComposerFormLayoutSetDescription)
    {
        $db = Loader::db();
        $displayOrder = $db->GetOne(
            'select count(ptComposerFormLayoutSetID) from PageTypeComposerFormLayoutSets where ptID = ?',
            array($this->ptID)
        );
        if (!$displayOrder) {
            $displayOrder = 0;
        }
        $db->Execute(
            'insert into PageTypeComposerFormLayoutSets (ptComposerFormLayoutSetName, ptComposerFormLayoutSetDescription, ptID, ptComposerFormLayoutSetDisplayOrder) values (?, ?, ?, ?)',
            array(
                $ptComposerFormLayoutSetName,
                $ptComposerFormLayoutSetDescription,
                $this->ptID,
                $displayOrder,
            )
        );

        return PageTypeComposerFormLayoutSet::getByID($db->Insert_ID());
    }

    /**
     * Returns true if pages of the current type are allowed beneath the passed parent page.
     *
     * @param \Concrete\Core\Page\Page $page
     */
    public function canPublishPageTypeBeneathPage(\Concrete\Core\Page\Page $page)
    {
        $target = $this->getPageTypePublishTargetObject();
        if (is_object($target)) {
            return $target->canPublishPageTypeBeneathTarget($this, $page);
        }
    }

    /**
     * @return \Concrete\Core\Page\Type\Validator\ValidatorInterface|null
     */
    public function getPageTypeValidatorObject()
    {
        if ($this->ptHandle) {
            $validator = \Core::make('manager/page_type/validator')->driver($this->ptHandle);
            $validator->setPageTypeObject($this);

            return $validator;
        }
    }

    public function createDraft(PageTemplate $pt, $u = false)
    {
        if (!is_object($u)) {
            $u = new User();
        }
        $db = Loader::db();
        $ptID = $this->getPageTypeID();
        $parent = Page::getByPath(Config::get('concrete.paths.drafts'));
        $data = array('cvIsApproved' => 0, 'cIsActive' => false, 'cAcquireComposerOutputControls' => true);
        $p = $parent->add($this, $data, $pt);

        // now we setup in the initial configurated page target
        $target = $this->getPageTypePublishTargetObject();
        $cParentID = $target->getDefaultParentPageID();
        if ($cParentID > 0) {
            $p->setPageDraftTargetParentPageID($cParentID);
        }

        // we have to publish the controls to the page. i'm not sure why
        $controls = PageTypeComposerControl::getList($this);
        $outputControls = array();
        foreach ($controls as $cn) {
            $cn->publishToPage($p, array(), $controls);
        }

        // now we need to clear out the processed controls in case we
        // save again in the same request
        CorePagePropertyPageTypeComposerControl::clearComposerRequestProcessControls();

        return $p;
    }

    public function renderComposerOutputForm($page = null, $targetPage = null)
    {
        $env = \Environment::get();
        $rec = $env->getRecord(
            DIRNAME_ELEMENTS . '/' . DIRNAME_PAGE_TYPES . '/composer/form/output/form/' . $this->getPageTypeHandle() . '.php',
            $this->getPackageHandle()
        );
        if ($rec->exists()) {
            $pagetype = $this;
            include $rec->file;
        } else {
            Loader::element('page_types/composer/form/output/form', array(
                'pagetype' => $this,
                'page' => $page,
                'targetPage' => $targetPage,
            ));
        }
    }
}
