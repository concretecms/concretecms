<?php
namespace Concrete\Controller\Backend;

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Error\UserMessageException;
use Symfony\Component\HttpFoundation\JsonResponse;

class PrivacyPolicy extends AbstractController
{

    public function acceptPrivacyPolicy()
    {
        $h = $this->app->make('helper/concrete/dashboard');
        if ($h->canRead()) {
            if ($this->app->make('token')->validate('accept_privacy_policy', $this->request->query->get('ccm_token'))) {
                $config = $this->app->make('config/database');
                $config->save('app.privacy_policy_accepted', true);
                return new JsonResponse(['accept_privacy_policy' => true]);
            }
        }
        throw new UserMessageException(t('Access Denied.'));
    }


}
