<?php

namespace Concrete\Controller\Frontend;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Filesystem\Element;
use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Http\Request;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Page\Theme\Theme as PageTheme;
use Concrete\Core\View\PreviewView;
use Concrete\Core\View\View;

class Theme extends Controller
{
    public function previewComponent(string $themeID, string $component)
    {
        if (preg_match('/^[A-Za-z0-9_-]+$/i', $component)) {
            $page = Page::getByPath('/dashboard/pages/themes');
            $checker = new Checker($page);
            if ($checker->canViewPage()) {
                $theme = PageTheme::getByID($themeID);
                if ($theme) {
                    $element = new Element(DIRNAME_BEDROCK . '/' . $component);
                    if ($element->exists()) {
                        // Todo: create a proxy page during content installation in a hidden area of the site tree
                        // that's not gated by permissions, so we don't have to fake it with the home page.
                        $proxyPageID = Page::getHomePageID();
                        $proxyPage = Page::getByID($proxyPageID);

                        $request = Request::getInstance();
                        $request->setCustomRequestUser(-1);
                        $request->setCurrentPage($proxyPage);

                        $view = new PreviewView('/frontend/preview_theme_component');
                        $view->setProxyPage($page);
                        $view->setPreviewElement($element);
                        $view->setViewTheme($theme);

                        return $view;
                    } else {
                        throw new UserMessageException(t('Unable to locate component to preview: %s', $component));
                    }
                } else {
                    throw new \RuntimeException(t('Invalid theme ID: %s', h($themeID)));
                }
            }
        } else {
            throw new \RuntimeException(t('Invalid component handle detected'));
        }
    }
}
