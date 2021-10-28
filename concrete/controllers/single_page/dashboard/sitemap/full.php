<?php

namespace Concrete\Controller\SinglePage\Dashboard\Sitemap;

use Concrete\Core\Page\Controller\DashboardPageController;

defined('C5_EXECUTE') or die('Access Denied.');

class Full extends DashboardPageController
{
    public function view()
    {
        $this->set('canRead', $this->canRead());
        $session = $this->app->make('session');
        $this->set('includeSystemPages', (bool) $session->get('ccm-sitemap-includeSystemPages'));
        $this->set('displayDoubleSitemap', (bool) $session->get('ccm-sitemap-displayDoubleSitemap'));
    }

    public function include_system_pages($include = 0)
    {
        if ($this->canRead()) {
            $session = $this->app->make('session');
            if ($include) {
                $session->set('ccm-sitemap-includeSystemPages', true);
            } else {
                $session->remove('ccm-sitemap-includeSystemPages');
            }
        }

        return $this->buildRedirect('/dashboard/sitemap/full');
    }

    public function display_double_sitemap($display = 0)
    {
        if ($this->canRead()) {
            $session = $this->app->make('session');
            if ($display) {
                $session->set('ccm-sitemap-displayDoubleSitemap', true);
            } else {
                $session->remove('ccm-sitemap-displayDoubleSitemap', true);
            }
        }

        return $this->buildRedirect('/dashboard/sitemap/full');
    }

    protected function canRead(): bool
    {
        return $this->app->make('helper/concrete/dashboard/sitemap')->canRead();
    }
}
