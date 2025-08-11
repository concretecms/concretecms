<?php

namespace Concrete\Core\Area;

use Concrete\Core\Area\CustomStyle as AreaCustomStyle;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Page\Collection\Collection;
use Concrete\Core\Page\Collection\Version\Version;
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
     * @return array|AreaCustomStyle[]
     */
    public function getCollectionVersionAreaStyles(Collection $collection): array
    {
        $this->loadCollectionVersionAreaStyleRecords($collection->getCollectionID(), $collection->getVersionID());
        $styles = [];
        foreach ($this->styles[$collection->getCollectionID()] as $arHandle => $issID) {
            $arHandle = $this->text->filterNonAlphaNum($arHandle);
            $obj = StyleSet::getByID($issID);
            if (is_object($obj)) {
                $a = new Area($arHandle);
                $obj = new AreaCustomStyle($obj, $a, $collection->getCollectionThemeObject());
                $styles[] = $obj;
            }
        }

        return $styles;
    }

    /**
     * @param \Concrete\Core\Page\Collection\Version\Version $version
     * @return array
     */
    public function getCollectionVersionAreaStyleIDs(Version $version): array
    {
        $this->loadCollectionVersionAreaStyleRecords($version->getCollectionID(), $version->getVersionID());

        return $this->styles[$version->getCollectionID()] ?? [];
    }

    /**
     * @param \Concrete\Core\Page\Stack\Stack $stack
     * @param \Concrete\Core\Page\Theme\Theme $theme
     * @return array|AreaCustomStyle[]
     */
    public function getStackAreaStyles(Stack $stack, Theme $theme): array
    {
        $this->loadCollectionVersionAreaStyleRecords($stack->getCollectionID(), $stack->getVersionID());
        $styles = [];
        foreach ($this->styles[$stack->getCollectionID()] as $arHandle => $issID) {
            $arHandle = $this->text->filterNonAlphaNum($arHandle);
            $obj = StyleSet::getByID($issID);
            if (is_object($obj)) {
                $a = new GlobalArea($arHandle);
                $obj = new AreaCustomStyle($obj, $a, $theme);
                $styles[] = $obj;
            }
        }

        return $styles;
    }

    protected function loadCollectionVersionAreaStyleRecords(int $collectionID, int $versionID): void
    {
        if (!isset($this->styles[$collectionID])) {
            $qb = $this->connection->createQueryBuilder();
            $qb->select('arHandle', 'issID')
                ->from('CollectionVersionAreaStyles')
                ->where('cID = :cID')
                ->andWhere('cvID = :cvID')
                ->andWhere('issID > 0')
                ->setParameter(':cID', $collectionID)
                ->setParameter(':cvID', $versionID);
            $r = $qb->execute()->fetchAllAssociative();
            if (count($r) > 0) {
                foreach ($r as $row) {
                    $this->styles[$collectionID][$row['arHandle']] = $row['issID'];
                }
            } else {
                $this->styles[$collectionID] = [];
            }
        }
    }
}