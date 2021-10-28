<?php
namespace Concrete\Core\Page\Theme\Documentation;

use Concrete\Core\Navigation\Item\ItemInterface;
use Concrete\Core\Navigation\NavigationFactory;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Theme;

class DocumentationNavigationFactory extends NavigationFactory
{

    /**
     * @var Theme
     */
    protected $theme;

    public function __construct(Theme $theme)
    {
        $this->theme = $theme;
        $this->home = $theme->getThemeDocumentationParentPage();
    }

    public function createItemFromPage(Page $page): ItemInterface
    {
        return new DocumentationNavigationPageItem($this->theme, $page);
    }

}
