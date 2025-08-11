<?php

namespace Concrete\Block\CoreThemeDocumentationBreadcrumb;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Html\Service\Navigation;
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
        return t('Adds breadcrumb navigation for use with internal theme documentation.');
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getBlockTypeName(): string
    {
        return t('Theme Documentation Breadcrumb');
    }

    /**
     * @return void
     */
    public function view(): void
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
