<?php

namespace Concrete\Controller\SinglePage\Dashboard\Multilingual;
use \Concrete\Core\Page\Controller\DashboardPageController;

defined('C5_EXECUTE') or die("Access Denied.");

class Setup extends DashboardPageController
{

    public $helpers = array('form');
    protected $pagesToCopy = array();

    public function view()
    {
        Loader::library('3rdparty/Zend/Locale');
        Loader::library('content_localization', 'multilingual');
        $localesx = MultilingualContentLocalization::getLanguages();
        $locales = array('' => t('** Choose a Language'));
        foreach ($localesx as $key => $value) {
            // remove languages like klingon
            //if (strlen($key) < 3) {
            $locales[$key] = $value;
            //}
        }
        asort($locales);
        $this->set('ch', Loader::helper('interface/flag', 'multilingual'));
        $this->set('pages', MultilingualSection::getList());
        $this->set('locales', $locales);

        $pkg = Package::getByHandle('multilingual');
        $this->set('defaultLanguage', $pkg->config('DEFAULT_LANGUAGE'));
        $this->set('redirectHomeToDefaultLanguage', $pkg->config('REDIRECT_HOME_TO_DEFAULT_LANGUAGE'));
        $this->set('useBrowserDetectedLanguage', $pkg->config('TRY_BROWSER_LANGUAGE'));
    }

    function populateCopyArray($startingPage)
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

    public function copy_tree()
    {
        set_time_limit(0);
        if (Loader::helper('validation/token')->validate('copy_tree')) {
            if ($this->post('copyTreeFrom') && $this->post('copyTreeTo') && $this->post('copyTreeFrom') != $this->post(
                    'copyTreeTo'
                )
            ) {
                $dc = Page::getByID($this->post('copyTreeTo'));
                $oc = Page::getByID($this->post('copyTreeFrom'));
                $dcp = new Permissions($dc);
                $ocp = new Permissions($oc);
                if (!$dcp->canAdminPage()) {
                    $this->error->add(
                        t('You must have admin privileges on the destination page to perform this action.')
                    );
                }
                if (!$ocp->canRead()) {
                    $this->error->add(t('You cannot read the original page.'));
                }

                if (!$this->error->has()) {
                    // duplicate all into the new node
                    $ms = MultilingualSection::getByID($this->post('copyTreeTo'));
                    $this->populateCopyArray($oc);

                    $aliases = array();
                    $created = array();
                    foreach ($this->pagesToCopy as $cc) {
                        $trcID = $ms->getTranslatedPageID($cc);
                        if (!$trcID) {
                            // this page doesn't exist in the new tree. So we need to duplicate it over there
                            // find where this page is going

                            $ccp = Page::getByID($cc->getCollectionParentID(), 'RECENT');
                            $trpcID = $ms->getTranslatedPageID($ccp);
                            $dest = Page::getByID($trpcID);
                            if ($cc->isAlias()) {
                                $aliases[] = array($cc->getCollectionID(), $dest->getCollectionID());
                            } else {
                                $newPage = $cc->duplicate($dest);
                                $ceated[$cc->getCollectionID()] = $newPage->getCollectionID();
                            }
                        } else {
                            if ($cc->isAlias()) {
                                $aliases[] = array($cc->getCollectionID(), false);
                            } else {
                                $created[$cc->getCollectionID()] = $trcID;
                            }
                        }
                    }
                    foreach ($aliases as $data) {
                        list($cID, $dest) = $data;
                        $cc = Page::getByID($cID);
                        if ($dest === false) {
                            $ccp = Page::getByID($cc->getCollectionParentID(), 'RECENT');
                            $dest = $ms->getTranslatedPageID($ccp);
                        }
                        if (isset($created[$cID])) {
                            $dest = $created[$cID];
                        }
                        $aliasID = $cc->addCollectionAlias(Page::getByID($dest));
                    }
                    $this->redirect('/dashboard/multilingual/setup', 'tree_copied');
                }

            } else {
                $this->error->add(t('You must choose two separate, valid language sections.'));
            }
        } else {
            $this->error->add(Loader::helper('validation/token')->getErrorMessage());
        }
        $this->view();
    }

