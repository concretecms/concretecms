<?php
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_System_Environment_Proxy extends DashboardBaseController {
	
	public function view() {
		$httpProxyHost = Config::get('HTTP_PROXY_HOST');
		//Log::addEntry('Proxy Host view config: ' . $httpProxyHost);
		$httpProxyPort = Config::get('HTTP_PROXY_PORT');
		$httpProxyUser = Config::get('HTTP_PROXY_USER');
		$httpProxyPwd = Config::get('HTTP_PROXY_PWD');
		$this->set('http_proxy_host', $httpProxyHost);
		$this->set('http_proxy_port', $httpProxyPort);
		$this->set('http_proxy_user', $httpProxyUser);
		$this->set('http_proxy_pwd', $httpProxyPwd);
	}
	
	public function update_proxy() {
		if ($this->token->validate("update_proxy")) {
			if ($this->isPost()) {
				//Log::addEntry('Proxy Host: ' . $this->post('http_proxy_host'));
				Config::save('HTTP_PROXY_HOST', $this->post('http_proxy_host'));
				Config::save('HTTP_PROXY_PORT', $this->post('http_proxy_port'));
				Config::save('HTTP_PROXY_USER', $this->post('http_proxy_user'));
				Config::save('HTTP_PROXY_PWD', $this->post('http_proxy_pwd'));
				//Log::addEntry('Proxy Host config: ' . Config::get('HTTP_PROXY_HOST'));
				$this->redirect('/dashboard/system/environment/proxy', 'proxy_saved');
			}
		} else {
			$this->set('error', array($this->token->getErrorMessage()));
		}
	}
	
	public function proxy_saved(){
		$this->set('message', t('Proxy configuration saved.'));
		$this->view();
	}	
}