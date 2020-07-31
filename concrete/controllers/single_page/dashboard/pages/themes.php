<?php

namespace Concrete\Controller\SinglePage\Dashboard\Pages;

use Concrete\Core\Package\ItemCategory\Manager;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Theme\Theme;
use Config;
use Exception;

class Themes extends DashboardPageController
{
    public function view()
    {
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
        $this->set('activate', $this->action('activate'));
        $this->set('install', $this->action('install'));
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

        return $this->buildRedirect($this->action('mobile_theme_saved'));
    }

    public function mobile_theme_saved()
    {
        $this->set('success', t('Mobile theme saved.'));
        $this->view();
    }

    public function remove($pThemeID, $token = '')
    {
        try {
            if (!$this->token->validate('remove', $token)) {
                throw new Exception($this->token->getErrorMessage());
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
                        foreach ($category->getItems($pkg) as $item) {
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
        $this->set('activate_confirm', $this->action('activate_confirm', $pThemeID, $this->token->generate('activate')));
    }

    public function install($pThemeHandle = null)
    {
        $th = Theme::getByFileHandle($pThemeHandle);
        if ($pThemeHandle == null) {
            return $this->buildRedirect($this->action());
        }

        $v = $this->app->make('helper/validation/error');
        try {
            if (is_object($th)) {
                $t = Theme::add($pThemeHandle);

                return $this->buildRedirect($this->action('inspect', $t->getThemeID(), 'install'));
            }
            throw new Exception('Invalid Theme');
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
        $val = $this->app->make('helper/validation/error');
        if (!$this->token->validate('activate', $token)) {
            $val->add($this->token->getErrorMessage());
            $this->set('error', $val);
        } elseif (!is_object($l)) {
            $val->add('Invalid Theme');
            $this->set('error', $val);
        } else {
            $l->applyToSite();

            return $this->buildRedirect($this->action('inspect', $l->getThemeID(), 'activate'));
        }

        $this->view();
    }
}
