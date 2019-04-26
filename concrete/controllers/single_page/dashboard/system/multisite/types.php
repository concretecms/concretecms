<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Multisite;

use Concrete\Controller\Element\Dashboard\SiteType\Menu;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\Application\UserInterface\Sitemap\JsonFormatter;
use Concrete\Core\Application\UserInterface\Sitemap\SkeletonSitemapProvider;
use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\Key\SiteTypeKey;
use Concrete\Core\Controller\Traits\MultisiteRequiredTrait;
use Concrete\Core\Entity\Site\Group\Group;
use Concrete\Core\Entity\Site\SkeletonLocale;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Template;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Site\Type\Skeleton\Service as SkeletonService;
use Concrete\Core\Site\User\Group\Service;
use Symfony\Component\HttpFoundation\JsonResponse;

class Types extends DashboardPageController
{

    use MultisiteRequiredTrait;

    protected $siteType;
    protected $skeleton;

    public function view()
    {
        $this->set('types', $this->app->make('site/type')->getList());
    }

    protected function setupSiteType($id)
    {
        $service = $this->app->make('site/type');
        $this->siteType = $service->getByID($id);
        if (is_object($this->siteType)) {
            $this->set('type', $this->siteType);
            $this->skeleton = $this->app->make(SkeletonService::class)->getSkeleton($this->siteType);
            return $this->siteType;
        } else {
            $this->redirect('/dashboard/system/multisite/types');
        }
    }

    public function view_type($id)
    {
        $type = $this->app->make('site/type')->getByID($id);
        if (!is_object($type)) {
            throw new \Exception(t('Invalid site type.'));
        }
        $sites = $this->app->make('site')->getByType($type);
        $this->set('pageTitle', t('View Site Type'));
        $this->set('type', $type);
        $this->set('type_menu', new Menu($type));
        $this->set('sites', $sites);
        $this->render('/dashboard/system/multisite/types/view_type');

        return $type;
    }

    public function edit($id)
    {
        /**
         * @var $type Type
         */
        $type = $this->view_type($id);
        $templates = array('-1' => t('** Choose Template'));
        $themes = array('-1' => t('** Choose Theme'));
        foreach(Template::getList() as $template) {
            $templates[$template->getPageTemplateID()] = $template->getPageTemplateDisplayName();
        }
        foreach(Theme::getList() as $theme) {
            $themes[$theme->getThemeID()] = $theme->getThemeDisplayName();
        }
        $this->set('templates', $templates);
        $this->set('themes', $themes);
        $this->set('handle', $type->getSiteTypeHandle());
        $this->set('name', $type->getSiteTypeName());
        $this->set('themeID', $type->getSiteTypeThemeID());
        $this->set('templateID', $type->getSiteTypeHomePageTemplateID());
        $this->set('pageTitle', t('Edit Site Type'));
        $this->set('buttonLabel', t('Save'));
        $this->set('action', 'update');
        $this->set('backURL', \URL::to('/dashboard/system/multisite/types', 'view_type', $type->getSiteTypeID()));
        $this->render('/dashboard/system/multisite/types/form');
    }

    protected function validateSave()
    {
        if (!$this->token->validate('submit')) {
            $this->error->add($this->token->getErrorMessage());
        }
        $vs = $this->app->make('helper/validation/strings');
        if (!$this->token->validate('submit')) {
            $this->error->add($this->token->getErrorMessage());
        }
        $handle = $this->request->request->get('handle');
        $name = $this->request->request->get('name');

        if (!$name) {
            $this->error->add(t('Name required.'));
        }

        if (!$handle) {
            $this->error->add(t('Handle required.'));
        } else if (!$vs->handle($handle)) {
            $this->error->add(t('Handles must contain only letters, numbers or the underscore symbol.'));
        }

        $template = $this->request->request->get('template');
        $theme = $this->request->request->get('theme');
        if ($template) {
            $pt = Template::getByID($template);
        }
        if ($theme) {
            $pageTheme = Theme::getByID($theme);
        }
        if (!isset($pt) && !is_object($pt)) {
            $this->error->add(t('A valid page template for the home page of the site is required.'));
        }
        if (!isset($pageTheme) && !is_object($pageTheme)) {
            $this->error->add(t('A valid site theme is required.'));
        }

        return [$name, $handle, $pageTheme, $pt];
    }

