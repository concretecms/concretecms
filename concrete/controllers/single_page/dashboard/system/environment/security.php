<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Environment;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Page\Controller\DashboardPageController;

class Security extends DashboardPageController
{
    public function view()
    {
        /** @var Repository $config */
        $config = $this->app->make(Repository::class);
        $content_security_policy = (array) $config->get('concrete.security.misc.content_security_policy');
        $this->set('content_security_policy', implode(PHP_EOL, $content_security_policy));
        $this->set('strict_transport_security', $config->get('concrete.security.misc.strict_transport_security'));
        $this->set('x_frame_options', $config->get('concrete.security.misc.x_frame_options'));
    }

    public function submit()
    {
        if (!$this->token->validate('update_security_policy')) {
            $this->error->add($this->token->getErrorMessage());
        }

        if (!$this->error->has()) {
            $content_security_policy = preg_split('/[\r\n]+/ms', (string) $this->post('content_security_policy'), -1, PREG_SPLIT_NO_EMPTY);
            if (count($content_security_policy) === 1) {
                $content_security_policy = $content_security_policy[0];
            } elseif (count($content_security_policy) === 0) {
                $content_security_policy = false;
            }

            $strict_transport_security = trim($this->post('strict_transport_security'));
            if (empty($strict_transport_security)) {
                $strict_transport_security = false;
            }

            $x_frame_options = trim($this->post('x_frame_options'));
            if (empty($x_frame_options)) {
                $x_frame_options = false;
            }

            /** @var Repository $config */
            $config = $this->app->make(Repository::class);
            $config->save('concrete.security.misc', [
                'content_security_policy' => $content_security_policy,
                'strict_transport_security' => $strict_transport_security,
                'x_frame_options' => $x_frame_options,
            ]);

            $this->flash('success', t('The settings has been successfully updated.'));

            return $this->buildRedirect([$this->getPageObject()]);
        }
    }
}
