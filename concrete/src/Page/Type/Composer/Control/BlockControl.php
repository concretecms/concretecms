<?php
namespace Concrete\Core\Page\Type\Composer\Control;

use Concrete\Core\Package\PackageList;
use Controller;
use Package;
use Block;
use BlockType;
use Environment;
use Concrete\Core\Page\Page;
use Area;
use PageTemplate;
use Concrete\Core\Page\Type\Composer\FormLayoutSet as PageTypeComposerFormLayoutSet;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\View\View;

class BlockControl extends Control
{
    protected $btID;
    protected $ptComposerControlTypeHandle = 'block';
    protected $bt = false;
    protected $b = false;
    protected $controller;

    public function setBlockTypeID($btID)
    {
        $this->btID = $btID;
        $this->setPageTypeComposerControlIdentifier($btID);
    }

    public function getBlockTypeID()
    {
        return $this->btID;
    }

    public function export($node)
    {
        $bt = $this->getBlockTypeObject();
        $node->addAttribute('handle', $bt->getBlockTypeHandle());
    }

    public function shouldPageTypeComposerControlStripEmptyValuesFromPage()
    {
        return true;
    }

    public function removePageTypeComposerControlFromPage()
    {
        $b = $this->getPageTypeComposerControlBlockObject($this->page);
        $b->deleteBlock();
    }

    public function onPageDraftCreate(Page $c)
    {
        return $this->publishToPage($c, [], []);
    }

    public function getPageTypeComposerControlBlockObject(Page $c)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make('database')->connection();
        if (!is_object($this->b)) {
            $setControl = $this->getPageTypeComposerFormLayoutSetControlObject();
            $r = $db->fetchAssoc(
                $q = 'select cdb.bID, cdb.arHandle from PageTypeComposerOutputBlocks cdb inner join CollectionVersionBlocks cvb on (cdb.bID = cvb.bID and cvb.cID = cdb.cID and cvb.cvID = ?) where cdb.ptComposerFormLayoutSetControlID = ? and cvb.cID = ? and cvb.cvID = ?',
                [
                    $c->getVersionID(),
                    $setControl->getPageTypeComposerFormLayoutSetControlID(),
                    $c->getCollectionID(),
                    $c->getVersionID(),
                ]
            );
            if (!$r) {
                // this is the first run. so we look for the proxy block.
                $pt = PageTemplate::getByID($c->getPageTemplateID());
                $outputControl = $setControl->getPageTypeComposerOutputControlObject($pt);
                if (is_object($outputControl)) {
                    $cm = $c->getPageTypeObject();
                    $mc = $cm->getPageTypePageTemplateDefaultPageObject($pt);
                    $r = $db->fetchAssoc(
                        'select bco.bID, cvb.arHandle from btCorePageTypeComposerControlOutput bco inner join CollectionVersionBlocks cvb on cvb.bID = bco.bID where ptComposerOutputControlID = ? and cvb.cID = ?',
                        [
                            $outputControl->getPageTypeComposerOutputControlID(),
                            $mc->getCollectionID(),
                        ]
                    );
                }
            }
            if ($r) {
                $b = Block::getByID($r['bID'], $c, $r['arHandle']);
                $this->setPageTypeComposerControlBlockObject($b);

                return $this->b;
            }
        }

