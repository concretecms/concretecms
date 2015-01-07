<?php
namespace Concrete\Controller\Backend\UserInterface;

use Exception;
use Page as ConcretePage;
use Permissions;

abstract class Block extends Page
{

    protected $page;
    protected $area;
    protected $block;

    public function on_start()
    {
        parent::on_start();
        $request = $this->request;
        $arHandle = $request->query->get('arHandle');
        $bID = $request->query->get('bID');
        $a = \Area::get($this->page, $arHandle);
        if (!is_object($a)) {
            throw new \Exception('Invalid Area');
        }
        $this->area = $a;
        if (!$a->isGlobalArea()) {
            $b = \Block::getByID($bID, $this->page, $a);
            $this->set('isGlobalArea', false);
        } else {
            $stack = \Stack::getByName($arHandle);
            $sc = ConcretePage::getByID($stack->getCollectionID(), 'RECENT');
            $b = \Block::getByID($bID, $sc, STACKS_AREA_NAME);
            $b->setBlockAreaObject($a); // set the original area object
            $this->set('isGlobalArea', true);
        }
        $this->block = $b;
        $this->permissions = new \Permissions($b);
        $this->set('bp', $this->permissions);
        $this->set('b', $b);
    }

    public function getViewObject()
    {
        if ($this->permissions->canViewEditInterface() && $this->canAccess()) {
            return \Concrete\Core\Controller\Controller::getViewObject();
        }
        throw new Exception(t('Access Denied'));
    }

    protected function getBlockToEdit()
    {
        $ax = $this->area;
        $cx = $this->page;
        if ($this->area->isGlobalArea()) {
            $ax = STACKS_AREA_NAME;
            $cx = \Stack::getByName($_REQUEST['arHandle']);
        }


        $b = \Block::getByID($_REQUEST['bID'], $cx, $ax);
        $nvc = $cx->getVersionToModify();
        if ($this->area->isGlobalArea()) {
            $xvc = $this->page->getVersionToModify(); // we need to create a new version of THIS page as well.
            $xvc->relateVersionEdits($nvc);
        }

        $b->loadNewCollection($nvc);

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
                $nb = $b->duplicate($nvc);
                $b->deleteBlock();
                $b = $nb;
            }
        }

        return $b;
    }

    public function action()
    {
        $url = call_user_func_array('parent::action', func_get_args());
        $url .= '&arHandle=' . urlencode($this->area->getAreaHandle());
        $url .= '&bID=' . $this->block->getBlockID();
        return $url;
    }

}

