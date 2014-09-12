<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Basics;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Loader;


class Timezone extends DashboardPageController {

     public function view(){
          $this->set('user_timezones',Config::get('concrete.misc.user_timezones'));
     }

     public function timezone_saved() {
		$this->set('success', t("User time zones have been saved."));
		$this->view();
	}

     public function update() {
          if ($this->token->validate("update_timezone")) {
          if ($this->isPost()) {
               Config::save('concrete.misc.user_timezones', ($this->post('user_timezones') ? true : false));
               $message = ($this->post('user_timezones') ? t('User time zones have been enabled') : t('User time zones have been disabled.'));
               $this->redirect('/dashboard/system/basics/timezone', 'timezone_saved');
          }
          } else {
               $this->error->add($this->token->getErrorMessage());
               $this->view();
          }

     }

}
