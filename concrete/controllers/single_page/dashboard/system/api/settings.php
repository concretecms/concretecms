<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Api;

use Concrete\Core\Page\Controller\DashboardPageController;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

class Settings extends DashboardPageController
{

    public function view()
    {
        $config = $this->app->make("config");
        $enable_api = (bool) $config->get('concrete.api.enabled');
        if ($enable_api) {
            $r = $this->app->make(ClientRepositoryInterface::class);
            $clients = $r->findAll();
            $this->set('clients', $clients);
        }
        $this->set("enable_api", $enable_api);

        $grantTypes = (array) $config->get('concrete.api.grant_types');
        $this->set('grantTypes', $grantTypes);
        $this->set('availableGrantTypes', $this->getAvailableGrantTypes());
    }

    protected function getAvailableGrantTypes()
    {
        return [
            'client_credentials' => t('Client Credentials'),
            'authorization_code' => t('Authorization Code'),
            'password_credentials' => t('Password Credentials'),
        ];
    }

    public function submit()
    {
        if (!$this->token->validate('submit')) {
            $this->error->add($this->token->getErrorMessage());
        }

        if (!$this->error->has()) {
            $enable_api = $this->request->request->get("enable_api") ? true : false;
            $config = $this->app->make('config');
            $config->save('concrete.api.enabled', $enable_api);

            if ($enable_api) {
                $enabledGrantTypes = (array)$this->request->request->get('enabledGrantTypes');
                foreach ($this->getAvailableGrantTypes() as $type => $label) {
                    $key = "concrete.api.grant_types.{$type}";
                    $enabled = in_array($type, $enabledGrantTypes);
                    $config->save($key, $enabled);
                }
            }
            
            $this->flash('success', t("API Settings updated successfully."));
            return $this->redirect('/dashboard/system/api/settings');
        }
    }

}
