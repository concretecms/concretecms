<?php

namespace Concrete\Block\CoreThemeDocumentationToc;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Documentation\DocumentationNavigationFactory;
use Concrete\Core\Page\Theme\Theme;

class Controller extends BlockController
{
    /**
     * @var bool
     */
    protected $btIsInternal = true;

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getBlockTypeDescription(): string
    {
        return t('Displays a table of contents list for theme documentation.');
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getBlockTypeName(): string
    {
        return t('Theme Documentation TOC');
    }

    /**
     * @return void
     */
    public function view(): void
    {
        $themeID = 0;
        $c = $this->getCollectionObject();
        $navigation = null;
        if ($c) {
            $parent = Page::getByID($c->getCollectionParentID());
            $theme = Theme::getByHandle($parent->getCollectionHandle());
            if ($theme) {
                $documentationPage = $theme->getThemeDocumentationParentPage();
                if ($documentationPage) {
                    $factory = new DocumentationNavigationFactory($theme);
                    $navigation = $factory->createNavigation();
                    $themeID = $theme->getThemeID();
                }
            }
        }
        $this->set('navigation', $navigation);
        $this->set('themeID', $themeID);
    }
}
