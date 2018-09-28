<?php

namespace Concrete\Controller\SinglePage\Dashboard\Sitemap;

use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\Page\Page;

class Explore extends DashboardPageController
{
    public function view($nodeID = 0, $auxMessage = false)
    {
        $dh = $this->app->make('helper/concrete/dashboard/sitemap');
        $this->set('dh', $dh);
        if (!$dh->canRead()) {
            return;
        }
        $this->requireAsset('core/sitemap');
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
            $redirectTo = $this->app->make(ResolverManagerInterface::class)->resolve(['/dashboard/sitemap/explore', $nc->getCollectionParentID(), 'order_updated']);

            return $this->app->make(ResponseFactoryInterface::class)->redirect($redirectTo);
        }
        $nodeID = (int) $nodeID;
        $this->set('nodeID', $nodeID);
        
        if ($auxMessage) {
            switch ($auxMessage) {
                case 'order_updated':
                    $this->set('message', t('Sort order saved'));
                    break;
            }
        }
        $this->set('includeSystemPages', (bool) $dh->includeSystemPages());
        $this->addHeaderItem(<<<'EOT'
<style type="text/css">
    div.ccm-sitemap-explore ul li.ccm-sitemap-explore-paging {
        display: none;
    }
</style>
EOT
        );
    }
}
