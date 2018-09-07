<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Permissions;

use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

class Security extends DashboardPageController
{
    public function view()
    {
        $config = $this->app->make('config');
        $this->set('invalidateOnIPMismatch', (bool) $config->get('concrete.security.session.invalidate_on_ip_mismatch'));
        $this->set('invalidateOnUserAgentMismatch', (bool) $config->get('concrete.security.session.invalidate_on_user_agent_mismatch'));
    }

    public function save()
    {
        if (!$this->token->validate('ccm-perm-sec')) {
            $this->error->add($this->token->getErrorMessage());
        } else {
            $config = $this->app->make('config');
            $post = $this->request->request;
            $config->save('concrete.security.session.invalidate_on_ip_mismatch', (bool) $post->get('invalidateOnIPMismatch'));
            $config->save('concrete.security.session.invalidate_on_user_agent_mismatch', (bool) $post->get('invalidateOnUserAgentMismatch'));
        }
        if ($this->error->has()) {
            $this->view();
        } else {
            $this->flash('success', t('Options saved successfully.'));

            return $this->app->make(ResponseFactoryInterface::class)->redirect(
                $this->app->make(ResolverManagerInterface::class)->resolve(['/dashboard/system/permissions/security']),
                302
            );
        }
    }
}
