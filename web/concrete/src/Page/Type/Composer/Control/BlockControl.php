<?php
namespace Concrete\Core\Page\Type\Composer\Control;

use Loader;
use \Concrete\Core\Foundation\Object;
use Controller;
use Package;
use Block;
use BlockType;
use Environment;
use Page;
use Area;
use PageTemplate;
use \Concrete\Core\Page\Type\Composer\FormLayoutSet as PageTypeComposerFormLayoutSet;

class BlockControl extends Control
{

    protected $btID;
    protected $ptComposerControlTypeHandle = 'block';
    protected $bt = false;
    protected $b = false;

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

    protected function getPageTypeComposerControlBlockObject(Page $c)
    {
        $db = Loader::db();
        if (!is_object($this->b)) {
            $setControl = $this->getPageTypeComposerFormLayoutSetControlObject();
            $r = $db->GetRow(
                'select bID, arHandle from PageTypeComposerOutputBlocks where cID = ? and ptComposerFormLayoutSetControlID = ?',
                array(
                    $c->getCollectionID(),
                    $setControl->getPageTypeComposerFormLayoutSetControlID()
                )
            );
            if (!$r['bID']) {
                // this is the first run. so we look for the proxy block.
                $pt = PageTemplate::getByID($c->getPageTemplateID());
                $outputControl = $setControl->getPageTypeComposerOutputControlObject($pt);
                if (is_object($outputControl)) {
                    $cm = $c->getPageTypeObject();
                    $mc = $cm->getPageTypePageTemplateDefaultPageObject($pt);
                    $r = $db->GetRow(
                        'select bco.bID, cvb.arHandle from btCorePageTypeComposerControlOutput bco inner join CollectionVersionBlocks cvb on cvb.bID = bco.bID where ptComposerOutputControlID = ? and cvb.cID = ?',
                        array(
                            $outputControl->getPageTypeComposerOutputControlID(),
                            $mc->getCollectionID()
                        )
                    );
                }
            }
            if ($r['bID']) {
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
        $txt = Loader::helper('text');
        $templates = array();
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

    public function render($label, $customTemplate, $description)
    {
        $obj = $this->getPageTypeComposerControlDraftValue();
        if (!is_object($obj)) {
            if ($this->page) {
                // we HAVE a page, but we don't have a block object, which means something has gone wrong.
                // we've lost the association. So we abort.
                Loader::element('page_types/composer/controls/invalid_block');
                return;
            }
            $obj = $this->getBlockTypeObject();
        }

        $cnt = $obj->getController();
        $cnt->setupAndRun('composer');

        $env = Environment::get();
        $form = Loader::helper('form');
        $set = $this->getPageTypeComposerFormLayoutSetControlObject()->getPageTypeComposerFormLayoutSetObject();

        if ($customTemplate) {
            $rec = $env->getRecord(
                DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle(
                ) . '/' . DIRNAME_BLOCK_TEMPLATES_COMPOSER . '/' . $customTemplate
            );
            if ($rec->exists()) {
                $template = DIRNAME_BLOCK_TEMPLATES_COMPOSER . '/' . $customTemplate;
            }
        }

        if (!isset($template)) {
            $template = FILENAME_BLOCK_COMPOSER;
        }

        $this->inc($template, array('view' => $this, 'control' => $this, 'obj' => $obj, 'description' => $description));
    }

    public function inc($file, $args = array())
    {
        extract($args);
        if (!isset($obj)) {
            $obj = $this->getPageTypeComposerControlDraftValue();
            if (!is_object($obj)) {
                $obj = $this->getBlockTypeObject();
            }
        }
        $controller = $obj->getController();
        extract($controller->getSets());
        extract($controller->getHelperObjects());
        $label = $this->getPageTypeComposerFormLayoutSetControlObject()->getPageTypeComposerControlDisplayLabel();
        $env = Environment::get();


        $pkg = false;
        if ($obj->getPackageID() > 0) {
            $pkg = Package::getByID($obj->getPackageID());
        }

        $path = $env->getPath(DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle() . '/' . $file, $pkg);
        include($path);
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
        $db = Loader::db();
        $q = 'select cvb.arHandle, cdb.bID, cdb.cbDisplayOrder from PageTypeComposerOutputBlocks cdb inner join CollectionVersionBlocks cvb on (cdb.bID = cvb.bID and cvb.cID = cdb.cID and cvb.cvID = ?) where cdb.ptComposerFormLayoutSetControlID = ? and cdb.cID = ?';
        $v = array($c->getVersionID(), $setControl->getPageTypeComposerFormLayoutSetControlID(), $c->getCollectionID());
        $row = $db->GetRow($q, $v);
        if ($row['bID'] && $row['arHandle']) {
            $db->Execute(
                'delete from PageTypeComposerOutputBlocks where ptComposerFormLayoutSetControlID = ? and cID = ?',
                array($setControl->getPageTypeComposerFormLayoutSetControlID(), $c->getCollectionID())
            );
        }

        $arHandle = $b->getAreaHandle();
        $blockDisplayOrder = $b->getBlockDisplayOrder();
        $b->deleteBlock();
        $ax = Area::getOrCreate($c, $arHandle);
        $b = $c->addBlock($bt, $ax, $data);
        $this->setPageTypeComposerControlBlockObject($b);
        $b->setAbsoluteBlockDisplayOrder($blockDisplayOrder);

        // make a reference to the new block
        $db = Loader::db();
        $db->Execute(
            'insert into PageTypeComposerOutputBlocks (cID, arHandle, ptComposerFormLayoutSetControlID, cbDisplayOrder, bID) values (?, ?, ?, ?, ?)',
            array(
                $c->getCollectionID(),
                $arHandle,
                $setControl->getPageTypeComposerFormLayoutSetControlID(),
                $b->getBlockDisplayOrder(),
                $b->getBlockID()
            )
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
