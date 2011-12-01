<?php
defined('C5_EXECUTE') or die("Access Denied.");
Loader::model("system/captcha/library");

class DashboardSystemPermissionsCaptchaController extends DashboardBaseController {
	
	public function view() {
		$list = SystemCaptchaLibrary::getList();
		$captchas = array();
		foreach($list as $sc) {
			$captchas[$sc->getSystemCaptchaLibraryHandle()] = $sc->getSystemCaptchaLibraryName();
		}
		$activeHandle = '';
		$scl = SystemCaptchaLibrary::getActive();
		if (is_object($scl)) {
			$activeHandle = $scl->getSystemCaptchaLibraryHandle();
		}
		$this->set('activeCaptcha', $activeHandle);
		$this->set('captchas', $captchas);
	}
	
	public function captcha_saved() {
		$this->set('message', t('Captcha settings saved.'));
		$this->view();
	}
	
	public function update_captcha() {
		if (Loader::helper("validation/token")->validate('update_captcha')) {
			$scl = SystemCaptchaLibrary::getByHandle($this->post('activeCaptcha'));
			if (is_object($scl)) {
				$scl->activate();
				
				$this->redirect('/dashboard/system/permissions/captcha', 'captcha_saved');
			} else {
				$this->error->add(t('Invalid captcha library.'));
			}
		} else {
			$this->error->add(Loader::helper('validation/token')->getErrorMessage());
		}
		$this->view();
	}
}