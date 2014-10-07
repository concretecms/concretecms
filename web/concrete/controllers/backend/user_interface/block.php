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

    public function __construct()
    {
        parent::__construct();
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

        //if this block is being changed, make sure it's a new version of the block.
        if ($b->isAlias()) {
            $nb = $b->duplicate($nvc);
            $b->deleteBlock();
            $b = $nb;
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

