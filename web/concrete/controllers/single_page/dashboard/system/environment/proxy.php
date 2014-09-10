<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Environment;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Loader;

class Proxy extends DashboardPageController {

	public function view() {
		$httpProxyHost = Config::get('concrete.proxy.host');
		//Log::addEntry('Proxy Host view config: ' . $httpProxyHost);
		$httpProxyPort = Config::get('concrete.proxy.port');
		$httpProxyUser = Config::get('concrete.proxy.user');
		$httpProxyPwd = Config::get('concrete.proxy.password');
		$this->set('http_proxy_host', $httpProxyHost);
		$this->set('http_proxy_port', $httpProxyPort);
		$this->set('http_proxy_user', $httpProxyUser);
		$this->set('http_proxy_pwd', $httpProxyPwd);
	}

	public function update_proxy() {
		if ($this->token->validate("update_proxy")) {
			if ($this->isPost()) {
				//Log::addEntry('Proxy Host: ' . $this->post('http_proxy_host'));
				Config::save('concrete.proxy.host', $this->post('http_proxy_host'));
				Config::save('concrete.proxy.port', $this->post('http_proxy_port'));
				Config::save('concrete.proxy.user', $this->post('http_proxy_user'));
				Config::save('concrete.proxy.password', $this->post('http_proxy_pwd'));
				//Log::addEntry('Proxy Host config: ' . Config::get('concrete.proxy.host'));
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
