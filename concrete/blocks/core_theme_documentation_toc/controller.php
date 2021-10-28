<?php
namespace Concrete\Block\CoreThemeDocumentationToc;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Navigation\NavigationFactory;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Documentation\DocumentationNavigationFactory;
use Concrete\Core\Page\Theme\Theme;

class Controller extends BlockController
{
    protected $btIsInternal = true;

    public function getBlockTypeDescription()
    {
        return t("Displays a table of contents list for theme documentation.");
    }

    public function getBlockTypeName()
    {
        return t("Theme Documentation TOC");
    }

    public function view()
    {
        $themeID = 0;
        $c = $this->getCollectionObject();
        if ($c) {
            $parent = Page::getByID($c->getCollectionParentID());
            $theme = Theme::getByHandle($parent->getCollectionHandle());
            if ($theme) {
                $documentationPage = $theme->getThemeDocumentationParentPage();
                if ($documentationPage) {
                    $factory = new DocumentationNavigationFactory($theme);
                    $navigation = $factory->createNavigation($theme->getThemeDocumentationParentPage());
                    $themeID = $theme->getThemeID();
                }
            }
        }
        $this->set('navigation', $navigation);
        $this->set('themeID', $themeID);
    }

}
