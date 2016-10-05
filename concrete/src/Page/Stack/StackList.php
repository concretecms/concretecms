<?php
namespace Concrete\Core\Page\Stack;

use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Page\Stack\Folder\Folder;
use Concrete\Core\Site\Tree\TreeInterface;
use Loader;
use Concrete\Core\Page\PageList;
use Concrete\Core\Search\StickyRequest;

class StackList extends PageList
{
    protected $foldersFirst;

    public function __construct()
    {
        parent::__construct();
        $this->foldersFirst = false;
        $this->query->leftJoin('p', 'Stacks', 's', 's.cID = p.cID');
        $this->ignorePermissions();
        $this->filterByPath(STACKS_PAGE_PATH);
        $this->filter(false, '(s.stMultilingualSection is null or s.stMultilingualSection = 0)');
        $this->includeRootPages();
        $this->sortByName();
    }

    public function filterBySiteTree(TreeInterface $tree)
    {
        $this->query->andWhere('s.siteTreeID = :stackSiteTreeID');
        $this->query->setParameter('stackSiteTreeID', $tree->getSiteTreeID());

    }

    public function setupAutomaticSorting(StickyRequest $request = null)
    {
        parent::setupAutomaticSorting($request);
        if ($this->foldersFirst) {
            $previousOrderBy = $this->query->getQueryPart('orderBy');
            $this->query->orderBy('pt.ptHandle', 'desc');
            $this->query->add('orderBy', $previousOrderBy, true);
        }
    }

    /**
     * Should we list stack folders first?
     *
     * @param bool $value
     */
    public function setFoldersFirst($value)
    {
        $this->foldersFirst = (bool) $value;
    }

    /**
     * Should we list stack folders first?
     *
     * @return bool
     */
    public function getFoldersFirst()
    {
        return $this->foldersFirst;
    }

    public function filterByFolder(Folder $folder)
    {
        $this->filterByParentID($folder->getPage()->getCollectionID());
    }

    public function filterByGlobalAreas()
    {
        $this->filter('stType', Stack::ST_TYPE_GLOBAL_AREA);
    }

    public function excludeGlobalAreas()
    {
        $this->filter(false, 'stType != '.Stack::ST_TYPE_GLOBAL_AREA.' or stType is null');
    }

    public function filterByUserAdded()
    {
        $this->filter('stType', Stack::ST_TYPE_USER_ADDED);
    }

    public function filterByStackCategory(StackCategory $category)
    {
        $this->filterByParentID($category->getPage()->getCollectionID());
    }

    /**
     * @param $queryRow
     *
     * @return \Stack
     */
    public function getResult($queryRow)
    {
        $stack = Stack::getByID($queryRow['cID'], 'ACTIVE');

        return $stack ?: parent::getResult($queryRow);
    }

    /*
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
    */

    /*
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

    public static function rescanMultilingualStacks()
    {
        $sl = new static();
        $stacks = $sl->get();
        foreach ($stacks as $stack) {
            $section = $stack->getMultilingualSection();
            if (!$section) {
                $section = false;
                $parent = \Page::getByID($stack->getCollectionParentID());
                if ($parent->getCollectionPath() == STACKS_PAGE_PATH) {
                    // this is the default
                    $section = Section::getDefaultSection();
                } elseif ($parent->getPageTypeHandle() == STACK_CATEGORY_PAGE_TYPE) {
                    $locale = $parent->getCollectionHandle();
                    $section = Section::getByLocale($locale);
                }

                if ($section) {
                    $stack->updateMultilingualSection($section);
                }
            }
        }
    }
    */
}
