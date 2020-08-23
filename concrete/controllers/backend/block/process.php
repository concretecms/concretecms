<?php

namespace Concrete\Controller\Backend\Block;

use Concrete\Core\Area\Area;
use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Stack\Pile\PileContent;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Permission\Checker;
use Symfony\Component\HttpFoundation\Response as SymphonyResponse;

class Process extends AbstractController
{
    /**
     * @param int $cID
     * @param string $arHandle
     * @param int $bID
     * @param int $pcID
     * @param int|null $dragAreaBlockID
     *
     * @throws \Concrete\Core\Error\UserMessageException
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function alias($cID, $arHandle, $pcID, $dragAreaBlockID = null): SymphonyResponse
    {
        $token = $this->app->make('token');
        if (!$token->validate()) {
            throw new UserMessageException($token->getErrorMessage());
        }
        $c = Page::getByID($cID);
        if (!$c || $c->isError()) {
            throw new UserMessageException(t('Unable to find the specified page'));
        }
        $this->request->setCurrentPage($c);
        $a = Area::getOrCreate($c, $arHandle);
        if ($a->isGlobalArea()) {
            $cx = Stack::getByName($arHandle);
            $ax = Area::get($c, STACKS_AREA_NAME);
        } else {
            $cx = $c;
            $ax = $a;
        }
        $pc = PileContent::get($pcID);
        if (!$pc || $pc->isError() || $pc->getItemType() !== 'BLOCK') {
            throw new UserMessageException(t('Unable to find the specified block'));
        }
        $bID = $pc->getItemID();
        $b = Block::getByID($bID);
        if (!$b) {
            throw new UserMessageException(t('Unable to find the specified block'));
        }
        $b->setBlockAreaObject($ax);
        $bt = BlockType::getByHandle($b->getBlockTypeHandle());
        $ap = new Checker($ax);
        if (!$ap->canAddBlock($bt)) {
            throw new UserMessageException(t('Access Denied'));
        }
        $nvc = $cx->getVersionToModify();
        if ($a->isGlobalArea()) {
            $xvc = $c->getVersionToModify(); // we need to create a new version of THIS page as well.
            $xvc->relateVersionEdits($nvc);
        }
        if (!$bt->isCopiedWhenPropagated()) {
            $btx = BlockType::getByHandle(BLOCK_HANDLE_SCRAPBOOK_PROXY);
            $nb = $nvc->addBlock($btx, $ax, ['bOriginalID' => $bID]);
        } else {
            $nb = $b->duplicate($nvc);
            $nb->move($nvc, $ax);
            if (!$nb) {
                throw new UserMessageException(t('Unable to find the specified block'));
            }
        }

        $nb->refreshCache();
        $dragAreaBlockID = (int) $dragAreaBlockID;
        if ($dragAreaBlockID !== 0) {
            $db = Block::getByID($dragAreaBlockID, $cx, $ax);
        } else {
            $db = null;
        }
        $nb->moveBlockToDisplayOrderPosition($db ?: null);
        $nb->refreshCache();

        return $this->app->make(ResponseFactoryInterface::class)->json([
            'aID' => (int) $a->getAreaID(),
            'arHandle' => $a->getAreaHandle(),
            'cID' => (int) $c->getCollectionID(),
            'bID' => (int) $nb->getBlockID(),
        ]);
    }
}
