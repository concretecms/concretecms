<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Multilingual;

use Concrete\Core\Entity\Site\Locale;
use Concrete\Core\Entity\Site\SiteTree;
use Concrete\Core\Multilingual\Service\UserInterface\Flag;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Core;
use Concrete\Core\Multilingual\Page\Section\Section;
use Localization;
use Loader;
use Page;
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
        $this->set('languages', $languages);
        $this->set('countries', $cl->getCountries());
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
        if ($this->post('msLanguage')) {
            $combination = $this->post('msLanguage') . '_' . $this->post('msCountry');
            foreach($this->site->getLocales() as $locale) {
                if ($locale->getLocale() == $combination) {
                    $this->error->add(t('This language/region combination already exists.'));
                }
            }
        }
        if (!$this->error->has()) {
            $locale = new Locale();
            $tree = new SiteTree();
            $locale->setCountry($this->request->request->get('msCountry'));
            $locale->setLanguage($this->request->request->get('msLanguage'));
            $locale->setSite($this->getSite());
            $locale->setSiteTree($tree);
            $this->entityManager->persist($locale);
            $this->entityManager->flush();
            $this->flash('success', t('Locale added successfully.'));
            return new JsonResponse($locale);
        } else {
            return new JsonResponse($this->error);
        }
    }

    /*
    public $helpers = array('form');
    protected $pagesToCopy = array();

    public function add_content_section()
    {
        if (Loader::helper('validation/token')->validate('add_content_section')) {
            if ((!Loader::helper('validation/numbers')->integer($this->post('pageID'))) || $this->post('pageID') < 1) {
                $this->error->add(t('You must specify a page for this multilingual content section.'));
            } else {
                $pc = Page::getByID($this->post('pageID'));
            }

            if (!$this->error->has()) {
                $lc = Section::getByID($this->post('pageID'));
                if (is_object($lc)) {
                    $this->error->add(t('A multilingual section page at this location already exists.'));
                }
            }

            if (!$this->error->has()) {
                if ($this->post('msLanguage')) {
                    $combination = $this->post('msLanguage') . '_' . $this->post('msCountry');
                    $locale = Section::getByLocale($combination, $this->getSite());
                    if (is_object($locale)) {
                        $this->error->add(t('This language/region combination already exists.'));
                    }
                }
            }

            if (!$this->error->has()) {
                Section::assign($this->getSite(), $pc, $this->post('msLanguage'), $this->post('msCountry'));
                $this->redirect('/dashboard/system/multilingual/setup', 'multilingual_content_updated');
            }
        } else {
            $this->error->add(Loader::helper('validation/token')->getErrorMessage());
        }
        $this->view();
    }

    public function view()
    {
        $ll = Core::make('localization/languages');
        $cl = Core::make('lists/countries');
        $languages = $ll->getLanguageList();

        $this->set('pages', Section::getList($this->getSite()));
        $this->set('languages', $languages);
        $this->set('countries', $cl->getCountries());
        $this->set('ch', Core::make('multilingual/interface/flag'));

        $this->set('defaultLocale', $this->getSite()->getConfigRepository()->get('multilingual.default_locale'));
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

    protected function populateCopyArray($startingPage)
    {
        $db = Loader::db();
        if ($startingPage->isAlias()) {
            $cID = $startingPage->getCollectionPointerOriginalID();
        } else {
            $cID = $startingPage->getCollectionID();
        }

        $q = "select cID from Pages where cParentID = ? order by cDisplayOrder asc";
        $r = $db->query($q, array($cID));
        while ($row = $r->fetchRow()) {
            $c = Page::getByID($row['cID'], 'RECENT');
            if (!$c->getAttribute('multilingual_exclude_from_copy')) {
                $this->pagesToCopy[] = $c;
                $this->populateCopyArray($c);
            }
        }
    }

    public function multilingual_content_enabled()
    {
        $this->set('message', t('Multilingual content enabled'));
        $this->view();
    }

    public function multilingual_content_updated()
    {
        $this->set('message', t('Multilingual content updated'));
        $this->view();
    }

    public function locale_section_removed()
    {
        $this->set('message', t('Section removed.'));
        $this->view();
    }

    public function default_locale_updated()
    {
        $this->set('message', t('Default Section settings updated.'));
        $this->view();
    }

    public function set_default()
    {
        if (Loader::helper('validation/token')->validate('set_default')) {
            $ll = Core::make('localization/languages');
            $languages = $ll->getLanguageList();
            $cl = Core::Make('lists/countries');
            $countries = $cl->getCountries();
            $lc = Section::getByLocale($this->post('defaultLocale'), $this->getSite());
            if (is_object($lc)) {
                $this->getSite()->getConfigRepository()->save('multilingual.default_locale', $this->post('defaultLocale'));
                $this->getSite()->getConfigRepository()->save('multilingual.redirect_home_to_default_locale', $this->post('redirectHomeToDefaultLocale'));
                $this->getSite()->getConfigRepository()->save('multilingual.use_browser_detected_locale', $this->post('useBrowserDetectedLocale'));
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
                $this->redirect('/dashboard/system/multilingual/setup', 'default_locale_updated');
            } else {
                $this->error->add(t('Invalid Section'));
            }
        } else {
            $this->error->add(Loader::helper('validation/token')->getErrorMessage());
        }
        $this->view();
    }

    public function remove_locale_section($sectionID = false, $token = false)
    {
        if (Loader::helper('validation/token')->validate('', $token)) {
            $lc = Section::getByID($sectionID);
            if (is_object($lc)) {
                $lc->unassign();
                $this->redirect('/dashboard/system/multilingual/setup', 'locale_section_removed');
            } else {
                $this->error->add(t('Invalid section'));
            }
        } else {
            $this->error->add(Loader::helper('validation/token')->getErrorMessage());
        }
        $this->view();
    }
    */
}
