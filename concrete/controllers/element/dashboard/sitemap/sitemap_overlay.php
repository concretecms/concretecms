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
        $this->requireAsset('core/sitemap');
        $this->set('overlayID', uniqid());
        $this->set('cParentID', (int) $this->request->query->get('cParentID'));
        $display = $this->request->query->get('display');
        if (!empty($display)) {
            $this->set('display', $display);
        }
    }
}