        return $this->b;
    }

    public function setPageTypeComposerControlBlockObject($b)
    {
        $this->b = $b;
    }

    public function getBlockTypeObject()
    {
        if (!is_object($this->bt)) {
            $this->bt = BlockType::getByID($this->btID);
        }

        return $this->bt;
    }

    public function getPageTypeComposerControlPageNameValue(Page $dc)
    {
        if (is_object($this->b)) {
            $controller = $this->b->getController();

            return $controller->getPageTypeComposerControlPageNameValue();
        }
    }

    public function canPageTypeComposerControlSetPageName()
    {
        $bt = $this->getBlockTypeObject();
        $controller = $bt->getController();
        if (method_exists($controller, 'getPageTypeComposerControlPageNameValue')) {
            return true;
        }

        return false;
    }

    public function isPageTypeComposerControlValueEmpty()
    {
        $bt = $this->getBlockTypeObject();
        $controller = $bt->getController();
        if (method_exists($controller, 'isPageTypeComposerControlValueEmpty')) {
            $bx = $this->getPageTypeComposerControlBlockObject($this->page);
            if (is_object($bx)) {
                $controller = $bx->getController();

                return $controller->isPageTypeComposerControlValueEmpty();
            }
        }

        return false;
    }

    public function pageTypeComposerFormControlSupportsValidation()
    {
        $bt = $this->getBlockTypeObject();
        $controller = $bt->getController();
        if (method_exists($controller, 'validate_composer')) {
            return true;
        }

        return false;
    }

    public function addAssetsToRequest(Controller $cnt)
    {
        $bt = $this->getBlockTypeObject();
        $controller = $bt->getController();
        $controller->setupAndRun('composer');
    }

    public function getPageTypeComposerControlCustomTemplates()
    {
        $bt = $this->getBlockTypeObject();
        $app = Application::getFacadeApplication();
        $txt = $app->make('helper/text');
        $templates = [];
        if (is_object($bt)) {
            $blocktemplates = $bt->getBlockTypeComposerTemplates();
            if (is_array($blocktemplates)) {
                foreach ($blocktemplates as $tpl) {
                    if (strpos($tpl, '.') !== false) {
                        $name = substr($txt->unhandle($tpl), 0, strrpos($tpl, '.'));
                    } else {
                        $name = $txt->unhandle($tpl);
                    }
                    $templates[] = new CustomTemplate($tpl, $name);
                }
            }
        }

        return $templates;
    }

    public function addToPageTypeComposerFormLayoutSet(PageTypeComposerFormLayoutSet $set, $import = false)
    {
        $layoutSetControl = parent::addToPageTypeComposerFormLayoutSet($set, $import);
        $pagetype = $set->getPageTypeObject();
        $pagetype->rescanPageTypeComposerOutputControlObjects();

        return $layoutSetControl;
    }

    protected function getController($obj)
    {
        if (!isset($this->controller)) {
            $this->controller = $obj->getController();
            $this->controller->setupAndRun('composer');
        }

        return $this->controller;
    }

    public function render($label, $customTemplate, $description)
    {
        $obj = $this->getPageTypeComposerControlDraftValue();
        if (!is_object($obj)) {
            if ($this->page) {
                // we HAVE a page, but we don't have a block object, which means something has gone wrong.
                // we've lost the association. So we abort.
                View::element('page_types/composer/controls/invalid_block');

                return;
            }
            $obj = $this->getBlockTypeObject();
        }

        $this->getController($obj);
        $app = Application::getFacadeApplication();
        $env = Environment::get();
        $form = $app->make('helper/form');
        $set = $this->getPageTypeComposerFormLayoutSetControlObject()->getPageTypeComposerFormLayoutSetObject();

        if ($customTemplate) {
            $rec = $env->getRecord(
                DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle(
                ) . '/' . DIRNAME_BLOCK_TEMPLATES_COMPOSER . '/' . $customTemplate
            );
            if ($rec->exists()) {
                $template = DIRNAME_BLOCK_TEMPLATES_COMPOSER . '/' . $customTemplate;
            } else {
                foreach (PackageList::get()->getPackages() as $pkg) {
                    $file =
                        (is_dir(DIR_PACKAGES . '/' . $pkg->getPackageHandle()) ? DIR_PACKAGES : DIR_PACKAGES_CORE)
                        . '/' . $pkg->getPackageHandle() . '/' . DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES_COMPOSER . '/' .
                        $customTemplate;
                    if (file_exists($file)) {
                        $template = DIRNAME_BLOCK_TEMPLATES_COMPOSER . '/' . $customTemplate;
                        break;
                    }
                }
            }
        }

        if (!isset($template)) {
            $template = FILENAME_BLOCK_COMPOSER;
        }

        $this->inc($template, ['control' => $this, 'obj' => $obj, 'description' => $description]);
    }

    public function action($task)
    {
        $obj = $this->getPageTypeComposerControlDraftValue();
        if (!is_object($obj)) {
            // we don't have a page, an area, or ANYTHING YET.
            $arguments = ['/ccm/system/block/action/add_composer',
                $this->getPageTypeComposerFormLayoutSetControlObject()->getPageTypeComposerFormLayoutSetControlID(),
                $task,
            ];

            return call_user_func_array(['\URL', 'to'], $arguments);
        } else {
            $area = $obj->getBlockAreaObject();
            $c = $area->getAreaCollectionObject();
            $arguments = [
                '/ccm/system/block/action/edit_composer',
                $c->getCollectionID(),
                urlencode($area->getAreaHandle()),
                $this->getPageTypeComposerFormLayoutSetControlObject()->getPageTypeComposerFormLayoutSetControlID(),
                $task,
            ];

            return call_user_func_array(['\URL', 'to'], $arguments);
        }
    }

    public function inc($file, $args = [])
    {
        extract($args);
        if (!isset($obj)) {
            $obj = $this->getPageTypeComposerControlDraftValue();
            if (!is_object($obj)) {
                $obj = $this->getBlockTypeObject();
            }
        }

        $controller = $this->getController($obj);

        extract($controller->getSets());
        extract($controller->getHelperObjects());
        $label = $this->getPageTypeComposerFormLayoutSetControlObject()->getPageTypeComposerControlDisplayLabel();
        $env = Environment::get();

        $pkg = false;
        if ($obj->getPackageID() > 0) {
            $pkg = Package::getByID($obj->getPackageID());
        }

        $view = $this;

        $r = $env->getRecord(DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle() . '/' . $file, $pkg);
        if ($r->exists()) {
            include $r->file;
        } else {
            foreach (PackageList::get()->getPackages() as $pkg) {
                $include =
                    (is_dir(DIR_PACKAGES . '/' . $pkg->getPackageHandle()) ? DIR_PACKAGES : DIR_PACKAGES_CORE)
                    . '/' . $pkg->getPackageHandle() . '/' . DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle() . '/' .
                    $file;
                if (file_exists($include)) {
                    include $include;
                }
            }
        }
    }

    public function getPageTypeComposerControlDraftValue()
    {
        if (is_object($this->page)) {
            return $this->getPageTypeComposerControlBlockObject($this->page);
        }
    }

    public function publishToPage(Page $c, $data, $controls)
    {
        // for blocks, we need to also grab their output
        $bt = $this->getBlockTypeObject();
        $pt = PageTemplate::getByID($c->getPageTemplateID());
        $setControl = $this->getPageTypeComposerFormLayoutSetControlObject();

        $b = $this->getPageTypeComposerControlBlockObject($c);
        if (!is_object($b)) {
            return;
        }

        // delete the block that this set control has placed on this version, because
        // we are going to replace it with a new one.
        $app = Application::getFacadeApplication();
        $db = $app->make('database')->connection();
        $q = 'select cvb.arHandle, cdb.bID, cvb.cvID, cdb.cbDisplayOrder from PageTypeComposerOutputBlocks cdb inner join CollectionVersionBlocks cvb on (cdb.bID = cvb.bID and cvb.cID = cdb.cID and cvb.cvID = ?) where cdb.ptComposerFormLayoutSetControlID = ? and cvb.cID = ? and cvb.cvID = ?';
        $v = [$c->getVersionID(), $setControl->getPageTypeComposerFormLayoutSetControlID(), $c->getCollectionID(), $c->getVersionID()];
        $row = $db->fetchAssoc($q, $v);
        if ($row && $row['bID'] && $row['arHandle']) {
            $db->executeQuery(
                'delete from PageTypeComposerOutputBlocks where ptComposerFormLayoutSetControlID = ? and cID = ? and cvID = ?',
                [$setControl->getPageTypeComposerFormLayoutSetControlID(), $c->getCollectionID(), $c->getVersionID()]
            );
        }

        $arHandle = $b->getAreaHandle();
        $blockDisplayOrder = $b->getBlockDisplayOrder();
        $bFilename = $b->getBlockFilename();
        $defaultStyles = $b->getCustomStyle();
        $b->deleteBlock();
        $ax = Area::getOrCreate($c, $arHandle);
        $b = $c->addBlock($bt, $ax, $data);
        $this->setPageTypeComposerControlBlockObject($b);
        $b->setAbsoluteBlockDisplayOrder($blockDisplayOrder);
        if ($bFilename) {
            $b->setCustomTemplate($bFilename);
        }

        if ($defaultStyles) {
            $b->setCustomStyleSet($defaultStyles->getStyleSet());
        }

        // make a reference to the new block
        $this->recordPageTypeComposerOutputBlock($b);
    }

    public function recordPageTypeComposerOutputBlock(\Concrete\Core\Block\Block $block)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make('database')->connection();
        $setControl = $this->getPageTypeComposerFormLayoutSetControlObject();
        $oc = $block->getBlockCollectionObject();
        $ocID = $oc->getCollectionID();
        $ovID = $oc->getVersionID();

        $db->executeQuery(
            'insert into PageTypeComposerOutputBlocks (cID, cvID, arHandle, ptComposerFormLayoutSetControlID, cbDisplayOrder, bID) values (?, ?, ?, ?, ?, ?)',
            [
                $ocID,
                $ovID,
                $block->getAreaHandle(),
                $setControl->getPageTypeComposerFormLayoutSetControlID(),
                $block->getBlockDisplayOrder(),
                $block->getBlockID(),
            ]
        );
    }

    public function validate()
    {
        $b = $this->getPageTypeComposerControlBlockObject($this->page);
        if (is_object($b)) {
            $controller = $b->getController();
            if (method_exists($controller, 'validate_composer')) {
                $e1 = $controller->validate_composer();
            }
            if (is_object($e1)) {
                return $e1;
            }
        }
    }
}
