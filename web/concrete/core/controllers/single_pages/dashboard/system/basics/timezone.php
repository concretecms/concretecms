<?php

defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_System_Basics_Timezone extends DashboardBaseController {

     public function view(){
          $this->set('user_timezones',ENABLE_USER_TIMEZONES);
     }

     public function timezone_saved() {
		$this->set('message', t("User time zones have been saved."));
		$this->view();
	}

     public function update() {
          if ($this->token->validate("update_timezone")) {
          if ($this->isPost()) {
               Config::save('ENABLE_USER_TIMEZONES', ($this->post('user_timezones') ? true : false));
               $message = ($this->post('user_timezones') ? t('User time zones have been enabled') : t('User time zones have been disabled.'));
               $this->redirect('/dashboard/system/basics/timezone', 'timezone_saved');
          }
          } else {
               $this->error->add($this->token->getErrorMessage());
               $this->view();
          }

     }

}