<?php
namespace Concrete\Core\Page\Stack;

use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\PageList;
use Concrete\Core\Page\Stack\Folder\Folder;
use Concrete\Core\Page\Type\Type;
use Concrete\Core\Search\StickyRequest;
use Doctrine\DBAL\Query\QueryBuilder;

class StackList extends PageList
{
    /**
     * @var bool
     */
    protected $foldersFirst = false;

    /**
     * @var \Concrete\Core\Multilingual\Page\Section\Section|null
     */
    private $languageSection;

    /**
     * @var bool
     */
    private $includeFolders = true;

    /**
     * @var bool
     */
    private $includeGlobalAreas = true;

    /**
     * @var bool
     */
    private $includeStacks = true;

    /**
     * @var null|false|\Concrete\Core\Page\Stack\Folder\Folder NULL for no filter; false for top-level items; a Folder instance otherwise
     */
    private $folder = null;

    public function __construct()
    {
        parent::__construct();
        /* retreive most recent version to include stacks that have no approved versions */
        $this->pageVersionToRetrieve = self::PAGE_VERSION_RECENT;
        $this->query->leftJoin('p', 'Stacks', 's', 's.cID = p.cID');
        $this->ignorePermissions();
        $this->includeSystemPages();
        $this->sortByName();
    }

    public function performAutomaticSorting(?StickyRequest $request = null)
    {
        parent::performAutomaticSorting($request);
        if ($this->foldersFirst) {
            $previousOrderBy = $this->query->getQueryPart('orderBy');
            $this->query->orderBy('pt.ptHandle', 'desc');
            $this->query->add('orderBy', $previousOrderBy, true);
        }
    }

    /**
     * @deprecated Use getLanguageSection/setLanguageSection
     */
    public function filterByLanguageSection(Section $ms)
    {
        $this->setLanguageSection($ms);
    }

    public function getLanguageSection(): ?Section
    {
        return $this->languageSection;
    }

    /**
     * @return $this
     */
    public function setLanguageSection(?Section $value = null): self
    {
        $this->languageSection = $value;

        return $this;
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
        $this->folder = $folder;
    }

    /**
     * @deprecated use setIncludeGlobalAreas(true) + setIncludeStacks(false) + setIncludeFolders(false)
     */
    public function filterByGlobalAreas()
    {
        $this
            ->setIncludeFolders(false)
            ->setIncludeGlobalAreas(true)
            ->setIncludeStacks(false)
        ;
    }

    /**
     * @deprecated use setIncludeGlobalAreas(false)
     */
    public function excludeGlobalAreas()
    {
        $this->setIncludeGlobalAreas(false);
    }

    public function getIncludeFolders(): bool
    {
        return $this->includeFolders;
    }

    /**
     * @return $this
     */
    public function setIncludeFolders(bool $value): self
    {
        $this->includeFolders = $value;

        return $this;
    }

    public function getIncludeGlobalAreas(): bool
    {
        return $this->includeGlobalAreas;
    }

    /**
     * @return $this
     */
    public function setIncludeGlobalAreas(bool $value): self
    {
        $this->includeGlobalAreas = $value;

        return $this;
    }

    public function getIncludeStacks(): bool
    {
        return $this->includeStacks;
    }

    /**
     * @return $this
     */
    public function setIncludeStacks(bool $value): self
    {
        $this->includeStacks = $value;

        return $this;
    }

    /**
     * List only the root stacks/folders?
     *
     * @return bool
     */
    public function getRootItemsOnly(): bool
    {
        return $this->folder === false;
    }

    /**
     * List only the root stacks/folders?
     *
     * @return $this
     */
    public function setRootItemsOnly(bool $value): self
    {
        if ($value) {
            $this->folder = false;
        }

        return $this;
    }

    public function filterByUserAdded()
    {
        $this->query->andWhere('s.stType = ' . $this->query->createNamedParameter(Stack::ST_TYPE_USER_ADDED));
    }

    public function filterByStackCategory(StackCategory $category)
    {
        $this->filterByParentID($category->getPage()->getCollectionID());
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Search\ItemList\Database\ItemList::deliverQueryObject()
     */
    public function deliverQueryObject()
    {
        $this->applyFolderFilter();
        return parent::deliverQueryObject();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Page\PageList::finalizeQuery()
     */
    public function finalizeQuery(QueryBuilder $query)
    {
        $query = parent::finalizeQuery($query);
        $languageSection = $this->getLanguageSection();
        if ($languageSection === null) {
            $query->andWhere('s.stMultilingualSection IS NULL OR s.stMultilingualSection = 0');
        } else {
            $query->andWhere('s.stMultilingualSection = ' . $query->createNamedParameter($languageSection->getCollectionID()));
        }
        $this->applyTypeFilter($query);

        return $query;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Page\PageList::getResult()
     *
     * @return \Concrete\Core\Page\Stack\Stack|\Concrete\Core\Page\Page|null returns a Page in case of folders
     */
    public function getResult($queryRow)
    {
        $stack = Stack::getByID($queryRow['cID'], 'ACTIVE');

        return $stack ?: parent::getResult($queryRow);
    }

    protected function applyFolderFilter()
    {
        if ($this->folder === false) {
            $rootPage = Page::getByPath(STACKS_PAGE_PATH);
            $this->filterByParentID($rootPage->getCollectionID());
        } elseif ($this->folder instanceof Folder) {
            $this->filterByParentID($this->folder->getPage()->getCollectionID());
        } else {
            $this->filterByPath(STACKS_PAGE_PATH);
        }
    }

    protected function applyTypeFilter(QueryBuilder $query)
    {
        $folders = $this->getIncludeFolders();
        $globalAreas = $this->getIncludeGlobalAreas();
        $stacks = $this->getIncludeStacks();
        if ($folders && $globalAreas && $stacks) {
            // No need to filter
            return;
        }
        if (!$folders && !$globalAreas && !$stacks) {
            // We won't have any result
            $query->andWhere('1 = 0');
            return;
        }
        $orList = [];
        if ($folders) {
            $orList[] = 'p.ptID = ' . $query->createNamedParameter(Type::getByHandle(STACK_CATEGORY_PAGE_TYPE)->getPageTypeID());
        }
        if ($globalAreas) {
            $orList[] = 's.stType = ' . $query->createNamedParameter(Stack::ST_TYPE_GLOBAL_AREA);
        }
        if ($stacks) {
            $orList[] = 's.stType IS NOT NULL AND s.stType <> ' . $query->createNamedParameter(Stack::ST_TYPE_GLOBAL_AREA);
        }
        $query->andWhere($query->expr()->or(...$orList));
    }
}
