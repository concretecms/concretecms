<?php
namespace Concrete\Controller\Backend\UserInterface;

use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Page\Page as ConcretePage;
use Concrete\Core\Permission\Checker as Permissions;

abstract class Block extends Page
{
    protected $page;
    protected $area;
    protected $block;

    protected function getEditResponse($b, $e = null)
    {
        $pr = new \Concrete\Core\Page\EditResponse();
        $pr->setPage($this->page);
        $pr->setAdditionalDataAttribute('aID', intval($this->area->getAreaID()));
        $pr->setAdditionalDataAttribute('arHandle', $this->area->getAreaHandle());
        $pr->setAdditionalDataAttribute('bID', intval($b->getBlockID()));
        if ($e) {
            $pr->setError($e);
        }
        return $pr;
    }

    public function on_start()
    {
        parent::on_start();
        $request = $this->request;
        $arHandle = $request->query->get('arHandle');
        if (!$arHandle) {
            $arHandle = $request->request->get('arHandle');
        }
        $bID = $request->query->get('bID');
        if (!$bID) {
            $bID = $request->request->get('bID');
        }
        $a = \Concrete\Core\Area\Area::get($this->page, $arHandle);
        if (!is_object($a)) {
            throw new UserMessageException('Invalid Area');
        }
        $this->area = $a;
        if (!$a->isGlobalArea()) {
            $this->set('isGlobalArea', false);
            $b = \Concrete\Core\Block\Block::getByID($bID, $this->page, $a);
        } else {
            $this->set('isGlobalArea', true);
            $stack = \Concrete\Core\Page\Stack\Stack::getByName($arHandle);
            $sc = ConcretePage::getByID($stack->getCollectionID(), 'RECENT');
            $b = \Concrete\Core\Block\Block::getByID($bID, $sc, STACKS_AREA_NAME);
            if ($b) {
                $b->setBlockAreaObject($a); // set the original area object
            }
        }
        if (!$b) {
            throw new UserMessageException(t('Access Denied'));
        }
        $this->block = $b;
        $this->permissions = new \Concrete\Core\Permission\Checker($b);
        $this->set('bp', $this->permissions);
        $this->set('b', $b);
    }

    public function getViewObject()
    {
        if ($this->permissions->canViewEditInterface() && $this->canAccess()) {
            return \Concrete\Core\Controller\Controller::getViewObject();
        }
        throw new UserMessageException(t('Access Denied'));
    }

    protected function getBlockToEdit()
    {
        $ax = $this->area;
        $cx = $this->page;
        if ($this->area->isGlobalArea()) {
            $ax = STACKS_AREA_NAME;
            $cx = \Concrete\Core\Page\Stack\Stack::getByName($_REQUEST['arHandle']);
        }

        $nvc = $cx->getVersionToModify();
        if ($this->area->isGlobalArea()) {
            $xvc = $this->page->getVersionToModify(); // we need to create a new version of THIS page as well.
            $xvc->relateVersionEdits($nvc);
        }

        $b = \Concrete\Core\Block\Block::getByID($_REQUEST['bID'], $nvc, $ax);

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
            $ob = \Concrete\Core\Block\Block::getByID($cnt->getOriginalBlockID());
            $ob->loadNewCollection($nvc);
            if (!is_object($ax)) {
                $ax = \Concrete\Core\Area\Area::getOrCreate($cx, $ax);
            }
            $ob->setBlockAreaObject($ax);
            $nb = $ob->duplicate($nvc);
            $nb->setAbsoluteBlockDisplayOrder($originalDisplayOrder);
            $b->deleteBlock();
            $b = &$nb;
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