    public function delete_type()
    {
        $service = $this->app->make('site/type');
        $type = $service->getByID($this->request->request->get('id'));
        if (!$this->token->validate('delete_type')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!is_object($type)) {
            $this->error->add(t('Invalid site type.'));
        }
        if (is_object($type) && $type->isDefault()) {
            $this->error->add(t('You may not delete the default site type.'));
        }
        if (!$this->error->has()) {
            $service->delete($type);
            $this->flash('success', t('Site type removed successfully.'));
            $this->redirect('/dashboard/system/multisite/types');
        }
        $this->view();
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
            $this->redirect('/dashboard/system/multisite/types', 'view_type', $type->getSiteTypeID());
        }
        $this->add($this->request->request->get('id'));
    }



    public function update()
    {
        $type = $this->app->make('site/type')->getByID($this->request->request->get('id'));
        if (!is_object($type)) {
            $this->error->add(t('Invalid site type.'));
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
            $this->redirect('/dashboard/system/multisite/types', 'view_type', $type->getSiteTypeID());
        }
        $this->edit($this->request->request->get('id'));
    }

    public function add()
    {
        $templates = array('-1' => t('** Choose Template'));
        $themes = array('-1' => t('** Choose Theme'));
        foreach(Template::getList() as $template) {
            $templates[$template->getPageTemplateID()] = $template->getPageTemplateDisplayName();
        }
        foreach(Theme::getList() as $theme) {
            $themes[$theme->getThemeID()] = $theme->getThemeDisplayName();
        }
        $this->set('templates', $templates);
        $this->set('themes', $themes);
        $this->set('buttonLabel', t('Add Site Type'));
        $this->set('backURL', \URL::to('/dashboard/system/multisite/types'));
        $this->set('action', 'create');
        $this->render('/dashboard/system/multisite/types/form');
    }


    public function view_attributes($id = null)
    {
        $this->setupSiteType($id);
        $this->set('type', $this->siteType);
        $this->set('skeleton', $this->skeleton);
        $this->set('category', $this->app->make(CategoryService::class)->getByHandle('site_type'));
        $this->set('type_menu', new Menu($this->siteType));
        $this->requireAsset('core/app/editable-fields');
        $this->render('/dashboard/system/multisite/types/view_attributes');
    }

    public function update_attribute($id = false)
    {
        $this->setupSiteType($id);
        $sr = new EditResponse();
        if ($this->token->validate()) {
            $ak = SiteTypeKey::getByID(intval($_REQUEST['name']));
            if (is_object($ak)) {
                $controller = $ak->getController();
                $val = $controller->createAttributeValueFromRequest();
                $val = $this->skeleton->setAttribute($ak, $val);
            }
        } else {
            $this->error->add($this->token->getErrorMessage());
        }

        if ($this->error->has()) {
            $sr->setError($this->error);
        } else {
            $sr->setMessage(t('Attribute saved successfully.'));
            $sr->setAdditionalDataAttribute('value',  $val->getDisplayValue());
        }
        $sr->outputJSON();
    }

    public function clear_attribute($id = false)
    {
        $this->setupSiteType($id);
        $sr = new EditResponse();
        if ($this->token->validate()) {
            $ak = SiteTypeKey::getByID(intval($_REQUEST['akID']));
            if (is_object($ak)) {
                $this->skeleton->clearAttribute($ak);
            }
        } else {
            $this->error->add($this->token->getErrorMessage());
        }
        if ($this->error->has()) {
            $sr->setError($this->error);
        } else {
            $sr->setMessage(t('Attribute cleared successfully.'));
        }
        $sr->outputJSON();
    }


    public function view_groups($siteTypeID = null)
    {
        $type = $this->setupSiteType($siteTypeID);
        $groups = $this->entityManager->getRepository(Group::class)
            ->findByType($type);
        $this->set('type_menu', new Menu($this->siteType));
        $this->set('groups', $groups);
        $this->render('/dashboard/system/multisite/types/view_groups');
    }

    protected function getGroup($siteGID)
    {
        $group = $this->entityManager->getRepository(Group::class)
            ->findOneByID($siteGID);
        if (!is_object($group)) {
            $this->error->add(t('Invalid group ID.'));
        }

        return $group;
    }

    public function add_group($id = null)
    {
        $this->setupSiteType($id);
        $this->set('type_menu', new Menu($this->siteType));
        $this->render('/dashboard/system/multisite/types/view_groups');
    }

    public function create_group($id = null)
    {
        $type = $this->setupSiteType($id);
        if (!$this->token->validate('create_group')) {
            $this->error->add($this->token->getErrorMessage());
        }

        if (!$this->error->has()) {
            $service = $this->app->make(Service::class);
            $service->addGroup($type, $this->request->request->get('groupName'));
            $this->flash('success', t('Group added successfully.'));
            $this->redirect('/dashboard/system/multisite/types/', 'view_groups', $type->getSiteTypeID());
        } else {
            $this->add_group();
        }
    }

    public function edit_group($siteGID = null)
    {
        $group = $this->getGroup($siteGID);
        $this->set('group', $group);
        $this->view_type($group->getSiteType()->getSiteTypeID());
        $this->render('/dashboard/system/multisite/types/view_groups');
    }

    public function update_group($siteGID = null)
    {
        if (!$this->token->validate('update_group')) {
            $this->error->add($this->token->getErrorMessage());
        }

        $group = $this->getGroup($siteGID);

        if (!$this->error->has()) {
            $group->setSiteGroupName($this->request->request->get('groupName'));
            $this->entityManager->persist($group);
            $this->entityManager->flush();
            $type = $group->getSiteType();
            $this->flash('success', t('Group updated successfully.'));
            $this->redirect('/dashboard/system/multisite/types', 'view_groups', $type->getSiteTypeID());
        } else {
            $this->edit_group();
        }
    }

    public function delete_group()
    {
        $group = $this->getGroup($this->request->request->get('siteGID'));

        if (!$this->token->validate('delete_group')) {
            $this->error->add($this->token->getErrorMessage());
        }

        if (!$this->error->has()) {
            $type = $group->getSiteType();
            $this->entityManager->remove($group);
            $this->entityManager->flush();

            $this->flash('success', t('Group deleted successfully.'));
            $this->redirect('/dashboard/system/multisite/types', 'view_groups', $type->getSiteTypeID());
        }
    }

    public function view_skeleton($id = null)
    {
        /**
         * @var $service Service
         */
        $service = $this->app->make('site/type');
        $type = $service->getByID($id);
        if (is_object($type)) {
            $this->set('type', $type);
            /**
             * @var $skeletonService SkeletonService
             */
            $skeletonService = $this->app->make(SkeletonService::class);
            $skeleton = $skeletonService->getSkeleton($type);
            if (!$skeleton) {
                $locale = new SkeletonLocale();
                $locale->setLanguage('en');
                $locale->setCountry('US');
                $skeleton = $skeletonService->createSkeleton($type, $locale);
            }
            $this->set('skeleton', $skeleton);
            $this->set('type_menu', new Menu($type));
        } else {
            $this->redirect('/dashboard/system/multisite/types');
        }
        $this->requireAsset('core/sitemap');
        $this->render('/dashboard/system/multisite/types/view_skeleton');
    }

    public function get_sitemap($id = null)
    {
        $service = $this->app->make('site/type');
        $type = $service->getByID($id);
        if ($type) {
            $provider = $this->app->make(SkeletonSitemapProvider::class, ['siteType' => $type]);
            $formatter = new JsonFormatter($provider);
            return new JsonResponse($formatter);
        }
        $this->redirect('/dashboard/system/multisite/types');
    }


}