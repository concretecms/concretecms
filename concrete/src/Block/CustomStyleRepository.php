<?php

namespace Concrete\Core\Block;

use Concrete\Core\Area\Area;
use Concrete\Core\Area\GlobalArea;
use Concrete\Core\Block\CustomStyle as BlockCustomStyle;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Page\Collection\Collection;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\StyleCustomizer\Inline\StyleSet;
use Concrete\Core\Utility\Service\Text;

class CustomStyleRepository
{
    protected $connection;
    protected $text;
    protected $styles = [];

    function __construct(Connection $connection, Text $text)
    {
        $this->connection = $connection;
        $this->text = $text;
    }

    /**
     * @param \Concrete\Core\Page\Collection\Collection $collection
     * @return array|BlockCustomStyle[]
     */
    public function getCollectionVersionBlockStyles(Collection $collection): array
    {
        $this->loadCollectionVersionBlockStyleRecords($collection->getCollectionID(), $collection->getVersionID());
        $styles = [];
        foreach ($this->styles[$collection->getCollectionID()] as $arHandle => $blocks) {
            foreach ($blocks as $bID => $issID) {
                $arHandle = $this->text->filterNonAlphaNum($arHandle);
                $obj = StyleSet::getByID($issID);
                if (is_object($obj)) {
                    $b = new Block();
                    $b->bID = $bID;
                    $a = new Area($arHandle);
                    $b->setBlockAreaObject($a);
                    $obj = new BlockCustomStyle($obj, $b, $collection->getCollectionThemeObject());
                    $styles[] = $obj;
                }
            }
        }

        return $styles;
    }

    /**
     * @param \Concrete\Core\Page\Stack\Stack $stack
     * @param \Concrete\Core\Page\Theme\Theme $theme
     * @return array|BlockCustomStyle[]
     */
    public function getStackBlockStyles(Stack $stack, Theme $theme): array
    {
        $this->loadCollectionVersionBlockStyleRecords($stack->getCollectionID(), $stack->getVersionID());
        $styles = [];
        foreach ($this->styles[$stack->getCollectionID()] as $blocks) {
            foreach ($blocks as $bID => $issID) {
                $obj = StyleSet::getByID($issID);
                if (is_object($obj)) {
                    $b = new Block();
                    $b->bID = $bID;
                    $a = new GlobalArea($stack->getStackName());
                    $b->setBlockAreaObject($a);
                    $obj = new BlockCustomStyle($obj, $b, $theme);
                    $styles[] = $obj;
                }
            }
        }

        return $styles;
    }

    /**
     * @param \Concrete\Core\Block\Block $block
     * @return int|null
     */
    public function getBlockStyleID(Block $block): ?int
    {
        $collection = $block->getBlockCollectionObject();
        $blockAreaHandle = $block->getAreaHandle();
        if ($blockAreaHandle && is_object($collection)) {
            $this->loadCollectionVersionBlockStyleRecords($collection->getCollectionID(), $collection->getVersionID());
            $a = $block->getBlockAreaObject();
            if ($a->isGlobalArea()) {
                // then we need to check against the global area name. We currently have the wrong area handle passed in
                $blockAreaHandle = STACKS_AREA_NAME;
            }
            foreach ($this->styles[$collection->getCollectionID()] as $arHandle => $blocks) {
                if ($arHandle == $blockAreaHandle) {
                    foreach ($blocks as $bID => $issID) {
                        if ($bID == $block->getBlockID()) {
                            return (int) $issID;
                        }
                    }
                }
            }
        }

        return null;
    }

    protected function loadCollectionVersionBlockStyleRecords(int $collectionID, int $versionID)
    {
        if (!isset($this->styles[$collectionID])) {
            $qb = $this->connection->createQueryBuilder();
            $qb->select('bID', 'arHandle', 'issID')
                ->from('CollectionVersionBlockStyles')
                ->where('cID = :cID')
                ->andWhere('cvID = :cvID')
                ->andWhere('issID > 0')
                ->setParameter(':cID', $collectionID)
                ->setParameter(':cvID', $versionID);
            $r = $qb->execute()->fetchAllAssociative();
            if (count($r) > 0) {
                foreach ($r as $row) {
                    $this->styles[$collectionID][$row['arHandle']][$row['bID']] = $row['issID'];
                }
            } else {
                $this->styles[$collectionID] = [];
            }
        }
    }
}