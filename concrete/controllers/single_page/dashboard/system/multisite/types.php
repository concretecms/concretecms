<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Multisite;

use Concrete\Core\Application\UserInterface\Sitemap\JsonFormatter;
use Concrete\Core\Application\UserInterface\Sitemap\SkeletonSitemapProvider;
use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Controller\Traits\MultisiteRequiredTrait;
use Concrete\Core\Entity\Attribute\Category;
use Concrete\Core\Entity\Site\Group\Group;
use Concrete\Core\Entity\Site\Skeleton;
use Concrete\Core\Entity\Site\SkeletonLocale;
use Concrete\Core\Entity\Site\Type;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Filesystem\Element;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Navigation\Breadcrumb\Dashboard\DashboardBreadcrumbFactory;
use Concrete\Core\Navigation\Item\Item;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Template;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Site\Type\Skeleton\Service as SkeletonService;
use Concrete\Core\Site\User\Group\Service;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

class Types extends DashboardPageController
{
    use MultisiteRequiredTrait;

    public function view()
    {
        $this->set('types', $this->app->make('site/type')->getList());
    }

    public function view_type($id = null)
    {
        $id = (int) $id;
        $type = $id === 0 ? null : $this->app->make('site/type')->getByID($id);
        if ($type === null) {
            $this->flash('error', t('The site type specified does not exist.'));

            return $this->buildRedirect($this->action());
        }
        $sites = $this->app->make('site')->getByType($type);
        $this->set('pageTitle', t('View Site Type'));
        $this->setCurrentSiteType($type);
        $this->set('sites', $sites);
        $this->set('urlResolver', $this->app->make(ResolverManagerInterface::class));
        $this->render('/dashboard/system/multisite/types/view_type');

        return $type;
    }

    public function add()
    {
        return $this->prepareAddOrEdit(new Type());
    }

    public function create()
    {
        list($name, $handle, $pageTheme, $pt) = $this->validateSave();
        if (!$this->error->has()) {
            $service = $this->app->make('site/type');
            $type = $service->add($handle, $name);
            $type->setSiteTypeThemeID($pageTheme->getThemeID());
            $type->setSiteTypeHomePageTemplateID($pt->getPageTemplateID());
            $this->entityManager->persist($type);
            $this->entityManager->flush();

            $this->flash('success', t('Site type created successfully.'));

            return $this->buildRedirect($this->action('view_type', $type->getSiteTypeID()));
        }

        return $this->add();
    }

    public function edit($id)
    {
        $id = (int) $id;
        $type = $id === 0 ? null : $this->app->make('site/type')->getByID($id);
        if ($type === null) {
            $this->flash('error', t('The site type specified does not exist.'));

            return $this->buildRedirect($this->action());
        }

        return $this->prepareAddOrEdit($type);
    }

    public function update()
    {
        $type = $this->app->make('site/type')->getByID((int) $this->request->request->get('id'));
        if ($type === null) {
            $this->flash('error', t('The site type specified does not exist.'));

            return $this->buildRedirect($this->action());
        }
        list($name, $handle, $pageTheme, $pt) = $this->validateSave();
        if (!$this->error->has()) {
            $type->setSiteTypeName($name);
            $type->setSiteTypeHandle($handle);
            $type->setSiteTypeThemeID($pageTheme->getThemeID());
            $type->setSiteTypeHomePageTemplateID($pt->getPageTemplateID());
            $this->entityManager->persist($type);
            $this->entityManager->flush();

            $this->flash('success', t('Site type updated successfully.'));

            return $this->buildRedirect($this->action('view_type', $type->getSiteTypeID()));
        }

        return $this->edit($type->getSiteTypeID());
    }

