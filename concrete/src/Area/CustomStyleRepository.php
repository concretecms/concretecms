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
     * @return \Concrete\Core\Area\CustomStyle[]
     */
    public function getCollectionVersionAreaStyles(Collection $collection): array
    {
        $versionID = $collection->getVersionID();
        if ($versionID === null) {
            return [];
        }
        $collectionID = $collection->getCollectionID();
        $this->loadCollectionVersionAreaStyleRecords($collectionID, $versionID);
        $styles = [];
        foreach ($this->styles[$collectionID][$versionID] as $arHandle => $issID) {
            $arHandle = $this->text->filterNonAlphaNum($arHandle);
            $obj = StyleSet::getByID($issID);
            if (is_object($obj)) {
                $a = new Area($arHandle);
                $styles[] = new AreaCustomStyle($obj, $a, $collection->getCollectionThemeObject());
            }
        }

        return $styles;
    }

    /**
     * @param \Concrete\Core\Page\Collection\Version\Version $version
     * @return array<string, int> the ID of the style set of each area, by area handle
     */
    public function getCollectionVersionAreaStyleIDs(Version $version): array
    {
        $versionID = $version->getVersionID();
        if ($versionID === null) {
            return [];
        }
        $collectionID = $version->getCollectionID();
        $this->loadCollectionVersionAreaStyleRecords($collectionID, $versionID);

        return $this->styles[$collectionID][$versionID];
    }

    /**
     * @param \Concrete\Core\Page\Stack\Stack $stack
     * @param \Concrete\Core\Page\Theme\Theme $theme
     * @return \Concrete\Core\Area\CustomStyle[]
     */
    public function getStackAreaStyles(Stack $stack, Theme $theme): array
    {
        $versionID = $stack->getVersionID();
        if ($versionID === null) {
            return [];
        }
        $collectionID = $stack->getCollectionID();
        $this->loadCollectionVersionAreaStyleRecords($collectionID, $versionID);
        $styles = [];
        foreach ($this->styles[$collectionID][$versionID] as $issID) {
            $obj = StyleSet::getByID($issID);
            if (is_object($obj)) {
                $a = new GlobalArea($stack->getStackName());
                $styles[] = new AreaCustomStyle($obj, $a, $theme);
            }
        }

        return $styles;
    }

    protected function loadCollectionVersionAreaStyleRecords(int $collectionID, ?int $versionID): void
    {
        if ($versionID === null) {
            return;
        }

        if (!isset($this->styles[$collectionID][$versionID])) {
            $qb = $this->connection->createQueryBuilder();
            $qb->select('arHandle', 'issID')
                ->from('CollectionVersionAreaStyles')
                ->where('cID = :cID')
                ->andWhere('cvID = :cvID')
                ->andWhere('issID > 0')
                ->setParameter(':cID', $collectionID)
                ->setParameter(':cvID', $versionID);
            $r = $qb->execute()->fetchAllAssociative();
            $this->styles[$collectionID][$versionID] = [];
            foreach ($r as $row) {
                $this->styles[$collectionID][$versionID][$row['arHandle']] = $row['issID'];
            }
        }
    }
}
