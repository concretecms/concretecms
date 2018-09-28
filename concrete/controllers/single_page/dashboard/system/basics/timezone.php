<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Basics;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Database\Connection\Timezone as ConnectionTimezone;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use DateTimeZone;
use Exception;

class Timezone extends DashboardSitePageController
{
    /**
     * @var ConnectionTimezone|null
     */
    private $connectionTimezone;

    /**
     * @return ConnectionTimezone
     */
    protected function getConnectionTimezone()
    {
        if ($this->connectionTimezone === null) {
            $this->connectionTimezone = $this->app->make(ConnectionTimezone::class);
        }

        return $this->connectionTimezone;
    }

    public function view()
    {
        $this->requireAsset('selectize');
        $dh = $this->app->make('date');
        $siteConfig = $this->getSite()->getConfigRepository();
        $config = $this->app->make('config');
        $this->set('user_timezones', $config->get('concrete.misc.user_timezones'));
        $this->set('timezone', $siteConfig->get('timezone'));
        $this->set('timezones', $dh->getGroupedTimezones());
        $phpTimezone = $config->get('app.server_timezone');
        $this->set('serverTimezonePHP', $dh->getTimezoneName($phpTimezone));

        $ctz = $this->getConnectionTimezone();

        $db = $this->app->make(Connection::class);
        $this->set('serverTimezoneDB', $ctz->getDatabaseTimezoneName());
        $deltaError = $ctz->getDeltaTimezone($phpTimezone);
        if ($deltaError === null) {
            $this->set('dbTimezoneOk', true);
        } else {
            $deltaError = $ctz->describeDeltaTimezone($deltaError);
            $this->set('dbTimezoneOk', false);
            $this->set('dbDeltaDescription', $deltaError);
            $this->set('compatibleTimezones', $ctz->getCompatibleTimezones());
        }
    }

    public function update()
    {
        if ($this->token->validate('update_timezone')) {
            if ($this->request->isPost()) {
                $config = $this->app->make('config');
                $siteConfig = $this->getSite()->getConfigRepository();
                $oldValue = $config->get('concrete.misc.user_timezones') ? true : false;
                $newValue = $this->request->request->get('user_timezones') ? true : false;
                $messages = [];
                if ($oldValue !== $newValue) {
                    $config->save('concrete.misc.user_timezones', $newValue);
                    $messages[] = $newValue ? t('User time zones have been enabled') : t('User time zones have been disabled.');
                }
                $oldValue = (string) $siteConfig->get('timezone');
                $newValue = $this->request->request->get('timezone');
                if (is_string($newValue) && strcasecmp($newValue, $oldValue) !== 0) {
                    $siteConfig->save('timezone', $newValue);
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
}
