<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Multilingual;

use Concrete\Core\Entity\Site\Locale;
use Concrete\Core\Entity\Site\SiteTree;
use Concrete\Core\Localization\Locale\Service;
use Concrete\Core\Multilingual\Service\UserInterface\Flag;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Template;
use Concrete\Core\User\User;
use Core;
use Concrete\Core\Multilingual\Page\Section\Section;
use Localization;
use Loader;
use Symfony\Component\HttpFoundation\JsonResponse;

defined('C5_EXECUTE') or die("Access Denied.");

class Setup extends DashboardSitePageController
{

    public function view()
    {
        $this->set('locales', $this->site->getLocales());
        $this->set('flag', $this->app->make(Flag::class));
        $cl = $this->app->make('helper/lists/countries');
        $ll = $this->app->make('localization/languages');
        $languages = $ll->getLanguageList();
        $templates = array('' => t('** Choose a Page Template'));
        foreach(Template::getList() as $template) {
            $templates[$template->getPageTemplateID()] = $template->getPageTemplateDisplayName();
        }
        $this->set('languages', $languages);
        $this->set('templates', $templates);
        $this->set('countries', $cl->getCountries());

        // settings section
        $defaultSourceLanguage = '';
        $defaultSourceCountry = '';
        $defaultSourceLocale = $this->getSite()->getConfigRepository()->get('multilingual.default_source_locale');
        if ($defaultSourceLocale) {
            if (strpos($defaultSourceLocale, '_') === false) {
                $defaultSourceLanguage = $defaultSourceLocale;
            } else {
                list($defaultSourceLanguage, $defaultSourceCountry) = explode('_', $defaultSourceLocale);
            }
        }
        $this->set('defaultSourceLanguage', $defaultSourceLanguage);
        $this->set('defaultSourceCountry', $defaultSourceCountry);
        $this->set('redirectHomeToDefaultLocale', $this->getSite()->getConfigRepository()->get('multilingual.redirect_home_to_default_locale'));
        $this->set('useBrowserDetectedLocale', $this->getSite()->getConfigRepository()->get('multilingual.use_browser_detected_locale'));
    }

    public function get_countries_for_language()
    {
        $cl = $this->app->make('helper/lists/countries');
        $result = array();
        $language = $this->request->query->get('language');
        if (is_string($language) && strlen($language)) {
            $cl = Core::Make('lists/countries');
            $result = $cl->getCountriesForLanguage($language);
        }
        return new JsonResponse($result);
    }

    public function load_icon()
    {
        $ch = $this->app->make(Flag::class);
        $msCountry = $this->request->request->get('msCountry');
        $flag = $ch->getFlagIcon($msCountry);
        if ($flag) {
            $html = $flag;
        } else {
            $html = "<div><strong>" . t('None') . "</strong></div>";
        }
        echo $html;
        exit;
    }

    public function add_content_section()
    {
        if (!$this->token->validate('add_content_section')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->request->request->get('msLanguage')) {
            $this->error->add(t('You must specify a valid language.'));
        }
        if (!$this->request->request->get('msCountry')) {
            $this->error->add(t('You must specify a valid country.'));
        }
        if (!$this->request->request->get('urlSlug')) {
            $this->error->add(t('You must specify a valid URL Slug for the home page.'));
        }
        $template = null;
        if ($this->request->request->has('template')) {
            $template = Template::getByID($this->request->request->get('template'));
        }

        if (!is_object($template)) {
            $this->error->add(t('You must specify a valid page template.'));
        }
        if ($this->post('msLanguage')) {
            $combination = $this->post('msLanguage') . '_' . $this->post('msCountry');
            foreach($this->site->getLocales() as $locale) {
                if ($locale->getLocale() == $combination) {
                    $this->error->add(t('This language/region combination already exists.'));
                }
            }
        }
        if (!$this->error->has()) {
            $service = new Service($this->entityManager);
            $locale = $service->add($this->getSite(), $this->request->request->get('msLanguage'), $this->request->request->get('msCountry'));
            $service->addHomePage($locale, $template, $this->request->request->get('homePageName'), $this->request->request->get('urlSlug'));
            $this->flash('success', t('Locale added successfully.'));
            return new JsonResponse($locale);
        } else {
            return new JsonResponse($this->error);
        }
    }

    public function set_default()
    {
        if (Loader::helper('validation/token')->validate('set_default')) {
            $ll = Core::make('localization/languages');
            $languages = $ll->getLanguageList();
            $cl = Core::Make('lists/countries');
            $countries = $cl->getCountries();
            $service = new Service($this->entityManager);
            /**
             * @var $locale Locale
             */
            $locale = $service->getByID($this->post('defaultLocale'));
            if (is_object($locale)) {
                $service->setDefaultLocale($locale);
                $redirectHomeToDefaultLocale = $this->post('redirectHomeToDefaultLocale') ? true : false;
                $this->getSite()->getConfigRepository()->save('multilingual.redirect_home_to_default_locale', $redirectHomeToDefaultLocale);
                if ($redirectHomeToDefaultLocale) {
                    $this->getSite()->getConfigRepository()->save('multilingual.use_browser_detected_locale', $this->post('useBrowserDetectedLocale') ? true : false);
                }
                $defaultSourceLocale = '';
                $s = $this->post('defaultSourceLanguage');
                if (is_string($s) && array_key_exists($s, $languages)) {
                    $defaultSourceLocale = $s;
                    $s = $this->post('defaultSourceCountry');
                    if (is_string($s) && array_key_exists($s, $countries)) {
                        $defaultSourceLocale .= '_' . $s;
                    }
                }
                $this->getSite()->getConfigRepository()->save('multilingual.default_source_locale', $defaultSourceLocale);
                $this->flash('success', t('Default Section settings updated.'));
                $this->redirect('/dashboard/system/multilingual/setup', 'view');
            } else {
                $this->error->add(t('Invalid Locale'));
            }
        } else {
            $this->error->add(Loader::helper('validation/token')->getErrorMessage());
        }
        $this->view();
    }

    public function remove_locale_section()
    {
        if (!$this->token->validate('remove_locale_section')) {
            $this->error->add($this->token->getErrorMessage());
        }

        $u = new User();
        if (!$u->isSuperUser()) {
            $this->error->add(t("Only the super user may remove a multilingual section."));
        }

        $service = new Service($this->entityManager);
        /**
         * @var $locale Locale
         */
        $locale = $service->getByID($this->post('siteLocaleID'));
        if (!is_object($locale)) {
            $this->error->add(t("Invalid locale object."));
        }

        if (!$this->error->has()) {
            $service->delete($locale);
            $this->flash('success', t('Section removed.'));
            $this->redirect('/dashboard/system/multilingual/setup', 'view');
        }

        $this->view();
    }

}
