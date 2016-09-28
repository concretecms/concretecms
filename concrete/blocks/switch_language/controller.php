<?php
namespace Concrete\Block\SwitchLanguage;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Routing\Redirect;
use Cookie;
use Session;

defined('C5_EXECUTE') or die("Access Denied.");

class Controller extends BlockController
{
    protected $btInterfaceWidth = 500;
    protected $btInterfaceHeight = 150;
    protected $btTable = 'btSwitchLanguage';

    public $helpers = ['form'];

    public function getBlockTypeDescription()
    {
        return t("Adds a front-end language switcher to your website.");
    }

    public function getBlockTypeName()
    {
        return t("Switch Language");
    }

    public function action_switch_language($currentPageID, $sectionID, $bID = false)
    {
        $lang = Section::getByID(intval($sectionID));
        if (is_object($lang)) {
            $page = \Page::getByID(intval($currentPageID));
            if (!$page->isError()) {
                $relatedID = $lang->getTranslatedPageID($page);
                if ($relatedID) {
                    $pc = \Page::getByID($relatedID);
                    Redirect::page($pc)->send();
                    exit;
                }
            }
            Redirect::page($lang)->send();
            exit;
        }

        Redirect::to('/');
        exit;
    }

    public function action_set_current_language()
    {
        if ($this->post('language')) {
            $section = Section::getByID($this->post('language'));
            if (is_object($section)) {
                Session::set('multilingual_default_locale', $section->getLocale());
                if ($this->post('remember')) {
                    Cookie::set('multilingual_default_locale', $section->getLocale(), time() + (60 * 60 * 24 * 365));
                } else {
                    Cookie::clear('multilingual_default_locale');
                }
            }
        }

        $this->action_switch_language($this->post('rcID'), $this->post('language'));
    }

    public function add()
    {
        $this->set('label', t('Choose Language'));
    }

    public function view()
    {
        $this->requireAsset('javascript', 'jquery');
        $ml = Section::getList();
        $c = \Page::getCurrentPage();
        $al = Section::getBySectionOfSite($c);
        $languages = [];
        $locale = null;
        if ($al !== null) {
            $locale = $al->getLanguage();
        }
        if (!$locale) {
            $locale = \Localization::activeLocale();
            $al = Section::getByLocale($locale);
        }
        foreach ($ml as $m) {
            $languages[$m->getCollectionID()] = $m->getLanguageText($m->getLocale());
        }
        $this->set('languages', $languages);
        $this->set('languageSections', $ml);
        $this->set('activeLanguage', $al ? $al->getCollectionID() : null);
        $dl = $this->app->make('multilingual/detector');
        $this->set('defaultLocale', $dl->getPreferredSection());
        $this->set('locale', $locale);
        $this->set('cID', $c->getCollectionID());
    }
}
