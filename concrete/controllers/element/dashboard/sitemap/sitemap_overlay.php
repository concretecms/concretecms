<?php

namespace Concrete\Controller\Element\Dashboard\Sitemap;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Error\UserMessageException;

defined('C5_EXECUTE') or die('Access Denied.');

class SitemapOverlay extends ElementController
{
    /**
     * @return string
     */
    public function getElement()
    {
        return 'dashboard/sitemap/sitemap_overlay';
    }

    public function view()
    {
        $sh = $this->app->make('helper/concrete/dashboard/sitemap');
        if (!$sh->canRead()) {
            throw new UserMessageException(t('Access Denied'));
        }
        $this->set('overlayID', uniqid());
        $this->set('cParentID', (int) $this->request->query->get('cParentID'));
        $this->set('display', (string) $this->request->query->get('display'));
        $this->set('includeSystemPages', $this->request->query->get('includeSystemPages') ? true : false);
    }
}
