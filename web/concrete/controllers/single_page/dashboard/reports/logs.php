<?php

namespace Concrete\Controller\SinglePage\Dashboard\Reports;
use \Concrete\Core\Page\Controller\DashboardPageController;
use \Concrete\Core\Logging\LogList;
use Log;
use Core;
use Request;

class Logs extends DashboardPageController {

    public function clear($token = '', $channel = false) {
        $valt = Core::make('helper/validation/token');
        if ($valt->validate('', $token)) {
            if (!$channel) {
                Log::clearAll();
            } else {
                Log::clearByChannel($channel);
            }
            $this->redirect('/dashboard/reports/logs');
        } else {
            $this->redirect('/dashboard/reports/logs');
        }
        $this->view();
    }

	public function view($page = 0) {
		$list = new LogList();
        $this->requireAsset('select2');
        $levels = array();
        foreach(Log::getLevels() as $level) {
            $levels[$level] = Log::getLevelDisplayName($level);
        }
        $this->set('levels', $levels);
        $channels = array('' => t('All Channels'));
        foreach(Log::getChannels() as $channel) {
            $channels[$channel] = Log::getChannelDisplayName($channel);
        }
        $r = Request::getInstance();
        if ($r->query->has('channel') && $r->query->get('channel') != '') {
            $list->filterByChannel($r->query->get('channel'));
            $this->set('selectedChannel', $r->query->get('channel'));
        }
        if ($r->query->has('level')) {
            $selectedlevels = $r->get('level');
            if (is_array($selectedlevels) && count($selectedlevels) != 8) {
                $list->filterByLevels($selectedlevels);
            }
        }
        if ($r->query->has('keywords') && $r->query->get('keywords') != '') {
            $list->filterByKeywords($r->query->get('keywords'));
        }

        $entries = $list->getPage();
        $this->set('list', $list);
        $this->set('entries', $entries);

        $this->set('levels', $levels);
        $this->set('channels', $channels);

       }
	
}