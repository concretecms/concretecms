<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Multisite;

use Concrete\Core\Application\UserInterface\OptionsForm\OptionsForm;
use Concrete\Core\Controller\Traits\MultisiteRequiredTrait;
use Concrete\Core\Entity\Site\Domain;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Filesystem\Element;
use Concrete\Core\Navigation\Breadcrumb\Dashboard\DashboardBreadcrumbFactory;
use Concrete\Core\Navigation\Item\Item;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Template;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Site\Type\OptionsFormProvider;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
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
        $id = (int) $id;
        $site = $id === 0 ? null : $this->app->make('site')->getByID($id);
        if ($site === null) {
            $this->flash('error', t('The site specified does not exist.'));

            return $this->buildRedirect($this->action());
        }
        $this->setCurrentSite($site);
        $this->set('pageTitle', t('View Site'));
        $this->render('/dashboard/system/multisite/sites/view_site');
    }

    public function add($siteTypeID = null)
    {
        $service = $this->app->make('site/type');
        $siteTypeID = (int) $siteTypeID;
        $siteType = $siteTypeID === 0 ? null : $service->getByID($siteTypeID);
        if ($siteType !== null) {
            $provider = new OptionsFormProvider($siteType);
            $optionsForm = new OptionsForm($provider);
            $this->set('type', $siteType);
            $this->set('optionsForm', $optionsForm);
            $this->set('timezone', $this->app->make('site')->getSite()->getConfigRepository()->get('timezone'));
            $this->set('timezones', $this->app->make('date')->getGroupedTimezones());
            $this->render('/dashboard/system/multisite/sites/form');
        } else {
            $list = $service->getUserAddedList();
            if (count($list) === 1) {
                return $this->buildRedirect($this->action(['add', $list[0]->getSiteTypeID()]));
            }
            $this->set('urlResolver', $this->app->make(ResolverManagerInterface::class));
            $this->set('types', $list);
            $this->set('service', $service);
            $this->render('/dashboard/system/multisite/sites/select_type');
        }
    }

    public function delete_site()
    {
        $service = $this->app->make('site');
        $id = (int) $this->request->request->get('id');
        $site = $id === 0 ? null : $service->getByID($id);
        if ($site === null) {
            $this->flash('error', t('The site specified does not exist.'));

            return $this->buildRedirect($this->action());
        }
        if (!$this->token->validate('delete_site')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if ($site->isDefault()) {
            $this->error->add(t('You may not delete the default site.'));
        }
        if (!$this->error->has()) {
            $service->delete($site);
            $this->flash('success', t('Site removed successfully.'));

            return $this->buildRedirect($this->action());
        }

        return $this->view_site($site->getSiteID());
    }

    public function view_domains($id = null)
    {
        $id = (int) $id;
        $service = $this->app->make('site');
        $site = $id === 0 ? null : $service->getByID($id);
        if ($site === null) {
            $this->flash('error', t('The site specified does not exist.'));

            return $this->buildRedirect($this->action());
        }
        $this->setCurrentSite($site);
        $domains = $service->getSiteDomains($site);
        $this->set('domains', $domains);
        $this->set('canonicalDomain', Url::createFromUrl($site->getSiteCanonicalURL())->getHost());
        $this->render('/dashboard/system/multisite/sites/view_domains');
    }

    public function add_domain()
    {
        $id = (int) $this->request->request->get('id');
        $service = $this->app->make('site');
        $site = $id === 0 ? null : $service->getByID($id);
        if ($site === null) {
            $this->flash('error', t('The site specified does not exist.'));

            return $this->buildRedirect($this->action());
        }
        if (!$this->token->validate('add_domain')) {
            $this->error->add($this->token->getErrorMessage());
        }
        $domainName = trim($this->request->request->get('domain'));
        if ($domainName === '') {
            $this->error->add(t('Please specify the domain'));
        }
        if (!$this->error->has()) {
            $domain = new Domain();
            $domain->setDomain($domainName);
            $domain->setSite($site);
            $this->entityManager->persist($domain);
            $this->entityManager->flush();
            $this->flash('success', t('Domain added successfully.'));

            return $this->buildRedirect($this->action('view_domains', $site->getSiteID()));
        }

        return $this->view_domains($site->getSiteID());
    }

    public function submit()
    {
        $vs = $this->app->make('helper/validation/strings');
        if (!$this->token->validate('submit')) {
            $this->error->add($this->token->getErrorMessage());
        }
        $typeID = (int) $this->request->request->get('siteTypeID');
        $type = $typeID === 0 ? null : $this->app->make('site/type')->getByID($typeID);
        if ($type === null) {
            $this->error->add(t('Type required.'));
        } else {
            $templateID = $type->getSiteTypeHomePageTemplateID();
            $template = $templateID ? Template::getByID($templateID) : null;
            $template = Template::getByID($type->getSiteTypeHomePageTemplateID());
            if ($template === null) {
                $this->error->add(t('This site type does not have a home page template assigned to it.'));
            }
            $themeID = $type->getSiteTypeThemeID();
            $theme = $themeID ? Theme::getByID($themeID) : null;
            if ($theme === null || $theme->isError()) {
                $this->error->add(t('This site type does not have a theme assigned to it.'));
            }
        }
        $handle = (string) $this->request->request->get('handle');
        if ($handle === '') {
            $this->error->add(t('Handle required.'));
        } elseif (!$vs->handle($handle)) {
            $this->error->add(t('Handles must contain only letters, numbers or the underscore symbol.'));
        }
        $name = trim((string) $this->request->request->get('name'));
        if ($name === '') {
            $this->error->add(t('Name required.'));
        }

        if (!$this->error->has()) {
            $service = $this->app->make('site');
            $site = $service->add($type, $theme, $handle, $name, 'en_US');
            $siteConfig = $site->getConfigRepository();
            $siteConfig->save('seo.canonical_url', $this->post('canonical_url'));
            $siteConfig->save('timezone', $this->request->request->get('timezone'));
            $this->flash('success', t('Site created successfully.'));

            return $this->buildRedirect($this->action('view_site', $site->getSiteID()));
        }

        return $this->add($type === null ? null : $type->getSiteTypeID());
    }

    public function delete_domain($domainID = null, $token = null)
    {
        $domainID = (int) $domainID;
        $domain = $domainID === 0 ? null : $this->entityManager->getRepository(Domain::class)->findOneByID($domainID);
        if ($domain === null) {
            $this->flash('error', t('The domain specified does not exist.'));

            return $this->buildRedirect($this->action());
        }
        $site = $domain->getSite();
        if (!$this->token->validate('delete_domain', $token)) {
            $this->error->add($this->token->getErrorMessage());
        }

        if (!$this->error->has()) {
            $this->entityManager->remove($domain);
            $this->entityManager->flush();
            $this->flash('success', t('Domain deleted successfully.'));

            return $this->buildRedirect($this->action('view_domains', $site->getSiteID()));
        }

        return $this->view_domains($site->getSiteID());
    }

    protected function setCurrentSite(?Site $site): void
    {
        $this->set('site', $site);
        if ($site === null || $site->getSiteID() === null) {
            $menu = null;
        } else {
            $breadcrumb = $this->app->make(DashboardBreadcrumbFactory::class)->getBreadcrumb($this->getPageObject());
            $breadcrumb->add(new Item('', $site->getSiteName()));
            $this->setBreadcrumb($breadcrumb);
            $menu = new Element('dashboard/system/multisite/site/menu', '', $this->getPageObject(), ['site' => $site]);
        }
        $this->set('siteMenu', $menu);
    }
}
