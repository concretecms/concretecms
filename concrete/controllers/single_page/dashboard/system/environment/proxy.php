<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Environment;

use Concrete\Core\Http\Request;
use Concrete\Core\Page\Controller\DashboardPageController;

class Proxy extends DashboardPageController
{
    public function view()
    {
        $config = $this->app->make('config');
        $this->set('http_proxy_host', $config->get('concrete.proxy.host'));
        $this->set('http_proxy_port', $config->get('concrete.proxy.port'));
        $this->set('http_proxy_user', $config->get('concrete.proxy.user'));
        $this->set('http_proxy_pwd', $config->get('concrete.proxy.password'));
    }

    public function update_proxy()
    {
        if (!$this->token->validate('update_proxy')) {
            $this->error->add($this->token->getErrorMessage());

            return;
        }

        if ($this->request->isMethod(Request::METHOD_POST)) {
            $postRequest = $this->request->request;

            $config = $this->app->make('config');
            $config->save('concrete.proxy.host', $postRequest->get('http_proxy_host'));
            $config->save('concrete.proxy.port', $postRequest->get('http_proxy_port'));
            $config->save('concrete.proxy.user', $postRequest->get('http_proxy_user'));
            $config->save('concrete.proxy.password', $postRequest->get('http_proxy_pwd'));

            return $this->buildRedirect($this->action('proxy_saved'));
        }
    }

    public function proxy_saved()
    {
        $this->set('success', t('Proxy configuration saved.'));
        $this->view();
    }
}
