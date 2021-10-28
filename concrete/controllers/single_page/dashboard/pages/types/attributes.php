<?php

namespace Concrete\Controller\SinglePage\Dashboard\Pages\Types;

use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Entity\Attribute\Category;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\Type as PageType;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Support\Facade\Url;

class Attributes extends DashboardPageController
{
    /**
     * @var PageType
     */
    protected $pageType;

    /**
     * @var Page
     */
    protected $defaultPage;

    public function view($ptID = 0)
    {
        $this->setupPageType($ptID);
        $this->set('pagetype', $this->pageType);

        $category = $this->getCategoryObject();
        $attributesView = $this->elementManager->get('attribute/editable_set_list', ['categoryEntity' => $category, 'attributedObject' => $this->defaultPage]);
        /** @var \Concrete\Controller\Element\Attribute\EditableSetList $controller */
        $controller = $attributesView->getElementController();
        $controller->setEditDialogURL(Url::to('/ccm/system/dialogs/type/attributes', $this->pageType->getPageTypeID()));

        $this->set('attributesView', $attributesView);
    }

    protected function setupPageType($ptID): void
    {
        if ($ptID > 0) {
            $this->pageType = PageType::getByID((int) $ptID);
        }

        if (!$this->pageType) {
            $this->buildRedirect('/dashboard/pages/types')->send();
            $this->app->shutdown();
        }

        $cmp = new Checker($this->pageType);
        if (!$cmp->canEditPageType()) {
            throw new \Exception(t('You do not have access to edit this page type.'));
        }

        $this->defaultPage = $this->pageType->getPageTypePageTemplateDefaultPageObject();
    }

    protected function getCategoryObject(): Category
    {
        $categoryService = $this->app->make(CategoryService::class);

        return $categoryService->getByHandle('collection');
    }
}
