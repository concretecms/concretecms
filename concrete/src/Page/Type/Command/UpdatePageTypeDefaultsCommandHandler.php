<?php

namespace Concrete\Core\Page\Type\Command;

use Concrete\Core\Block\Block;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\Command\UpdatePageTypeDefaultsCommand;

class UpdatePageTypeDefaultsCommandHandler
{

    public function __invoke(UpdatePageTypeDefaultsCommand $command)
    {
        $page = Page::getByID($command->getPageID(), $command->getCollectionVersionID());

        $blocksToUpdate = json_decode($command->getBlocksToUpdate());
        $blocksToAdd = json_decode($command->getBlocksToAdd());
        $handlesToOrder = [];

        $pageTypeDefaultPage = Page::getByID($command->getPageTypeDefaultPageID());
        if (!$pageTypeDefaultPage->isMasterCollection()) {
            return;
        }

        foreach ($blocksToAdd as $blockToAdd) {
            $pageTypeBlock = Block::getByID($blockToAdd->bID, $pageTypeDefaultPage, $blockToAdd->pageTypeArHandle);
            $pageTypeBlock->alias($page);
            $addedChildPageblock = Block::getByID($pageTypeBlock->getBlockID(), $page, $blockToAdd->pageTypeArHandle);
            $addedChildPageblock->setAbsoluteBlockDisplayOrder($blockToAdd->actualDisplayOrder);
        }

        foreach ($blocksToUpdate as $blockToUpdate) {
            $pageBlock = Block::getByID($blockToUpdate->bID, $page, $blockToUpdate->arHandle);
            array_merge(
                $handlesToOrder,
                $this->processBlockUpdateActions($blockToUpdate->actions, $pageTypeDefaultPage, $page, $pageBlock)
            );
        }

        foreach ($handlesToOrder as $handleToOrder) {
            $page->rescanDisplayOrder($handleToOrder);
        }
    }

    private function processBlockUpdateActions($actions, $pageTypeDefaultPage, $page, $pageBlock)
    {
        $db = \Database::connection();
        $handlesToOrder = [];
        /* @var Block $blockService */
        foreach ($actions as $action) {
            // Update all forked pages by page type
            if ($action->name == 'update_forked') {
                $pageTypeBlock = Block::getByID($action->pageTypeBlockID, $pageTypeDefaultPage,
                    $action->pageTypeArHandle);
                $bt = $pageTypeBlock->getBlockTypeObject();

                $pageBlock->deleteBlock();

                if ($bt->isCopiedWhenPropagated()) {
                    $pageBlock = $pageTypeBlock->duplicate($page, true);
                } else {
                    $pageTypeBlock->alias($page);
                }
                // Update block area by page type, if changed
            } elseif ($action->name == 'change_arHandle') {
                $actualArHandle = $action->actualArHandle;
                $pageCollectionID = $page->getCollectionID();
                $pageVersionID = $page->getVersionID();

                $db->executeQuery(
                    'UPDATE CollectionVersionBlockStyles SET arHandle = ?  WHERE cID = ? and cvID = ? and bID = ?',
                    [$actualArHandle, $pageCollectionID, $pageVersionID, $pageBlock->getBlockID()]
                );
                $db->executeQuery(
                    'UPDATE CollectionVersionBlocks SET arHandle = ?  WHERE cID = ? and cvID = ? and bID = ?',
                    [$actualArHandle, $pageCollectionID, $pageVersionID, $pageBlock->getBlockID()]
                );
                // Update display order by page type
            } elseif ($action->name == 'change_display_order') {
                $pageBlock->setAbsoluteBlockDisplayOrder($action->actualDisplayOrder);
                array_push($handlesToOrder, $action->actualArHandle);
                // If block doesn't appear in page type, delete it
            } elseif ($action->name == 'delete') {
                $pageBlock->deleteBlock();
            }
        }

        return $handlesToOrder;
    }



}