    public function load_icons()
    {
        if (!$this->post('msLanguage')) {
            return false;
        }
        $ch = Loader::helper('interface/flag', 'multilingual');
        Loader::library('3rdparty/Zend/Locale');
        // here's what we do. We load all locales, then we filter through all those that match the posted language code
        // and we return html for all regions in that language
        $locales = Zend_Locale::getLocaleList();
        $countries = array();
        $html = '';

        foreach ($locales as $locale => $none) {
            $zl = new Zend_Locale($locale);
            if ($zl->getLanguage() == $this->post('msLanguage') || $zl->toString() == $this->post('msLanguage')) {
                if ($zl->getRegion()) {
                    $countries[$zl->getRegion()] = Zend_Locale::getTranslation(
                        $zl->getRegion(),
                        'country',
                        ACTIVE_LOCALE
                    );
                }
            }
        }

        asort($countries);
        $i = 1;
        foreach ($countries as $region => $value) {
            $flag = $ch->getFlagIcon($region);
            if ($flag) {
                $checked = "";
                if ($this->post('selectedLanguageIcon') == $region) {
                    $checked = "checked=\"checked\"";
                } else {
                    if ($i == 1 && (!$this->post('selectedLanguageIcon'))) {
                        $checked = "checked=\"checked\"";
                    }
                }
                $html .= '<li><label><input type="radio" name="msIcon" ' . $checked . ' id="languageIcon' . $i . '" value="' . $region . '" onchange="ccm_multilingualUpdateLocale(\'' . $region . '\')" /><span class="image-wrapper">' . $flag . '' . $value . '</span></label></li>';
                $i++;
            }
        }

        if ($i == 1) {
            $html = "<li><label><span><strong>" . t('None') . "</strong></span></label></li>";
        }


        print $html;
        exit;
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

    public function tree_copied()
    {
        $this->set('message', t('Multilingual tree copied.'));
        $this->view();
    }

    public function language_section_removed()
    {
        $this->set('message', t('Language section removed.'));
        $this->view();
    }

    public function default_language_updated()
    {
        $this->set('message', t('Default language settings updated.'));
        $this->view();
    }

    public function set_default()
    {
        if (Loader::helper('validation/token')->validate('set_default')) {
            $lc = MultilingualSection::getByLocale($this->post('defaultLanguage'));
            if (is_object($lc)) {
                $pkg = Package::getByHandle('multilingual');
                $pkg->saveConfig('DEFAULT_LANGUAGE', $this->post('defaultLanguage'));
                $pkg->saveConfig('REDIRECT_HOME_TO_DEFAULT_LANGUAGE', $this->post('redirectHomeToDefaultLanguage'));
                $pkg->saveConfig('TRY_BROWSER_LANGUAGE', $this->post('useBrowserDetectedLanguage'));
                $this->redirect('/dashboard/multilingual/setup', 'default_language_updated');

            } else {
                $this->error->add(t('Invalid language section'));
            }
        } else {
            $this->error->add(Loader::helper('validation/token')->getErrorMessage());
        }
        $this->view();
    }

    public function remove_language_section($sectionID = false, $token = false)
    {
        if (Loader::helper('validation/token')->validate('', $token)) {
            $lc = MultilingualSection::getByID($sectionID);
            if (is_object($lc)) {

                $lc->unassign();
                $this->redirect('/dashboard/multilingual/setup', 'language_section_removed');

            } else {
                $this->error->add(t('Invalid language section'));
            }
        } else {
            $this->error->add(Loader::helper('validation/token')->getErrorMessage());
        }
        $this->view();
    }

    public function add_content_section()
    {
        if (Loader::helper('validation/token')->validate('add_content_section')) {
            if ((!Loader::helper('validation/numbers')->integer($this->post('pageID'))) || $this->post('pageID') < 1) {
                $this->error->add(t('You must specify a page for this multilingual content section.'));
            } else {
                $pc = Page::getByID($this->post('pageID'));
            }

            if (!$this->error->has()) {
                $lc = MultilingualSection::getByID($this->post('pageID'));
                if (is_object($lc)) {
                    $this->error->add(t('A language section page at this location already exists.'));
                }
            }

            if (!$this->error->has()) {
                if ($this->post('msIcon')) {
                    $combination = $this->post('msLanguage') . '_' . $this->post('msIcon');
                    $locale = MultilingualSection::getByLocale($combination);
                    if (is_object($locale)) {
                        $this->error->add(t('This language/region combination already exists.'));
                    }
                }
            }

            if (!$this->error->has()) {
                MultilingualSection::assign($pc, $this->post('msLanguage'), $this->post('msIcon'));
                $this->redirect('/dashboard/multilingual/setup', 'multilingual_content_updated');
            }
        } else {
            $this->error->add(Loader::helper('validation/token')->getErrorMessage());
        }
        $this->view();
    }

}
