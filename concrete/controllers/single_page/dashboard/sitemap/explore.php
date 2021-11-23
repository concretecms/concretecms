<?php

namespace Concrete\Controller\SinglePage\Dashboard\Sitemap;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Page;

defined('C5_EXECUTE') or die('Access Denied.');

class Explore extends DashboardPageController
{
    public function view($nodeID = 0, $auxMessage = false)
    {
        $canRead = $this->canRead();
        $this->set('canRead', $canRead);
        if (!$canRead) {
            return;
        }
        $task = $this->request->request->get('task');
        if ($task === null) {
            $task = $this->request->query->get('task');
        }
        $cNodeID = $this->request->request->get('cNodeID');
        if ($cNodeID === null) {
            $cNodeID = $this->request->query->get('cNodeID');
        }
        if ($task !== null && $cNodeID !== null) {
            $nc = Page::getByID($cNodeID);
            switch ($task) {
                case 'send_to_top':
                    $nc->movePageDisplayOrderToTop();
                    break;
                case 'send_to_bottom':
                    $nc->movePageDisplayOrderToBottom();
                    break;
            }

            return $this->buildRedirect(['/dashboard/sitemap/explore', $nc->getCollectionParentID(), 'order_updated']);
        }
        $nodeID = (int) $nodeID;
        $this->set('nodeID', $nodeID);
        switch ($auxMessage) {
            case 'order_updated':
                $this->set('message', t('Sort order saved'));
                break;
        }
        $this->set('includeSystemPages', (bool) $this->app->make('session')->get('ccm-sitemap-includeSystemPages'));
        $this->addHeaderItem(
            <<<'EOT'
<style type="text/css">
    div.ccm-sitemap-explore ul li.ccm-sitemap-explore-paging {
        display: none;
    }
</style>
EOT
        );
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

        return $this->buildRedirect('/dashboard/sitemap/explore');
    }

    protected function canRead(): bool
    {
        return $this->app->make('helper/concrete/dashboard/sitemap')->canRead();
    }
}
