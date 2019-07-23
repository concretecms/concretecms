<?php
namespace Concrete\Controller\SinglePage\Dashboard\Reports;

use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\Handler\DatabaseHandler;
use Concrete\Core\Logging\Levels;
use Concrete\Core\Logging\LogEntry;
use Concrete\Core\Logging\LogList;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\User\User;
use Log;
use Request;

class Logs extends DashboardPageController
{
    protected function isReportEnabled()
    {
        $config = $this->app->make('config');
        $enabled = $config->get('concrete.log.enable_dashboard_report');

        return $enabled;
    }

    public function clear($token = '', $channel = false)
    {
        if ($this->isReportEnabled()) {
            $valt = $this->app->make('helper/validation/token');
            if ($valt->validate('', $token)) {
                if (!$channel) {
                    DatabaseHandler::clearAll();
                } else {
                    DatabaseHandler::clearByChannel($channel);
                }
                $this->redirect('/dashboard/reports/logs');
            } else {
                $this->redirect('/dashboard/reports/logs');
            }
        }
        $this->view();
    }

    public function view($page = 0)
    {
        $this->set('isReportEnabled', $this->isReportEnabled());

        $this->requireAsset('selectize');

        $levels = [];
        foreach (Log::getLevels() as $level) {
            $levels[$level] = Levels::getLevelDisplayName($level);
        }

        $channels = ['' => t('All Channels')];
        foreach (Channels::getChannels() as $channel) {
            $channels[$channel] = Channels::getChannelDisplayName($channel);
        }

        $r = Request::getInstance();

        $wdt = $this->app->make('helper/form/date_time');
        $date_from = $wdt->translate('date_from', $r->query->all());
        $date_to = $wdt->translate('date_to', $r->query->all());

        $query = http_build_query([
            'channel' => $r->query->get('channel'),
            'keywords' => $r->query->get('keywords'),
            'level' => $r->query->get('level'),
            'date_from' => $date_from,
            'date_to' => $date_to,
        ]);

        $list = $this->getFilteredList();

        $entries = $list->getPage();
        $this->set('list', $list);
        $this->set('entries', $entries);

        $this->set('levels', $levels);
        $this->set('channels', $channels);

        $this->set('wdt', $wdt);
        $this->set('date_from', $date_from);
        $this->set('date_to', $date_to);

        $this->set('query', $query);

        $settingsPage = Page::getByPath('/dashboard/system/environment/logging');
        $settingsPagePermissions = $settingsPage && !$settingsPage->isError() ? new Checker($settingsPage) : null;
        if ($settingsPagePermissions !== null && $settingsPagePermissions->canViewPage()) {
            $this->set('settingsPage', (string) $this->app->make(ResolverManagerInterface::class)->resolve([$settingsPage]));
        } else {
            $this->set('settingsPage', null);
        }
    }

    public function csv($token = '')
    {
        if ($this->isReportEnabled()) {
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
        $this->view();
    }

    protected function getFilteredList()
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

        $wdt = $this->app->make('helper/form/date_time');
        $date_from = $wdt->translate('date_from', $r->query->all());
        $date_to = $wdt->translate('date_to', $r->query->all());

        $date = $this->app->make('date');

        if ($date_from != '' && $date->toDB($date_from) !== false) {
            $list->filterByTime(strtotime($date_from), '>=');
        }
        if ($date_to != '' && $date->toDB($date_to) !== false) {
            $list->filterByTime(strtotime($date_to), '<=');
        }

        return $list;
    }

    public function deleteLog($logID, $token = '')
    {
        if ($this->isReportEnabled()) {
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
}
