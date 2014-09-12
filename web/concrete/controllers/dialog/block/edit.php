<?php
namespace Concrete\Controller\Dialog\Block;

use Concrete\Controller\Backend\UserInterface\Block as BackendInterfaceBlockController;
use Concrete\Core\Block\View\BlockView;
use BlockType;
use Concrete\Core\Cache\Cache;
use Core;
use Area;

class Edit extends BackendInterfaceBlockController
{

    protected $viewPath = '/dialogs/block/edit';

    public function view()
    {
        $bv = new BlockView($this->block);
        if (isset($_REQUEST['arGridMaximumColumns'])) {
            $this->area->setAreaGridMaximumColumns(intval($_REQUEST['arGridMaximumColumns']));
        }
        if (isset($_REQUEST['arEnableGridContainer']) && $_REQUEST['arEnableGridContainer'] == 1) {
            $this->area->enableGridContainer();
        }
        $bv->addScopeItems(array('c' => $this->page, 'a' => $this->area, 'dialogController' => $this));
        $this->set('bv', $bv);
    }

    public function submit()
    {
        if ($this->validateAction() && $this->canAccess()) {

            $c = $this->page;
            $a = \Area::get($this->page, $_REQUEST['arHandle']);
            $ax = $a;
            $cx = $c;
            if ($a->isGlobalArea()) {
                $ax = STACKS_AREA_NAME;
                $cx = \Stack::getByName($_REQUEST['arHandle']);
            }
            $b = \Block::getByID($_REQUEST['bID'], $cx, $ax);

            $pr = new \Concrete\Core\Page\EditResponse();
            $pr->setPage($this->page);

            $bi = $b->getInstance();
            if ($b->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) {
                $_b = \Block::getByID($bi->getOriginalBlockID());
                $bi = $_b->getInstance(); // for validation
            }
            $e = $bi->validate($_POST);
            $pr->setAdditionalDataAttribute('aID', $a->getAreaID());
            $pr->setAdditionalDataAttribute('arHandle', $a->getAreaHandle());
            $pr->setError($e);

            if ((!is_object($e)) || (($e instanceof \Concrete\Core\Error\Error) && (!$e->has()))) {
                $bt = BlockType::getByHandle($b->getBlockTypeHandle());
                if (!$bt->includeAll()) {
                    // we make sure to create a new version, if necessary
                    $nvc = $cx->getVersionToModify();
                } else {
                    $nvc = $cx; // keep the same one
                }

                if ($a->isGlobalArea()) {
                    $xvc = $c->getVersionToModify(); // we need to create a new version of THIS page as well.
                    $xvc->relateVersionEdits($nvc);
                }

                $ob = $b;
                // replace the block with the version of the block in the later version (if applicable)
                $b = \Block::getByID($_REQUEST['bID'], $nvc, $ax);

                if ($b->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) {
                    // if we're editing a scrapbook display block, we add a new block in this position for the real block type
                    // set the block to the display order
                    // delete the scrapbook display block, and save the data
                    /*
                    $originalDisplayOrder = $b->getBlockDisplayOrder();
                    $btx = BlockType::getByHandle($_b->getBlockTypeHandle());
                    $nb = $nvc->addBlock($btx, $ax, array());
                    $nb->setAbsoluteBlockDisplayOrder($originalDisplayOrder);
                    $b->deleteBlock();
                    $b = &$nb;
                    */

                    $originalDisplayOrder = $b->getBlockDisplayOrder();
                    $cnt = $b->getController();
                    $ob = \Block::getByID($cnt->getOriginalBlockID());
                    $ob->loadNewCollection($nvc);
                    if (!is_object($ax)) {
                        $ax = Area::getOrCreate($cx, $ax);
                    }
                    $ob->setBlockAreaObject($ax);
                    $nb = $ob->duplicate($nvc);
                    $nb->setAbsoluteBlockDisplayOrder($originalDisplayOrder);
                    $b->deleteBlock();
                    $b = & $nb;

                } else {
                    if ($b->isAlias()) {

                        // then this means that the block we're updating is an alias. If you update an alias, you're actually going
                        // to duplicate the original block, and update the newly created block. If you update an original, your changes
                        // propagate to the aliases
                        $nb = $ob->duplicate($nvc);
                        $b->deleteBlock();
                        $b = &$nb;
                    }
                }

                $pr->setAdditionalDataAttribute('bID', $b->getBlockID());
                // we can update the block that we're submitting
                $b->update($_POST);
            }
            $pr->outputJSON();
        }
    }

    protected function canAccess()
    {
        return $this->permissions->canEditBlock();
    }

}

