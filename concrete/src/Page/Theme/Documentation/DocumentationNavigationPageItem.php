<?php
namespace Concrete\Core\Page\Theme\Documentation;

use Concrete\Core\Navigation\Item\PageItem;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Theme;

class DocumentationNavigationPageItem extends PageItem
{

    /**
     * @var Theme
     */
    protected $theme;

    /**
     * Item constructor.
     * @param string $url
     * @param string $name
     * @param bool $isActive
     */
    public function __construct(Theme $theme, Page $page, bool $isActive = false)
    {
        $this->theme = $theme;
        $this->pageID = $page->getCollectionID();
        $this->name = $page->getCollectionName();
        $this->isActive = $isActive;
    }

    public function getURL(): string
    {
        return \URL::to('/dashboard/pages/themes/preview', $this->theme->getThemeID(), $this->getPageID());
    }

}