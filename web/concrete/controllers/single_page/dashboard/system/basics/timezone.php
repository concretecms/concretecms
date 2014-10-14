<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Basics;

use Concrete\Core\Page\Controller\DashboardPageController;
use Config;

class Timezone extends DashboardPageController
{

    public function view()
    {
        $this->set('user_timezones', Config::get('concrete.misc.user_timezones'));
        $this->set('timezone', Config::get('app.timezone'));

        $timezones = \DateTimeZone::listIdentifiers();
        $grouped_timezones = array();
        foreach ($timezones as $timezone) {
            $exploded = explode('/', $timezone);
            if (count($exploded) > 1) {
                if (!isset($grouped_timezones[$exploded[0]])) {
                    $grouped_timezones[$exploded[0]] = array();
                }
                $grouped_timezones[$exploded[0]][] = $timezone;
            } else {
                if (!isset($grouped_timezones['Misc'])) {
                    $grouped_timezones['Misc'] = array();
                }
                $grouped_timezones['Misc'][] = $timezone;
            }
        }

        $this->set('timezones', $grouped_timezones);
    }

    public function timezone_saved()
    {
        $this->set('success', t("User time zones have been saved."));
        $this->view();
    }

    public function update()
    {
        if ($this->token->validate("update_timezone")) {
            if ($this->isPost()) {
                Config::save('concrete.misc.user_timezones', ($this->post('user_timezones') ? true : false));
                $message = ($this->post('user_timezones') ? t('User time zones have been enabled') : t(
                    'User time zones have been disabled.'));

                if (strtolower(\Config::get('app.timezone')) !== strtolower($this->post('timezone'))) {
                    \Config::save('app.timezone', $this->post('timezone'));
                }
                $this->redirect('/dashboard/system/basics/timezone', 'timezone_saved');
            }
        } else {
            $this->error->add($this->token->getErrorMessage());
            $this->view();
        }

    }

}
