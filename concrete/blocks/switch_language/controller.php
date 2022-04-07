<?php

namespace Concrete\Block\SwitchLanguage;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\Http\Response;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Cookie;
use Session;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends BlockController implements UsesFeatureInterface
{
    protected $btInterfaceWidth = 500;
    protected $btInterfaceHeight = 150;
    protected $btTable = 'btSwitchLanguage';

    public $helpers = ['form'];

    /**
     * @var string
     */
    public $label;

    public function getBlockTypeDescription()
    {
        return t('Adds a front-end language switcher to your website.');
    }

    public function getBlockTypeName()
    {
        return t('Switch Language');
    }

    public function getRequiredFeatures(): array
    {
        return [
            Features::MULTILINGUAL
        ];
    }

    /**
     * @param int $currentPageID
     * @param int $sectionID
     *
     * @return \League\URL\URLInterface
     */
    public function resolve_language_url($currentPageID, $sectionID)
    {
        $resolve = ['/'];
        $lang = Section::getByID((int) $sectionID);
        if (is_object($lang)) {
            $resolve = [$lang];
            $page = \Page::getByID((int) $currentPageID);
            if (!$page->isError()) {
                $relatedID = $lang->getTranslatedPageID($page);
                if ($relatedID) {
                    $pc = \Page::getByID($relatedID);
                    $resolve = [$pc];
                } elseif ($page->isGeneratedCollection()) {
                    $this->app->make('session')->set('multilingual_default_locale', $lang->getLocale());
                    $resolve = [$page];
                }
            }
        }

        return $this->app->make(ResolverManagerInterface::class)->resolve($resolve);
    }

    public function action_switch_language($currentPageID, $sectionID, $bID = false)
    {
        $to = $this->resolve_language_url($currentPageID, $sectionID);
        $rf = $this->app->make(ResponseFactoryInterface::class);

        return $rf->redirect($to, Response::HTTP_FOUND);
    }

    public function action_set_current_language()
    {
        if ($this->request->request->has('language')) {
            $section = Section::getByID($this->request->request->get('language'));
            if (is_object($section)) {
                Session::set('multilingual_default_locale', $section->getLocale());
                if ($this->request->request->get('remember')) {
                    Cookie::set('multilingual_default_locale', $section->getLocale(), time() + (60 * 60 * 24 * 365));
                } else {
                    Cookie::clear('multilingual_default_locale');
                }
            }
        }

        return $this->action_switch_language($this->request->request->get('rcID'), $this->request->request->get('language'));
    }

    public function add()
    {
        $this->set('label', t('Choose Language'));
    }

    public function view()
    {
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
        $mlAccessible = [];
        foreach ($ml as $m) {
            $pc = new Checker(\Page::getByID($m->getCollectionID()));
            if ($pc->canRead()) {
                $mlAccessible[] = $m;
                $languages[$m->getCollectionID()] = $m->getLanguageText($m->getLocale());
            }
        }
        $this->set('languages', $languages);
        $this->set('languageSections', $mlAccessible);
        $this->set('activeLanguage', $al ? $al->getCollectionID() : null);
        $dl = $this->app->make('multilingual/detector');
        $this->set('defaultLocale', $dl->getPreferredSection());
        $this->set('locale', $locale);
        $this->set('cID', $c->getCollectionID());
    }
}
