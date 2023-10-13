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
     * @var Page
     */
    protected $page;

    /**
     * Item constructor.
     * @param string $url
     * @param string $name
     * @param bool $isActive
     */
    public function __construct(Theme $theme, Page $page, bool $isActive = false)
    {
        $this->theme = $theme;
        $this->page = $page;
        $this->name = $page->getCollectionName();
        $this->isActive = $isActive;
    }

    public function isDocumentationCategory(): bool
    {
        return in_array($this->page->getPageTypeHandle(), [THEME_DOCUMENTATION_CATEGORY_PAGE_TYPE]);
    }

    public function getPageID(): int
    {
        return $this->page->getCollectionID();
    }

    public function getTarget(): string
    {
        if (in_array($this->page->getPageTypeHandle(), [THEME_DOCUMENTATION_PAGE_TYPE])) {
            return '_top';
        } else {
            return '_blank';
        }
    }

    public function getURL(): string
    {
        if (in_array($this->page->getPageTypeHandle(), [THEME_DOCUMENTATION_PAGE_TYPE])) {
            // We use the built-in render function which preserves the toolbar at the top with navigation, allows for
            // customizing the page with the customizer, etc...
            return \URL::to('/dashboard/pages/themes/preview', $this->theme->getThemeID(), $this->getPageID());
        } else {
            // This is a custom page added by the theme, which might behave strangely in the customizer or in the
            // built-in docs chrome - so let's just render it directly.
            return (string) $this->page->getCollectionLink();
        }
    }

}