<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Page\Page;
use Concrete\Core\StyleCustomizer\Inline\StyleSet;

abstract class AbstractPageContentRoutine extends AbstractRoutine
{

    public function importPageAreas(Page $page, \SimpleXMLElement $px)
    {
        foreach ($px->area as $ax) {
            if (isset($ax->blocks)) {
                foreach ($ax->blocks->block as $bx) {
                    if ($bx['type'] != '') {
                        // we check this because you might just get a block node with only an mc-block-id, if it's an alias
                        $bt = BlockType::getByHandle((string) $bx['type']);
                        if (!is_object($bt)) {
                            throw new \Exception(t('Invalid block type handle: %s', strval($bx['type'])));
                        }
                        $btc = $bt->getController();
                        $btc->import($page, (string) $ax['name'], $bx);
                    } else {
                        if ($bx['mc-block-id'] != '') {
                            // we find that block in the master collection block pool and alias it out
                            $bID = array_search((string) $bx['mc-block-id'], ContentImporter::getMasterCollectionTemporaryBlockIDs());
                            if ($bID) {
                                $mc = Page::getByID($page->getMasterCollectionID(), 'RECENT');
                                $block = Block::getByID($bID, $mc, (string) $ax['name']);
                                $block->alias($page);

                                if ($block->getBlockTypeHandle() == BLOCK_HANDLE_LAYOUT_PROXY) {
                                    // we have to go get the blocks on that page in this layout.
                                    $btc = $block->getController();
                                    $arLayout = $btc->getAreaLayoutObject();
                                    $columns = $arLayout->getAreaLayoutColumns();
                                    foreach ($columns as $column) {
                                        $area = $column->getAreaObject();
                                        $blocks = $area->getAreaBlocksArray($mc);
                                        foreach ($blocks as $_b) {
                                            $_b->alias($page);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if (isset($ax->style)) {
                $area = \Area::get($page, (string) $ax['name']);
                $set = StyleSet::import($ax->style);
                $page->setCustomStyleSet($area, $set);
            }
        }
    }



}
