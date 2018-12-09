<?php
namespace Concrete\Controller\Element\Dashboard\Sitemap;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Error\UserMessageException;

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
        $cParentID = $this->request->query->get('cParentID');
        if ($cParentID !== null) {
            $this->set('cParentID', (int) $cParentID);
        }
        $display = $this->request->query->get('display');
        if (!empty($cParentID)) {
            $this->set('display', $display);
        }
    }
}
