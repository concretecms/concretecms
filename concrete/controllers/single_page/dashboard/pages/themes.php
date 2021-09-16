<?php

namespace Concrete\Controller\SinglePage\Dashboard\Pages;

use Concrete\Core\Block\BlockType\Set;
use Concrete\Core\Package\ItemCategory\Manager;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\PageList;
use Concrete\Core\Page\Theme\Documentation\DocumentationNavigationFactory;
use Concrete\Core\Page\Theme\Documentation\Installer;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\StyleCustomizer\Skin\SkinInterface;
use Config;
use Exception;

class Themes extends DashboardSitePageController
{
    public function view()
    {
        $tArray = Theme::getList();
        $tArray2 = Theme::getAvailableThemes();

        $this->set('tArray', $tArray);
        $this->set('tArray2', $tArray2);

        $activeTheme = Theme::getSiteTheme();
        $this->set('activeTheme', $activeTheme);

        if ($activeTheme->hasSkins()) {
            $themeSkinIdentifier = $this->site->getThemeSkinIdentifier();
            if (!$themeSkinIdentifier) {
                $themeSkinIdentifier = SkinInterface::SKIN_DEFAULT;
            }
            $this->set('themeSkinIdentifier', $themeSkinIdentifier);
        }

        $this->set('activate', $this->action('activate'));
        $this->set('install', $this->action('install'));
    }

    public function save_selected_skin($themeSkinIdentifier = null, $token = null)
    {
        $activeTheme = Theme::getSiteTheme();
        if (!$this->token->validate('save_selected_skin', $token)) {
            $this->error->add($this->token->getErrorMessage());
        }

        if (!$themeSkinIdentifier) {
            $this->error->add(t('You must specify a valid theme skin identifier.'));
        }

        if (!$this->error->has()) {
            $this->site->setThemeSkinIdentifier(h($themeSkinIdentifier));
            $this->entityManager->persist($this->site);
            $this->entityManager->flush();
            $this->flash('success', t('Theme skin updated.'));
        }
        return $this->buildRedirect($this->action());
    }

    public function install_documentation($pThemeID = null)
    {
        $theme = Theme::getByID($pThemeID);
        if ($theme) {
            if (!$this->token->validate('install_documentation')) {
                $this->error->add($this->token->getErrorMessage());
            }

            if (!$this->error->has()) {
                $installer = $this->app->make(Installer::class);
                $installer->install($theme, $theme->getDocumentationProvider());
                $this->flash('success', t('Theme documentation installed.'));
            }
        }
        return $this->buildRedirect($this->action());
    }

    public function uninstall_documentation($pThemeID = null)
    {
        $theme = Theme::getByID($pThemeID);
        if ($theme) {
            if (!$this->token->validate('uninstall_documentation')) {
                $this->error->add($this->token->getErrorMessage());
            }

            if (!$this->error->has()) {
                $installer = $this->app->make(Installer::class);
                $installer->clearDocumentation($theme, $theme->getDocumentationProvider());
                $this->flash('success', t('Theme documentation removed.'));
            }
        }
        return $this->buildRedirect($this->action());
    }



    public function preview($pThemeID = null, $previewPageID = null)
    {
        $theme = Theme::getByID($pThemeID);
        if ($theme) {
            $skins = $theme->getSkins();
            $this->set('customizeTheme', $theme);
            $this->set('skins', $skins);
            $this->set('selectedSkin', $theme->getThemeDefaultSkin());
            $this->set('blockTypeSets', Set::getList());
            $this->setTheme('concrete');
            $this->setThemeViewTemplate('empty.php');

            $previewPage = $this->app->make('site')->getSite()->getSiteHomePageObject();
            $themeDocumentationPages = $theme->getThemeDocumentationPages();
            if (count($themeDocumentationPages)) {
                if ($previewPageID) {
                    foreach ($themeDocumentationPages as $themeDocumentationPage) {
                        if ($themeDocumentationPage->getCollectionID() == $previewPageID) {
                            $previewPage = $themeDocumentationPage;
                        }
                    }
                } else {
                    $themeDocumentationParent = $theme->getThemeDocumentationParentPage();
                    if ($themeDocumentationParent) {
                        $previewPage = $themeDocumentationParent->getFirstChild();
                    }
                }
                $factory = new DocumentationNavigationFactory($theme);
                $this->set('documentationNavigation', $factory->createNavigation());
            }
            $this->set('previewPage', $previewPage);
            $this->render('/dashboard/pages/themes/preview');
        } else {
            return $this->buildRedirect($this->action());
        }
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
                /** @var Manager $manager */
                $manager = $this->app->make(Manager::class, ['application' => $this->app]);
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

    public function install()
    {
        $th = Theme::getByFileHandle($this->request->request->get('theme'));

        if (!$th) {
            $this->error->add(t('Invalid theme handle.'));
        }

        if (!$this->token->validate('install_theme')) {
            $this->error->add($this->token->getErrorMessage());
        }

        $existing = Theme::getByHandle($this->request->request->get('theme'));
        if ($existing) {
            $this->error->add('That theme has already been installed.');
        }

        if (!$this->error->has()) {
            try {
                $t = Theme::add($this->request->request->get('theme'));
                $this->flash('success', t('Theme %s installed successfully', $t->getThemeName()));
                return $this->buildRedirect($this->action('inspect', $t->getThemeID(), 'install'));
            } catch (Exception $e) {
                $this->error->add($e);
            }
        }

        $this->view();
    }

    public function activate_confirm()
    {
        $theme = Theme::getByID($this->request->request->get('pThemeID'));
        if (!$theme) {
            $this->error->add(t('Invalid theme.'));
        }
        if (!$this->token->validate('activate_confirm')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            $theme->applyToSite();
            $this->flash('success', t('Applied %s theme to site', $theme->getThemeName()));
            return $this->buildRedirect($this->action('inspect', $theme->getThemeID(), 'activate'));
        }

        $this->view();
    }
}
