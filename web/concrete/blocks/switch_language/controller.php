<?php
namespace Concrete\Block\SwitchLanguage;
use Concrete\Core\Block\BlockController;

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

    public function on_page_view()
    {
        $this->addHeaderItem(Loader::helper('html')->javascript('jquery.js'));
    }

    public function add()
    {
        $this->set('label', t('Choose Language'));
    }

    public function view()
    {
        $uh = Loader::helper('concrete/urls');
        $bt = BlockType::getByHandle('switch_language');

        Loader::model('section', 'multilingual');
        $ml = MultilingualSection::getList();
        $c = Page::getCurrentPage();
        $al = MultilingualSection::getBySectionOfSite($c);
        $languages = array();
        $locale = ACTIVE_LOCALE;
        if (is_object($al)) {
            $locale = $al->getLanguage();
        }
        foreach ($ml as $m) {
            $languages[$m->getCollectionID()] = $m->getLanguageText($locale) . ' ' . (strlen(
                    $m->msIcon
                ) ? '(' . $m->msIcon . ')' : '');
        }
        $this->set('languages', $languages);
        $this->set('languageSections', $ml);
        $this->set('action', $uh->getBlockTypeToolsURL($bt) . '/switch');
        if (is_object($al)) {
            $this->set('activeLanguage', $al->getCollectionID());
        }

        $pkg = Package::getByHandle('multilingual');
        $mdl = Loader::helper('default_language', 'multilingual');
        $this->set('defaultLanguage', $mdl->getSessionDefaultLocale());
        $this->set('cID', $c->getCollectionID());

    }

}