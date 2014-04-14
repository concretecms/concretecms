<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Page_Dashboard_System_Registration_AuthenticationTypes extends DashboardPageController {

	public function view($message = NULL) {

		$ats = AuthenticationType::getList(true);
		$this->set("ats",$ats);
		if (!$this->error) {
			$this->error = Loader::helper('validation/error');
		}
		$errors = array();
		$errors[0] = 'Invalid Error Code.';
		$errors[1] = 'Invalid Authentication Type';
		if (Session::has('authenticationTypesErrorCode')) {
			$atec = Session::get('authenticationTypesErrorCode');
			Session::remove('authenticationTypesErrorCode');
			if (!isset($errors[$atec])) {
				$atec = 0;
			}
			$this->error->add($errors[$atec]);
		}
		$this->set('error',$this->error);
	}

	public function reorder() {
		$order = $this->post('order');
		$l = count($order);
		for ($i=0;$i<$l;$i++){
			try {
				$at = AuthenticationType::getByID($order[$i]);
				$at->setAuthenticationTypeDisplayOrder($i);
			} catch (exception $e){}
		}
		exit;
	}

	public function enable($atid) {
		$this->error = Loader::helper('validation/error');
		try {
			$at = AuthenticationType::getByID($atid);
			$at->enable();
		} catch (Exception $e) {
			$this->error->add($e->getMessage());
		}
		$this->set('message',$at->getAuthenticationTypeName()." authentication has been enabled.");
		$this->view();
	}

	public function disable($atid) {
		$this->error = Loader::helper('validation/error');
		try {
			$at = AuthenticationType::getByID($atid);
			$at->disable();
		} catch (Exception $e) {
			$this->error->add($e->getMessage());
		}
		$this->set('message',$at->getAuthenticationTypeName()." authentication has been disabled.");
		$this->view();
	}

	public function save($atid) {
		$values = $this->post();
		try {
			$at = AuthenticationType::getByID($atid);
			try {
				$at->controller->saveAuthenticationType($values);
			} catch (Exception $e) {
				$this->error->add($e->getMessage());
				$this->set('error',$this->error);
			}
		} catch (Exception $e) {
			Session::set('authenticationTypesErrorCode', 1);
			$this->redirect('dashboard/system/registration/authentication_types/');
			exit;
		}
		$this->set('message',$at->getAuthenticationTypeName()." authentication has been saved.");
		$this->view();
	}

	public function edit($atid) {
		try {
			$at = AuthenticationType::getByID($atid);
		} catch (Exception $e) {
			Session::set('authenticationTypesErrorCode', 1);
			$this->redirect('dashboard/system/registration/authentication_types/');
			exit;
		}
		$this->set('at',$at);
		$this->set('editmode',true);
	}

}
