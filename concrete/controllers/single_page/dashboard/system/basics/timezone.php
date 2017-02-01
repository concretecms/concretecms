<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Basics;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Database\Connection\Connection;
use DateTime;
use DateTimeZone;
use Exception;

class Timezone extends DashboardPageController
{
    public function view()
    {
        $dh = $this->app->make('date');
        $config = $this->app->make('config');
        $this->set('user_timezones', $config->get('concrete.misc.user_timezones'));
        $this->set('timezone', $config->get('app.timezone'));
        $this->set('timezones', $dh->getGroupedTimezones());
        $phpTimezone = $config->get('app.server_timezone');
        $this->set('serverTimezonePHP', $dh->getTimezoneName($phpTimezone));
        $db = $this->app->make(Connection::class);
        $this->set('serverTimezoneDB', $db->fetchColumn('select @@time_zone'));
        $deltaError = $this->getDeltaTimezone($phpTimezone);
        if ($deltaError === null) {
            $this->set('dbTimezoneOk', true);
        } else {
            $interval = $dh->describeInterval(60 * abs($deltaError['maxDeltaMinutes']), true);
            if ($deltaError['dstProblems']) {
                $deltaError = t(/*i18n: %s is an interval, like "3 hours and 30 minutes"*/'The way PHP and database handle daylight saving times differs by %s.', $interval);
            } else {
                if ($deltaError['maxDeltaMinutes'] > 0) {
                    $deltaError = t(/*i18n: %s is an interval, like "3 hours and 30 minutes"*/'The database timezone has times greater by %s compared to the PHP timezone.', $interval);
                } else {
                    $deltaError = t(/*i18n: %s is an interval, like "3 hours and 30 minutes"*/'The database timezone has times smaller by %s compared to the PHP timezone.', $interval);
                }
            }
            $this->set('dbTimezoneOk', false);
            $this->set('dbDeltaDescription', $deltaError);
            $this->set('compatibleTimezones', $this->getCompatibleTimezones());
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

    public function setSystemTimezone()
    {
        if ($this->token->validate('set_system_timezone')) {
            $timezoneName = $this->post('new-timezone');
            $timezone = null;
            if (is_string($timezoneName) && $timezoneName !== '') {
                try {
                    $timezone = new DateTimeZone($timezoneName);
                } catch (Exception $x) {
                }
            }
            if ($timezone === null) {
                $this->error->add(t('Invalid time zone specified.'));
            } else {
                $this->app->make('config')->save('app.server_timezone', $timezoneName);
            }
        } else {
            $this->error->add($this->token->getErrorMessage());
        }
        if ($this->error->has()) {
            $this->view();
        } else {
            $this->flash('message', t('The system PHP time zone has been updated.'));
            $this->redirect($this->action(''));
        }
    }

    /**
     * @return array
     */
    protected function getCompatibleTimezones()
    {
        $dh = $this->app->make('date');
        $validTimezones = [];
        foreach ($dh->getTimezones() as $timezoneID => $timezoneName) {
            if ($this->getDeltaTimezone($timezoneID) === null) {
                $validTimezones[$timezoneID] = $timezoneName;
            }
        }

        return $validTimezones;
    }

    /**
     * Check if a PHP time zone is compatible with the database timezone.
     *
     * @param DateTimeZone|string $phpTimezone
     *
     * @return null|array If the time zone matches, we'll return null, otherwise an array with the keys 'dstProblems' (boolean) and 'maxDeltaMinutes' (int)
     */
    protected function getDeltaTimezone($phpTimezone)
    {
        if (!($phpTimezone instanceof DateTimeZone)) {
            $phpTimezone = new DateTimeZone($phpTimezone);
        }
        $data = $this->getTimestamps();
        extract($data);
        $sometimesSame = false;
        $maxDeltaMinutes = 0;
        foreach ($timestamps as $index => $timestamp) {
            $databaseValue = new DateTime($databaseDatetimes[$index], $phpTimezone);
            $phpValue = DateTime::createFromFormat('U', $timestamp, $phpTimezone);
            $deltaMinutes = (int) floor(($phpValue->getTimestamp() - $databaseValue->getTimestamp()) / 60);
            if ($deltaMinutes === 0) {
                $sometimesSame = true;
            } else {
                if (abs($deltaMinutes) > abs($maxDeltaMinutes)) {
                    $maxDeltaMinutes = $deltaMinutes;
                }
            }
        }

        if ($maxDeltaMinutes === 0) {
            return null;
        } else {
            return [
                'dstProblems' => $sometimesSame,
                'maxDeltaMinutes' => $maxDeltaMinutes,
            ];
        }
    }

    /**
     * @return array {
     *     @var int[] $timestamps
     *     @var string[] $databaseDatetimes
     * }
     */
    protected function getTimestamps()
    {
        $cache = $this->app->make('cache/request')->getItem('ccm/timezone/test-timestamps');
        if ($cache->isMiss()) {
            // Let's check the timestamp at solstices,
            // to be sure we also check potential daylight saving time changes.
            $timestamps = [
                mktime(12, 0, 0, 6, 21, date('Y')),
                mktime(12, 0, 0, 12, 21, date('Y')),
            ];
            $db = $this->app->make(Connection::class);
            $sql = 'SELECT ';
            foreach ($timestamps as $index => $timestamp) {
                if ($index > 0) {
                    $sql .= ', ';
                }
                $sql .= "FROM_UNIXTIME($timestamp) as datetime_$index";
            }
            $rs = $db->executeQuery($sql);
            $row = $rs->fetch();
            $rs->closeCursor();
            $databaseDatetimes = [];
            foreach (array_keys($timestamps) as $index) {
                $databaseDatetimes[$index] = $row["datetime_$index"];
            }
            $result = [
                'timestamps' => $timestamps,
                'databaseDatetimes' => $databaseDatetimes,
            ];
            $cache->set($result)->expiresAfter(60)->save();
        } else {
            $result = $cache->get();
        }

        return $result;
    }
}
