<?php
namespace Concrete\Controller\SinglePage\Dashboard\Pages;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Theme\Theme;
use Config;
use Loader;
use View;
use Exception;
use Concrete\Core\Package\ItemCategory\Manager;
use Concrete\Core\Package\PackageService;

class Themes extends DashboardPageController
{
    protected $helpers = array('html');

    public function view()
    {
        $tArray = array();
        $tArray2 = array();

        $tArray = Theme::getList();
        $tArray2 = Theme::getAvailableThemes();

        $this->set('tArray', $tArray);
        $this->set('tArray2', $tArray2);
        $siteThemeID = 0;
        $obj = Theme::getSiteTheme();
        if (is_object($obj)) {
            $siteThemeID = $obj->getThemeID();
        }

        $this->set('siteThemeID', $siteThemeID);
        $this->set('activate', View::url('/dashboard/pages/themes', 'activate'));
        $this->set('install', View::url('/dashboard/pages/themes', 'install'));
    }

    public function save_mobile_theme()
    {
        if (!$this->token->validate('save_mobile_theme')) {
            $this->error->add(t('Invalid CSRF token. Please refresh and try again.'));
            return $this->view();
        }

        $pt = Theme::getByID($this->post('MOBILE_THEME_ID'));
        if (is_object($pt)) {
            Config::save('concrete.misc.mobile_theme_id', $pt->getThemeID());
        } else {
            Config::save('concrete.misc.mobile_theme_id', 0);
        }
        $this->redirect('/dashboard/pages/themes', 'mobile_theme_saved');
    }

    public function mobile_theme_saved()
    {
        $this->set('success', t('Mobile theme saved.'));
        $this->view();
    }

    public function remove($pThemeID, $token = '')
    {

        try {
            $valt = Loader::helper('validation/token');
            if (!$valt->validate('remove', $token)) {
                throw new Exception($valt->getErrorMessage());
            }
            /** @var \Concrete\Core\Page\Theme\Theme $pl */
            $pl = Theme::getByID($pThemeID);
            if (!is_object($pl)) {
                throw new Exception(t('Invalid theme.'));
            }

            if (!$pl->isUninstallable()) {
                throw new Exception(t('You can not uninstall a core theme'));
            }
            $obj = Theme::getSiteTheme();
            if (is_object($obj)) {
                $siteThemeID = $obj->getThemeID();
            }
            if ($siteThemeID === $pl->getThemeID()) {
                 throw new Exception(t('You can not uninstall an active theme'));
            }


            $localUninstall = true;
            if ($pl->getPackageID() > 0) {
                $pkg = $this->app->make(PackageService::class)->getByID($pl->getPackageID());
                // then we check to see if this is the only theme in that package. If so, we uninstall the package too
                $manager = new Manager($this->app);
                $categories = $manager->getPackageItemCategories();
                $items = [];
                foreach ($categories as $category) {
                    if ($category->hasItems($pkg)) {
                        foreach($category->getItems($pkg) as $item) {
                            $items[] = $item;
                        }
                    }
                }

                if (count($items) == 1) {
                    $_pl = $items[0];
                    if ($_pl instanceof Theme && $_pl->getThemeID() == $pThemeID) {
                        $pkg->uninstall();
                        $localUninstall = false;
                    }
                }
            }
            if ($localUninstall) {
                $pl->uninstall();
            }
            $this->set('message', t('Theme uninstalled.'));
        } catch (Exception $e) {
            $this->error->add($e);
        }
        $this->view();
    }

    public function activate($pThemeID)
    {
        $valt = Loader::helper('validation/token');
        $this->set('activate_confirm', View::url('/dashboard/pages/themes', 'activate_confirm', $pThemeID, $valt->generate('activate')));
    }

    public function install($pThemeHandle = null)
    {
        $th = Theme::getByFileHandle($pThemeHandle);
        if ($pThemeHandle == null) {
            $this->redirect('/dashboard/pages/themes');
        }

        $v = Loader::helper('validation/error');
        try {
            if (is_object($th)) {
                $t = Theme::add($pThemeHandle);
                $this->redirect('/dashboard/pages/themes/inspect', $t->getThemeID(), 'install');
            } else {
                throw new Exception('Invalid Theme');
            }
        } catch (Exception $e) {
            switch ($e->getMessage()) {
                case Theme::E_THEME_INSTALLED:
                    $v->add(t('That theme has already been installed.'));
                    break;
                default:
                    $v->add($e->getMessage());
                    break;
            }

            $this->set('error', $v);
        }
        $this->view();
    }

    // this can be run from /layouts/add/ or /layouts/edit/ or /layouts/ - anything really

    public function activate_confirm($pThemeID, $token)
    {
        $l = Theme::getByID($pThemeID);
        $val = Loader::helper('validation/error');
        $valt = Loader::helper('validation/token');
        if (!$valt->validate('activate', $token)) {
            $val->add($valt->getErrorMessage());
            $this->set('error', $val);
        } elseif (!is_object($l)) {
            $val->add('Invalid Theme');
            $this->set('error', $val);
        } else {
            $l->applyToSite();
            $this->redirect('/dashboard/pages/themes/inspect', $l->getThemeID(), 'activate');
        }
        $this->view();
    }
}
