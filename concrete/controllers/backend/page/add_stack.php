<?php
namespace Concrete\Controller\Backend\Page;

use Concrete\Controller\Backend\UserInterface\Page;
use Concrete\Core\Area\Area;
use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Permission\Checker;

class AddStack extends Page
{
    public function addStack()
    {
        $token = $this->app->make('token');
        if (!$token->validate()){
            throw new UserMessageException($token->getErrorMessage());
        }
        $valn = $this->app->make('helper/validation/numbers');
        $stID = $this->request->request->get('stID', $this->request->query->get('stID'));
        $stack = $valn->integer($stID, 1) ? Stack::getByID((int) $stID) : null;
        if (!$stack) {
            throw new UserMessageException(t('Invalid stack.'));
        }
        $arHandle = $this->request->query->get('arHandle');
        $a = Area::get($this->page, $arHandle);
        if (!$a) {
            throw new UserMessageException(t('Unable to find the requested area'));
        }
        if ($a->isGlobalArea()) {
            $cx = Stack::getByName($arHandle);
            if (!$cx) {
                throw new UserMessageException(t('Unable to find the requested stack'));
            }
            $ax = Area::get($this->page, STACKS_AREA_NAME);
        } else {
            $cx = $this->page;
            $ax = $a;
        }
        $ap = new Checker($ax);

        if (!$ap->canAddStackToArea($stack)) {
            throw new UserMessageException(t('The stack contains invalid block types.'));
        }
        // we've already run permissions on the stack at this point, at least for viewing the stack.
        $btx = BlockType::getByHandle(BLOCK_HANDLE_STACK_PROXY);
        $nvc = $cx->getVersionToModify();
        if ($a->isGlobalArea()) {
            $xvc = $this->page->getVersionToModify(); // we need to create a new version of THIS page as well.
            $xvc->relateVersionEdits($nvc);
        }
        $nb = $nvc->addBlock($btx, $ax, ['stID' => $stack->getCollectionID()]);
        $dragAreaBlockID = $this->request->request->get('dragAreaBlockID', $this->request->query->get('dragAreaBlockID'));
        if ($valn->integer($dragAreaBlockID, 1)) {
            $afterBlock = Block::getByID($dragAreaBlockID) ?: null;
        } else {
            $afterBlock = null;
        }
        $nb->moveBlockToDisplayOrderPosition($afterBlock);

        return $this->app->make(ResponseFactoryInterface::class)->json([
            'aID' => $a->getAreaID(),
            'arHandle' => $a->getAreaHandle(),
            'cID' => $this->page->getCollectionID(),
            'bID' => $nb->getBlockID(),
            'error' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Backend\UserInterface::canAccess()
     */
    protected function canAccess()
    {
        return $this->permissions->canEditPageContents();
    }
}
