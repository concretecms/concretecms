<?php
namespace Concrete\Core\Page\Search;

use Concrete\Core\Cache\Cache;
use Concrete\Core\Page\Page;
use Concrete\Core\Search\Index\IndexManagerInterface;
use Concrete\Core\Support\Facade\Application;
use Loader;
use Config;
use PageList;
use Collection;
use Area;
use Concrete\Core\Area\SubArea;
use Block;
use stdClass;

class IndexedSearch
{
    public $searchBatchSize;
    public $searchReindexTimeout;

    private $cPathSections = array();
    private $searchableAreaNames;

    public function __construct()
    {
        $this->searchReindexTimeout = Config::get('concrete.misc.page_search_index_lifetime');
        $this->searchBatchSize = Config::get('concrete.limits.page_search_index_batch');
    }

    public function getSearchableAreaAction()
    {
        $action = Config::get('concrete.misc.search_index_area_method');
        if (!strlen($action)) {
            $action = 'blacklist';
        }

        return $action;
    }

    public function getSavedSearchableAreas()
    {
        $areas = Config::get('concrete.misc.search_index_area_list');
        $areas = unserialize($areas);
        if (!is_array($areas)) {
            $areas = array();
        }

        return $areas;
    }

    public function clearSearchIndex()
    {
        $db = Loader::db();
        $db->Execute('truncate table PageSearchIndex');
    }

    public function matchesArea($arHandle)
    {
        if (!isset($this->arHandles)) {
            $searchableAreaNamesInitial = $this->getSavedSearchableAreas();
            if ($this->getSearchableAreaAction() == 'blacklist') {
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
        if (is_object($page) && ($page instanceof Collection) && ($page->getAttribute('exclude_search_index') != 1)) {
            $datetime = Loader::helper('date')->getOverridableNow();
            $db->Replace(
                'PageSearchIndex',
                array(
                    'cID' => $page->getCollectionID(),
                    'cName' => $page->getCollectionName(),
                    'cDescription' => $page->getCollectionDescription(),
                    'cPath' => $page->getCollectionPath(),
                    'cDatePublic' => $page->getCollectionDatePublic(),
                    'content' => $this->getBodyContentFromPage($page),
                    'cDateLastIndexed' => $datetime,
                ),
                array('cID'),
                true
            );
        } else {
            $db->Execute('delete from PageSearchIndex where cID = ?', array($page->getCollectionID()));
        }
    }

    public function getBodyContentFromPage($c)
    {
        $text = '';

        $tagsToSpaces = array(
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
        );
        $blarray = array();
        $db = Loader::db();
        $r = $db->Execute(
            'select bID, arHandle from CollectionVersionBlocks where cID = ? and cvID = ?',
            array($c->getCollectionID(), $c->getVersionID())
        );
        $th = Loader::helper('text');
        while ($row = $r->FetchRow()) {
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

        return $text;
    }

    /**
     * Reindexes the search engine.
     */
    public function reindexAll($fullReindex = false)
    {
        Cache::disableAll();

        /** @var IndexManagerInterface $indexStack */
        $indexStack = Application::getFacadeApplication()->make(IndexManagerInterface::class);

        $db = Loader::db();

        if ($fullReindex) {
            $db->Execute("truncate table PageSearchIndex");
        }

        $pl = new PageList();
        $pl->ignoreAliases();
        $pl->ignorePermissions();
        $pl->sortByCollectionIDAscending();
        $pl->filter(
            false,
            '(c.cDateModified > psi.cDateLastIndexed or UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(psi.cDateLastIndexed) > ' . $this->searchReindexTimeout . ' or psi.cID is null or psi.cDateLastIndexed is null)'
        );
        $pl->filter(false, '(ak_exclude_search_index is null or ak_exclude_search_index = 0)');
        $pages = $pl->get($this->searchBatchSize);

        $num = 0;
        foreach ($pages as $c) {
            $indexStack->index(Page::class, $c);
        }

        $pnum = Collection::reindexPendingPages();
        $num = $num + $pnum;

        Cache::enableAll();
        $result = new stdClass();
        $result->count = $num;

        return $result;
    }
}