    public function delete_type()
    {
        $id = (int) $this->request->request->get('id');
        $service = $this->app->make('site/type');
        $type = $id === 0 ? null : $service->getByID($id);
        if ($type === null) {
            $this->flash('error', t('The site type specified does not exist.'));

            return $this->buildRedirect($this->action());
        }
        if (!$this->token->validate('delete_type')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if ($type->isDefault()) {
            $this->error->add(t('You may not delete the default site type.'));
        }
        $sites = $this->app->make('site')->getByType($type);
        if (count($sites) > 0) {
            $this->error->add(t('You must delete all sites of this type before you can remove this site type.'));
        }
        if (!$this->error->has()) {
            $service->delete($type);
            $this->flash('success', t('Site type removed successfully.'));

            return $this->buildRedirect($this->action());
        }

        return $this->view_type($type->getSiteTypeID());
    }

    public function view_skeleton($id = null)
    {
        $id = (int) $id;
        $service = $this->app->make('site/type');
        $type = $id === 0 ? null : $service->getByID($id);
        if ($type === null) {
            $this->flash('error', t('The site type specified does not exist.'));

            return $this->buildRedirect($this->action());
        }
        $this->setCurrentSiteType($type);
        $this->prepareSkelpeton($type, true);
        $this->render('/dashboard/system/multisite/types/view_skeleton');
    }

    public function get_sitemap($id = null)
    {
        $id = (int) $id;
        $service = $this->app->make('site/type');
        $type = $id === 0 ? null : $service->getByID($id);
        if ($type === null) {
            throw new UserMessageException(t('The site type specified does not exist.'));
        }
        $provider = $this->app->make(SkeletonSitemapProvider::class, ['siteType' => $type]);
        $formatter = new JsonFormatter($provider);

        return $this->app->make(ResponseFactoryInterface::class)->json($formatter);
    }

    public function view_groups($id = null)
    {
        $id = (int) $id;
        $type = $id === 0 ? null : $this->app->make('site/type')->getByID($id);
        if ($type === null) {
            $this->flash('error', t('The site type specified does not exist.'));

            return $this->buildRedirect($this->action());
        }
        $this->setCurrentSiteType($type);
        $this->set('groups', $this->entityManager->getRepository(Group::class)->findByType($type));
        $this->set('group', null);
        $this->render('/dashboard/system/multisite/types/view_groups');
    }

    public function add_group($id = null)
    {
        $id = (int) $id;
        $type = $id === 0 ? null : $this->app->make('site/type')->getByID($id);
        if ($type === null) {
            $this->flash('error', t('The site type specified does not exist.'));

            return $this->buildRedirect($this->action());
        }
        $this->setCurrentSiteType($type);
        $this->set('group', new Group());
        $this->render('/dashboard/system/multisite/types/view_groups');
    }

    public function create_group($id = null)
    {
        $id = (int) $id;
        $type = $id === 0 ? null : $this->app->make('site/type')->getByID($id);
        if ($type === null) {
            $this->flash('error', t('The site type specified does not exist.'));

            return $this->buildRedirect($this->action());
        }
        if (!$this->token->validate('create_group')) {
            $this->error->add($this->token->getErrorMessage());
        }
        $name = trim((string) $this->request->request->get('groupName'));
        if ($name === '') {
            $this->error->add(t('Please specify the group name.'));
        }
        if (!$this->error->has()) {
            $service = $this->app->make(Service::class);
            $service->addGroup($type, $name);
            $this->flash('success', t('Group added successfully.'));

            return $this->buildRedirect($this->action('view_groups', $type->getSiteTypeID()));
        }

        return $this->add_group($type->getSiteTypeID());
    }

    public function edit_group($siteGID = null)
    {
        $siteGID = (int) $siteGID;
        $group = $siteGID === 0 ? null : $this->entityManager->getRepository(Group::class)->findOneByID($siteGID);
        if ($group === null) {
            $this->flash('error', t('The group specified does not exist.'));

            return $this->buildRedirect($this->action());
        }
        $type = $group->getSiteType();
        $this->setCurrentSiteType($type);
        $this->set('group', $group);
        $this->render('/dashboard/system/multisite/types/view_groups');
    }

    public function update_group($siteGID = null)
    {
        $siteGID = (int) $siteGID;
        $group = $siteGID === 0 ? null : $this->entityManager->getRepository(Group::class)->findOneByID($siteGID);
        if ($group === null) {
            $this->flash('error', t('The group specified does not exist.'));

            return $this->buildRedirect($this->action());
        }
        if (!$this->token->validate('update_group')) {
            $this->error->add($this->token->getErrorMessage());
        }
        $name = trim((string) $this->request->request->get('groupName'));
        if ($name === '') {
            $this->error->add(t('Please specify the group name.'));
        }
        if (!$this->error->has()) {
            $group->setSiteGroupName($name);
            $this->entityManager->flush($group);
            $this->flash('success', t('Group updated successfully.'));

            return $this->buildRedirect($this->action('view_groups', $group->getSiteType()->getSiteTypeID()));
        }

        return $this->edit_group($group->getSiteGroupID());
    }

    public function delete_group()
    {
        $siteGID = (int) $this->request->request->get('siteGID', 0);
        $group = $siteGID === 0 ? null : $this->entityManager->getRepository(Group::class)->findOneByID($siteGID);
        if ($group === null) {
            $this->flash('error', t('The group specified does not exist.'));

            return $this->buildRedirect($this->action());
        }
        if (!$this->token->validate('delete_group')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            $type = $group->getSiteType();
            $this->entityManager->remove($group);
            $this->entityManager->flush();
            $this->flash('success', t('Group deleted successfully.'));

            return $this->buildRedirect($this->action('view_groups', $type->getSiteTypeID()));
        }

        return $this->edit_group($group->getSiteGroupID());
    }

    public function view_attributes($id = null)
    {
        $id = (int) $id;
        $type = $id === 0 ? null : $this->app->make('site/type')->getByID($id);
        if ($type === null) {
            $this->flash('error', t('The site type specified does not exist.'));

            return $this->buildRedirect($this->action());
        }

        $this->setCurrentSiteType($type);
        $this->prepareSkelpeton($type, false);

        $category = $this->getCategoryObject();
        $skeleton = $this->get('skeleton');

        if ($skeleton !== null) {
            $attributesView = $this->elementManager->get('attribute/editable_set_list', ['categoryEntity' => $category, 'attributedObject' => $skeleton]);
            /** @var \Concrete\Controller\Element\Attribute\EditableSetList $controller */
            $controller = $attributesView->getElementController();
            $controller->setEditDialogURL(Url::to('/ccm/system/dialogs/site_type/attributes', $type->getSiteTypeID()));

            $this->set('attributesView', $attributesView);
        }

        $this->render('/dashboard/system/multisite/types/view_attributes');
    }

    protected function prepareAddOrEdit(Type $type): void
    {
        $this->set('pageTitle', $type->getSiteTypeID() === null ? t('Add Site Type') : t('Edit Site Type'));
        $this->setCurrentSiteType($type);
        $templates = ['' => t('** Choose Template')];
        foreach (Template::getList() as $template) {
            $templates[$template->getPageTemplateID()] = $template->getPageTemplateDisplayName();
        }
        $this->set('templates', $templates);
        $themes = ['' => t('** Choose Theme')];
        foreach (Theme::getList() as $theme) {
            $themes[$theme->getThemeID()] = $theme->getThemeDisplayName();
        }
        $this->set('themes', $themes);
        $this->render('/dashboard/system/multisite/types/form');
    }

    protected function validateSave(): array
    {
        if (!$this->token->validate('submit')) {
            $this->error->add($this->token->getErrorMessage());
        }
        $vs = $this->app->make('helper/validation/strings');
        if (!$this->token->validate('submit')) {
            $this->error->add($this->token->getErrorMessage());
        }
        $handle = (string) $this->request->request->get('handle');
        if ($handle === '') {
            $this->error->add(t('Handle required.'));
        } elseif (!$vs->handle($handle)) {
            $this->error->add(t('Handles must contain only letters, numbers or the underscore symbol.'));
        }
        $name = (string) $this->request->request->get('name');
        if ($name === '') {
            $this->error->add(t('Name required.'));
        }
        $pageThemeID = (int) $this->request->request->get('theme');
        $pageTheme = $pageThemeID < 1 ? null : Theme::getByID($pageThemeID);
        if ($pageTheme === null || $pageTheme->isError()) {
            $this->error->add(t('A valid site theme is required.'));
        }
        $pageTemplateID = (int) $this->request->request->get('template');
        $pageTemplate = $pageTemplateID < 1 ? null : Template::getByID($pageTemplateID);
        if ($pageTemplate === null) {
            $this->error->add(t('A valid page template for the home page of the site is required.'));
        }

        return [$name, $handle, $pageTheme, $pageTemplate];
    }

    protected function setCurrentSiteType(?Type $type): void
    {
        $this->set('type', $type);
        if ($type === null || $type->getSiteTypeID() === null) {
            $menu = null;
        } else {
            $breadcrumb = $this->app->make(DashboardBreadcrumbFactory::class)->getBreadcrumb($this->getPageObject());
            $breadcrumb->add(new Item('', $type->getSiteTypeName()));
            $this->setBreadcrumb($breadcrumb);
            $menu = new Element('dashboard/system/multisite/site_type/menu', '', $this->getPageObject(), ['type' => $type]);
        }
        $this->set('typeMenu', $menu);
    }

    protected function getTypeSkeleton(?Type $type, bool $createIfNotFound = false): ?Skeleton
    {
        if ($type === null || $type->getSiteTypeID() === null) {
            return null;
        }
        $skeletonService = $this->app->make(SkeletonService::class);
        $skeleton = $skeletonService->getSkeleton($type);
        if ($skeleton !== null || $createIfNotFound === false) {
            return $skeleton;
        }
        $locale = new SkeletonLocale();
        $locale->setLanguage('en');
        $locale->setCountry('US');

        return $skeletonService->createSkeleton($type, $locale);
    }

    protected function prepareSkelpeton(?Type $type, bool $createIfNotFound): void
    {
        $this->set('skeleton', $this->getTypeSkeleton($type, $createIfNotFound));
    }

    protected function getCategoryObject(): Category
    {
        $categoryService = $this->app->make(CategoryService::class);

        return $categoryService->getByHandle('site_type');
    }
}
