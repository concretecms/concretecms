<?php
namespace Concrete\Core\Page\Stack;

use Concrete\Core\Multilingual\Page\Section\Section;
use Loader;
use Concrete\Core\Legacy\PageList;
use Page;
use \Concrete\Core\Legacy\DatabaseItemList;

class StackList extends PageList
{

    public function __construct()
    {
        $this->ignoreAliases = true;
        $this->ignorePermissions = true;
        $this->filterByPageTypeHandle(STACKS_PAGE_TYPE);
        $this->addToQuery('inner join Stacks on Stacks.cID = p1.cID');
        $this->sortBy('p1.cDisplayOrder', 'asc');
    }

    public function filterByGlobalAreas()
    {
        $this->filter('stType', Stack::ST_TYPE_GLOBAL_AREA);
    }

    public function filterByUserAdded()
    {
        $this->filter('stType', Stack::ST_TYPE_USER_ADDED);
    }

    public function filterByStackCategory(StackCategory $category)
    {
        $this->filterByParentID($category->getPage()->getCollectionID());
    }

    public function filterByPageLanguage(\Concrete\Core\Page\Page $page)
    {
        $ms = Section::getBySectionOfSite($page);
        if (!is_object($ms)) {
            $ms = static::getPreferredSection();
        }

        if (is_object($ms)) {
            $this->filterByLanguageSection($ms);
        }

    }

    public function filterByLanguageSection(Section $ms)
    {
        $this->filter('stMultilingualSection', $ms->getCollectionID());
    }


    public static function export(\SimpleXMLElement $x)
    {
        $db = Loader::db();
        $r = $db->Execute('select stName, cID, stType from Stacks order by stName asc');
        if ($r->NumRows()) {
            $gas = $x->addChild('stacks');
            while ($row = $r->FetchRow()) {
                $stack = Stack::getByName($row['stName']);
                if (is_object($stack)) {
                    $stack->export($gas);
                }
            }
        }
    }

    public function get($itemsToGet = 0, $offset = 0)
    {
        if ($this->getQuery() == '') {
            $this->setBaseQuery();
        }
        $stacks = array();
        $this->setItemsPerPage($itemsToGet);
        $r = DatabaseItemList::get($itemsToGet, $offset);
        foreach ($r as $row) {
            $s = Stack::getByID($row['cID'], 'RECENT');
            $stacks[] = $s;
        }
        return $stacks;
    }

    public static function rescanMultilingualStacks()
    {
        $sl = new static();
        $stacks = $sl->get();
        foreach($stacks as $stack) {
            $section = $stack->getMultilingualSection();
            if (!$section) {
                $section = false;
                $parent = \Page::getByID($stack->getCollectionParentID());
                if ($parent->getCollectionPath() == STACKS_PAGE_PATH) {
                    // this is the default
                    $section = Section::getDefaultSection();
                } else if ($parent->getPageTypeHandle() == STACK_CATEGORY_PAGE_TYPE) {
                    $locale = $parent->getCollectionHandle();
                    $section = Section::getByLocale($locale);
                }

                if ($section) {
                    $stack->updateMultilingualSection($section);
                }
            }
        }

    }

}
