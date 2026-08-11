<?php
namespace Concrete\Core\Page\Search;

use Concrete\Core\Cache\Cache;
use Concrete\Core\Page\Page;
use Concrete\Core\Search\Index\IndexManagerInterface;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Utility\SearchIndexContentSanitizer;
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
    private $contentSanitizer;

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
        // Area handles are plain strings; no object classes are expected.
        $areas = $areas ? unserialize($areas, ['allowed_classes' => false]) : [];
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
        $blarray = [];
        $db = Loader::db();
        $r = $db->Execute(
            'SELECT `bID`, `arHandle` FROM `CollectionVersionBlocks` WHERE `cID` = ? AND `cvID` = ? ORDER BY `arHandle` ASC, `cbDisplayOrder` ASC',
            [$c->getCollectionID(), $c->getVersionID()]
        );
        while ($row = $r->fetch()) {
            if ($this->matchesArea($row['arHandle'])) {
                $b = Block::getByID($row['bID'], $c, $row['arHandle']);
                if (!is_object($b)) {
                    continue;
                }
                $bi = $b->getInstance();
                if (method_exists($bi, 'getSearchableContent')) {
                    $searchableContent = $this->sanitizeSearchableContent((string) $bi->getSearchableContent());
                    if ($searchableContent !== '') {
                        $text .= $searchableContent . ' ';
                    }
                }
                unset($b);
                unset($bi);
            }
        }

        // add content defined by a page type controller
        if ($pageController = $c->getPageController()) {
            $searchableContent = $this->sanitizeSearchableContent((string) $pageController->getSearchableContent());

            if ($searchableContent !== '') {
                $text .= $searchableContent . ' ';
            }
        }

        return $text;
    }

    protected function sanitizeSearchableContent(string $content): string
    {
        return $this->getContentSanitizer()->sanitize($content);
    }

    protected function getContentSanitizer(): SearchIndexContentSanitizer
    {
        if ($this->contentSanitizer === null) {
            $this->contentSanitizer = Application::getFacadeApplication()->make(SearchIndexContentSanitizer::class);
        }

        return $this->contentSanitizer;
    }

}
