<?php
namespace Concrete\Core\Page\Theme\Documentation;

use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Theme;

class ThemeCategoryDocumentationPage extends ThemeDocumentationPage
{


    /**
     * @var DocumentationPageInterface[]
     */
    protected $childPages = [];

    /**
     * @param Theme $theme
     * @param string $name
     * @param string $contentFile
     */
    public function __construct(Theme $theme, string $name, array $childPages, string $contentFile = null)
    {
        $this->name = $name;
        $this->theme = $theme;
        $this->childPages = $childPages;
        $this->contentFile = $contentFile;
    }

    public function getDocumentationPageTypeHandle(): string
    {
        return THEME_DOCUMENTATION_CATEGORY_PAGE_TYPE;
    }


    /**
     * @return DocumentationPageInterface[]
     */
    public function getChildPages(): array
    {
        return $this->childPages;
    }

    /**
     * @param DocumentationPageInterface[] $childPages
     */
    public function setChildPages(array $childPages): void
    {
        $this->childPages = $childPages;
    }

    public function installDocumentationPage(Page $parent): Page
    {
        $page  = parent::installDocumentationPage($parent);
        if (count($this->getChildPages())) {
            foreach ($this->getChildPages() as $childDocumentationPage) {
                $childDocumentationPage->installDocumentationPage($page);
            }
        }
        return $page;
    }

}