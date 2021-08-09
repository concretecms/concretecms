<?php
namespace Concrete\Block\CoreThemeDocumentationBreadcrumb;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Html\Service\Navigation;

class Controller extends BlockController
{
    protected $btIsInternal = true;

    public function getBlockTypeDescription()
    {
        return t("Adds breadcrumb navigation for use with internal theme documentation.");
    }

    public function getBlockTypeName()
    {
        return t("Theme Documentation Breadcrumb");
    }

    public function view()
    {
        $c = $this->getCollectionObject();
        if ($c) {
            $theme = $c->getCollectionThemeObject();
        }
        $this->set('currentPage', $c);
        $this->set('theme', $theme);
    }

}
