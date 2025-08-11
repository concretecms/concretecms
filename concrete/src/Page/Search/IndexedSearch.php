<?php
namespace Concrete\Core\Page\Search;

use Concrete\Core\Cache\Cache;
use Concrete\Core\Page\Page;
use Concrete\Core\Search\Index\IndexManagerInterface;
use Concrete\Core\Support\Facade\Application;
use Loader;
use Config;
use PageList;
use Concrete\Core\Page\Collection\Collection;
use Concrete\Core\Area\Area;
use Concrete\Core\Area\SubArea;
use Block;
use stdClass;

class IndexedSearch
{
    public $searchBatchSize;
    public $searchReindexTimeout;

    private $cPathSections = [];
    private $searchableAreaNames;

    public function __construct()
    {
        $this->searchReindexTimeout = Config::get('concrete.misc.page_search_index_lifetime');
        $this->searchBatchSize = Config::get('concrete.limits.page_search_index_batch');
    }

    public static function getSearchableAreaAction()
    {
        $action = (string) Config::get('concrete.misc.search_index_area_method');
        if ($action === '') {
            $action = 'denylist';
        }

        return $action;
    }

    public static function getSavedSearchableAreas()
    {
        $areas = Config::get('concrete.misc.search_index_area_list');
        $areas = $areas ? unserialize($areas) : [];
        if (!is_array($areas)) {
            $areas = [];
        }

        return $areas;
    }

    public static function clearSearchIndex()
    {
        $db = Loader::db();
        $db->Execute('truncate table PageSearchIndex');
    }

    public function matchesArea($arHandle)
    {
        if (!isset($this->searchableAreaNames)) {
            $searchableAreaNamesInitial = $this->getSavedSearchableAreas();
            if ('denylist' == $this->getSearchableAreaAction()) {
                $areas = Area::getHandleList();
                foreach ($areas as $blArHandle) {
                    if (!in_array($blArHandle, $searchableAreaNamesInitial)) {
                        $this->searchableAreaNames[] = $blArHandle;
                    }
                }
            } else {
                $this->searchableAreaNames = $searchableAreaNamesInitial;
            }
        }

        foreach ($this->searchableAreaNames as $sarHandle) {
            if (preg_match('/^' . preg_quote($sarHandle . SubArea::AREA_SUB_DELIMITER, '/') . '.+/i', $arHandle)) {
                return true;
            } else {
                if (in_array($arHandle, $this->searchableAreaNames)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function reindexPage($page)
    {
        $db = Loader::db();
        if (is_object($page) && ($page instanceof Collection) && (1 != $page->getAttribute('exclude_search_index'))) {
            $datetime = Loader::helper('date')->getOverridableNow();
            $db->Replace(
                'PageSearchIndex',
                [
                    'cID' => $page->getCollectionID(),
                    'cName' => $page->getCollectionName(),
                    'cDescription' => $page->getCollectionDescription(),
                    'cPath' => $page->getCollectionPath(),
                    'cDatePublic' => $page->getCollectionDatePublic(),
                    'content' => $this->getBodyContentFromPage($page),
                    'cDateLastIndexed' => $datetime,
                ],
                ['cID'],
                true
            );
        } else {
            $db->Execute('delete from PageSearchIndex where cID = ?', [$page->getCollectionID()]);
        }
    }

    public function getBodyContentFromPage($c)
    {
        $text = '';

        $tagsToSpaces = [
            '<br>',
            '<br/>',
            '<br />',
            '<p>',
            '</p>',
            '</ p>',
            '<div>',
            '</div>',
            '</ div>',
            '&nbsp;',
        ];
        $blarray = [];
        $db = Loader::db();
        $r = $db->Execute(
            'SELECT `bID`, `arHandle` FROM `CollectionVersionBlocks` WHERE `cID` = ? AND `cvID` = ? ORDER BY `arHandle` ASC, `cbDisplayOrder` ASC',
            [$c->getCollectionID(), $c->getVersionID()]
        );
        $th = Loader::helper('text');
        while ($row = $r->fetch()) {
            if ($this->matchesArea($row['arHandle'])) {
                $b = Block::getByID($row['bID'], $c, $row['arHandle']);
                if (!is_object($b)) {
                    continue;
                }
                $bi = $b->getInstance();
                if (method_exists($bi, 'getSearchableContent')) {
                    $searchableContent = $bi->getSearchableContent();
                    if (strlen(trim($searchableContent))) {
                        $text .= $th->decodeEntities(
                                strip_tags(str_ireplace($tagsToSpaces, ' ', $searchableContent)),
                                ENT_QUOTES,
                                APP_CHARSET
                            ) . ' ';
                    }
                }
                unset($b);
                unset($bi);
            }
        }

        // add content defined by a page type controller
        if ($pageController = $c->getPageController()) {
            $searchableContent = $pageController->getSearchableContent();

            if (trim((string) $searchableContent) !== '') {
                $text .= $th->decodeEntities(
                        strip_tags(str_ireplace($tagsToSpaces, ' ', $searchableContent)),
                        ENT_QUOTES,
                        APP_CHARSET
                    ) . ' ';
            }
        }

        return $text;
    }

}
