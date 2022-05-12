<?php

namespace Concrete\Controller\Backend\Block;

use Concrete\Core\Area\Area;
use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Stack\Pile\Pile;
use Concrete\Core\Page\Stack\Pile\PileContent;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Permission\Checker;
use Concrete\Core\User\User;
use Concrete\Core\Validation\CSRF\Token;
use Symfony\Component\HttpFoundation\Response as SymphonyResponse;

defined('C5_EXECUTE') or die('Access Denied.');

class Process extends AbstractController
{
    /**
     * @param int $cID
     * @param string $arHandle
     * @param int $bID
     * @param int $pcID
     * @param int|null $dragAreaBlockID
     * @param int|null $orphanedBlockID
     *
     * @throws \Concrete\Core\Error\UserMessageException
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function alias($cID, $arHandle, $pcID, $dragAreaBlockID = null, $orphanedBlockID = null, $stackBlockID = null): SymphonyResponse
    {
        $isOrphanedBlock = (int) $orphanedBlockID > 0;
        $isStackBlock = (int) $stackBlockID > 0;
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

        if ($isStackBlock) {
            $b = Block::getByID($stackBlockID);
        } else if ($isOrphanedBlock) {
            $b = Block::getByID($orphanedBlockID);
        } else {
            $pc = PileContent::get($pcID);
            if (!$pc || $pc->isError() || $pc->getItemType() !== 'BLOCK') {
                throw new UserMessageException(t('Unable to find the specified block'));
            }
            $b = Block::getByID($pc->getItemID());
        }

        if (!$b) {
            throw new UserMessageException(t('Unable to find the specified block'));
        }

        $bID = $b->getBlockID();

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

        if ($isOrphanedBlock) {
            $nb = $b->duplicate($nvc);
            $nb->move($nvc, $ax);
            if (!$nb) {
                throw new UserMessageException(t('Unable to find the specified block'));
            }
            $b->delete(true);
        } else {
            if (!$bt->isCopiedWhenPropagated()) {
                $btx = BlockType::getByHandle(BLOCK_HANDLE_SCRAPBOOK_PROXY);
                $nb = $nvc->addBlock($btx, $ax, ['bOriginalID' => $bID]);
            } else {
                $nb = $b->duplicate($nvc, 'duplicate_clipboard');
                $nb->move($nvc, $ax);
                if (!$nb) {
                    throw new UserMessageException(t('Unable to find the specified block'));
                }
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

    public function copy(int $cID, string $arHandle, int $bID): SymphonyResponse
    {
        $valt = $this->app->make(Token::class);
        if (!($valt->validate('tools/clipboard/to'))) {
            throw new UserMessageException($valt->getErrorMessage());
        }
        $u = $this->app->make(User::class);
        if (!$u->isRegistered()) {
            throw new UserMessageException(t('Access Denied.'));
        }
        $c = Page::getByID($cID);
        $cp = new Checker($c);
        if (!$cp->canViewPage()) {
            throw new UserMessageException(t('Access Denied.'));
        }
        $a = Area::get($c, $arHandle);
        if (!$a || $a->isError()) {
            throw new UserMessageException(t('Unable to find the area specified.'));
        }
        $ap = new Checker($a);
        if (!$ap->canViewArea()) {
            throw new UserMessageException(t('Access Denied.'));
        }
        if ($a->isGlobalArea()) {
            $ax = STACKS_AREA_NAME;
            $cx = Stack::getByName($arHandle);
            if (!$cx || $cx->isError()) {
                throw new UserMessageException(t('Unable to find the stack specified.'));
            }
        } else {
            $cx = $ax = null;
        }
        $b = Block::getByID($bID, $cx, $ax);
        if ($b && !$b->isError() && $b->getBlockTypeHandle() === BLOCK_HANDLE_SCRAPBOOK_PROXY) {
            $bi = $b->getInstance();
            $b = Block::getByID($bi->getOriginalBlockID());
        }
        if (!$b || $b->isError()) {
            throw new UserMessageException(t('Unable to find the block specified.'));
        }
        $p = Pile::getDefault();

        $p->add($b);

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    public function removeFromClipboard($pcID, $cID)
    {
        $u = $this->app->make(User::class);
        if (!$u->isRegistered()) {
            throw new UserMessageException(t('Access Denied.'));
        }
        $valt = $this->app->make(Token::class);
        if (!($valt->validate('tools/clipboard/from'))) {
            throw new UserMessageException($valt->getErrorMessage());
        }
        $c = Page::getByID($cID);
        $cp = new Checker($c);
        if (!$cp->canViewPage()) {
            die(t('Access Denied.'));
        }
        $pileContent = PileContent::get($pcID);
        if (!$pileContent || !$pileContent->getPileContentID() || !$pileContent->getPile()->isMyPile()) {
            throw new UserMessageException(t('Access Denied.'));
        }
        $pileContent->delete();

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }
}
