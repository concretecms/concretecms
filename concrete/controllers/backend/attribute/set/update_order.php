<?php

namespace Concrete\Controller\Backend\Attribute\Set;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;

class UpdateOrder extends UserInterface
{
    public function view()
    {
        $post = $this->request->request;
        $akCategoryID = (int) $post->get('categoryID');
        $akCategory = $akCategoryID === 0 ? null : $this->app->make(CategoryService::class)->getByID($akCategoryID);
        if ($akCategory === null) {
            throw new UserMessageException(t('Failed to find the attribute category.'));
        }
        $uats = $post->get('asID', null);
        $uats = is_array($uats) ? array_values(array_filter(array_map('intval', $uats))) : [];
        if ($uats === []) {
            throw new UserMessageException(t('Missing list of attributes.'));
        }
        $manager = $akCategory->getController()->getSetManager();
        $manager->updateAttributeSetDisplayOrder($uats);

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Backend\UserInterface::canAccess()
     */
    protected function canAccess()
    {
        $page = Page::getByPath('/dashboard/system/attributes/sets');
        if (!$page || $page->isError()) {
            return false;
        }
        $cp = new Checker($page);

        return $cp->canViewPage();
    }
}
