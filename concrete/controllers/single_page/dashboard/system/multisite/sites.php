<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Multisite;

use Concrete\Controller\Element\Dashboard\Site\Menu;
use Concrete\Core\Application\UserInterface\OptionsForm\OptionsForm;
use Concrete\Core\Controller\Traits\MultisiteRequiredTrait;
use Concrete\Core\Entity\Site\Domain;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Template;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Routing\Redirect;
use Concrete\Core\Site\Type\OptionsFormProvider;
use Concrete\Core\Url\Url;

class Sites extends DashboardPageController
{

    use MultisiteRequiredTrait;

    public function view()
    {
        $this->set('sites', $this->app->make('site')->getList());
    }

    public function view_site($id = null)
    {
        $site = $this->setSite($id);
        $this->set('pageTitle', t('View Site'));
        $this->set('site', $site);
        $this->set('site_menu', new Menu($site));
        $this->render('/dashboard/system/multisite/sites/view_site');
    }

    public function view_domains($id = null)
    {
        $site = $this->setSite($id);
        $service = $this->app->make('site');
        $domains = $service->getSiteDomains($site);
        $this->set('domains', $domains);
        $this->set('site', $site);
        $this->set('site_menu', new Menu($site));
        $this->set('canonicalDomain', Url::createFromUrl($site->getSiteCanonicalURL())->getHost());
        $this->render('/dashboard/system/multisite/sites/view_domains');
    }

    public function delete_domain($domainID = null, $token = null)
    {
        if ($domainID) {
            $domain = $this->getDomain($domainID);
            if (is_object($domain)) {
                $site = $this->setSite($domain->getSite());
            }
        }

        if (!$this->token->validate('delete_domain', $token)) {
            $this->error->add($this->token->getErrorMessage());
        }

        if (!$this->error->has()) {
            $this->entityManager->remove($domain);
            $this->entityManager->flush();
            $this->flash('success', t('Domain deleted successfully.'));
            $this->redirect('/dashboard/system/multisite/sites', 'view_domains', $site->getSiteID());
        } else {
            $this->view();
        }
    }

    public function delete_site()
    {
        $service = $this->app->make('site');
        /**
         * @var $site Site
         */
        $site = $service->getByID($this->request->request->get('id'));
        if (!$this->token->validate('delete_site')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!is_object($site)) {
            $this->error->add(t('Invalid site.'));
        }
        if (is_object($site) && $site->isDefault()) {
            $this->error->add(t('You may not delete the default site.'));
        }
        if (!$this->error->has()) {
            $service->delete($site);
            $this->flash('success', t('Site removed successfully.'));
            $this->redirect('/dashboard/system/multisite/sites');
        }
        $this->view();
    }


    protected function getDomain($domainID)
    {
        $domain = $this->entityManager->getRepository(Domain::class)
            ->findOneByID($domainID);
        if (!is_object($domain)) {
            $this->error->add(t('Invalid domain ID.'));
        }

        return $domain;
    }

    protected function setSite($id)
    {
        $service = $this->app->make('site');
        $site = $service->getByID($id);
        if (is_object($site)) {
            $this->set('site', $site);
            $this->set('site_menu', new Menu($site));
        } else {
            throw new \Exception(t('Invalid site.'));
        }
        return $site;
    }

    public function add_domain()
    {
        $site = $this->setSite($this->request->request->get('id'));
        if (!$this->token->validate('add_domain')) {
            $this->error->add($this->token->getErrorMessage());
        }

        if (!$this->error->has()) {
            $domain = new Domain();
            $domain->setDomain($this->request->request->get('domain'));
            $domain->setSite($site);
            $this->entityManager->persist($domain);
            $this->entityManager->flush();
            $this->flash('success', t('Domain added successfully.'));
            $this->redirect('/dashboard/system/multisite/sites', 'view_domains', $site->getSiteID());
        } else {
            $this->view();
        }
    }


    public function add($siteTypeID = null)
    {
        $service = $this->app->make('site/type');
        $siteType = null;
        if ($siteTypeID) {
            $siteType = $service->getByID(intval($siteTypeID));
        }
        if ($siteType) {
            $provider = new OptionsFormProvider($siteType);
            $optionsForm = new OptionsForm($provider);
            $this->set('buttonLabel', t('Add'));
            $this->set('type', $siteType);
            $this->set('optionsForm', $optionsForm);
            $this->set('timezone', $this->app->make('site')->getSite()->getConfigRepository()->get('timezone'));
            $this->set('timezones', $this->app->make('date')->getGroupedTimezones());
            $this->render('/dashboard/system/multisite/sites/form');

        } else {
            $list = $service->getUserAddedList();
            if (count($list) == 1) {
                return $this->redirect('/dashboard/system/multisite/sites/add', $list[0]->getSiteTypeID());
            } else {
                $this->set('types', $list);
                $this->set('service', $service);
                $this->render('/dashboard/system/multisite/sites/select_type');
            }
        }
    }

    public function submit()
    {
        $vs = $this->app->make('helper/validation/strings');
        if (!$this->token->validate('submit')) {
            $this->error->add($this->token->getErrorMessage());
        }
        $handle = $this->request->request->get('handle');
        $name = $this->request->request->get('name');
        $type = \Core::make('site/type')->getByID($this->request->request->get('siteTypeID'));

        if (!$name) {
            $this->error->add(t('Name required.'));
        }
        if (!$type) {
            $this->error->add(t('Type required.'));
        }

        if (!$handle) {
            $this->error->add(t('Handle required.'));
        } else if (!$vs->handle($handle)) {
            $this->error->add(t('Handles must contain only letters, numbers or the underscore symbol.'));
        }

        if (is_object($type) && $type->getSiteTypeHomePageTemplateID()) {
            $template = Template::getByID($type->getSiteTypeHomePageTemplateID());
        }
        if (!isset($template) || !is_object($template)) {
            $this->error->add(t('This site type does not have a home page template assigned to it.'));
        }

        $theme = null;
        if (is_object($type) && $type->getSiteTypeThemeID()) {
            $theme = Theme::getByID($type->getSiteTypeThemeID());
        }
        if (!isset($theme) || !is_object($theme)) {
            $this->error->add(t('This site type does not have a theme assigned to it.'));
        }

        if (!$this->error->has()) {
            $service = $this->app->make('site');
            $site = $service->add($type, $theme, $handle, $name, 'en_US');

            $siteConfig = $site->getConfigRepository();
            $siteConfig->save('seo.canonical_url', $this->post('canonical_url'));
            $siteConfig->save('timezone', $this->request->request->get('timezone'));

            $this->flash('success', t('Site created successfully.'));
            return Redirect::to('/dashboard/system/multisite/sites', 'view_site', $site->getSiteID());
        }
        $this->add($this->request->request->get('siteTypeID'));
    }

}