<?php
namespace Concrete\Block\SwitchLanguage;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Multilingual\Page\Section;
use Concrete\Core\Routing\Redirect;

defined('C5_EXECUTE') or die("Access Denied.");

class Controller extends BlockController
{

    protected $btInterfaceWidth = "500";
    protected $btInterfaceHeight = "150";
    protected $btTable = 'btSwitchLanguage';

    public $helpers = array('form');

    public function getBlockTypeDescription()
    {
        return t("Adds a front-end language switcher to your website.");
    }

    public function getBlockTypeName()
    {
        return t("Switch Language");
    }

    public function action_switch_language()
    {
        $lang = Section::getByID($_REQUEST['language']);
        if (is_object($lang)) {
            if ($this->post('currentPageID')) {
                $page = \Page::getByID($this->post('ccmMultilingualCurrentPageID'));
                if (!$page->isError()) {
                    $relatedID = $lang->getTranslatedPageID($page);
                    if ($relatedID) {
                        $pc = \Page::getByID($relatedID);
                        Redirect::page($pc)->send();
                        exit;
                    }
                }
            }
            Redirect::page($lang)->send();
            exit;
        }

        Redirect::to('/');
        exit;
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
        $languages = array();
        $locale = \Localization::activeLocale();
        if (is_object($al)) {
            $locale = $al->getLanguage();
        }
        foreach ($ml as $m) {
            $languages[$m->getCollectionID()] = $m->getLanguageText($locale);
        }
        $this->set('languages', $languages);
        $this->set('languageSections', $ml);
        if (is_object($al)) {
            $this->set('activeLanguage', $al->getCollectionID());
        }
        $dl = \Core::make('multilingual/detector');
        $this->set('defaultLocale', $dl->getPreferredSection());
        $this->set('cID', $c->getCollectionID());
    }

}