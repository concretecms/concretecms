<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Seo;

use Concrete\Core\Page\Controller\DashboardPageController;
use Config;

class Statistics extends DashboardPageController
{
    public function view($updated = false)
    {
        if ($this->isPost()) {
            $sv = $this->post('STATISTICS_TRACK_PAGE_VIEWS') == 1 ? 1 : 0;
            Config::save('concrete.statistics.track_page_views', $sv);
            $this->redirect('/dashboard/system/seo/statistics', '1');
        }
        if ($updated) {
            $this->set('message', t('Statistics tracking preference saved.'));
        }
        $this->set('STATISTICS_TRACK_PAGE_VIEWS', Config::get('concrete.statistics.track_page_views'));
    }
}
