<?php
namespace Concrete\Block\CoreThemeDocumentationToc;

use Concrete\Core\Block\BlockController;

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
        $pages = [];
        $themeID = 0;
        $c = $this->getCollectionObject();
        if ($c) {
            $theme = $c->getCollectionThemeObject();
            if ($theme) {
                $pages = $theme->getThemeDocumentationPages();
                $themeID = $theme->getThemeID();
            }
        }
        $this->set('pages', $pages);
        $this->set('themeID', $themeID);
    }

}
