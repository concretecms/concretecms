<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Basics;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Database\Connection\Connection;
use DateTime;

class Timezone extends DashboardPageController
{
    public function view()
    {
        $dh = $this->app->make('helper/date');
        $config = $this->app->make('config');
        $this->set('user_timezones', $config->get('concrete.misc.user_timezones'));
        $this->set('timezone', $config->get('app.timezone'));
        $this->set('timezones', $dh->getGroupedTimezones());
        $phpTimezone = $config->get('app.server_timezone');
        $this->set('serverTimezone', $dh->getTimezoneName($phpTimezone));
        $deltaError = null;
        $db = $this->app->make(Connection::class);
        /* @var Connection $db */
        $tsNow = time();
        $ts180Days = $tsNow + 180 * 24 * 60 * 60;
        $rs = $db->executeQuery("select FROM_UNIXTIME($tsNow) as d0, FROM_UNIXTIME($ts180Days) as d1");
        $row = $rs->fetch();
        $rs->closeCursor();
        $dbNow = new DateTime($row['d0']);
        $db180Days = new DateTime($row['d1']);
        $phpNow = DateTime::createFromFormat('U', $tsNow);
        $php180Days = DateTime::createFromFormat('U', $ts180Days);
        $delta = (int) floor(($phpNow->getTimestamp() - $dbNow->getTimestamp()) / 60);
        if ($delta !== 0) {
            $interval = $dh->describeInterval(60 * abs($delta), true);
            if ($delta > 0) {
                $deltaError = t(/*i18n: %s is an interval, like "3 hours and 30 minutes"*/'The database timezone has times greater by %s compared to the PHP timezone.', $interval);
            } else {
                $deltaError = t(/*i18n: %s is an interval, like "3 hours and 30 minutes"*/'The database timezone has times smaller by %s compared to the PHP timezone.', $interval);
            }
        } else {
            $delta = (int) floor(($php180Days->getTimestamp() - $db180Days->getTimestamp()) / 60);
            if ($delta !== 0) {
                $interval = $dh->describeInterval(60 * abs($delta), true);
                $deltaError = t(/*i18n: %s is an interval, like "3 hours and 30 minutes"*/'The way PHP and database handle daylight saving times differs by %s.', $interval);
            }
        }
        if ($deltaError === null) {
            $this->set('dbTimezoneOk', true);
        } else {
            $this->set('dbTimezoneOk', false);
            $this->set('dbDeltaDescription', $deltaError);
        }
    }

    protected function describeDeltaMinutes($delta)
    {
        $negative = ($delta < 0) ? true : false;
        $dh = $this->app->make('helper/date');
        $interval = $dh->describeInterval(60 * abs($delta));

        return $interval;
    }

    public function update()
    {
        if ($this->token->validate('update_timezone')) {
            if ($this->request->isPost()) {
                $config = $this->app->make('config');
                $oldValue = $config->get('concrete.misc.user_timezones') ? true : false;
                $newValue = $this->request->request->get('user_timezones') ? true : false;
                $messages = [];
                if ($oldValue !== $newValue) {
                    $config->save('concrete.misc.user_timezones', $newValue);
                    $messages[] = $newValue ? t('User time zones have been enabled') : t('User time zones have been disabled.');
                }
                $oldValue = (string) $config->get('app.timezone');
                $newValue = $this->request->request->get('timezone');
                if (is_string($newValue) && strcasecmp($newValue, $oldValue) !== 0) {
                    $config->save('app.timezone', $newValue);
                    $messages[] .= t('Default application timezone has been updated.');
                }
                if (!empty($messages)) {
                    $this->flash('message', implode("\n", $messages));
                }
                $this->redirect('/dashboard/system/basics/timezone');
            }
        } else {
            $this->error->add($this->token->getErrorMessage());
            $this->view();
        }
    }
}
