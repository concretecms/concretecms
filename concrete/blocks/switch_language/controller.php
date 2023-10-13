<?php

namespace Concrete\Block\SwitchLanguage;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\Http\Response;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Multilingual\Service\Detector;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Cookie;
use Session;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends BlockController implements UsesFeatureInterface
{
    protected $btInterfaceWidth = 500;
    protected $btInterfaceHeight = 150;
    protected $btTable = 'btSwitchLanguage';
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputForRegisteredUsers = true;
    protected $btCacheBlockOutputLifetime = 3600;

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
     * @deprecated Use Detector::getSwitchLink instead
     */
    public function resolve_language_url($currentPageID, $sectionID)
    {
        return $this->app->make('multilingual/detector')->getSwitchLink($currentPageID, $sectionID);
    }

    /**
     * @deprecated Use /ccm/frontend/multilingual/switch_language instead
     */
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
        /** @var Detector $dl */
        $dl = $this->app->make('multilingual/detector');
        $ml = $dl->getAvailableSections();
        $c = Page::getCurrentPage();
        $languages = [];
        $mlAccessible = [];
        foreach ($ml as $m) {
            $pc = new Checker(Page::getByID($m->getCollectionID()));
            if ($pc->canRead()) {
                $mlAccessible[] = $m;
                $languages[$m->getCollectionID()] = $m->getLanguageText($m->getLocale());
            }
        }
        $this->set('languages', $languages);
        $this->set('languageSections', $mlAccessible);
        $al = $dl->getActiveSection($c);
        $this->set('activeLanguage', $al ? $al->getCollectionID() : null);
        $this->set('defaultLocale', $dl->getPreferredSection());
        $this->set('locale', $dl->getActiveLocale($c));
        $this->set('cID', $c->getCollectionID());
        $this->set('detector', $dl);
    }
}
