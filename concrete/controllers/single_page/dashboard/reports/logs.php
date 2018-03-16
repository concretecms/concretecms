<?php

namespace Concrete\Controller\SinglePage\Dashboard\Reports;

use Concrete\Core\Logging\LogEntry;
use Concrete\Core\Logging\LogList;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\User\User;
use Log;
use Request;

class Logs extends DashboardPageController
{
    public function clear($token = '', $channel = false)
    {
        $valt = $this->app->make('helper/validation/token');
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

    public function view($page = 0)
    {
        $this->requireAsset('selectize');

        $levels = [];
        foreach (Log::getLevels() as $level) {
            $levels[$level] = Log::getLevelDisplayName($level);
        }

        $channels = ['' => t('All Channels')];
        foreach (Log::getChannels() as $channel) {
            $channels[$channel] = Log::getChannelDisplayName($channel);
        }

        $r = Request::getInstance();
        $query = http_build_query([
            'channel' => $r->query->get('channel'),
            'keywords' => $r->query->get('keywords'),
            'level' => $r->query->get('level'),
        ]);

        $list = $this->getFilteredList();

        $entries = $list->getPage();
        $this->set('list', $list);
        $this->set('entries', $entries);

        $this->set('levels', $levels);
        $this->set('channels', $channels);

        $this->set('query', $query);
    }

    public function csv($token = '')
    {
        $valt = $this->app->make('helper/validation/token');
        if (!$valt->validate('', $token)) {
            $this->redirect('/dashboard/reports/logs');
        } else {
            $list = $this->getFilteredList();
            $entries = $list->get(0);

            $fileName = 'Log Search Results';

            header('Content-Type: text/csv');
            header('Cache-control: private');
            header('Pragma: public');
            $date = date('Ymd');
            header('Content-Disposition: attachment; filename=' . $fileName . "_form_data_{$date}.csv");

            $fp = fopen('php://output', 'w');

            // write the columns
            $row = [
                t('Date'),
                t('Level'),
                t('Channel'),
                t('User'),
                t('Message'),
            ];

            fputcsv($fp, $row);

            foreach ($entries as $ent) {
                $uID = $ent->getUserID();
                if (empty($uID)) {
                    $user = t('Guest');
                } else {
                    $u = User::getByUserID($uID);
                    if (is_object($u)) {
                        $user = $u->getUserName();
                    } else {
                        $user = tc('Deleted user', 'Deleted (id: %s)', $uID);
                    }
                }

                $row = [
                    $ent->getDisplayTimestamp(),
                    $ent->getLevelName(),
                    $ent->getChannelDisplayName(),
                    $user,
                    $ent->getMessage(),
                ];

                fputcsv($fp, $row);
            }

            fclose($fp);
            die;
        }
    }

    public function getFilteredList()
    {
        $list = new LogList();

        $r = Request::getInstance();
        if ($r->query->has('channel') && $r->query->get('channel') != '') {
            $list->filterByChannel($r->query->get('channel'));
            $this->set('selectedChannel', h($r->query->get('channel')));
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

        return $list;
    }

    public function deleteLog($logID, $token = '')
    {
        $valt = $this->app->make('helper/validation/token');
        if ($valt->validate('', $token) && !empty($logID)) {
            $log = LogEntry::getByID($logID);
            if (is_object($log)) {
                $log->delete();
            }
            $this->redirect('/dashboard/reports/logs');
        } else {
            $this->redirect('/dashboard/reports/logs');
        }
        $this->view();
    }
}
