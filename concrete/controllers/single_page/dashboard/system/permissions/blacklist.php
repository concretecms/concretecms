<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Permissions;

use Concrete\Core\Page\Controller\DashboardPageController;

class Blacklist extends DashboardPageController
{
    public function view()
    {
        $config = $this->app->make('config');
        $this->set('banEnabled', $config->get('concrete.security.ban.ip.enabled') ? true : false);
        $this->set('allowedAttempts', (int) $config->get('concrete.security.ban.ip.attempts'));
        $this->set('attemptsTimeWindow', (int) $config->get('concrete.security.ban.ip.time'));
        $this->set('banDuration', (int) $config->get('concrete.security.ban.ip.length'));
    }

    public function update_ipblacklist()
    {
        if ($this->token->validate('update_ipblacklist')) {
            $post = $this->request->request;
            $valn = $this->app->make('helper/validation/numbers');
            /* @var \Concrete\Core\Utility\Service\Validation\Numbers $valn */

            $enabled = $post->get('banEnabled') ? true : false;

            $allowedAttempts = $post->get('allowedAttempts');
            if (!$valn->integer($allowedAttempts) || ($allowedAttempts = (int) $allowedAttempts) < 1) {
                $this->error->add(t('Please specify a number greater than zero for the maximum number of failed login attempts'));
            }

            $attemptsTimeWindow = $post->get('attemptsTimeWindow');
            if (!$valn->integer($attemptsTimeWindow) || ($attemptsTimeWindow = (int) $attemptsTimeWindow) < 1) {
                $this->error->add(t('Please specify a number greater than zero for the failed login attempts time window'));
            }

            if ($post->get('banDurationUnlimited')) {
                $banDuration = 0;
            } else {
                $banDuration = $post->get('banDuration');
                if (!$valn->integer($banDuration) || ($banDuration = (int) $banDuration) < 1) {
                    $this->error->add(t('Please specify a number greater than zero for the ban duration'));
                }
            }

            if (!$this->error->has()) {
                $config = $this->app->make('config');
                $config->save('concrete.security.ban.ip.enabled', $enabled);
                $config->save('concrete.security.ban.ip.attempts', $allowedAttempts);
                $config->save('concrete.security.ban.ip.time', $attemptsTimeWindow);
                $config->save('concrete.security.ban.ip.length', $banDuration);
                $this->flash('success', t('IP Blacklist settings saved.'));
                $this->redirect($this->action(''));
            }
        } else {
            $this->error->add($this->token->getErrorMessage());
        }
    }
}
