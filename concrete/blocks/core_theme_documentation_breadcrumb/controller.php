<?php
namespace Concrete\Block\CoreThemeDocumentationBreadcrumb;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Html\Service\Navigation;
use Concrete\Core\Page\Theme\Theme;

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
        $navigation = new Navigation();
        $parents = array_reverse($navigation->getTrailToCollection($c));
        unset($parents[0]); // top level themes node
        $theme = Theme::getByHandle($parents[1]->getCollectionHandle());
        unset($parents[1]); // theme handle node.
        $this->set('parents', $parents);
        $this->set('currentPage', $c);
        $this->set('theme', $theme);
    }

}
